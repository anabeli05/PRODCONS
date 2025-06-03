<?php
session_start();
// Asegurarse de que el registro de errores esté activado si es necesario (verifica tu configuración de php.ini también)
ini_set('log_errors', '1');
// ini_set('error_log', '/path/to/your/php-error.log'); // <<-- Reemplaza con la ruta a tu archivo de log

require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion.php'; // Ruta absoluta a tu archivo de conexión

$article = null;
$error = null;

// Verificar si se proporcionó un ID de artículo en la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $article_id = $_GET['id'];

    // Conexión MySQLi
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    // Verificar si la conexión se estableció correctamente
    if ($conn) {
        // Obtener el artículo con el ID proporcionado
        $sql = "SELECT a.*, u.nombre as autor_nombre, GROUP_CONCAT(ia.Url_Imagen) as imagenes
                FROM articulos a 
                JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID
                LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
                WHERE a.ID_Articulo = ? AND a.Estado = 'publicado'
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
    <link rel="stylesheet" href="/PRODCONS/PI2do/Header visitantes/barra_principal.css">
    <link rel="stylesheet" href="/PRODCONS/footer/footer/footer.css">

    <!-- Google Cloud Translation API -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <!-- Script de traducción global -->
    <script src='/PRODCONS/translate.js'></script>
</head>
<body>

    <!-- Barra para regresar - Puedes ajustar la URL según necesites -->
    <section class="barra_left">
        <i class="flecha_left">
            <a href="javascript:history.back()" title="Regresar a la página anterior">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                </svg>
            </a>
        </i>
    </section>

    <!-- =====================================================================    CUADRO DE SELECCIÓN DE IDIOMA - NO MODIFICAR LA ESTRUCTURA    Puedes modificar el texto, pero mantén los IDs y la estructura        - El botón X permite cerrar/ocultar el selector de idioma    ===================================================================== -->
    <div class="language-toggle" id="language-toggle">
        <button class="close-button" id="close-language-toggle" onclick="document.getElementById('language-toggle').style.display='none'">✕</button>
        <p id="toggle-text">¿Cambiar idioma?</p>
        <div class="language-buttons">
            <button id="btn-es" onclick="cambiarIdioma('espanol')" class="active">Español</button>
            <button id="btn-en" onclick="cambiarIdioma('ingles')">English</button>
        </div>
    </div>

    <?php if ($article): ?>
        <!-- Header convertido en section - con contenido dinámico -->
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

        <main>
            <!-- Aquí podrías agregar más secciones del artículo si el contenido está estructurado -->
            <!-- Por ahora, usamos el mismo contenido que en el header para simplificar -->
            <section>
                <h2>Contenido</h2>
                 <p><?php echo nl2br(htmlspecialchars($article['Contenido'])); ?></p>
            </section>
        </main>

        <!-- Sección de autor y fecha -->
        <section class="autor-bibliografias">
             <div class="publicado">PUBLICADO EL <?php
                try {
                    $date = new DateTime($article['Fecha de Creacion']);
                    echo $date->format('d F Y');
                } catch (Exception $e) {
                    echo 'Fecha inválida';
                }
            ?> |
            POR <?php echo htmlspecialchars($article['autor_nombre']); ?></div>
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
    // Function to update button states based on current language    function updateLanguageButtons() {        const btnEs = document.getElementById('btn-es');        const btnEn = document.getElementById('btn-en');        const toggleText = document.getElementById('toggle-text');                // Get current language from localStorage or default to Spanish        const currentLang = localStorage.getItem('preferredLanguage') || 'es';                // Update active button        if (currentLang === 'en') {            btnEs.classList.remove('active');            btnEn.classList.add('active');            toggleText.innerText = 'Change language?';        } else {            btnEn.classList.remove('active');            btnEs.classList.add('active');            toggleText.innerText = '¿Cambiar idioma?';        }    }
    
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

<?php include $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/footer/Visitante/footer.php'; ?>

</body>
</html> 