<?php
session_start();
// Asegurarse de que el registro de errores esté activado si es necesario (verifica tu configuración de php.ini también)
ini_set('log_errors', '1');
// ini_set('error_log', '/path/to/your/php-error.log'); // <<-- Reemplaza con la ruta a tu archivo de log

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID']) || !isset($_SESSION['Rol']) || $_SESSION['Rol'] != 'Usuario') {
    // Redirigir al login con un mensaje de error
    $_SESSION['login_error'] = "Debes iniciar sesión para ver este artículo";
    header("Location: /PRODCONS/PI2do/inicio_sesion/login.php");
    exit();
}

// Establecer la localización a español para mostrar nombres de meses correctamente
setlocale(LC_TIME, 'es_ES', 'es_ES.utf8', 'es');

require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion.php'; // Ruta absoluta a tu archivo de conexión

// Obtener datos del usuario logueado
$usuario_id = $_SESSION['Usuario_ID'];
$usuario_nombre = $_SESSION['Nombre'];

$article = null;
$error = null;

// Verificar si se proporcionó un ID de artículo en la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $article_id = (int)$_GET['id']; // Convertir a entero explícitamente

    // Conexión MySQLi
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    // Verificar si la conexión se estableció correctamente
    if ($conn) {
        // Obtener el artículo con el ID proporcionado, incluyendo likes y comentarios
        $sql = "SELECT a.*, u.nombre as autor_nombre, 
                       GROUP_CONCAT(ia.Url_Imagen) as imagenes,
                       (SELECT COUNT(*) FROM likes WHERE Articulo_ID = a.ID_Articulo) as total_likes,
                       (SELECT COUNT(*) FROM comentarios_autor WHERE Articulo_ID = a.ID_Articulo) as total_comentarios
                FROM articulos a 
                JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID
                LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
                WHERE a.ID_Articulo = ? AND a.Estado = 'Publicado'
                GROUP BY a.ID_Articulo";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $article_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $article = $result->fetch_assoc();
            $stmt->close();
        } else {
            $error = "Error al preparar la consulta: " . $conn->error;
        }
        $conexion->cerrar_conexion();
    } else {
        $error = "Error de conexión a la base de datos.";
    }
} else {
    $error = "No se especificó un ID de artículo válido.";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article ? htmlspecialchars($article['Titulo']) : 'Artículo no encontrado'; ?> - PRODCONS</title>
    <link rel="stylesheet" href="/PRODCONS/PI2do/postWeb/code.css"> <!-- Estilos postWeb -->
    <link rel="stylesheet" href="/PRODCONS/PI2do/Header_post/header_post.css">
    <link rel="stylesheet" href="/PRODCONS/PI2do/footer/footer.css">
    <style>
        .interaccion {
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .likes-comentarios {
            display: flex;
            gap: 20px;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .likes button, .comentarios button {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px 12px;
            transition: all 0.2s;
        }

        .likes button:hover, .comentarios button:hover {
            background: #f5f5f5;
        }

        .likes button svg, .comentarios button svg {
            width: 24px;
            height: 24px;
            fill: #666;
        }

        .likes button.liked svg {
            fill: #e44d26;
        }

        .nuevo-comentario {
            padding: 20px;
            border-top: 1px solid #eee;
        }

        #commentForm {
            display: flex;
            gap: 10px;
        }

        #commentText {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 60px;
            resize: vertical;
        }

        #commentsList {
            padding: 20px;
        }

        .comment {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .comment:last-child {
            border-bottom: none;
        }

        .comment-author {
            font-weight: bold;
            color: #333;
        }

        .comment-date {
            color: #666;
            font-size: 0.9em;
        }
    </style>

    <!-- Google Cloud Translation API -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <!-- Script de traducción global -->
    <script src='/PRODCONS/translate.js'></script>
</head>
<body>
    <?php if ($article): ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/header_post/header_post.php'; ?>

        <!-- Sección del artículo -->
        <section class="header-section">
            <h1><?php echo htmlspecialchars($article['Titulo']); ?></h1>
            <div class="contenedor-imagenes">
                <div class="texto">
                    <p><?php echo nl2br(htmlspecialchars($article['Contenido'])); ?></p>
                </div>
                <div class="imagenes">
                    <?php if ($article['imagenes']): ?>
                        <img src="/PRODCONS/PI2do/imagenes/articulos/<?php echo htmlspecialchars(explode(',', $article['imagenes'])[0]); ?>" alt="<?php echo htmlspecialchars($article['Titulo']); ?>" class="imagen-primera">
                    <?php else: ?>
                        <div class="w-full h-full bg-[#f5f5f5] flex items-center justify-center text-[#666]">Sin imagen</div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Article content will go here -->
        <main>
            <!-- Aquí podrías agregar más secciones del artículo si el contenido está estructurado -->
            <!-- Por ahora, usamos el mismo contenido que en el header para simplificar -->
            <section>
                <h2>Contenido</h2>
                 <p><?php echo nl2br(htmlspecialchars($article['Contenido'])); ?></p>
            </section>
        </main>

        <!-- Sección de autor y fecha -->
        <div class="article-meta">
            <div class="meta-info-container">
                <div class="publicado">Publicado el <?php
                    try {
                        $date = new DateTime($article['Fecha de Publicacion']);
                        echo $date->format('d F Y');
                    } catch (Exception $e) {
                        echo 'Fecha inválida';
                    }
                ?></div>
                <div class="autor">| Por <?php echo htmlspecialchars($article['autor_nombre']); ?></div>
            </div>
        </div>

        <!-- Sección de bibliografías (el "recuadro negro") -->
        <section class="autor-bibliografias">
            <!-- Aquí irán las bibliografías -->
            <h2>Bibliografías</h2>
            <?php if (!empty($article['Bibliografias'])): ?>
                <p><?php echo nl2br(htmlspecialchars($article['Bibliografias'])); ?></p>
            <?php else: ?>
                <p>No hay bibliografía disponible para este artículo.</p>
            <?php endif; ?>
        </section>

        <!-- Sección de interacción (likes y comentarios) -->
        <section class="interaccion">
            <div class="likes-comentarios">
                <div class="likes">
                    <button id="likeButton" onclick="toggleLike()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        <span id="likeCount"><?php echo isset($article['total_likes']) ? $article['total_likes'] : 0; ?></span>
                    </button>
                </div>
                <div class="comentarios">
                    <button id="commentButton" onclick="toggleComments()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                            <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/>
                        </svg>
                        <span id="commentCount"><?php echo isset($article['total_comentarios']) ? $article['total_comentarios'] : 0; ?></span>
                    </button>
                </div>
            </div>

            <!-- Sección de comentarios (oculta por defecto) -->
            <div id="commentsSection" style="display: none;">
                <div class="nuevo-comentario">
                    <form id="commentForm">
                        <textarea id="commentText" placeholder="Escribe tu comentario..." required></textarea>
                        <button type="submit">Publicar</button>
                    </form>
                </div>
                <div id="commentsList">
                    <!-- Los comentarios se cargarán aquí dinámicamente -->
                </div>
            </div>
        </section>

    <?php else: ?>
        <div class="container mx-auto mt-10 p-5 bg-white rounded-md shadow-md">
            <h1 class="text-2xl font-bold mb-4">Error</h1>
            <p><?php echo $error ?? 'Artículo no encontrado.'; ?></p>
        </div>
    <?php endif; ?>

<!-- =====================================================================
SCRIPT PARA ACTUALIZAR BOTONES DE IDIOMA - NO MODIFICAR
Este script mantiene sincronizada la interfaz de idioma
 
- El botón X permite ocultar el selector de idioma cuando no se necesita
===================================================================== -->
<script>
    // Function to update button states based on current language
    function updateLanguageButtons() {
        const btnEs = document.getElementById('btn-es');
        const btnEn = document.getElementById('btn-en');
        const toggleText = document.getElementById('toggle-text');
        
        // Get current language from localStorage or default to Spanish
        const currentLang = localStorage.getItem('preferredLanguage') || 'es';
        
        // Update active button
        if (currentLang === 'en') {
            btnEs.classList.remove('active');
            btnEn.classList.add('active');
            toggleText.innerText = 'Change language?';
        } else {
            btnEn.classList.remove('active');
            btnEs.classList.add('active');
            toggleText.innerText = '¿Cambiar idioma?';
        }
    }
    
    // Call this function on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateLanguageButtons();
        
        // Add event listener to update buttons when language changes
        const observer = new MutationObserver(function(mutations) {
            updateLanguageButtons();
        });
        
        // Start observing the document with the configured parameters
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['lang'] });
        
        // Add event listener for the close button
        document.getElementById('close-language-toggle').addEventListener('click', function() {
            document.getElementById('language-toggle').style.display = 'none';
        });
    });
    
    // Override the cambiarIdioma function to update button states
    const originalCambiarIdioma = window.cambiarIdioma;
    window.cambiarIdioma = function(idioma) {
        if (typeof originalCambiarIdioma === 'function') {
            originalCambiarIdioma(idioma);
        } else {
            // Fallback if the original function isn't available
            translateContent(idioma === 'ingles' ? 'en' : 'es');
        }
        
        // Update button states
        setTimeout(updateLanguageButtons, 100);
    };
