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
$article_id = null;

// Verificar si se proporcionó un ID de artículo válido
$article_id = isset($_GET['ID_Articulo']) ? filter_var($_GET['ID_Articulo'], FILTER_VALIDATE_INT) : null;
if ($article_id === false || $article_id <= 0) {
    $article_id = null;
}

// Debug: Mostrar información sobre el ID recibido
error_log("Debug - ID_Articulo recibido: " . $_GET['ID_Articulo'] ?? 'null');
error_log("Debug - article_id después del cast: " . $article_id);

if ($article_id <= 0) {
    // Debug: Mostrar información sobre el error
    error_log("Error - ID de artículo inválido: " . $article_id);
    error_log("Error - ID recibido: " . $_GET['ID_Articulo'] ?? 'null');
    error_log("Error - Tipo de ID recibido: " . gettype($_GET['ID_Articulo'] ?? null));
    
    // Mostrar mensaje de error en la página en lugar de redirigir
    $error = "Error al cargar el artículo. Por favor, intenta de nuevo.";
} else {
    // Conexión MySQLi
    try {
        $conexion = new Conexion();
        $conexion->abrir_conexion();
        $conn = $conexion->conexion;
        
        if (!$conn) {
            throw new Exception("Error de conexión a la base de datos");
        }

        // Obtener el artículo con el ID proporcionado
        $sql = "SELECT a.*, u.Nombre as autor_nombre, 
                   GROUP_CONCAT(ia.Url_Imagen) as imagenes,
                   (SELECT COUNT(*) FROM likes WHERE ID_Articulo = ? AND visto = 1) as total_likes,
                   (SELECT COUNT(DISTINCT ComentarioUsuario_ID) FROM comentarios_autor WHERE ID_Articulo = ? AND visto = 1) as total_comentarios,
                   (SELECT visto FROM likes WHERE ID_Articulo = ? AND Usuario_ID = ? LIMIT 1) as usuario_ha_liked
            FROM articulos a 
            JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID
            LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
            WHERE a.ID_Articulo = ? AND a.Estado = 'Publicado'
            GROUP BY a.ID_Articulo";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conn->error);
        }

        // Bind parameters (5 parámetros: iiiii)
        $stmt->bind_param("iiiii", $article_id, $article_id, $article_id, $usuario_id, $article_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $article = $result->fetch_assoc();
        $stmt->close();

        if (!$article) {
            throw new Exception("No se encontró el artículo con ID " . $article_id);
        }

    } catch (Exception $e) {
        $error = "Error al obtener el artículo: " . $e->getMessage();
        error_log("Error en ver-articulo-usuario.php: " . $e->getMessage());
    } finally {
        if (isset($conexion)) {
            $conexion->cerrar_conexion();
        }
    }
}

