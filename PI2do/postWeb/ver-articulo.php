<?php
session_start();
// Asegurarse de que el registro de errores esté activado si es necesario (verifica tu configuración de php.ini también)
ini_set('log_errors', '1');
// ini_set('error_log', '/path/to/your/php-error.log'); // <<-- Reemplaza con la ruta a tu archivo de log

// Establecer la localización a español para mostrar nombres de meses correctamente
setlocale(LC_TIME, 'es_ES', 'es_ES.utf8', 'es');

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
    <link rel="stylesheet" href="/PRODCONS/PI2do/postWeb/code.css">
     <link rel="stylesheet" href="/PRODCONS/PI2do/header_post/header_post.css">
    <link rel="stylesheet" href="/PRODCONS/PI2do/footer/footer.css">
    
    <!-- =====================================================================
    SCRIPTS DE TRADUCCIÓN - REQUERIDOS
    No eliminar estas líneas, son necesarias para la funcionalidad de traducción
    ===================================================================== -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>
    
    <!-- =====================================================================
    ESTILOS PARA EL CUADRO DE IDIOMA - PERSONALIZABLE
    Puedes modificar los estilos para cambiar la apariencia del cuadro de idioma
    
    - El botón X permite cerrar el selector cuando no se necesita
    ===================================================================== -->
        <style>        .language-toggle {            position: fixed;          /* Posición fija en la pantalla */            top: 20px;                /* Distancia desde la parte superior - PERSONALIZABLE */            right: 20px;              /* Distancia desde la derecha - PERSONALIZABLE */            background-color: #fff;   /* Color de fondo - PERSONALIZABLE */            border: 1px solid #ddd;   /* Borde - PERSONALIZABLE */            border-radius: 8px;       /* Bordes redondeados - PERSONALIZABLE */            padding: 10px 15px;       /* Espaciado interno - PERSONALIZABLE */            box-shadow: 0 2px 10px rgba(0,0,0,0.1); /* Sombra - PERSONALIZABLE */            z-index: 1000;            /* Capa de visualización - MANTENER ALTO */            font-family: Arial, sans-serif; /* Fuente - PERSONALIZABLE */        }                .language-toggle p {            margin: 0 0 8px 0;            font-size: 14px;          /* Tamaño de texto - PERSONALIZABLE */            font-weight: bold;        /* Negrita - PERSONALIZABLE */        }                .language-toggle .language-buttons {            display: flex;            gap: 10px;                /* Espacio entre botones - PERSONALIZABLE */        }                .language-toggle button {            padding: 5px 10px;        /* Espaciado interno de botones - PERSONALIZABLE */            border: none;             /* Sin borde - PERSONALIZABLE */            border-radius: 4px;       /* Bordes redondeados - PERSONALIZABLE */            background-color: #f0f0f0; /* Color de fondo - PERSONALIZABLE */            cursor: pointer;            transition: background-color 0.2s;        }                .language-toggle button:hover {            background-color: #e0e0e0; /* Color al pasar el mouse - PERSONALIZABLE */        }                .language-toggle button.active {            background-color: #4CAF50; /* Color del botón activo - PERSONALIZABLE */            color: white;             /* Color de texto del botón activo - PERSONALIZABLE */        }                /* Estilos para el botón de cerrar (X) */        .close-button {            position: absolute;       /* Posición absoluta dentro del contenedor */            top: 0;                   /* Distancia desde arriba - PERSONALIZABLE */            right: 0;                 /* Distancia desde la derecha - PERSONALIZABLE */            font-size: 8px;           /* Tamaño de la X más pequeño - PERSONALIZABLE */            background-color: transparent; /* Fondo completamente transparente */            border: none;             /* Sin borde */                        cursor: pointer;          /* Cursor tipo mano al pasar por encima */                        color: #bbb;              /* Color más claro para la X */                        padding: 6px 8px;         /* Padding más grande para aumentar el área de clic */            line-height: 1;           /* Altura de línea ajustada */            opacity: 0.7;             /* Ligeramente transparente */            z-index: 1001;            /* Se asegura que esté por encima para poder hacer clic */        }                .close-button:hover {            color: #999;              /* Color al pasar el ratón más sutil - PERSONALIZABLE */            opacity: 0.9;             /* Aumenta ligeramente la opacidad al pasar el ratón */        }
    </style>
</head>
<body>
    <!-- Barra para regresar y header principal -->
    <section class="barra_left">
        <i class="flecha_left">
<a href="/PRODCONS/" title="Regresar a la página principal">
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

    <?php else: ?>
        <div class="container mx-auto mt-10 p-5 bg-white rounded-md shadow-md">
            <h1 class="text-2xl font-bold mb-4">Error</h1>
            <p><?php echo $error ?? 'Artículo no encontrado.'; ?></p>
        </div>
    <?php endif; ?>

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

<?php include $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/footer/Visitante/footer.php'; ?>
<script src='/PRODCONS/PI2do/header_post/header_post.js'></script>

</body>
</html> 