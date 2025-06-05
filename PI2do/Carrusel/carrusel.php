<?php
require_once __DIR__ . '/../Base de datos/conexion.php';

// Función para traducir el nombre del mes a español
if (!function_exists('traducirMesEspanol')) {
    function traducirMesEspanol($mesIngles) {
        $meses = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre'
        ];
        return $meses[$mesIngles] ?? $mesIngles; // Devuelve el mes traducido o el original si no se encuentra
    }
}

try {
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    $publicaciones = [];
    $sql = "SELECT a.Titulo as Titulo, a.Contenido as descripcion, a.ID_Articulo as link, 
            a.`Fecha de Publicacion` as Fecha, u.Nombre as autor, 
            GROUP_CONCAT(ia.Url_Imagen) as imagen 
            FROM articulos a 
            JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
            LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID 
            WHERE a.Estado = 'publicado' 
            GROUP BY a.ID_Articulo 
            ORDER BY a.`Fecha de Publicacion` DESC LIMIT 10";
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
                        <img src="<?= htmlspecialchars($pub['imagen'] ?? '') ?>" alt="<?= htmlspecialchars($pub['Titulo'] ?? '') ?>" class="post-img">
                    </div>
                    <div class="post-body">
                        <h2><?= htmlspecialchars($pub['Titulo'] ?? '') ?></h2>
                        <p class="descripcion"><?php 
                            $descripcion = htmlspecialchars($pub['descripcion'] ?? '');
                            // Truncar descripción a aproximadamente 401 caracteres si es más larga
                            if (strlen($descripcion) > 401) {
                                $descripcion = substr($descripcion, 0, 401) . '...';
                            }
                            echo $descripcion;
                        ?></p>
                        <a href="/PRODCONS/PI2do/postWeb/ver-articulo.php?id=<?= htmlspecialchars($pub['link'] ?? '') ?>" class="post-link">Ver más...</a>
                        <span>Publicado el <?php 
                            $fecha_timestamp = strtotime($pub['Fecha'] ?? '');
                            // Check if timestamp is valid before formatting
                            if ($fecha_timestamp !== false) {
                                $dia = date('d', $fecha_timestamp);
                                $mes_ingles = date('F', $fecha_timestamp);
                                $mes_espanol = traducirMesEspanol($mes_ingles);
                                $año = date('Y', $fecha_timestamp);
                                echo htmlspecialchars("$dia de $mes_espanol de $año");
                            } else {
                                echo "Fecha desconocida"; // Or handle as needed
                            }
                        ?></span>
                        <span> | Por   <?= htmlspecialchars($pub['autor'] ?? '') ?></span>
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

    </div>

    <button class="prev" aria-label="Publicación anterior">‹</button>
    <button class="next" aria-label="Publicación siguiente">›</button>
</div>
</head>

<script src="/PRODCONS/Carrusel/carrusel.js"></script>
</body>
</html>
