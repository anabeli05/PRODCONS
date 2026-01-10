 <!-- Carrusel destacado -->
 <section class="carrusel-destacado">
        <?php 
        // Incluir el archivo de conexión
        require_once __DIR__ . '/PI2do/Base de datos/conexion.php';
        
        // Inicializar la conexión a la base de datos
        $conexion = new Conexion();
        $conexion->abrir_conexion();
        $conn = $conexion->conexion;

        // Consulta para el carrusel 
        $stmt_carousel = $conn->prepare("SELECT a.*, u.Nombre as autor_nombre, 
                               GROUP_CONCAT(ia.Url_Imagen) as imagenes
                               FROM articulos a 
                               JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                               LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
                               WHERE a.Estado = 'Publicado' 
                               GROUP BY a.ID_Articulo
                               ORDER BY a.`Fecha de Publicacion` DESC");

        if (!$stmt_carousel) {
            die("Error en la preparación de la consulta del carrusel: " . $conn->error);
        }

        $stmt_carousel->execute();
        $publicaciones_carousel = $stmt_carousel->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_carousel->close();

        if (!empty($publicaciones_carousel)): ?>
            <div class="carousel-container">
                <div class="carousel">
                    <?php foreach ($publicaciones_carousel as $pub): 
                        $article_id = isset($pub['ID_Articulo']) ? (int)$pub['ID_Articulo'] : 0;
                        $imagen_principal_carousel = $pub['imagenes'] ?? '/PRODCONS/PI2do/imagenes/default-post.jpg';
                    ?>
                        <div class="carousel-item">
                            <article class="post" data-post-id="<?php echo htmlspecialchars($pub['ID_Articulo'] ?? ''); ?>">
                                <div class="post-header">
                                    <img src="<?= htmlspecialchars($imagen_principal_carousel) ?>" 
                                         alt="<?= htmlspecialchars($pub['Titulo'] ?? '') ?>" 
                                         class="post-img">
                                </div>
                                <div class="post-body">
                                    <h2><?= htmlspecialchars($pub['Titulo'] ?? '') ?></h2>
                                    <p class="descripcion"><?php 
                                        $contenido = htmlspecialchars($pub['Contenido'] ?? '');
                                        if (strlen($contenido) > 100) {
                                            $contenido = substr($contenido, 0, 401) . '...';
                                        }
                                        echo $contenido;
                                    ?></p>
                                    <a href="/PRODCONS/PI2do/postWeb/ver-articulo-usuario.php?id=<?php echo htmlspecialchars($pub['ID_Articulo'] ?? ''); ?>" class="post-link">Leer más...</a>
                                    <span>Publicado el <?php 
                                        $fecha_timestamp = strtotime($pub['Fecha de Publicacion'] ?? '');
                                        if ($fecha_timestamp !== false) {
                                            $dia = date('d', $fecha_timestamp);
                                            $mes_ingles = date('F', $fecha_timestamp);
                                            $mes_espanol = traducirMesEspanol($mes_ingles);
                                            $año = date('Y', $fecha_timestamp);
                                            echo htmlspecialchars("$dia de $mes_espanol de $año");
                                        } else {
                                            echo "Fecha desconocida";
                                        }
                                    ?></span>
                                    <span> | Por   <?= htmlspecialchars($pub['autor_nombre'] ?? '') ?></span>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="prev" aria-label="Publicación anterior">‹</button>
                <button class="next" aria-label="Publicación siguiente">›</button>
            </div>
        <?php else: ?>
            <div class="no-posts">
                <p>No hay publicaciones destacadas disponibles en este momento.</p>
            </div>
        <?php endif; ?>
        <?php 
        // Close the database connection
        $conexion->cerrar_conexion();
        ?>
    </section>

    </main>
    <script src='PI2do/Carrusel/carrusel.js'></script>