</script>

    <script>
        // Función para alternar el like
        function toggleLike() {
            const likeButton = document.getElementById('likeButton');
            const likeCount = document.getElementById('likeCount');
            const articleId = <?php echo json_encode($article_id); ?>;
            const userId = <?php echo json_encode($usuario_id); ?>;
            
            // Verificar si ya existe un like
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/PRODCONS/PI2do/Dashboard_Usuario/likes and comments/handle_like.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        likeButton.classList.toggle('liked');
                        likeCount.textContent = response.total_likes;
                    }
                }
            };
            xhr.send('post_id=' + articleId + '&user_id=' + userId);
        }

        // Función para alternar la visibilidad de los comentarios
        function toggleComments() {
            const commentsSection = document.getElementById('commentsSection');
            commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
        }

        // Función para manejar el envío de comentarios
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const commentText = document.getElementById('commentText').value;
            if (!commentText.trim()) return;

            const articleId = <?php echo json_encode($article_id); ?>;
            const userId = <?php echo json_encode($usuario_id); ?>;
            const userName = <?php echo json_encode($usuario_nombre); ?>;

            // Enviar el comentario al servidor
            const formData = new FormData();
            formData.append('article_id', articleId);
            formData.append('Usuario_ID', userId);
            formData.append('comment_text', commentText);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/PRODCONS/PI2do/postWeb/comentarios.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Crear el nuevo comentario
                        const commentsList = document.getElementById('commentsList');
                        const commentCount = document.getElementById('commentCount');
                        
                        const comment = document.createElement('div');
                        comment.className = 'comment';
                        comment.innerHTML = `
                            <div class="comment-author">${userName}:</div>
                            <div class="comment-text">${commentText}</div>
                            <div class="comment-date">${new Date().toLocaleDateString()}</div>
                        `;
                        
                        // Agregar el nuevo comentario al inicio de la lista
                        commentsList.insertBefore(comment, commentsList.firstChild);
                        
                        // Limpiar el formulario
                        document.getElementById('commentText').value = '';
                        
                        // Actualizar el contador de comentarios
                        commentCount.textContent = parseInt(commentCount.textContent) + 1;
                    }
                }
            };
            xhr.send(formData);
        });
    </script>

    <section class="logo">
        <div class="header_2">
            <img class="prodcons" src="/PRODCONS/PI2do/imagenes/prodcon/logoSinfondo.png" alt="Logo" />
            <div class="subtitulos">
                <a href="/PRODCONS/PI2do/pr/produccionr.php">PRODUCCIÓN RESPONSABLE</a>
                <a href="/PRODCONS/PI2do/cr/consumores.php">CONSUMO RESPONSABLE</a>
            </div>
        </div>
    </section>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/footer/Visitante/footer.php'; ?>

</body>
</html>