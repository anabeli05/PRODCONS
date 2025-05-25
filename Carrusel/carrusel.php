<?php
require_once __DIR__ . '/../Base de datos/conexion.php';

try {
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    $publicaciones = [];
    $sql = "SELECT titulo, descripcion, link, fecha, autor, imagen FROM publicaciones ORDER BY fecha DESC LIMIT 6";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $publicaciones[] = $row;
        }
    }
} catch (Exception $e) {
    error_log("Error en el carrusel: " . $e->getMessage());
    $publicaciones = [];
} finally {
    if (isset($conexion)) {
        $conexion->cerrar_conexion();
    }
}
?>

<link href="/PRODCONS/Carrusel/carrusel.css" rel="stylesheet">

<div class="carousel-container">
    <div class="carousel">
        <?php if (!empty($publicaciones)): ?>
            <?php foreach ($publicaciones as $pub): ?>
            <div class="carousel-item post">
                <div class="post-header">
                    <img src="<?= htmlspecialchars($pub['imagen']) ?>" alt="<?= htmlspecialchars($pub['titulo']) ?>" class="post-img">
                </div>
                <div class="post-body">
                    <h2><?= htmlspecialchars($pub['titulo']) ?></h2>
                    <p class="descripcion"><?= htmlspecialchars($pub['descripcion']) ?></p>
                    <a href="<?= htmlspecialchars($pub['link']) ?>" class="post-link">Ver más...</a>
                    <span>Publicado el <?= htmlspecialchars($pub['fecha']) ?></span>
                    <span>| Por: <?= htmlspecialchars($pub['autor']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-posts">
                <p>No hay publicaciones disponibles en este momento. ¡Vuelve pronto!</p>
            </div>
        <?php endif; ?>
    </div>
    <?php if (!empty($publicaciones)): ?>
    <button class="prev" aria-label="Publicación anterior">‹</button>
    <button class="next" aria-label="Publicación siguiente">›</button>
    <?php endif; ?>
</div>
<script src="/PRODCONS/Carrusel/carrusel.js"></script>
