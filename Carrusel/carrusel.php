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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrusel de Publicaciones</title>
    <link href="/PRODCONS/Carrusel/carrusel.css" rel="stylesheet">

<!-- Carrusel de publicaciones -->
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
                        <span> | Por: <?= htmlspecialchars($pub['autor']) ?></span>
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
        <!-- <button class="prev" aria-label="Publicación anterior">‹</button>
        <button class="next" aria-label="Publicación siguiente">›</button> -->
    <?php endif; ?>
</div>

<article class="carousel-item post">
            <div class="post-header">
                <div class="post-img-1"></div>
            </div>
            <div class="post-body">
                <h2>Menos plásticos mas vida</h2>
                <p class="descripcion">El plástico nos rodea: en casa, en tiendas y hasta en los océanos. Con pequeñas decisiones, podemos reducir su uso y hacer la diferencia. ¿Listo para cambiar hábitos y ayudar al planeta?</p>
                <a href="/PRODCONS/postWeb/articulo1.html" class="post-link">Leer más...</a>
                <span>Publicado el 14 de febrero del 2025</span>
                <span>| Juan Pablo Mancilla Rodriguez</span>
            </div>
        </article>

        <article class="carousel-item post">
            <div class="post-header">
                <div class="post-img-2"></div>
            </div>
            <div class="post-body">
                <h2>Tu puedes hacer la diferencia</h2>
                <p>Cada elección cuenta. Adoptar hábitos más sostenibles en el día a día no solo reduce nuestra huella ecológica, sino que inspira un cambio real en la sociedad. ¿Te animas a dar el primer paso?</p>
                <a href="/PRODCONS/postWeb/articulo2.html" class="post-link">Leer más...</a>
                <span>Publicado el 19 de Febrero del 2025</span>
                <span>| Yureni Elizabeth Sierra Aguilar</span>
            </div>
        </article>

        <article class="carousel-item post">
            <div class="post-header">
                <div class="post-img-3"></div>
            </div>
            <div class="post-body">
                <h2>La Revolución de la Moda Sostenible</h2>
                <p class="descripcion">La industria de la moda es poderosa, pero también contaminante. Apostar por opciones sostenibles es clave para un futuro más limpio. ¿Sabes cómo tu ropa puede marcar la diferencia?</p>
                <a href="/PRODCONS/postWeb/articulo3.php" class="post-link">Leer más...</a>
                <span>Publicado el 19 de Febrero del 2025</span>
                <span>| Daniel Sahid Barroso Alvarez</span>
            </div>
        </article>

        <article class="carousel-item post">
            <div class="post-header">
                <div class="post-img-4"></div>
            </div>
            <div class="post-body">
                <h2>Crea tu propio huerto y sus ventajas</h2>
                <p class="descripcion">Cultivar tus propios alimentos te da frescura, control y una alimentación más sana. Además, reduces residuos y cuidas el medioambiente. ¿Te animas a empezar tu propio huerto?</p>
                <a href="/PRODCONS/postWeb/articulo4.php" class="post-link">Leer más...</a>
                <span>Publicado el 20 de Febrero del 2025</span>
                <span>| Xiomara Anabeli Cobian Ramirez</span>
            </div>
        </article>

        <article class="carousel-item post">
            <div class="post-header">
                <div class="post-img-5"></div>
            </div>
            <div class="post-body">
                <h2>Reduciendo residuos en el hogar</h2>
                <p class="descripcion">Consumimos sin medida, sin pensar en el impacto. Es momento de tomar decisiones responsables y reducir nuestra huella ecológica. Cada elección cuenta. ¿Qué harás hoy por un futuro más verde?</p>
                <a href="/PRODCONS/postWeb/articulo5.php" class="post-link">Leer más...</a>
                <span>Publicado el 21 de Febrero del 2025</span>
                <span>| Fernando Benitez Astudillo</span>
            </div>
        </article>

        <article class="carousel-item post">
            <div class="post-header">
                <div class="post-img-6"></div>
            </div>
            <div class="post-body">
                <h2>Consumo Digital y Producción Responsable</h2>
                <p class="descripcion">El consumo digital impacta el planeta más de lo que imaginas. Optar por prácticas responsables en tecnología puede hacer una gran diferencia. ¿Sabes cómo reducir tu impacto digital?</p>
                <a href="/PRODCONS/postWeb/articulo6.php" class="post-link">Leer más...</a>
                <span>Publicado el 21 de Febrero del 2025</span>
                <span>| Isabela Monserrat Vidrio Camarena</span>
            </div>
        </article>

    </div>

    <button class="prev" aria-label="Publicación anterior">‹</button>
    <button class="next" aria-label="Publicación siguiente">›</button>
</div>
</head>

<script src="/PRODCONS/Carrusel/carrusel.js"></script>
</body>
</html>
