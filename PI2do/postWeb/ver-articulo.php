<?php
session_start();
ini_set('log_errors', '1');
setlocale(LC_TIME, 'es_ES', 'es_ES.utf8', 'es');
require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion.php';

$article = null;
$error = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $article_id = $_GET['id'];
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    if ($conn) {
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

    <!-- Google Cloud Translation API -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <!-- Script de traducción global -->
    <script src='/PRODCONS/translate.js'></script>

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
    </style>
</head>
<body>
    <header>
        <div class="header-contenedor">
            <i class="flecha_left">
                <a href="/PRODCONS/index.php" title="Regresar a la página principal">
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
                    <img class="españa" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" onclick="cambiarIdioma('espanol')" alt="Cambiar a español" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                </div>
            </div>
        </div>
    </header>

    <?php if ($article): ?>
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
        <div class="contenido-principal">
            <p><?php echo nl2br(htmlspecialchars($article['Contenido'])); ?></p>
        </div>

    <script>
        // Funciones para el cambio de idioma (copiadas de index.php)
        function cambiarIdioma(idioma) {
            const banderaPrincipal = document.getElementById('banderaIdioma');
            const banderaIngles = document.querySelector('.ingles');
            const banderaEspana = document.querySelector('.españa');
            
            if (banderaPrincipal) {
                banderaPrincipal.src = idioma === 'ingles' 
                    ? "/PRODCONS/PI2do/imagenes/logos/ingles.png" 
                    : "/PRODCONS/PI2do/imagenes/logos/espanol.png";
            }
            
            if (banderaIngles && banderaEspana) {
                banderaIngles.style.display = idioma === 'espanol' ? 'none' : 'block';
                banderaEspana.style.display = idioma === 'espanol' ? 'block' : 'none';
            }
            
            currentLanguage = idioma === 'ingles' ? 'en' : 'es';
            translateContent(currentLanguage);
            
            const opciones = document.getElementById('idiomasOpciones');
            if (opciones) {
                opciones.style.display = 'none';
            }
        }

        function alternarIdioma() {
            const bandera = document.getElementById('banderaIdioma');
            let idiomaActual = bandera.getAttribute('data-idioma') || 'es';
            let nuevoIdioma, nuevaBandera;

            if (idiomaActual === 'es') {
                nuevoIdioma = 'en';
                nuevaBandera = '/PRODCONS/PI2do/imagenes/logos/ingles.png';
            } else {
                nuevoIdioma = 'es';
                nuevaBandera = '/PRODCONS/PI2do/imagenes/logos/espanol.png';
            }

            bandera.src = nuevaBandera;
            bandera.setAttribute('data-idioma', nuevoIdioma);

            translateContent(nuevoIdioma);
            localStorage.setItem('preferredLanguage', nuevoIdioma);
        }
    </script>

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

        <!-- Sección de bibliografías -->
        <section class="autor-bibliografias">
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

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/footer/Visitante/footer.php'; ?>
</body>
</html>
