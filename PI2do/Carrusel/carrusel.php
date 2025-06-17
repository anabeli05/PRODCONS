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

    $sql = "SELECT 
            a.*, 
            u.Nombre as autor_nombre,
            (SELECT Url_Imagen FROM imagenes_articulos WHERE Articulo_ID = a.ID_Articulo ORDER BY ID_Imagen LIMIT 1) as imagen_principal,
            (SELECT COUNT(*) FROM likes WHERE ID_Articulo = a.ID_Articulo) as total_likes,
            (SELECT COUNT(*) FROM comentarios WHERE ID_Articulo = a.ID_Articulo) as total_comentarios
            FROM articulos a 
            JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
            WHERE a.Estado = 'Publicado' 
            ORDER BY a.`Fecha de Publicacion` DESC";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $publicaciones = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $publicaciones[] = $row;
        }
    }
} catch (Exception $e) {

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
                <article class="post" data-post-id="<?= htmlspecialchars($pub['ID_Articulo'] ?? '') ?>">
                    <div class="post-header">
                        <img src="<?= htmlspecialchars($pub['imagen_principal'] ?? '/PRODCONS/PI2do/imagenes/default-post.jpg') ?>" 
                             alt="<?= htmlspecialchars($pub['Titulo'] ?? '') ?>" 
                             class="post-img">
                    </div>
                    <div class="post-body">
                        <h2><?= htmlspecialchars($pub['Titulo'] ?? '') ?></h2>
                        <p class="descripcion"><?php 
                            $contenido = htmlspecialchars($pub['Contenido'] ?? '');
                            // Truncar contenido a aproximadamente 100 caracteres si es más largo
                            if (strlen($contenido) > 100) {
                                $contenido = substr($contenido, 0, 401) . '...';
                            }
                            echo $contenido;
                        ?></p>
                        <a href="/PRODCONS/PI2do/postWeb/ver-articulo.php?id=<?= htmlspecialchars($pub['ID_Articulo'] ?? '') ?>" class="post-link">Leer más...</a>
                        <div class="post-stats">
                            <span class="likes"><i class="fas fa-heart"></i> <?= htmlspecialchars($pub['total_likes'] ?? '0') ?></span>
                            <span class="comments"><i class="fas fa-comment"></i> <?= htmlspecialchars($pub['total_comentarios'] ?? '0') ?></span>
                        </div>
                        <span>Publicado el <?php 
                            $fecha_timestamp = strtotime($pub['Fecha de Publicacion'] ?? '');
                            // Check if timestamp es válido antes de formatear
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
                        <span> | Por <?= htmlspecialchars($pub['autor'] ?? '') ?></span>
                        <div class="post-interactions">
                            <span class="like-count">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 2.748l-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <?= htmlspecialchars($pub['total_likes'] ?? '0') ?> Me gusta
                            </span>
                            <span class="comment-count">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                </svg>
                                <?= htmlspecialchars($pub['total_comments'] ?? '0') ?> Comentarios
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-posts">
                <p>No hay publicaciones disponibles en este momento. ¡Vuelve pronto!</p>
            </div>
        <?php endif; ?>
    </div>
    <button class="prev" aria-label="Publicación anterior">‹</button>
    <button class="next" aria-label="Publicación siguiente">›</button>
