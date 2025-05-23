<?php 
session_start();
include '../../Base de datos/conexion.php';

$Usuario_ID = $_SESSION['Usuario_ID']; // Asegúrate de que este valor esté en la sesión

$stmt = $conn->prepare("SELECT * FROM notificaciones WHERE usuario_id = ? ORDER BY fecha DESC");
$stmt->execute([$Usuario_ID]);
$notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PRODCONS - Notificaciones--</title>
  <link href='noti-box.css' rel="stylesheet">

</head>
<body>
  <section class="notificaciones">
    <div class="notifications-container">
      <?php if (empty($notificaciones)): ?>
        <div class="notification">No tienes notificaciones nuevas.</div>
      <?php else: ?>
        <?php foreach ($notificaciones as $noti): ?>
          <div class="notification" data-post-id="<?= $noti['post_id'] ?>">
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
                <div class="user-comment"><strong><?= htmlspecialchars($noti['autor']) ?></strong> comentó: “<?= htmlspecialchars($noti['mensaje']) ?>”</div>
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
                <div class="admin-comment">Motivo: “<?= htmlspecialchars($noti['mensaje']) ?>”</div>
                <a href="ver-publicacion.php?id=<?= $noti['post_id'] ?>" class="view-post-button">Ver publicación</a>
                <div class="time"><?= htmlspecialchars($noti['fecha']) ?></div>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</body>
</html>