<?php 
session_start();
include '../../Base de datos/conexion.php';

// Verificar que el usuario esté autenticado y sea SuperAdmin
if (!isset($_SESSION['Usuario_ID']) || !isset($_SESSION['Rol'])) {
    header('Location: /PRODCONS/PI2do/inicio_sesion/login.php');
    exit();
}

if ($_SESSION['Rol'] !== 'Super Admin') {
    header('Location: /PRODCONS/PI2do/inicio_sesion/login.php?error=acceso_denegado');
    exit();
}

$Usuario_ID = $_SESSION['Usuario_ID'];

// Inicializar la conexión
$conexion = new Conexion();
try {
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    // Obtener artículos pendientes de revisión
    $stmt_pendientes = $conn->prepare("SELECT a.*, u.Nombre as autor_nombre 
                                     FROM articulos a 
                                     JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                                     WHERE a.Estado = 'pendiente' 
                                     ORDER BY a.`Fecha de Creacion` DESC");
    
    if ($stmt_pendientes) {
        $stmt_pendientes->execute();
        
        // En lugar de fetchAll, usar bind_result y fetch para MySQLi
        $articulos_pendientes = [];
        $stmt_pendientes->bind_result($ID_Articulo, $Titulo, $Contenido, $Bibliografias, $Usuario_ID_Art, $Fecha_de_Creacion, $Fecha_de_Publicacion, $Estado, $Motivo_de_Rechazo, $autor_nombre); // Vincula todas las columnas seleccionadas

        while ($stmt_pendientes->fetch()) {
            // Construye el array asociativo manualmente
            $articulos_pendientes[] = [
                'ID_Articulo' => $ID_Articulo,
                'Titulo' => $Titulo,
                'Contenido' => $Contenido,
                'Bibliografias' => $Bibliografias,
                'Usuario_ID' => $Usuario_ID_Art, // Usar un nombre de variable diferente si ya usaste $Usuario_ID
                'Fecha de Creacion' => $Fecha_de_Creacion,
                'Fecha de Publicacion' => $Fecha_de_Publicacion,
                'Estado' => $Estado,
                'Motivo de Rechazo' => $Motivo_de_Rechazo,
                'autor_nombre' => $autor_nombre
            ];
        }
        $stmt_pendientes->close();
    } else {
         // Manejar error en la preparación de la consulta
         throw new Exception("Error preparando la consulta de artículos pendientes: " . $conn->error);
    }

    // Procesar la acción de aceptar/rechazar artículo
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && isset($_POST['ID_Articulo'])) {
        $articulo_id = $_POST['ID_Articulo'];
        $accion = $_POST['accion'];
        $mensaje = $_POST['mensaje'] ?? '';

        // Re-abrir conexión si se cerró en el finally del bloque de arriba
         // Esto depende de cómo manejes la conexión en tu clase Conexion
         // Si la clase mantiene la conexión abierta, no necesitas re-abrirla.
         // Si la cierra, necesitarás re-abrirla aquí para las operaciones POST.
         // Asumiendo que la conexión puede haberse cerrado, la re-abrimos:
         $conexion->abrir_conexion();
         $conn = $conexion->conexion;

        if ($accion === 'aceptar') {
            // Actualizar estado del artículo
            $stmt_update = $conn->prepare("UPDATE articulos SET Estado = 'publicado' WHERE ID_Articulo = ?");
            if ($stmt_update) {
                 $stmt_update->bind_param("i", $articulo_id);
                 $stmt_update->execute();
                 $stmt_update->close();
            } else {
                throw new Exception("Error preparando la actualización de estado: " . $conn->error);
            }

            // Crear notificación para el editor
            $stmt_notif = $conn->prepare("INSERT INTO noti_box (Usuario_ID, Mensaje, Fecha, Leido) 
                                        SELECT Usuario_ID, 'Tu artículo ha sido aprobado y publicado', NOW(), 0 
                                        FROM articulos WHERE ID_Articulo = ?");
            if ($stmt_notif) {
                $stmt_notif->bind_param("i", $articulo_id);
                $stmt_notif->execute();
                $stmt_notif->close();
            } else {
                 throw new Exception("Error preparando la inserción de notificación (aceptar): " . $conn->error);
            }

        } elseif ($accion === 'rechazar') {
            if (empty($mensaje)) {
                $error = "Debes proporcionar un motivo para el rechazo.";
            } else {
                // Actualizar estado del artículo
                $stmt_update = $conn->prepare("UPDATE articulos SET Estado = 'rechazado' WHERE ID_Articulo = ?");
                 if ($stmt_update) {
                    $stmt_update->bind_param("i", $articulo_id);
                    $stmt_update->execute();
                    $stmt_update->close();
                 } else {
                     throw new Exception("Error preparando la actualización de estado (rechazar): " . $conn->error);
                 }

                // Crear notificación para el editor
                $stmt_notif = $conn->prepare("INSERT INTO noti_box (Usuario_ID, Mensaje, Fecha, Leido) 
                                            SELECT Usuario_ID, ?, NOW(), 0 
                                            FROM articulos WHERE ID_Articulo = ?");
                 if ($stmt_notif) {
                    $mensaje_notif = "Tu artículo ha sido rechazado. Motivo: " . $mensaje;
                    $stmt_notif->bind_param("si", $mensaje_notif, $articulo_id);
                    $stmt_notif->execute();
                    $stmt_notif->close();
                 } else {
                     throw new Exception("Error preparando la inserción de notificación (rechazar): " . $conn->error);
                 }
            }
        }

        // Redirigir para evitar reenvío del formulario
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
} catch (Exception $e) {
    error_log("Error en noti-box.php: " . $e->getMessage());
    $error = "Error de base de datos: " . $e->getMessage();
} finally {
    if (isset($conexion)) {
        $conexion->cerrar_conexion();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Panel de Control SuperAdmin</title>
    <link href='noti-box.css' rel="stylesheet">
</head>
<body>
    <section class="articulos-pendientes">
        <h2>Artículos Pendientes de Revisión</h2>
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="pending-articles-container">
            <?php if (empty($articulos_pendientes)): ?>
                <div class="no-pending">No hay artículos pendientes de revisión.</div>
            <?php else: ?>
                <?php foreach ($articulos_pendientes as $articulo): ?>
                    <div class="article-card">
                        <h3><?= htmlspecialchars($articulo['Titulo']) ?></h3>
                        <p class="author">Autor: <?= htmlspecialchars($articulo['autor_nombre']) ?></p>
                        <p class="date">Fecha: <?= htmlspecialchars($articulo['Fecha']) ?></p>
                        <div class="article-content">
                            <?= htmlspecialchars(substr($articulo['Contenido'], 0, 200)) ?>...
                        </div>
                        <div class="article-actions">
                            <form method="post" class="action-form">
                                <input type="hidden" name="ID_Articulo" value="<?= $articulo['ID_Articulo'] ?>">
                                <input type="hidden" name="accion" value="aceptar">
                                <button type="submit" class="btn-accept">Aprobar</button>
                            </form>
                            <form method="post" class="action-form">
                                <input type="hidden" name="ID_Articulo" value="<?= $articulo['ID_Articulo'] ?>">
                                <input type="hidden" name="accion" value="rechazar">
                                <input type="text" name="mensaje" placeholder="Motivo del rechazo" required>
                                <button type="submit" class="btn-reject">Rechazar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <style>
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 4px solid #c62828;
        }

        .article-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .article-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-accept {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-reject {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .action-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .action-form input[type="text"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }
    </style>
</body>
</html>