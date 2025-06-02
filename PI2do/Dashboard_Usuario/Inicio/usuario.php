<<<<<<< Updated upstream:PI2do/Dashboard_Usuario/Inicio/usuario.php
<?php
// Incluir el archivo de verificación
include '../inicio_sesion/verificar_registro.php';
include '../../Base de datos/conexion.php'; // Incluir el archivo de conexión

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../inicio_sesion/login.php'); // Redirigir a la página de login si no está logueado
    exit();
}

// Crear instancia de Conexion y obtener la conexión
$conexion = new Conexion();
$conexion->abrir_conexion(); // Llamar a abrir_conexion() para establecer la conexión
$conn = $conexion->conexion;

// Verificar si la conexión se estableció correctamente
if (!$conn) {
    die("Error de conexión a la base de datos.");
}

// Obtener los posts publicados desde la base de datos
$stmt = $conn->prepare("SELECT a.*, u.nombre as autor_nombre 
                       FROM articulos a 
                       JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                       WHERE a.Estado = 'publicado' 
                       ORDER BY a.`Fecha de Creacion` DESC");

if (!$stmt) {
    die("Error en la preparación de la consulta de artículos: " . $conn->error);
}

$stmt->execute();
$publicaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Cerrar la conexión al finalizar el script (o después de obtener los datos)
$conexion->cerrar_conexion();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS</title>
    <link rel="stylesheet" href="/PRODCONS/PI2do/Dashboard_Usuario/usuario.css">
    <link rel="stylesheet" href="/PRODCONS/PI2do/footer/Visitante/footer.css">
    <link rel="stylesheet" href="/PRODCONS/PI2do/articulos.css">
    <link href="/PRODCONS/PI2do/Header visitantes/barra_principal.css" rel="stylesheet">
    <!-- Google Cloud Translation API -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <!-- Script de traducción global -->
    <script src="traslate.js"></script>
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
        
        /* Estilos para likes y comentarios */
        .post-footer {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        
        .interaction-buttons {
            display: flex;
            margin-left: 15px;
            align-items: center;
        }
        
        .like-button-small, .comment-toggle-small {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0;
            margin: 0 5px;
            color: #666;
            font-size: 12px;
        }
        
        .like-button-small svg, .comment-toggle-small svg {
            margin-right: 3px;
        }
        
        .like-button-small:hover, .like-button-small.liked {
            color: #e74c3c;
        }
        
        .comment-toggle-small:hover {
            color: #3498db;
        }
        
        .comments-section {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
        }
        
        .existing-comments {
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 10px;
        }
        
        .comment {
            background-color: white;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 8px;
        }
        
        .comment-author {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .comment-date {
            font-size: 0.8em;
            color: #777;
        }
        
        .add-comment textarea {
            width: 100%;
            min-height: 60px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
            resize: vertical;
        }
        
        .add-comment button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .add-comment button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <!-- Menú hamburguesa (solo en móviles) -->
    <div class="mobile-menu-container">
        <div class="hamburger-icon" onclick="toggleMenu()">
            <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </div>
        <nav class="mobile-menu" id="mobileMenu">
            <div class="close-icon" onclick="toggleMenu()">
                <svg class="close-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>
            <div class="mobile-menu-content">
                <ul>
                    <li><a href='/PRODCONS/PI2do/pr/produccionr.html'>PRODUCCIÓN RESPONSABLE</a></li>
                    <li><a href='/PRODCONS/PI2do/cr/consumores.html'>CONSUMO RESPONSABLE</a></li>
                    <li>
                        <form class="search-form">
                            <button class="lupa" type="button">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </button>
                            <input type="search" placeholder="Buscar..." />
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <style>
        /* Estilos para la hamburguesa solo en móviles */
        .mobile-menu-container {
            display: none;
        }

        @media (max-width: 768px) {
            .mobile-menu-container {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                z-index: 1000;
            }

            .hamburger-icon {
                padding: 1rem;
                background: #fff;
                cursor: pointer;
            }

            .menu-icon {
                width: 24px;
                height: 24px;
            }

            .mobile-menu {
                position: fixed;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100vh;
                background: #fff;
                transition: 0.3s;
                padding: 2rem;
            }

            .mobile-menu.active {
                left: 0;
            }

            .close-icon {
                text-align: right;
                margin-bottom: 2rem;
                cursor: pointer;
            }

            .mobile-menu-content ul {
                list-style: none;
                padding: 0;
            }

            .mobile-menu-content li {
                margin-bottom: 1rem;
            }
        }
    </style>

    <script>
        function toggleMenu() {
            const menu = document.getElementById("mobileMenu");
            menu.classList.toggle("active");
        }
    </script>

    <header>
        <div class="header-contenedor">
            <div class="principal">
                <a class="navlink" href='/PRODCONS/PI2do/empresas_responsables/empresasr.html'>EMPRESAS RESPONSABLES</a>

                <!-- =====================================================================
                SELECTOR DE BANDERA PARA CAMBIO DE IDIOMA - PERSONALIZABLE
                Estos elementos controlan la selección de idioma en la página principal
                ===================================================================== -->
                <!-- Bandera principal visible - Puedes cambiar la imagen por defecto aquí -->
                <div id="idiomaToggle">
                    <img id="banderaIdioma" src='/PRODCONS/PI2do/imagenes/logos/espanol.png' alt="Idioma" onclick="alternarIdioma()" data-idioma="es">
                </div>
            </div>
        </div>
    </header>

    <section class="logo">
        <div class="header_2">
            <img class="prodcons" src='/PRODCONS/PI2do/imagenes/prodcon/logoSinfondo.png' alt="Logo">

            <div class="subtitulos">
                <li><a href="/PRODCONS/PI2do/pr/produccionr.html">PRODUCCIÓN RESPONSABLE</a></li>
                <li><a href="/PRODCONS/PI2do/cr/consumores.html">CONSUMO RESPONSABLE</a></li>

                <form class="search-form">
                    <button class="lupa">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="36" height="36"
                            stroke-width="1.5">
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                            <path d="M21 21l-6 -6"></path>
                        </svg>
                    </button>
                    <input type="search" placeholder="Buscar..." />
                </form>
            </div>
        </div>
    </section>


    <!-- Menú de hamburguesa -->
    <div class="menu" id="menu">
        <ul>
            <li><a href="/PRODCONS/PI2do/pr/produccionr.html" onclick="setActive(this)">PRODUCCION RESPONSABLE</a></li>
            <li><a href="/PRODCONS/PI2do/cr/consumores.html" onclick="setActive(this)">CONSUMO RESPONSABLE</a></li>
            <li><a href="#">Lupa de búsqueda</a></li>
        </ul>
    </div>

    <script>
        // Toggle para la visibilidad del menú de hamburguesa
        function toggleMenu() {
            const menu = document.getElementById("menu");
            menu.classList.toggle("active");
        }

        // Función para alternar entre idiomas
        function alternarIdioma() {
            const bandera = document.getElementById('banderaIdioma');
            const idiomaActual = bandera.getAttribute('data-idioma');
            const nuevoIdioma = idiomaActual === 'es' ? 'en' : 'es';
            
            // Cambiar la imagen de la bandera
            bandera.src = nuevoIdioma === 'en' 
                ? "/PRODCONS/PI2do/imagenes/logos/ingles.png" 
                : "/PRODCONS/PI2do/imagenes/logos/espanol.png";
            
            // Actualizar el atributo data-idioma
            bandera.setAttribute('data-idioma', nuevoIdioma);
            
            // Realizar la traducción
            translateContent(nuevoIdioma);
            
            // Guardar preferencia
            localStorage.setItem('preferredLanguage', nuevoIdioma);
        }
        
        // Cargar idioma al inicio
        document.addEventListener('DOMContentLoaded', function() {
            const savedLanguage = localStorage.getItem('preferredLanguage') || 'es';
            const bandera = document.getElementById('banderaIdioma');
            bandera.src = savedLanguage === 'en' 
                ? "/PRODCONS/PI2do/imagenes/logos/ingles.png" 
                : "/PRODCONS/PI2do/imagenes/logos/espanol.png";
            bandera.setAttribute('data-idioma', savedLanguage);
            
            // Iniciar traducción
            translateContent(savedLanguage);
        });

        // Función de traducción
        function translateContent(lang) {
            // Aquí iría la lógica de traducción
            // Puedes usar la API de Google Cloud Translation o cualquier otro método
            console.log('Traduciendo al idioma:', lang);
        }
    </script>


    <div class="sobrecont">
        <div class="cuadro-info">
            <h2>BLOG</h2>
            <p>Somos una organización dedicada a cuidar del medio ambiente, aplicándolo en nuestra vida diaria y
                promoviendo a los demás a hacerlo para el bienestar de todos.</p>
        </div>
        <img class="imagen-principal" src="/PRODCONS/PI2do/imagenes/tractor.png" alt="Imagen Principal">
    </div>


    <h3 class="apubli"> MIRA MAS DE NUESTRO CONTENIDO </h3>


    <section class="post-list">
        <div class="content">
            <?php if (empty($publicaciones)): ?>
                <p>No hay publicaciones disponibles en este momento.</p>
            <?php else: ?>
                <?php foreach ($publicaciones as $post): ?>
                    <article class="post" data-post-id="<?php echo htmlspecialchars($post['ID_Articulo']); ?>">
                        <div class="post-header">
                            <?php 
                                // Aquí podrías agregar la lógica para mostrar la imagen del post
                                // Dependiendo de cómo almacenas las imágenes (ej. en otra tabla vinculada por ID_Articulo)
                                // Por ahora, usaré un div genérico, puedes adaptarlo.
                            ?>
                            <div class="post-img-placeholder"></div> <?php // Placeholder for image
                            ?>
                        </div>
                        <div class="post-body">
                            <h2><?php echo htmlspecialchars($post['Titulo']); ?></h2>
                            <p class="descripcion"><?php echo htmlspecialchars($post['Contenido']); ?> </p>
                            <div class="post-footer">
                                <a href="/PRODCONS/PI2do/postWeb/articulo.php?id=<?php echo htmlspecialchars($post['ID_Articulo']); ?>" class="post-link">Leer más...</a>
                                <div class="interaction-buttons">
                                    <button class="like-button-small" data-post-id="<?php echo htmlspecialchars($post['ID_Articulo']); ?>" title="Me gusta">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                        </svg>
                                        <span class="likes-count">0</span> <?php // Update this span dynamically with actual like count
                                        ?>
                                    </button>
                                    <button class="comment-toggle-small" data-post-id="<?php echo htmlspecialchars($post['ID_Articulo']); ?>" title="Comentarios">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                            <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                        </svg>
                                        <span class="comments-count">0</span> <?php // Update this span dynamically with actual comment count
                                        ?>
                                    </button>
                                </div>
                            </div>
                            <div class="comments-section" style="display: none;">
                                <div class="existing-comments">
                                    <!-- Los comentarios se mostrarán aquí -->
                                </div>
                                <div class="add-comment">
                                    <textarea placeholder="Escribe tu comentario..."></textarea>
                                    <button>Publicar</button>
                                </div>
                            </div>
                            <span>Publicado el <?php echo htmlspecialchars($post['Fecha de Creacion']); ?> </span>
                            <span>| <?php echo htmlspecialchars($post['autor_nombre']); ?> </span>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    

    <!-- <div class="carousel-container">
        <div class="carousel">
            <?php foreach ($publicaciones as $pub): ?>
            <div class="carousel-item">
                <div class="post-header">
                    <img src="<?= $pub['imagen'] ?>" alt="<?= $pub['titulo'] ?>" class="post-img">
                </div>
                <div class="post-body">
                    <h2><?= $pub['titulo'] ?></h2>
                    <p class="descripcion"><?= $pub['descripcion'] ?></p>
                    <a href="<?= $pub['link'] ?>" class="post-link">Leer más...</a>
                    <span>Publicado el <?= $pub['fecha'] ?></span>
                    <span>| <?= $pub['autor'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="prev">‹</button>
        <button class="next">›</button>
    </div> -->
    
    <!--FOOTER-->
    <!-- <footer>
        <div class="footer-container"></div>
      </footer> -->
      
      <script src="/PRODCONS/PI2do/Header visitantes/barra_principal.js"></script>
<!-- <script src="/PRODCONS/footer/footer.js"></script> -->

<script>
    // Funciones para manejar likes y comentarios
    // Simulación de almacenamiento local para likes y comentarios
    
    // Inicializar datos si no existen
    if (!localStorage.getItem('articleLikes')) {
        localStorage.setItem('articleLikes', JSON.stringify({1: 0, 2: 0, 3: 0, 4: 0, 5: 0, 6: 0}));
    }
    
    if (!localStorage.getItem('articleComments')) {
        localStorage.setItem('articleComments', JSON.stringify({
            1: [], 2: [], 3: [], 4: [], 5: [], 6: []
        }));
    }
    
    // Registro de likes de usuario
    if (!localStorage.getItem('userLikes')) {
        localStorage.setItem('userLikes', JSON.stringify({
            1: false, 2: false, 3: false, 4: false, 5: false, 6: false
        }));
    }
    
    // Cargar los datos existentes al iniciar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar likes
        const likes = JSON.parse(localStorage.getItem('articleLikes'));
        const userLikes = JSON.parse(localStorage.getItem('userLikes'));
        
        for (const [articleId, count] of Object.entries(likes)) {
            document.getElementById(`likes-count-${articleId}`).textContent = count;
            
            // Si el usuario ya dio like, cambiar el corazón a relleno
            if (userLikes[articleId]) {
                const likeButton = document.querySelector(`[data-post-id="${articleId}"]`);
                likeButton.classList.add('liked');
                likeButton.setAttribute('title', 'Ya has dado like');
                const heartIcon = likeButton.querySelector('svg');
                heartIcon.innerHTML = '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>';
            }
        }
        
        // Cargar comentarios
        const comments = JSON.parse(localStorage.getItem('articleComments'));
        for (const [articleId, articleComments] of Object.entries(comments)) {
            document.getElementById(`comments-count-${articleId}`).textContent = articleComments.length;
        }
    });
    
    // Función para mostrar mensaje de alerta personalizado
    function showAlert(message, success) {
        const alertBox = document.createElement('div');
        alertBox.style.position = 'fixed';
        alertBox.style.top = '20px';
        alertBox.style.left = '50%';
        alertBox.style.transform = 'translateX(-50%)';
        alertBox.style.backgroundColor = success ? '#dff0d8' : '#f8d7da';
        alertBox.style.color = success ? '#155724' : '#721c24';
        alertBox.style.padding = '10px 20px';
        alertBox.style.borderRadius = '5px';
        alertBox.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        alertBox.style.zIndex = '1000';
        alertBox.style.textAlign = 'center';
        alertBox.innerText = message;
        
        document.body.appendChild(alertBox);
        
        // Eliminar la alerta después de 3 segundos
        setTimeout(() => {
            alertBox.style.opacity = '0';
            alertBox.style.transition = 'opacity 0.5s';
            setTimeout(() => document.body.removeChild(alertBox), 500);
        }, 3000);
    }
    
    // Función para dar "me gusta"
    function likePost(articleId) {
        // Preparar datos para enviar al backend
        const formData = new FormData();
        formData.append('post_id', articleId);

        // Enviar la solicitud al script de backend
        fetch('/PRODCONS/PI2do/Dashboard_Usuario/likes and comments/handle_like.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Si el like fue exitoso, actualizar la UI
                const likeCountElement = document.getElementById(`likes-count-${articleId}`);
                if (likeCountElement) {
                    let currentLikes = parseInt(likeCountElement.textContent);
                    likeCountElement.textContent = currentLikes + 1;
                }
                // Cambiar el corazón a relleno y deshabilitar el botón
                const likeButton = document.querySelector(`[data-post-id="${articleId}"]`);
                if (likeButton) {
                    likeButton.classList.add('liked');
                    likeButton.setAttribute('title', data.message);
                    likeButton.onclick = null;
                    const heartIcon = likeButton.querySelector('svg');
                    if (heartIcon) {
                        heartIcon.innerHTML = '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>';
                    }
                }
                showAlert(data.message, true);
            } else {
                showAlert(data.message, false);
            }
        })
        .catch(error => {
            console.error('Error al enviar like:', error);
            showAlert('Error al procesar el like.', false);
        });
    }
    
    // Función para mostrar/ocultar sección de comentarios
    function toggleComments(articleId) {
        const commentsSection = document.getElementById(`comments-section-${articleId}`);
        if (commentsSection.style.display === 'none') {
            commentsSection.style.display = 'block';
            loadComments(articleId);
        } else {
            commentsSection.style.display = 'none';
        }
    }
    
    // Función para cargar comentarios
    function loadComments(articleId) {
        const comments = JSON.parse(localStorage.getItem('articleComments'))[articleId];
        const commentsContainer = document.getElementById(`existing-comments-${articleId}`);
        commentsContainer.innerHTML = '';
        
        if (comments.length === 0) {
            commentsContainer.innerHTML = '<p>No hay comentarios aún. ¡Sé el primero en comentar!</p>';
            return;
        }
        
        for (const comment of comments) {
            const commentElement = document.createElement('div');
            commentElement.className = 'comment';
            commentElement.innerHTML = `
                <div class="comment-author">Usuario</div>
                <div class="comment-content">${comment.text}</div>
                <div class="comment-date">${comment.date}</div>
            `;
            commentsContainer.appendChild(commentElement);
        }
    }
    
    // Función para agregar un comentario
    function addComment(articleId) {
        const commentInput = document.getElementById(`comment-input-${articleId}`);
        const commentText = commentInput.value.trim();
        
        if (!commentText) return;
        
        // Preparar datos para enviar al backend
        const formData = new FormData();
        formData.append('post_id', articleId);
        formData.append('comment_text', commentText);

        // Enviar la solicitud al script de backend
        fetch('/PRODCONS/PI2do/Dashboard_Usuario/likes and comments/handle_comment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar la UI
                const commentsCount = document.getElementById(`comments-count-${articleId}`);
                if (commentsCount) {
                    let currentCount = parseInt(commentsCount.textContent);
                    commentsCount.textContent = currentCount + 1;
                }
                commentInput.value = '';
                
                // Recargar los comentarios
                loadComments(articleId);
                showAlert(data.message, true);
            } else {
                showAlert(data.message, false);
            }
        })
        .catch(error => {
            console.error('Error al enviar comentario:', error);
            showAlert('Error al procesar el comentario.', false);
        });
    }

    // Ejemplo de ajuste para likePost (podría necesitar más cambios dependiendo de la implementación)
    document.addEventListener('click', function(event) {
        if (event.target.closest('.like-button-small')) {
            const likeButton = event.target.closest('.like-button-small');
            const postId = likeButton.getAttribute('data-post-id');
            // Call your like handling function
            // likePost(postId);
            console.log('Like button clicked for post ID:', postId);

             // Implement fetch call to handle_like.php here
             fetch('/PRODCONS/PI2do/Dashboard_Usuario/likes and comments/handle_like.php', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/x-www-form-urlencoded',
                 },
                 body: 'post_id=' + postId
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     // Update the like count display
                     const likesCountSpan = likeButton.querySelector('.likes-count');
                     likesCountSpan.textContent = data.likes_count; // Assuming the backend returns the new count
                     likeButton.classList.toggle('liked'); // Toggle a 'liked' class for styling if needed
                 } else {
                     console.error('Error liking post:', data.error);
                     alert('Error al dar like: ' + data.error);
                 }
             })
             .catch(error => {
                 console.error('Fetch error:', error);
                 alert('Error de comunicación al dar like.');
             });
        }
    });

    // Ejemplo de ajuste para toggleComments
    document.addEventListener('click', function(event) {
        if (event.target.closest('.comment-toggle-small')) {
             const commentToggleButton = event.target.closest('.comment-toggle-small');
             const postElement = commentToggleButton.closest('.post');
             const commentsSection = postElement.querySelector('.comments-section');

             if (commentsSection) {
                 const postId = postElement.getAttribute('data-post-id');
                 // Call your toggle comments function
                 // toggleComments(postId);
                 console.log('Comment toggle clicked for post ID:', postId);

                 // Toggle visibility of comments section
                  if (commentsSection.style.display === 'none') {
                     commentsSection.style.display = 'block';
                     // Here you would also fetch and display existing comments for this post
                      fetchComments(postId, postElement.querySelector('.existing-comments'));
                 } else {
                     commentsSection.style.display = 'none';
                      // Clear existing comments when hiding
                      postElement.querySelector('.existing-comments').innerHTML = '';
                 }
             }
        }
    });

    // Ejemplo de ajuste para addComment (usando event delegation en el contenedor de comentarios)
    document.addEventListener('click', function(event) {
        if (event.target.closest('.add-comment button')) {
            const addCommentButton = event.target.closest('.add-comment button');
             const commentsSection = addCommentButton.closest('.comments-section');
             const postElement = commentsSection.closest('.post');

            if (postElement && commentsSection) {
                const postId = postElement.getAttribute('data-post-id');
                const commentInput = commentsSection.querySelector('textarea');
                const commentContent = commentInput.value.trim();

                if (commentContent === '') {
                    alert('El comentario no puede estar vacío.');
                    return;
                }

                // Call your add comment handling function
                // addComment(postId, commentContent);
                 console.log('Add comment button clicked for post ID:', postId, 'Content:', commentContent);

                 // Implement fetch call to handle_comment.php here
                  fetch('/PRODCONS/PI2do/Dashboard_Usuario/likes and comments/handle_comment.php', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/x-www-form-urlencoded',
                      },
                      body: 'post_id=' + postId + '&contenido_comentario=' + encodeURIComponent(commentContent)
                  })
                  .then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          alert('Comentario publicado con éxito!');
                          commentInput.value = ''; // Clear the input
                           // Optionally refresh comments for this post
                          const existingCommentsContainer = commentsSection.querySelector('.existing-comments');
                           fetchComments(postId, existingCommentsContainer);
                           // Update comment count
                           const commentsCountSpan = postElement.querySelector('.comments-count');
                           commentsCountSpan.textContent = parseInt(commentsCountSpan.textContent) + 1; // Simple increment

                      } else {
                          console.error('Error publishing comment:', data.error);
                          alert('Error al publicar comentario: ' + data.error);
                      }
                  })
                  .catch(error => {
                      console.error('Fetch error:', error);
                      alert('Error de comunicación al publicar comentario.');
                  });
            }
        }
    });

    // Function to fetch and display existing comments
     function fetchComments(postId, commentsContainer) {
         // Clear existing comments
         commentsContainer.innerHTML = 'Cargando comentarios...';

         // Implement fetch call to get comments for the post
          fetch('/PRODCONS/PI2do/Dashboard_Usuario/likes and comments/fetch_comments.php?post_id=' + postId)
          .then(response => response.json())
          .then(data => {
              commentsContainer.innerHTML = ''; // Clear loading message
              if (data.success && data.comments.length > 0) {
                  data.comments.forEach(comment => {
                      const commentElement = document.createElement('div');
                      commentElement.classList.add('comment');
                      commentElement.innerHTML = `
                          <div class="comment-author">${htmlspecialchars(comment.autor_nombre)}</div>
                          <div class="comment-text">${htmlspecialchars(comment.contenido)}</div>
                          <div class="comment-date">${comment.fecha}</div>
                      `;
                      commentsContainer.appendChild(commentElement);
                  });
              } else if (data.success && data.comments.length === 0) {
                   commentsContainer.innerHTML = '<p>No hay comentarios aún.</p>';
              } else {
                  console.error('Error fetching comments:', data.error);
                   commentsContainer.innerHTML = '<p>Error al cargar comentarios.</p>';
              }
          })
          .catch(error => {
              console.error('Fetch error:', error);
               commentsContainer.innerHTML = '<p>Error de comunicación al cargar comentarios.</p>';
          });
     }

      // Helper function for HTML entities encoding
      function htmlspecialchars(str) {
          const map = {
              '&': '&amp;',
              '<': '&lt;',
              '>': '&gt;',
              '"': '&quot;',
              ''': '&#039;'
          };
          return str.replace(/[&<>'"]/g, function(m) { return map[m]; });
      }

    // Marcar todas las notificaciones como vistas (This might be for a different Notibox)
    // document.addEventListener('DOMContentLoaded', function() { ... });

</script>
</body>

</html>
=======
<?php
session_start();
var_dump($_SESSION); // <-- Esto mostrará el contenido de la sesión
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] != 'Usuario') {
    header("Location: /PRODCONS/inicio_sesion/login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS</title>
    <link rel="stylesheet" href="/PRODCONS/styles.css">
    <link rel="stylesheet" href="/PRODCONS/footer/footer.css">
    <link rel="stylesheet" href="articulos.css">
    <link href="/PRODCONS/Header visitantes/barra_principal.css" rel="stylesheet">
    <!-- Google Cloud Translation API -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <!-- Script de traducción global -->
    <script src="/PRODCONS/traslate.js"></script>
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
        
        /* Estilos para likes y comentarios */
        .post-footer {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        
        .interaction-buttons {
            display: flex;
            margin-left: 15px;
            align-items: center;
        }
        
        .like-button-small, .comment-toggle-small {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0;
            margin: 0 5px;
            color: #666;
            font-size: 12px;
        }
        
        .like-button-small svg, .comment-toggle-small svg {
            margin-right: 3px;
        }
        
        .like-button-small:hover, .like-button-small.liked {
            color: #e74c3c;
        }
        
        .comment-toggle-small:hover {
            color: #3498db;
        }
        
        .comments-section {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
        }
        
        .existing-comments {
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 10px;
        }
        
        .comment {
            background-color: white;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 8px;
        }
        
        .comment-author {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .comment-date {
            font-size: 0.8em;
            color: #777;
        }
        
        .add-comment textarea {
            width: 100%;
            min-height: 60px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
            resize: vertical;
        }
        
        .add-comment button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .add-comment button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <header>
        <div class="header-contenedor">
            <div class="principal">
                <a class="navlink" href="/PRODCONS/empresas_responsables/empresasr.html">EMPRESAS RESPONSABLES</a>

                <!-- Bandera para cambiar idioma (alterna con cada clic) -->
                <div id="idiomaToggle">
                    <img class="españa" id="banderaIdioma" src="/PRODCONS/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
                </div>
            </div>
        </div>
    </header>

    <section class="logo">
        <div class="header_2">
            <svg class="hamburguesa" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round" width="36" height="36" stroke-width="1.5" onclick="toggleMenu()">
                <path d="M4 6l16 0"></path>
                <path d="M4 12l16 0"></path>
                <path d="M4 18l16 0"></path>
            </svg>

            <img class="prodcons" src="/PRODCONS/imagenes/prodcon/logoSinfondo.png" alt="Logo">

            <div class="subtitulos">
                <li><a href="/PRODCONS/pr/produccionr.html">PRODUCCIÓN RESPONSABLE</a></li>
                <li><a href="/PRODCONS/cr/consumores.html">CONSUMO RESPONSABLE</a></li>

                <form class="search-form">
                    <button class="lupa">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="36" height="36"
                            stroke-width="1.5">
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                            <path d="M21 21l-6 -6"></path>
                        </svg>
                    </button>
                    <input type="search" placeholder="Buscar..." />
                </form>
            </div>
        </div>
    </section>


    <!-- Menú de hamburguesa -->
    <div class="menu" id="menu">
        <ul>
            <li><a href="/PRODCONS/pr/produccionr.html" onclick="setActive(this)">PRODUCCION RESPONSABLE</a></li>
            <li><a href="/PRODCONS/cr/consumores.html" onclick="setActive(this)">CONSUMO RESPONSABLE</a></li>
            <li><a href="#">Lupa de búsqueda</a></li>
        </ul>
    </div>

    <script>
        // Toggle para la visibilidad del menú de hamburguesa
        function toggleMenu() {
            const menu = document.getElementById("menu");
            menu.classList.toggle("active");
        }

        // Función para alternar entre idiomas con un solo clic
        function alternarIdioma() {
            const bandera = document.getElementById('banderaIdioma');
            const idiomaActual = bandera.src.includes('ingles.png') ? 'ingles' : 'espanol';
            const nuevoIdioma = idiomaActual === 'ingles' ? 'espanol' : 'ingles';
            
            // Cambiar la imagen de la bandera inmediatamente
            bandera.src = nuevoIdioma === 'ingles' 
                ? "/PRODCONS/imagenes/logos/ingles.png" 
                : "/PRODCONS/imagenes/logos/espanol.png";
            
            // Realizar la traducción
            translateContent(nuevoIdioma === 'ingles' ? 'en' : 'es');
            
            // Guardar la preferencia en localStorage
            localStorage.setItem('preferredLanguage', nuevoIdioma === 'ingles' ? 'en' : 'es');
        }
        
        // Cargar el idioma guardado al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            const savedLanguage = localStorage.getItem('preferredLanguage');
            if (savedLanguage) {
                const bandera = document.getElementById('banderaIdioma');
                bandera.src = savedLanguage === 'en' 
                    ? "/PRODCONS/imagenes/logos/ingles.png" 
                    : "/PRODCONS/imagenes/logos/espanol.png";
            }
        });
    </script>


    <div class="sobrecont">
        <div class="cuadro-info">
            <h2>BLOG</h2>
            <p>Somos una organización dedicada a cuidar del medio ambiente, aplicándolo en nuestra vida diaria y
                promoviendo a los demás a hacerlo para el bienestar de todos.</p>
        </div>
        <img class="imagen-principal" src="/PRODCONS/imagenes/tractor.png" alt="Imagen Principal">
    </div>


    <h3 class="apubli"> MIRA MAS DE NUESTRO CONTENIDO </h3>


    <section class="post-list">
        <div class="content">
            <article class="post">
                <div class="post-header">
                    <div class="post-img-1"></div> 
                </div>
                <div class="post-body">
                    <h2>Menos plásticos mas vida</h2>
                    <p class="descripcion">El plástico nos rodea: en casa, en tiendas y hasta en los océanos. Con pequeñas decisiones, podemos reducir su uso y hacer la diferencia. ¿Listo para cambiar hábitos y ayudar al planeta? </p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo1.php" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(1)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-1">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(1)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-1">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-1" style="display: none;">
                        <div class="existing-comments" id="existing-comments-1">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-1" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(1)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 14 de febrero del 2025 </span>
                    <span>| Juan Pablo Mancilla Rodriguez</span>
                </div>
            </article>

            <article class="post">
                <div class="post-header">
                    <div class="post-img-2"></div>
                </div>
                <div class="post-body">
                    <h2>Tu puedes hacer la diferencia</h2>
                    <p>Cada elección cuenta. Adoptar hábitos más sostenibles en el día a día no solo reduce nuestra huella ecológica, sino que inspira un cambio real en la sociedad. ¿Te animas a dar el primer paso?</p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo2.html" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(2)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-2">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(2)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-2">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-2" style="display: none;">
                        <div class="existing-comments" id="existing-comments-2">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-2" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(2)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 19 de Febrero del 2025 </span>
                    <span>| Yureni Elizabeth Sierra Aguilar </span>
                </div>
                <div>

                    
                </div>

                
            </article>

            <article class="post">
                <div class="post-header">
                    <div class="post-img-3"></div>
                </div>
                <div class="post-body">
                    <h2>La Revolución de la Moda Sostenible </h2>
                    <p class="descripcion">La industria de la moda es poderosa, pero también contaminante. Apostar por opciones sostenibles es clave para un futuro más limpio. ¿Sabes cómo tu ropa puede marcar la diferencia?</p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo3.html" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(3)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-3">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(3)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-3">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-3" style="display: none;">
                        <div class="existing-comments" id="existing-comments-3">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-3" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(3)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 19 de Febrero del 2025</span>
                    <span> | Daniel Sahid Barroso Alvarez </span>
                </div>
            </article>

            <article class="post">
                <div class="post-header">
                    <div class="post-img-4"></div>
                </div>
                <div class="post-body">

                    <h2>Crea tu propio huerto y sus ventajas</h2>
                    <p class="descripcion">Cultivar tus propios alimentos te da frescura, control y una alimentación más sana. Además, reduces residuos y cuidas el medioambiente. ¿Te animas a empezar tu propio huerto? </p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo4.html" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(4)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-4">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(4)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-4">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-4" style="display: none;">
                        <div class="existing-comments" id="existing-comments-4">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-4" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(4)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 20 de Febrero del 2025 </span>
                    <span>| Xiomara Anabeli Cobian Ramirez</span>
                </div>
            </article>

            <article class="post">
                <div class="post-header">
                    <div class="post-img-5"></div>
                </div>
                <div class="post-body">
                    <h2>Reduciendo residuos en el hogar</h2>
                    <p class="descripcion">Consumimos sin medida, sin considerar el impacto. Es momento de tomar decisiones responsables y reducir nuestra huella ecológica. Cada elección cuenta. ¿Qué harás hoy por un futuro más verde?</p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo5.html" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(5)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-5">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(5)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-5">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-5" style="display: none;">
                        <div class="existing-comments" id="existing-comments-5">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-5" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(5)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 21 de Febrero del 2025 </span>
                    <span>| Fernando Benitez Astudillo</span>
                </div>
            </article>

            <article class="post">
                <div class="post-header">
                    <div class="post-img-6"></div>
                </div>
                <div class="post-body">

                    <h2>Consumo Digital y Producción Responsable</h2>
                    <p class="descripcion">El consumo digital impacta el planeta más de lo que imaginas. Optar por prácticas responsables en tecnología puede hacer una gran diferencia. ¿Sabes cómo reducir tu impacto digital?</p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo6.html" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(6)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-6">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(6)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-6">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-6" style="display: none;">
                        <div class="existing-comments" id="existing-comments-6">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-6" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(6)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 21 de Febrero del 2025 </span>
                    <span>| Isabela Monserrat Vidrio Camarena</span>
                </div>
            </article>
        </div>
    </section>

    

    <!-- <div class="carousel-container">
        <div class="carousel">
            <?php foreach ($publicaciones as $pub): ?>
            <div class="carousel-item">
                <div class="post-header">
                    <img src="<?= $pub['imagen'] ?>" alt="<?= $pub['titulo'] ?>" class="post-img">
                </div>
                <div class="post-body">
                    <h2><?= $pub['titulo'] ?></h2>
                    <p class="descripcion"><?= $pub['descripcion'] ?></p>
                    <a href="<?= $pub['link'] ?>" class="post-link">Leer más...</a>
                    <span>Publicado el <?= $pub['fecha'] ?></span>
                    <span>| <?= $pub['autor'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="prev">‹</button>
        <button class="next">›</button>
    </div> -->
    
    <!--FOOTER-->
    <!-- <footer>
        <div class="footer-container"></div>
      </footer> -->
      
      <script src="/PRODCONS/Header visitantes/barra_principal.js"></script>
<!-- <script src="/PRODCONS/footer/footer.js"></script> -->

<script>
    // Funciones para manejar likes y comentarios
    // Simulación de almacenamiento local para likes y comentarios
    
    // Inicializar datos si no existen
    if (!localStorage.getItem('articleLikes')) {
        localStorage.setItem('articleLikes', JSON.stringify({1: 0, 2: 0, 3: 0, 4: 0, 5: 0, 6: 0}));
    }
    
    if (!localStorage.getItem('articleComments')) {
        localStorage.setItem('articleComments', JSON.stringify({
            1: [], 2: [], 3: [], 4: [], 5: [], 6: []
        }));
    }
    
    // Registro de likes de usuario
    if (!localStorage.getItem('userLikes')) {
        localStorage.setItem('userLikes', JSON.stringify({
            1: false, 2: false, 3: false, 4: false, 5: false, 6: false
        }));
    }
    
    // Cargar los datos existentes al iniciar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar likes
        const likes = JSON.parse(localStorage.getItem('articleLikes'));
        const userLikes = JSON.parse(localStorage.getItem('userLikes'));
        
        for (const [articleId, count] of Object.entries(likes)) {
            document.getElementById(`likes-count-${articleId}`).textContent = count;
            
            // Si el usuario ya dio like, cambiar el corazón a relleno
            if (userLikes[articleId]) {
                const likeButton = document.querySelector(`[onclick="likePost(${articleId})"]`);
                likeButton.classList.add('liked');
                likeButton.setAttribute('title', 'Ya has dado like');
                const heartIcon = likeButton.querySelector('svg');
                heartIcon.innerHTML = '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>';
            }
        }
        
        // Cargar comentarios
        const comments = JSON.parse(localStorage.getItem('articleComments'));
        for (const [articleId, articleComments] of Object.entries(comments)) {
            document.getElementById(`comments-count-${articleId}`).textContent = articleComments.length;
        }
    });
    
    // Función para mostrar mensaje de alerta personalizado
    function showAlert(message) {
        const alertBox = document.createElement('div');
        alertBox.style.position = 'fixed';
        alertBox.style.top = '20px';
        alertBox.style.left = '50%';
        alertBox.style.transform = 'translateX(-50%)';
        alertBox.style.backgroundColor = '#f8d7da';
        alertBox.style.color = '#721c24';
        alertBox.style.padding = '10px 20px';
        alertBox.style.borderRadius = '5px';
        alertBox.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        alertBox.style.zIndex = '1000';
        alertBox.style.textAlign = 'center';
        alertBox.innerText = message;
        
        document.body.appendChild(alertBox);
        
        // Eliminar la alerta después de 3 segundos
        setTimeout(() => {
            alertBox.style.opacity = '0';
            alertBox.style.transition = 'opacity 0.5s';
            setTimeout(() => document.body.removeChild(alertBox), 500);
        }, 3000);
    }
    
    // Función para dar "me gusta"
    function likePost(articleId) {
        const userLikes = JSON.parse(localStorage.getItem('userLikes'));
        
        // Verificar si el usuario ya ha dado like a este artículo
        if (userLikes[articleId]) {
            showAlert('Ya has dado like a este artículo');
            return;
        }
        
        // Actualizar conteo de likes
        const likes = JSON.parse(localStorage.getItem('articleLikes'));
        likes[articleId]++;
        localStorage.setItem('articleLikes', JSON.stringify(likes));
        
        // Registrar que este usuario ha dado like
        userLikes[articleId] = true;
        localStorage.setItem('userLikes', JSON.stringify(userLikes));
        
        // Actualizar la UI
        document.getElementById(`likes-count-${articleId}`).textContent = likes[articleId];
        
        // Efecto visual al dar like
        const likeButton = document.querySelector(`[onclick="likePost(${articleId})"]`);
        likeButton.classList.add('liked');
        likeButton.setAttribute('title', 'Ya has dado like');
        
        // Cambiar el corazón a relleno cuando se da like
        const heartIcon = likeButton.querySelector('svg');
        heartIcon.innerHTML = '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>';
    }
    
    // Función para mostrar/ocultar sección de comentarios
    function toggleComments(articleId) {
        const commentsSection = document.getElementById(`comments-section-${articleId}`);
        if (commentsSection.style.display === 'none') {
            commentsSection.style.display = 'block';
            loadComments(articleId);
        } else {
            commentsSection.style.display = 'none';
        }
    }
    
    // Función para cargar comentarios
    function loadComments(articleId) {
        const comments = JSON.parse(localStorage.getItem('articleComments'))[articleId];
        const commentsContainer = document.getElementById(`existing-comments-${articleId}`);
        commentsContainer.innerHTML = '';
        
        if (comments.length === 0) {
            commentsContainer.innerHTML = '<p>No hay comentarios aún. ¡Sé el primero en comentar!</p>';
            return;
        }
        
        for (const comment of comments) {
            const commentElement = document.createElement('div');
            commentElement.className = 'comment';
            commentElement.innerHTML = `
                <div class="comment-author">Usuario</div>
                <div class="comment-content">${comment.text}</div>
                <div class="comment-date">${comment.date}</div>
            `;
            commentsContainer.appendChild(commentElement);
        }
    }
    
    // Función para agregar un comentario
    function addComment(articleId) {
        const commentInput = document.getElementById(`comment-input-${articleId}`);
        const commentText = commentInput.value.trim();
        
        if (!commentText) return;
        
        const comments = JSON.parse(localStorage.getItem('articleComments'));
        const now = new Date();
        const formattedDate = now.toLocaleString('es-ES');
        
        comments[articleId].push({
            text: commentText,
            date: formattedDate
        });
        
        localStorage.setItem('articleComments', JSON.stringify(comments));
        
        // Actualizar la UI
        document.getElementById(`comments-count-${articleId}`).textContent = comments[articleId].length;
        commentInput.value = '';
        
        // Recargar los comentarios
        loadComments(articleId);
    }
</script>
<?php include $_SERVER['DOCUMENT_ROOT'].'/PRODCONS/footer/Usuario/footer.php'; ?>
</body>

</html>
>>>>>>> Stashed changes:Dashboard_Usuario/usuario.php
