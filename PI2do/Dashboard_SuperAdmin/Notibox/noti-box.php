<?php 
session_start();

// Verificar sesión y rol
if (!isset($_SESSION['Usuario_ID']) || !isset($_SESSION['Rol'])) {
    header('Location: /PRODCONS/PI2do/inicio_sesion/login.php');
    exit();
}

if ($_SESSION['Rol'] !== 'Super Admin') {
    header('Location: /PRODCONS/PI2do/inicio_sesion/login.php?error=acceso_denegado');
    exit();
}

// Inicializar variables
$Usuario_ID = $_SESSION['Usuario_ID'];
$error = '';
$articulos_pendientes = [];

try {
    // Incluir y conectar
    require_once '../../Base de datos/conexion.php';
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    
    // Obtener artículos pendientes
    $sql = "SELECT a.*, u.Nombre as autor_nombre 
            FROM articulos a 
            JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
            WHERE a.Estado = 'pendiente' 
            ORDER BY a.Fecha de Creacion DESC";
    
    $result = $conexion->ejecutar_consulta($sql);
    if ($result) {
        $articulos_pendientes = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        throw new Exception("Error en la consulta: " . $conexion->conexion->error);
    }

    // Procesar la acción de aceptar/rechazar artículo
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && isset($_POST['ID_Articulo'])) {
        $articulo_id = $_POST['ID_Articulo'];
        $accion = $_POST['accion'];
        $mensaje = $_POST['mensaje'] ?? '';

        if ($accion === 'aceptar') {
            // Actualizar estado del artículo
            $sql = "UPDATE articulos SET Estado = 'publicado' WHERE ID_Articulo = ?";
            $stmt = $conexion->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $articulo_id);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception("Error actualizando el estado del artículo: " . $conexion->conexion->error);
            }

            // Crear notificación para el editor
            $sql = "INSERT INTO noti_box (Usuario_ID, Mensaje, Fecha, Leido) 
                    SELECT Usuario_ID, 'Tu artículo ha sido aprobado y publicado', NOW(), 0 
                    FROM articulos WHERE ID_Articulo = ?";
            $stmt = $conexion->conexion->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $articulo_id);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new Exception("Error creando la notificación: " . $conexion->conexion->error);
            }
        } elseif ($accion === 'rechazar') {
            if (empty($mensaje)) {
                $error = "Debes proporcionar un motivo para el rechazo.";
            } else {
                // Actualizar estado del artículo
                $sql = "UPDATE articulos SET Estado = 'rechazado' WHERE ID_Articulo = ?";
                $stmt = $conexion->conexion->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $articulo_id);
                    $stmt->execute();
                    $stmt->close();

                    // Crear notificación para el editor
                    $sql = "INSERT INTO noti_box (Usuario_ID, Mensaje, Fecha, Leido) 
                            SELECT Usuario_ID, ?, NOW(), 0 
                            FROM articulos WHERE ID_Articulo = ?";
                    $stmt = $conexion->conexion->prepare($sql);
                    if ($stmt) {
                        $mensaje_notif = "Tu artículo ha sido rechazado. Motivo: " . $mensaje;
                        $stmt->bind_param("si", $mensaje_notif, $articulo_id);
                        $stmt->execute();
                        $stmt->close();
                    }
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

</body>
</html>