?>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="user-id" content="<?php echo htmlspecialchars($usuario_id); ?>" />
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
            align-items: center;
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
            font-size: 16px;
            color: #666;
            min-width: 120px;
            justify-content: center;
            border-radius: 4px;
        }

        .likes button:hover, .comentarios button:hover {
            background: #f5f5f5;
        }

        .likes button svg, .comentarios button svg {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        .likes button.liked {
            color: #e44d26;
        }

        .likes button.liked svg {
            fill: #e44d26;
        }

        .likes button span, .comentarios button span {
            font-weight: 500;
        }

        .like-count, .comments-count {
            font-weight: 500;
        }

        #commentsSection {
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 25px;
            display: none;
        }

        #commentsSection.visible {
            display: block;
        }

        .comments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .comments-title {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
        }

        .comments-count {
            color: #666;
            font-size: 0.9em;
        }

        .nuevo-comentario {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        #commentText {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            min-height: 80px;
            resize: vertical;
            font-size: 1em;
            line-height: 1.5;
        }

        #commentText:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,.25);
        }

        #commentForm button[type="submit"] {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: all 0.2s;
            margin-top: 15px;
        }

        #commentForm button[type="submit"]:hover {
            background: #0056b3;
        }

        #commentForm button[type="submit"]:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Estilos para los comentarios */
        .comment {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .comment-avatar {
            margin-right: 12px;
        }

        .comment-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .comment-info {
            flex: 1;
        }

        .comment-author {
            font-weight: bold;
            color: #333;
            display: block;
        }

        .comment-date {
            font-size: 0.85em;
            color: #666;
            display: block;
        }

        .comment-content {
            color: #333;
            line-height: 1.5;
        }

        .loading-comments {
            padding: 20px;
            text-align: center;
            color: #666;
        }
        }

        .comment {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .comment-author {
            font-weight: bold;
            color: #333;
            font-size: 1.1em;
        }

        .comment-date {
            color: #666;
            font-size: 0.9em;
        }

        .comment-text {
            color: #333;
            line-height: 1.6;
            font-size: 1em;
        }

        /* Animación de carga */
        .loading-comments {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .loading-comments::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Estilo para cuando no hay comentarios */
        .no-comments {
            text-align: center;
            padding: 30px;
            color: #666;
            font-size: 1.1em;
        }
    </style>
    <style>
        /* Estilos para el recuadro de color y contenido principal */
        .header-section {
            background-color: #3f5022;
            color: white;
            margin-top: 0;
            position: relative;
            overflow: hidden;
        }

        .contenedor-imagenes {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 0;
            max-width: 100%;
            margin: 0 auto;
            width: 100%;
            border-radius: 0;
        }

        .texto {
            flex: 1;
            text-align: left;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 20px;
            width: 50%;
        }

        .texto h1 {
            margin: 0;
            font-size: 2rem;
            color: white;
        }

        .imagenes {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            width: 50%;
        }

        .contenido-principal {
            margin-top: 40px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Estilos para el selector de idiomas */
        .language-selector {
            position: relative;
            display: inline-block;
        }

        .language-selector img {
            cursor: pointer;
            width: 32px;
            height: 32px;
        }

        #idiomasOpciones {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 5px;
        }

        .idioma-btn {
            background: none;
            border: none;
            padding: 5px;
            cursor: pointer;
            display: block;
            width: 100%;
            text-align: left;
        }

        .idioma-btn:hover {
            background: #f5f5f5;
        }

        .idioma-btn img {
            width: 24px;
            height: 24px;
            margin-right: 8px;
        }

        .interaccion {
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .likes-comentarios {
            display: flex;
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
        <header>
            <div class="header-contenedor">
                <i class="flecha_left">
                    <a href="/PRODCONS/PI2do/Dashboard_Usuario/Inicio/usuario.php" title="Regresar a la página principal">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                        </svg>
                    </a>
                </i>
                <div class="principal">
                    <a class="navlink" href="/PRODCONS/PI2do/empresas_responsables/empresasr.php">EMPRESAS RESPONSABLES</a>
                    <!-- =====================================================================
                    SELECTOR DE BANDERA PARA CAMBIO DE IDIOMA - PERSONALIZABLE
                    Estos elementos controlan la selección de idioma en la página principal
                    ===================================================================== -->
                    <!-- Bandera principal visible - Puedes cambiar la imagen por defecto aquí -->
                    <div id="idiomaToggle" style="display: inline-block; margin-left: 15px;">
                        <img class="españa" id="banderaIdioma" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
                        </div>
                    <!-- Opciones de banderas desplegables - Puedes cambiar las imágenes aquí -->
                    <div id="idiomasOpciones" style="display: none;">
                        <img class="ingles" src="/PRODCONS/PI2do/imagenes/logos/ingles.png" onclick="cambiarIdioma('ingles')" alt="Cambiar a inglés" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                        <img class="españa" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" onclick="cambiarIdioma('espanol')" alt="Cambiar a español" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">                    </div>
                </div>
            </div>
        </header>

        <!-- Sección del artículo -->
        <section class="header-section">
            <div class="contenedor-imagenes">
                <div class="texto">
                    <h1><?php echo htmlspecialchars($article['Titulo']); ?></h1>
                    <p><?php echo nl2br(htmlspecialchars($article['Introduccion'])); ?></p>
                </div>
                <div class="imagenes">
                    <?php if ($article['imagenes']): ?>
                        <img src="<?php echo htmlspecialchars(explode(',', $article['imagenes'])[0]); ?>" alt="<?php echo htmlspecialchars($article['Titulo']); ?>" class="imagen-primera">
                    <?php else: ?>
                        <div class="w-full h-full bg-[#f5f5f5] flex items-center justify-center text-[#666]">Sin imagen</div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php if (!empty($article['imagenes'])): ?>
            <div class="carrusel-imagenes">
                <?php 
                $imagenes = explode(',', $article['imagenes']);
                foreach ($imagenes as $index => $imagen):
                    $active = $index == 0 ? 'imagen-activa' : 'imagen-inactiva';
                ?>
                    <img src="<?php echo htmlspecialchars($imagen); ?>" class="<?php echo $active; ?>" alt="Imagen del artículo">
                <?php endforeach; ?>
                <div class="carrusel-indicadores">
                    <?php foreach ($imagenes as $index => $imagen): ?>
                        <div class="carrusel-indicador <?php echo $index == 0 ? 'activa' : ''; ?>" data-index="<?php echo $index; ?>"></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="contenido-principal">
            <p><?php echo nl2br(htmlspecialchars($article['Contenido'])); ?></p>
        </div>

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

        <!-- ID del usuario -->
        <input type="hidden" id="userId" value="<?php echo $_SESSION['Usuario_ID']; ?>">

        <!-- Sección de interacción (likes, favoritos y comentarios) -->
        <section class="interaccion">
            <div class="likes-comentarios">
                <!-- Sección de Likes -->
                <div class="likes" 
                    data-post-id="<?php echo $article['ID_Articulo']; ?>"
                    data-total-likes="<?php echo isset($article['total_likes']) ? $article['total_likes'] : 0; ?>"
                    data-has-liked="<?php echo isset($article['usuario_ha_liked']) && $article['usuario_ha_liked'] ? 'true' : 'false'; ?>">
                    <button id="likeButton" class="like-button">
                        <svg viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        <span id="likeCount" class="like-count"><?php echo isset($article['total_likes']) ? $article['total_likes'] : '0'; ?></span>
                    </button>
                </div>
                <!-- Sección de Comentarios -->
                <div class="comentarios" data-post-id="<?php echo $article['ID_Articulo']; ?>">
                    <button type="button" class="comment-toggle" data-post-id="<?php echo $article['ID_Articulo']; ?>" onclick="window.handleComments(event)">
                        <svg viewBox="0 0 24 24">
                            <path fill="currentColor" d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/>
                        </svg>
                        <span id="commentCount"><?php echo isset($article['total_comentarios']) ? $article['total_comentarios'] : 0; ?></span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Sección de comentarios -->
        <div id="commentsSection" class="mt-8" style="display: none;">
            <div class="comments-header">
                <h2 class="comments-title">Comentarios</h2>
                <span class="comments-count" id="commentsCountDisplay"><?php echo isset($article['total_comentarios']) ? $article['total_comentarios'] . ' comentarios' : '0 comentarios'; ?></span>
            </div>

            <!-- Lista de comentarios -->
            <div id="commentsList" class="comments-list">
                <!-- Los comentarios se cargarán dinámicamente -->
            </div>

            <!-- Mensaje cuando no hay comentarios -->
            <div id="noComments" class="no-comments" style="display: none;">
                ¡Sé el primero en comentar!
            </div>

            <!-- Formulario de nuevo comentario -->
            <div class="nuevo-comentario">
                <form id="commentForm" class="comment-form">
                    <textarea id="commentText" placeholder="Escribe tu comentario..." required></textarea>
                    <button type="submit" class="submit-comment">Publicar</button>
                </form>
            </div>
        </div>
        <script>
            // Almacenar el ID del usuario en localStorage
            const userId = document.getElementById('userId').value;
            if (userId) {
                localStorage.setItem('userId', userId);
            }

            // Inicializar interacciones
            document.addEventListener('DOMContentLoaded', function() {
                window.initInteractions();
            });
        </script>

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

    <section class="logo">
        <div class="header_2">
            <img class="prodcons" src="/PRODCONS/PI2do/imagenes/prodcon/logoSinfondo.png" alt="Logo" />
            <div class="subtitulos">
                <a href="/PRODCONS/PI2do/pr/produccionr_usuario.php">PRODUCCIÓN RESPONSABLE</a>
                <a href="/PRODCONS/PI2do/cr/consumores_usuario.php">CONSUMO RESPONSABLE</a>
            </div>
        </div>
    </section>


    <?php include $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/footer/Visitante/footer.php'; ?>
    <script>
    // Inicializar traducción al cargar la página
    window.addEventListener('load', async function() {
        try {
            // Inicializar traducción
            const preferredLanguage = localStorage.getItem('preferredLanguage') || 'es';
            const bandera = document.getElementById('banderaIdioma');
            if (bandera) {
                bandera.src = preferredLanguage === 'en' ? "/PRODCONS/PI2do/imagenes/logos/ingles.png" : "/PRODCONS/PI2do/imagenes/logos/espanol.png";
                bandera.setAttribute('data-idioma', preferredLanguage);
            }
            translateContent(preferredLanguage);
        } catch (error) {
            console.error('Error al inicializar traducción:', error);
        }
    });
</script>

    <!-- Scripts -->
    <script src="/PRODCONS/PI2do/postWeb/interacciones.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userId = '<?php echo isset($_SESSION["Usuario_ID"]) ? $_SESSION["Usuario_ID"] : ""; ?>';
            if (userId) {
                // Actualizar el input hidden existente
                const userIdInput = document.getElementById('userId');
                if (userIdInput) {
                    userIdInput.value = userId;
                }
                
                // Inicializar interacciones después de asegurar que el userId está disponible
                if (typeof window.initInteractions === 'function') {
                    window.initInteractions();
                } else {
                    console.error('La función initInteractions no está definida');
                }
            } else {
                console.error('Usuario no autenticado');
            }
        });
    </script>
</body>
</html>
