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
            WHERE a.Estado = 'Borrador' 
            ORDER BY `Fecha de Creacion` DESC";
    
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PRODCONS - Panel de Control SuperAdmin</title>
  <link href="noti-box.css" rel="stylesheet"/><!--Error al no querer llamar resuelto temporalmente-->
  <link rel="stylesheet" href="../Dashboard/sidebar.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  
</head>
<style>
body {
    font-family: 'Segoe UI', 'Inter', sans-serif;
    background-color: #f5f7fa;
    margin: 0;
    padding: 0;
}


.articulos-pendientes {
    max-width: 960px;
    margin: 40px auto;
    padding: 0 20px;
}

.articulos-pendientes h2 {
    font-size: 2rem;
    color: #2c3e50;
    margin-bottom: 20px;
}


.error-message {
    background-color: #ffebee;
    color: #c62828;
    padding: 15px;
    margin: 15px 0;
    border-radius: 6px;
    border-left: 5px solid #c62828;
    font-size: 1rem;
}


.article-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.article-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
}

.article-card h3 {
    margin-top: 0;
    font-size: 1.4rem;
    color: #34495e;
}

.article-card .author,
.article-card .date {
    font-size: 0.95rem;
    color: #555;
    margin: 5px 0;
}

.article-content {
    margin-top: 10px;
    font-size: 1rem;
    color: #333;
}


.article-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.btn-accept,
.btn-reject {
    padding: 10px 18px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-accept {
    background-color: #4CAF50;
}

.btn-accept:hover {
    background-color: #388e3c;
    transform: scale(1.05);
}

.btn-reject {
    background-color: #f44336;
}

.btn-reject:hover {
    background-color: #d32f2f;
    transform: scale(1.05);
}

.action-form {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.action-form input[type="text"] {
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    width: 220px;
}


.no-pending {
    background: linear-gradient(135deg, #e0f7fa, #e8f5e9);
    border-left: 6px solid #26a69a;
    padding: 30px 25px;
    margin-top: 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    animation: fadeIn 0.7s ease-out;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.no-post-icon {
    font-size: 3.5rem;
    margin-bottom: 10px;
    animation: bounce 1s infinite alternate;
}

.no-pending h3 {
    font-size: 1.8rem;
    color: #2e7d32;
    margin-bottom: 8px;
}

.no-pending p {
    font-size: 1.1rem;
    color: #388e3c;
    margin-bottom: 20px;
}

.reload-button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #26a69a;
    color: white;
    font-weight: bold;
    border-radius: 25px;
    text-decoration: none;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.reload-button:hover {
    background-color: #00897b;
    transform: scale(1.05);
}


@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes bounce {
    from { transform: translateY(0); }
    to { transform: translateY(-10px); }
}

</style>
<body>
  <?php include('../Dashboard/sidebar.php'); ?>

  <section class="articulos-pendientes">
    <h2>📋 Artículos Pendientes de Revisión</h2>

    <?php if (isset($error)): ?>
      <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="pending-articles-container">
    <?php if (empty($articulos_pendientes)): ?>
    <div class="no-pending">
        <div class="no-post-icon">🎉</div>
        <h3>¡Todo al día!</h3>
        <p>No hay artículos pendientes de revisión por ahora.</p>
    </div>
<?php else: ?>

        <?php foreach ($articulos_pendientes as $articulo): ?>
          <div class="article-card">
            <h3><?= htmlspecialchars($articulo['Titulo']) ?></h3>
            <p class="author">✍️ Autor: <?= htmlspecialchars($articulo['autor_nombre']) ?></p>
            <p class="date">📅 Fecha: <?= htmlspecialchars($articulo['Fecha']) ?></p>
            <div class="article-content">
              <?= htmlspecialchars(substr($articulo['Contenido'], 0, 200)) ?>...
            </div>

            <div class="article-actions">
              <form method="post" class="action-form">
                <input type="hidden" name="ID_Articulo" value="<?= $articulo['ID_Articulo'] ?>">
                <input type="hidden" name="accion" value="aceptar">
                <button type="submit" class="btn-accept">✅ Aprobar</button>
              </form>

              <form method="post" class="action-form">
                <input type="hidden" name="ID_Articulo" value="<?= $articulo['ID_Articulo'] ?>">
                <input type="hidden" name="accion" value="rechazar">
                <input type="text" name="mensaje" placeholder="Motivo del rechazo" required>
                <button type="submit" class="btn-reject">❌ Rechazar</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</body>
</html>