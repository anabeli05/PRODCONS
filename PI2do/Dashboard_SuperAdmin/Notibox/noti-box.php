<?php 
session_start();
include '../../Base de datos/conexion.php';

$Usuario_ID = $_SESSION['Usuario_ID']; // Asegúrate de que este valor esté en la sesión

// Obtener blogs pendientes de revisión
$stmt_pendientes = $conn->prepare("SELECT p.*, u.nombre as autor_nombre 
                                 FROM posts p 
                                 JOIN usuarios u ON p.autor = u.id 
                                 WHERE p.estado = 'pendiente' 
                                 ORDER BY p.fecha_creacion DESC");
$stmt_pendientes->execute();
$blogs_pendientes = $stmt_pendientes->fetchAll(PDO::FETCH_ASSOC);

// Obtener notificaciones incluyendo mensajes
$stmt = $conn->prepare("SELECT n.*, u.nombre as remitente_nombre 
                       FROM notificaciones n 
                       LEFT JOIN usuarios u ON n.remitente_id = u.id 
                       WHERE n.usuario_id = ? 
                       ORDER BY n.fecha DESC");
$stmt->execute([$Usuario_ID]);
$notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar la acción de aceptar/rechazar blog
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        // Eliminado: envío de mensajes a editores
        // if ($_POST['accion'] === 'enviar_mensaje' && isset($_POST['editor_id']) && isset($_POST['mensaje'])) {
        //     ... código eliminado ...
        // }
        if (isset($_POST['post_id'])) {
            $post_id = $_POST['post_id'];
            $accion = $_POST['accion'];
            $mensaje = $_POST['mensaje'] ?? '';

            if ($accion === 'aceptar') {
                // Actualizar estado del post
                $stmt_update = $conn->prepare("UPDATE posts SET estado = 'publicado' WHERE id = ?");
                $stmt_update->execute([$post_id]);

                // Crear notificación para el autor
                $stmt_notif = $conn->prepare("INSERT INTO notificaciones (usuario_id, tipo, mensaje, post_id, fecha) 
                                            SELECT autor, 'aceptada', 'Tu publicación ha sido aceptada', ?, NOW() 
                                            FROM posts WHERE id = ?");
                $stmt_notif->execute([$post_id, $post_id]);

            } elseif ($accion === 'rechazar') {
                // Actualizar estado del post
                $stmt_update = $conn->prepare("UPDATE posts SET estado = 'rechazado' WHERE id = ?");
                $stmt_update->execute([$post_id]);

                // Crear notificación para el autor
                $stmt_notif = $conn->prepare("INSERT INTO notificaciones (usuario_id, tipo, mensaje, post_id, fecha) 
                                            SELECT autor, 'rechazada', ?, ?, NOW() 
                                            FROM posts WHERE id = ?");
                $stmt_notif->execute([$mensaje, $post_id, $post_id]);
            }

            // Redirigir para evitar reenvío del formulario
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
        // NUEVO: Eliminar mensaje
        elseif ($_POST['accion'] === 'eliminar_mensaje' && isset($_POST['noti_id'])) {
            $noti_id = $_POST['noti_id'];
            $stmt_del = $conn->prepare("DELETE FROM notificaciones WHERE id = ? AND tipo = 'mensaje' AND (usuario_id = ? OR remitente_id = ?)");
            $stmt_del->execute([$noti_id, $Usuario_ID, $Usuario_ID]);
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PRODCONS - Panel de Control</title>
  <link href='noti-box.css' rel="stylesheet">
</head>
<body>
  <!-- Sección de Blogs Pendientes -->
  <section class="blogs-pendientes">
    <h2>Blogs Pendientes de Revisión</h2>
    <div class="pending-blogs-container">
      <?php if (empty($blogs_pendientes)): ?>
        <div class="no-pending">No hay blogs pendientes de revisión.</div>
      <?php else: ?>
        <?php foreach ($blogs_pendientes as $blog): ?>
          <div class="blog-card">
            <h3><?= htmlspecialchars($blog['titulo']) ?></h3>
            <p class="author">Autor: <?= htmlspecialchars($blog['autor_nombre']) ?></p>
            <p class="date">Fecha: <?= htmlspecialchars($blog['fecha_creacion']) ?></p>
            <div class="blog-content">
              <?= htmlspecialchars(substr($blog['contenido'], 0, 200)) ?>...
            </div>
            <div class="blog-actions">
              <form method="post" class="action-form">
                <input type="hidden" name="post_id" value="<?= $blog['id'] ?>">
                <input type="hidden" name="accion" value="aceptar">
                <button type="submit" class="btn-accept">Aceptar</button>
              </form>
              <form method="post" class="action-form">
                <input type="hidden" name="post_id" value="<?= $blog['id'] ?>">
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

  <!-- Sección de Notificaciones -->
  <section class="notificaciones">
    <h2>Notificaciones</h2>
    <div class="notifications-container">
      <?php if (empty($notificaciones)): ?>
        <div class="notification">No tienes notificaciones nuevas.</div>
      <?php else: ?>
        <?php foreach ($notificaciones as $noti): ?>
          <div class="notification" data-post-id="<?= $noti['post_id'] ?? '' ?>">
            <?php if ($noti['tipo'] === 'like'): ?>
              <div class="like-icon">❤️</div>
              <div class="content">
                <div class="like-summary"><?= htmlspecialchars($noti['mensaje']) ?></div>
                <a href="ver-publicacion.php?id=<?= $noti['post_id'] ?>" class="view-post-button">Ver publicación</a>
                <div class="time"><?= htmlspecialchars($noti['fecha']) ?></div>
              </div>
            <?php elseif ($noti['tipo'] === 'comentario'): ?>
              <div class="avatar" style="background-image: url('user.jpg')"></div>
              <div class="content">
                <div class="user-comment"><strong><?= htmlspecialchars($noti['autor']) ?></strong> comentó: "<?= htmlspecialchars($noti['mensaje']) ?>"</div>
                <a href="ver-publicacion.php?id=<?= $noti['post_id'] ?>" class="view-post-button">Ver publicación</a>
                <div class="time"><?= htmlspecialchars($noti['fecha']) ?></div>
              </div>
            <?php elseif ($noti['tipo'] === 'aceptada'): ?>
              <div class="avatar" style="background-image: url('perfil.png')"></div>
              <div class="content">
                <div class="status-accepted">Tu publicación fue aceptada y ya está en línea</div>
                <a href="ver-publicacion.php?id=<?= $noti['post_id'] ?>" class="view-post-button">Ver publicación</a>
                <div class="time"><?= htmlspecialchars($noti['fecha']) ?></div>
              </div>
            <?php elseif ($noti['tipo'] === 'rechazada'): ?>
              <div class="avatar" style="background-image: url('perfil.png')"></div>
              <div class="content">
                <div class="status-rejected">Tu publicación fue rechazada</div>
                <div class="rejection-details">
                  <h4>Motivo del Rechazo:</h4>
                  <p class="rejection-message"><?= htmlspecialchars($noti['mensaje']) ?></p>
                  <div class="rejection-actions">
                    <a href="ver-publicacion.php?id=<?= $noti['post_id'] ?>" class="view-post-button">Ver publicación</a>
                    <button class="edit-post-button" onclick="editarPublicacion(<?= $noti['post_id'] ?>)">Editar Publicación</button>
                  </div>
                </div>
                <div class="time"><?= htmlspecialchars($noti['fecha']) ?></div>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>

  <style>
    .rejection-details {
        background-color: #fff3f3;
        border-left: 4px solid #ff4444;
        padding: 15px;
        margin: 10px 0;
        border-radius: 4px;
    }

    .rejection-details h4 {
        color: #ff4444;
        margin: 0 0 10px 0;
    }

    .rejection-message {
        color: #666;
        margin: 0 0 15px 0;
        line-height: 1.4;
    }

    .rejection-actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .edit-post-button {
        background-color: #4CAF50;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
    }

    .edit-post-button:hover {
        background-color: #45a049;
    }

    .delete-message-button {
        background: none;
        border: none;
        color: #ff4444;
        font-size: 18px;
        cursor: pointer;
        margin-left: 10px;
        vertical-align: middle;
        transition: color 0.2s;
    }
    .delete-message-button:hover {
        color: #b71c1c;
    }
  </style>

  <script>
  function editarPublicacion(postId) {
      // Redirigir a la página de edición del post
      window.location.href = `editar-publicacion.php?id=${postId}`;
  }
  </script>
</body>
</html>