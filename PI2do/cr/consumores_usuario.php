<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Consumo Responsable</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/PRODCONS/PI2do/header_post/header_post.css">
  <link rel="stylesheet" href="/PRODCONS/PI2do/pr/stylesconsumores.css">
  <link rel="stylesheet" href="/PRODCONS/footer/footer/footer.css">
  <link rel="stylesheet" href="/PRODCONS/articulos.css">
  
  <!-- Scripts de traducción -->
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
  <script src="/PRODCONS/translate.js"></script>

  <style>
    /* Estilos para la bandera de idioma */
    #banderaIdioma {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid black;
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    #banderaIdioma:hover {
        transform: scale(1.1);
    }

    .header-section {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 2rem;
    }

    .contenedor-imagenes {
        display: flex;
        gap: 2rem;
        align-items: flex-start;
        margin-top: 2rem;
    }

    .texto {
        flex: 1;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .imagenes {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .imagen-primera {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
        .contenedor-imagenes {
            flex-direction: column;
        }
    }
  </style>
</head>

<body class="bg-gray-50">
    <header>
        <div class="header-contenedor">
            <a href="/PRODCONS/PI2do/Dashboard_Usuario/Inicio/usuario.php" class="flecha_left">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
                </svg>
            </a>
            <div class="principal">
                <!-- Selector de bandera para cambio de idioma -->
                <div id="idiomaToggle" style="display: inline-block; margin-left: 15px;">
                    <img class="españa" id="banderaIdioma" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
                </div>
                <!-- Opciones de banderas desplegables -->
                <div id="idiomasOpciones" style="display: none;">
                    <img class="ingles" src="/PRODCONS/PI2do/imagenes/logos/ingles.png" onclick="cambiarIdioma('ingles')" alt="Cambiar a inglés" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                    <img class="españa" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" onclick="cambiarIdioma('espanol')" alt="Cambiar a español" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                </div>
            </div>
        </div>
    </header>

    <!-- Contenedor principal-->
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/PRODCONS/PI2do/Base de datos/conexion.php';
    
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    // Función para traducir el nombre del mes a español
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

    $stmt = $conn->prepare("SELECT a.*, u.Nombre as autor_nombre, 
                           GROUP_CONCAT(ia.Url_Imagen) as imagenes
                           FROM articulos a 
                           JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                           LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
                           WHERE a.Estado = 'Publicado' 
                           GROUP BY a.ID_Articulo
                           ORDER BY a.`Fecha de Creacion` DESC 
                           LIMIT 3");

    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->execute();
    $publicaciones_carousel = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // Obtener los posts publicados para la sección "MIRA MAS DE NUESTRO CONTENIDO"
    $stmt = $conn->prepare("SELECT a.*, u.Nombre as autor_nombre, 
                           GROUP_CONCAT(ia.Url_Imagen) as imagenes
                           FROM articulos a 
                           JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                           LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
                           WHERE a.Estado = 'Publicado' 
                           GROUP BY a.ID_Articulo
                           ORDER BY a.`Fecha de Creacion` DESC");

    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->execute();
    $publicaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conexion->cerrar_conexion();
    ?>
        <!-- Cuadro verde -->
        <div style="background-color: rgb(109, 151, 109);" 
             class="w-full md:w-100 text-white p-8 rounded-md flex flex-col justify-center">
            <h2 class="text-3xl font-bold mb-6 leading-tight">EL CONSUMO RESPONSABLE</h2>
            <p class="text-lg leading-relaxed">
                El consumo responsable implica elegir productos y servicios que minimicen el impacto ambiental, fomenten la economía local y respeten los derechos de los trabajadores. Es una forma de tomar decisiones conscientes para reducir el desperdicio y promover un futuro más sostenible.
            </p>
            <p class="text-lg leading-relaxed mt-4">
                Cada decisión de compra que tomamos tiene un impacto en el medio ambiente y en la sociedad. Al elegir productos locales, duraderos y éticos, contribuimos a crear un mundo más justo y sostenible para las futuras generaciones.
            </p>
        </div>

        <!-- Imagen -->
        <div class="w-full md:w-100 flex items-center justify-center mt-6 md:mt-0">
            <img src="/PRODCONS/PI2do/imagenes/produccion.png" alt="Producción Responsable" class="max-w-full h-auto rounded-md shadow-lg" />
        </div>
    </div>

<!-- Carrusel destacado
<section class="carrusel-destacado">
    </?php 
    
    // Re-fetch publications for the carousel to include like/comment counts if needed, 
    // or pass them from the main query if it already fetches them.
    // For simplicity, assuming the included carrusel.php already fetches necessary data
    // and modifying its loop to include interaction buttons.
    // Alternatively, if carrusel.php is only for the visual structure, 
    // we might need to duplicate the loop here with interaction buttons.
    // Given the user's request is to 'annex' the icons to the carousel *in usuario.php*,
    // it is safer to assume the latter and add the loop with buttons here.

    // Fetching data again specifically for the carousel section
    $conexion_carousel = new Conexion();
    $conexion_carousel->abrir_conexion();
    $conn_carousel = $conexion_carousel->conexion;

    $publicaciones_carousel = [];
    $sql_carousel = "SELECT a.Titulo as Titulo, a.Contenido as descripcion, CAST(a.ID_Articulo AS UNSIGNED) as ID_Articulo, 
                     a.`Fecha de Publicacion` as Fecha, u.Nombre as autor_nombre, 
                     ia.Url_Imagen as imagen_principal 
                     FROM articulos a 
                     JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                     LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID AND ia.Orden_Imagen = 1
                     WHERE a.Estado = 'Publicado' 
                     ORDER BY a.`Fecha de Publicacion` DESC LIMIT 10";
    $result_carousel = $conn_carousel->query($sql_carousel);

    if ($result_carousel && $result_carousel->num_rows > 0) {
        while ($row_carousel = $result_carousel->fetch_assoc()) {
            $publicaciones_carousel[] = $row_carousel;
        }
    }
    $conexion_carousel->cerrar_conexion();
    
    ?>
    Duplicating carousel structure to add interaction buttons
    <div class="carousel-container">
        <div class="carousel">
            </?php if (!empty($publicaciones_carousel)): ?>
                </?php foreach ($publicaciones_carousel as $pub): 
    $article_id = isset($pub['ID_Articulo']) ? (int)$pub['ID_Articulo'] : 0;
    $imagen_principal_carousel = $pub['imagen_principal'] ?? '/PRODCONS/PI2do/imagenes/default-post.jpg';
?>                ?>
                    <div class="carousel-item post" data-post-id="</?php echo htmlspecialchars($article_id); ?>">
                <div class="post-header">
                            <img src="/PRODCONS/PI2do/imagenes/articulos/</?= htmlspecialchars($imagen_principal_carousel) ?>" alt="<?= htmlspecialchars($pub['Titulo'] ?? '') ?>" class="post-img">
                </div>
                <div class="post-body">
                            <h2></?= htmlspecialchars($pub['Titulo'] ?? '') ?></h2>
                            <p class="descripcion"></?php 
                                $descripcion = htmlspecialchars($pub['descripcion'] ?? '');
                                // Truncar descripción a aproximadamente 401 caracteres si es más larga
                                if (strlen($descripcion) > 401) {
                                    $descripcion = substr($descripcion, 0, 401) . '...';
                                }
                                echo $descripcion;
                            ?></p>

                    <div class="post-footer">
                                <div class="post-actions">
                                <a href="/PRODCONS/PI2do/postWeb/ver-articulo-usuario.php?ID_Articulo=</?php echo htmlspecialchars($article_id); ?>" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                                        <button class="like-button-small" data-post-id="</?php echo htmlspecialchars($article_id); ?>" title="Me gusta">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                            <span class="likes-count">0</span>
                            </button>
                                        <button class="comment-toggle-small" data-post-id="</?php echo htmlspecialchars($article_id); ?>" title="Comentarios">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                            <span class="comments-count">0</span>
                            </button>
                        </div>
                    </div>
                        </div>

                            <div class="comments-section" style="display: none;">
                                <div class="existing-comments">
                                    Los comentarios se mostrarán aquí via AJAX
                        </div>
                        <div class="add-comment">
                                    <textarea placeholder="Escribe tu comentario..."></textarea>
                                    <button>Publicar</button>
                        </div>
                    </div>
                            
                            <span style="font-size: 12px; line-height: 1.2;">Publicado el </?php 
                                $fecha_timestamp = strtotime($pub['Fecha'] ?? '');
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
                            <span>| Por   </?= htmlspecialchars($pub['autor_nombre'] ?? '') ?></span>
                    
                </div>
                </div>
                </div>
                </?php endforeach; ?>
            </?php else: ?>
                <div class="no-posts">
                    <p>No hay publicaciones disponibles en este momento. ¡Vuelve pronto!</p>
                        </div>
            </?php endif; ?>
                    </div>
        <button class="prev" aria-label="Publicación anterior">‹</button>
        <button class="next" aria-label="Publicación siguiente">›</button>
                        </div>
</section>

    <h3 class="apubli"> MIRA MAS DE NUESTRO CONTENIDO </h3>

    <section class="post-list">
        <div class="content">
            </?php foreach ($publicaciones as $post): 
    $article_id = isset($post['ID_Articulo']) ? (int)$post['ID_Articulo'] : 0;
    // Check if images data is available before exploding
    $imagenes_string = $post['imagenes'] ?? '';
    $imagenes = !empty($imagenes_string) ? explode(',', $imagenes_string) : [];
    $imagen_principal = !empty($imagenes[0]) ? $imagenes[0] : '/PRODCONS/PI2do/imagenes/default-post.jpg';
?>            ?>
                <article class="post" data-post-id="</?php echo htmlspecialchars($article_id); ?>">
                <div class="post-header">
                        <img src="</?php echo htmlspecialchars($imagen_principal); ?>" alt="</?php echo htmlspecialchars($post['Titulo'] ?? ''); ?>" class="post-img">
                </div>
                <div class="post-body">
                        <h2></?php echo htmlspecialchars($post['Titulo'] ?? ''); ?></h2>
                        <p class="descripcion"></?php 
                            $contenido = htmlspecialchars($post['Contenido'] ?? '');
                            // Truncar contenido a aproximadamente 401 caracteres para coincidir con el carrusel
                            if (strlen($contenido) > 401) {
                                $contenido = substr($contenido, 0, 401) . '...';
                            }
                            echo $contenido;
                        ?></p>
                        
                    <div class="post-footer">
                            <div class="post-actions">
                                <a href="/PRODCONS/PI2do/postWeb/ver-articulo-usuario.php?ID_Articulo=</?php echo htmlspecialchars($article_id); ?>" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                                    <button class="like-button-small" data-post-id="</?php echo htmlspecialchars($article_id); ?>" title="Me gusta">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                        <span class="likes-count">0</span>
                            </button>
                                    <button class="comment-toggle-small" data-post-id="</?php echo htmlspecialchars($article_id); ?>" title="Comentarios">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                        <span class="comments-count">0</span>
                            </button>
                        </div>
                    </div>
                        </div>

                        <div class="comments-section" style="display: none;">
                            <div class="existing-comments">
                                Los comentarios se mostrarán aquí via AJAX
                        </div>
                        <div class="add-comment">
                                <textarea placeholder="Escribe tu comentario..."></textarea>
                                <button>Publicar</button>
                        </div>
                    </div>

                        <span style="font-size: 12px; line-height: 1.2;">Publicado el </?php 
                            $fecha_timestamp = strtotime($post['Fecha de Publicacion'] ?? '');
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
                        <span>| Por </?php echo htmlspecialchars($post['autor_nombre'] ?? ''); ?></span>
                </div>
            </article>
            </?php endforeach; ?>
        </div>
    </section>
    </main>-->
    
<script>
        // Script para activar/desactivar la barra de búsqueda
        const btnLupa = document.getElementById('btnLupa');
        const barraBusqueda = document.getElementById('barraBusqueda');

        if (btnLupa && barraBusqueda) {
            btnLupa.addEventListener('click', () => {
                barraBusqueda.classList.toggle('activa');
                if (barraBusqueda.classList.contains('activa')) {
                    barraBusqueda.focus();
                }
            });
        }

        // Función para normalizar y eliminar acentos
        function normalizeText(text) {
            return text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        }

        // Función para escapar caracteres especiales de regex
        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        // Función para resaltar texto
        function highlightText(text, searchTerm) {
            if (!searchTerm) return text;
            const safeTerm = escapeRegExp(searchTerm);
            const regex = new RegExp(`(${safeTerm})`, 'gi');
            return text.replace(regex, '<span class="search-highlight">$1</span>');
        }

        // Función de búsqueda mejorada
        function searchArticles(searchTerm) {
            const articles = document.querySelectorAll('.post');
            const normalizedSearch = normalizeText(searchTerm.trim());

            if (!normalizedSearch) {
                // Si no hay término de búsqueda, mostrar todos los artículos
                articles.forEach(article => {
                    article.style.display = 'block';
                    const title = article.querySelector('h2');
                    const description = article.querySelector('.descripcion');
                    if (title) title.innerHTML = title.textContent;
                    if (description) description.innerHTML = description.textContent;
                });
                return;
            }

            articles.forEach(article => {
                const title = article.querySelector('h2');
                const description = article.querySelector('.descripcion');
                if (!title) return;
                const titleText = title.textContent;
                const descriptionText = description ? description.textContent : '';
                const normalizedTitle = normalizeText(titleText);
                const normalizedDescription = normalizeText(descriptionText);
                const content = normalizedTitle + ' ' + normalizedDescription;

                if (content.includes(normalizedSearch)) {
                    article.style.display = 'block';
                    // Resaltar el texto que coincide
                    title.innerHTML = highlightText(titleText, searchTerm);
                    if (description) {
                        description.innerHTML = highlightText(descriptionText, searchTerm);
                    }
                } else {
                    article.style.display = 'none';
                }
            });
        }

        // Agregar event listeners para ambos buscadores
        document.addEventListener('DOMContentLoaded', function() {
            const searchInputs = document.querySelectorAll('input[type="search"], input[type="text"]');
            searchInputs.forEach(input => {
                input.addEventListener('input', (e) => {
                    searchArticles(e.target.value);
                });
            });
        });

        // Funciones para manejar likes y comentarios
        function likePost(articleId) {
            console.log('Like post:', articleId);
            // Implementar lógica de likes aquí
            
            // AJAX call to handle_like.php
            fetch('/PRODCONS/PI2do/Dashboard_Usuario/likes and comments/handle_like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'article_id=' + articleId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update like count on the page
                    const likesCountSpan = document.querySelector(`article[data-post-id="${articleId}"] .likes-count`);
                    if (likesCountSpan) {
                        likesCountSpan.textContent = data.likes;
                    }
                    // Optional: Change heart icon color/style
                } else {
                    console.error('Error liking post:', data.message);
                    alert('Error: ' + data.message); // Show error to user
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Hubo un error al procesar tu solicitud de like.'); // Generic error message
            });
        }

    function toggleComments(articleId) {
        const commentsSection = document.getElementById(`comments-section-${articleId}`);
            if (commentsSection) {
        if (commentsSection.style.display === 'none') {
            commentsSection.style.display = 'block';
        } else {
            commentsSection.style.display = 'none';
        }
    }
        }

    function addComment(articleId) {
        const commentInput = document.getElementById(`comment-input-${articleId}`);
        const commentText = commentInput.value.trim();
        
            if (!commentText) {
                alert('Por favor, escribe un comentario.');
                return;
            }
            
            console.log('Add comment for article:', articleId, 'Content:', commentText);
            // Implementar lógica de comentarios aquí

            // AJAX call to handle_comment.php
            fetch('/PRODCONS/PI2do/Dashboard_Usuario/likes and comments/handle_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'article_id=' + articleId + '&comment=' + encodeURIComponent(commentText)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add the new comment to the comments section
                    const existingCommentsDiv = document.querySelector(`article[data-post-id="${articleId}"] .existing-comments`);
                    if (existingCommentsDiv) {
                        const newCommentHtml = `<p><strong>${data.author}:</strong> ${htmlspecialchars(commentText)}</p>`; // Assuming author name is returned or handled server-side
                        existingCommentsDiv.innerHTML += newCommentHtml; // Append new comment
                    }
                    commentInput.value = ''; // Clear the input field
                } else {
                    console.error('Error adding comment:', data.message);
                    alert('Error: ' + data.message); // Show error to user
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Hubo un error al publicar tu comentario.'); // Generic error message
            });
    }
</script>

    <?php include $_SERVER['DOCUMENT_ROOT'].'/PRODCONS/footer/footer/footer.php'; ?>
    <script src='/PRODCONS/Header_visitantes/barra_principal.js'></script>

</body>
</html>
