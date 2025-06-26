<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol correcto
if (!isset($_SESSION['Usuario_ID']) || !isset($_SESSION['Rol']) || $_SESSION['Rol'] != 'Usuario') {
    // Redirigir al login con un mensaje de error
    $_SESSION['login_error'] = "Tu cuenta no está registrada, por favor regístrate";
    header("Location: /PRODCONS/PI2do/inicio_sesion/login.php");
    exit();
}

// Incluir el archivo de verificación

include '../../Base de datos/conexion.php'; // Incluir el archivo de conexión

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
// Crear instancia de Conexion y obtener la conexión
$conexion = new Conexion();
$conexion->abrir_conexion(); // Llamar a abrir_conexion() para establecer la conexión
$conn = $conexion->conexion;

// Verificar si la conexión se estableció correctamente
if (!$conn) {
    die("Error de conexión a la base de datos.");
}

// Obtener los posts publicados desde la base de datos - Updated query to match index.php
$stmt = $conn->prepare("SELECT a.*, u.Nombre as autor_nombre, 
                               GROUP_CONCAT(ia.Url_Imagen) as imagenes
                       FROM articulos a 
                       JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                               LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
                               WHERE a.Estado = 'Publicado' 
                               GROUP BY a.ID_Articulo
                               ORDER BY a.`Fecha de Publicacion` DESC");

if (!$stmt) {
    die("Error en la preparación de la consulta de artículos: " . $conn->error);
}

$stmt->execute();
$publicaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Consulta para el carrusel (solo 3 publicaciones más recientes)
$stmt_carousel = $conn->prepare("SELECT a.*, u.Nombre as autor_nombre, 
                               GROUP_CONCAT(ia.Url_Imagen) as imagenes
                               FROM articulos a 
                               JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                               LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
                               WHERE a.Estado = 'Publicado' 
                               GROUP BY a.ID_Articulo
                               ORDER BY a.`Fecha de Publicacion` DESC LIMIT 3");

if (!$stmt_carousel) {
    die("Error en la preparación de la consulta del carrusel: " . $conn->error);
}

$stmt_carousel->execute();
$publicaciones_carousel = $stmt_carousel->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_carousel->close();

// Consulta para el carrusel (todas las publicaciones publicadas)
$stmt_carousel_all = $conn->prepare("SELECT a.*, u.Nombre as autor_nombre, 
                           GROUP_CONCAT(ia.Url_Imagen) as imagenes
                           FROM articulos a 
                           JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                           LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
                           WHERE a.Estado = 'Publicado' 
                           GROUP BY a.ID_Articulo
                           ORDER BY a.`Fecha de Publicacion` DESC");

if (!$stmt_carousel_all) {
    die("Error en la preparación de la consulta del carrusel: " . $conn->error);
}

$stmt_carousel_all->execute();
$publicaciones_carousel_all = $stmt_carousel_all->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_carousel_all->close();

// Cerrar la conexión al finalizar el script
$conexion->cerrar_conexion();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS</title>
    <link rel="stylesheet" href="/PRODCONS/styles.css">
    <link rel="stylesheet" href="/PRODCONS/footer/footer/footer.css">
    <link rel="stylesheet" href="/PRODCONS/articulos.css">
    <link rel="stylesheet" href="/PRODCONS/PI2do/Header_visitantes/barra_principal.css">

    <!-- Google Cloud Translation API -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <!-- Script de traducción global -->
    <script src='translate.js'></script>
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
        
        .carrusel-destacado {
            width: 100%;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            position: relative;
        }

        .carousel-container {
            width: 100%;
        }

        .carousel {
            display: flex;
            gap: 20px;
            transition: transform 0.5s ease-in-out;
            flex-wrap: nowrap;
        }

        .carousel-item {
            flex: 0 0 100%;
            min-width: 100%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            margin: 0;
        }

        .post-header {
            margin-bottom: 20px;
        }

        .post-header img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            object-fit: cover;
        }

        .post-body h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #040404;
            font-family: Georgia, serif;
        }

        .descripcion {
            font-size: 18px;
            line-height: 1.6;
            color: #333333;
            font-family: Georgia, serif;
        }

        .post-footer {
            display: flex;
            align-items: center;
            color: #333333;
            font-size: 14px;
            font-family: Georgia, serif;
            margin-top: 20px;
        }

        .post-footer span {
            margin-right: 10px;
            font-weight: bold;
        }

        .prev, .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            padding: 15px;
            cursor: pointer;
            font-size: 24px;
            transition: background 0.3s;
        }

        .prev:hover, .next:hover {
            background: rgba(0,0,0,0.7);
        }

        .prev {
            left: 20px;
        }

        .next {
            right: 20px;
        }

        .no-posts {
            text-align: center;
            padding: 40px;
            color: #333333;
            font-family: Georgia, serif;
        }
        
        /* Estilos para los botones de interacción */
        .post-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .interaction-buttons {
            display: flex;
            gap: 10px;
        }

        .like-button-small,
        .comment-toggle-small {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border: none;
            background: none;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #666;
        }

        .like-button-small:hover,
        .comment-toggle-small:hover {
            color: #000;
            transform: scale(1.1);
        }

        .post-link {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        .post-link:hover {
            background-color: #45a049;
        }

        /* Estilos para la sección de comentarios */
        .comments-section {
            margin-top: 15px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .add-comment {
            margin-top: 10px;
        }
        
        .add-comment textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
            resize: vertical;
            min-height: 60px;
        }
        
        .add-comment button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }
        
        .add-comment button:hover {
            background-color: #45a049;
        }

        .existing-comments {
            margin-bottom: 15px;
        }
    </style>

</head>

<body>

    <!-- Botón hamburguesa (solo en móviles) -->
    <div class="hamburger-icon" onclick="toggleMenu()">☰</div>

    <!-- Menú hamburguesa lateral -->
        <nav class="mobile-menu" id="mobileMenu">
      <div class="close-icon" onclick="toggleMenu()">✖</div>

      <!-- Logo dentro del menú -->
      <div class="mobile-logo">
        <img src='/PRODCONS/PI2do/imagenes/prodcon/logoSinfondo.png' alt="Logo" />
            </div>

      <!-- Opciones del menú -->
      <ul>
<a href='/PRODCONS/PI2do/pr/produccionr_usuario.php'>PRODUCCIÓN RESPONSABLE</a>
<a href='/PRODCONS/PI2do/cr/consumores_usuario.php'>CONSUMO RESPONSABLE</a>
         <li>
          <form>
            <button id="btnLupa" class="lupa" aria-label="Abrir buscador" type="button">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"
                   stroke-width="1.5">
                <circle cx="10" cy="10" r="7"></circle>
                <line x1="21" y1="21" x2="15" y2="15"></line>
                                </svg>
                            </button>
            <div class="buscador-container">
              <input type="text" id="barraBusqueda" class="barra-busqueda" placeholder="Buscar..." />
            </div>
                        </form>
                    </li>
                </ul>
        </nav>

    <script>
        function toggleMenu() {
            const menu = document.getElementById("mobileMenu");
            const burger = document.querySelector(".hamburger-icon");
            
            menu.classList.toggle("active");

            // Ocultar hamburguesa si el menú está activo
            if (menu.classList.contains("active")) {
                burger.style.display = "none";
            } else {
                burger.style.display = "block";
            }
        }
    </script>

    <header>
        <div class="header-contenedor">
            <div class="principal">
<a class="navlink" href='/PRODCONS/PI2do/empresas_responsables/empresasr.php'>EMPRESAS RESPONSABLES</a>
            <a href="/PRODCONS/PI2do/inicio_sesion/logout.php" class="link-login">CERRAR SESIÓN</a>
             <!-- =====================================================================
                SELECTOR DE BANDERA PARA CAMBIO DE IDIOMA - PERSONALIZABLE
                Estos elementos controlan la selección de idioma en la página principal
                ===================================================================== -->
                <!-- Bandera principal visible - Puedes cambiar la imagen por defecto aquí -->
                <div id="idiomaToggle">
<img class="españa" id="banderaIdioma" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
                </div>
                <!-- Opciones de banderas desplegables - Puedes cambiar las imágenes aquí -->
                <div id="idiomasOpciones">
<img class="ingles" src="/PRODCONS/PI2do/imagenes/logos/ingles.png" onclick="cambiarIdioma('ingles')" alt="Cambiar a inglés">
<img class="españa" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" onclick="cambiarIdioma('espanol')" alt="Cambiar a español">
                </div>
            </div>
        </div>
    </header>

<style>
.link-login {
    color: #000000; /* Negro */
    font-weight: bold;
    font-family: 'Arial', sans-serif;
    text-decoration: none;
    cursor: pointer;
    text-transform: uppercase;
    margin-left: 15px;
    transition: color 0.3s ease;
}

.link-login:hover {
    color: #000000; /* Mantener negro al hacer hover */
    text-decoration: none;
}

.idioma-text {
    color: #000000; /* Negro */
    font-weight: bold;
    font-family: 'Arial', sans-serif;
    font-size: 16.1px;
                cursor: pointer;
    text-transform: uppercase;
    background: none;
    border: none;
    padding: 0;
    margin-left: 15px;
    transition: color 0.3s ease;
}

.idioma-text:hover {
    color: #000000; /* Mantener negro al hacer hover */
}

.idioma-text:focus {
    outline: none;
    box-shadow: none;
        }
    </style>

    <script>
function updateIdiomaButtonText(lang) {
    const btnIdioma = document.getElementById('btnIdioma');
    if (!btnIdioma) return;
    if (lang === 'es') {
        btnIdioma.textContent = 'IDIOMA';
    } else if (lang === 'en') {
        btnIdioma.textContent = 'LANGUAGE';
    }
}

document.getElementById('btnIdioma').addEventListener('click', function() {
    const currentLang = localStorage.getItem('preferredLanguage') || 'es';
    const newLang = currentLang === 'es' ? 'en' : 'es';
    localStorage.setItem('preferredLanguage', newLang);
    translateContent(newLang);
    updateIdiomaButtonText(newLang);
});

// Update button text on page load based on saved language
document.addEventListener('DOMContentLoaded', function() {
    const savedLanguage = localStorage.getItem('preferredLanguage') || 'es';
    updateIdiomaButtonText(savedLanguage);
});
</script>

    <section class="logo">
        <div class="header_2">
            <img class="prodcons" src='/PRODCONS/PI2do/imagenes/prodcon/logoSinfondo.png' alt="Logo">

            <div class="subtitulos">
<a href='/PRODCONS/PI2do/pr/produccionr_usuario.php'>PRODUCCIÓN RESPONSABLE</a>
<a href='/PRODCONS/PI2do/cr/consumores_usuario.php'>CONSUMO RESPONSABLE</a>

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

    <script>
        
        // =====================================================================
        // FUNCIONES DE CONTROL DE IDIOMA - MODIFICAR CON PRECAUCIÓN
        // =====================================================================
        
        /**
         * Función para alternar entre idiomas al hacer clic en la bandera principal
         * Esta función cambia la imagen de la bandera y realiza la traducción
         * MODIFICAR SOLO si necesitas cambiar la apariencia o comportamiento
         */
        function alternarIdioma() {
            const bandera = document.getElementById('banderaIdioma');
            const idiomaActual = bandera.src.includes('ingles.png') ? 'ingles' : 'espanol';
            const nuevoIdioma = idiomaActual === 'ingles' ? 'espanol' : 'ingles';
            
            // Cambiar la imagen de la bandera - Puedes modificar las rutas si cambias las imágenes
            bandera.src = nuevoIdioma === 'ingles' 
                ? '/PRODCONS/PI2do/imagenes/logos/ingles.png' // Ruta a la imagen de la bandera inglesa
                : '/PRODCONS/PI2do/imagenes/logos/espanol.png'; // Ruta a la imagen de la bandera española
            
            // Realizar la traducción - NO MODIFICAR esta línea
            translateContent(nuevoIdioma === 'ingles' ? 'en' : 'es');
            
            // Guardar la preferencia en localStorage - NO MODIFICAR esta línea
            localStorage.setItem('preferredLanguage', nuevoIdioma === 'ingles' ? 'en' : 'es');
        }

        // Cargar el idioma guardado al iniciar la página - NO MODIFICAR
        document.addEventListener('DOMContentLoaded', function() {
            const savedLanguage = localStorage.getItem('preferredLanguage');
            if (savedLanguage) {
            const bandera = document.getElementById('banderaIdioma');
            bandera.src = savedLanguage === 'en' 
                    ? '/PRODCONS/PI2do/imagenes/logos/ingles.png' 
                    : '/PRODCONS/PI2do/imagenes/logos/espanol.png';
            translateContent(savedLanguage);
            }
        });
</script>

    <section class="logo">
        <div class="header_2">
            <img class="prodcons" src='/PRODCONS/PI2do/imagenes/prodcon/logoSinfondo.png' alt="Logo">

            <div class="subtitulos">
<a href='/PRODCONS/PI2do/pr/produccionr_usuario.php'>PRODUCCIÓN RESPONSABLE</a>
<a href='/PRODCONS/PI2do/cr/consumores_usuario.php'>CONSUMO RESPONSABLE</a>

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

    <script>
        
        // =====================================================================
        // FUNCIONES DE CONTROL DE IDIOMA - MODIFICAR CON PRECAUCIÓN
        // =====================================================================
        
        /**
         * Función para alternar entre idiomas al hacer clic en la bandera principal
         * Esta función cambia la imagen de la bandera y realiza la traducción
         * MODIFICAR SOLO si necesitas cambiar la apariencia o comportamiento
         */
        function alternarIdioma() {
            const bandera = document.getElementById('banderaIdioma');
            const idiomaActual = bandera.src.includes('ingles.png') ? 'ingles' : 'espanol';
            const nuevoIdioma = idiomaActual === 'ingles' ? 'espanol' : 'ingles';
            
            // Cambiar la imagen de la bandera - Puedes modificar las rutas si cambias las imágenes
            bandera.src = nuevoIdioma === 'ingles' 
                ? '/PRODCONS/PI2do/imagenes/logos/ingles.png' // Ruta a la imagen de la bandera inglesa
                : '/PRODCONS/PI2do/imagenes/logos/espanol.png'; // Ruta a la imagen de la bandera española
            
            // Realizar la traducción - NO MODIFICAR esta línea
            translateContent(nuevoIdioma === 'ingles' ? 'en' : 'es');
            
            // Guardar la preferencia en localStorage - NO MODIFICAR esta línea
            localStorage.setItem('preferredLanguage', nuevoIdioma === 'ingles' ? 'en' : 'es');
        }
        
        // Cargar el idioma guardado al iniciar la página - NO MODIFICAR
        document.addEventListener('DOMContentLoaded', function() {
            const savedLanguage = localStorage.getItem('preferredLanguage');
            if (savedLanguage) {
                const bandera = document.getElementById('banderaIdioma');
                bandera.src = savedLanguage === 'en' 
                    ? '/PRODCONS/PI2do/imagenes/logos/ingles.png' 
                    : '/PRODCONS/PI2do/imagenes/logos/espanol.png';
            translateContent(savedLanguage);
            }
        });
    </script>

    <!-- Contenido principal -->
    <main class="main-content">
    <div class="sobrecont">
        <div class="cuadro-info">
            <h2>BLOG</h2>
            <p>Somos una organización dedicada a cuidar del medio ambiente, aplicándolo en nuestra vida diaria y
                promoviendo a los demás a hacerlo para el bienestar de todos.</p>
        </div>
        <img class="imagen-principal" src="/PRODCONS/PI2do/imagenes/tractor.png" alt="Imagen Principal">
    </div>

<!-- Carrusel destacado -->
<section class="carrusel-destacado">
    <?php 
    // Usar la consulta que ya obtuvo todas las publicaciones
    if (!empty($publicaciones_carousel_all)): ?>
        <div class="carousel-container">
            <div class="carousel">
                <?php foreach ($publicaciones_carousel_all as $pub): 
                    $article_id = isset($pub['ID_Articulo']) ? (int)$pub['ID_Articulo'] : 0;
                    $imagenes = explode(',', $pub['imagenes'] ?? '');
                    $imagen_principal = !empty($imagenes) ? $imagenes[0] : '/PRODCONS/PI2do/imagenes/default-post.jpg';
                ?>
                    <article class="carousel-item post" data-post-id="<?php echo htmlspecialchars($pub['ID_Articulo'] ?? ''); ?>">
                        <div class="post-header">
                            <img src="<?php echo htmlspecialchars($imagen_principal); ?>" 
                                 alt="<?php echo htmlspecialchars($pub['Titulo'] ?? ''); ?>" 
                                 class="post-img">
                        </div>
                        <div class="post-body">
                            <h2><?php echo htmlspecialchars($pub['Titulo'] ?? ''); ?></h2>
                            <p class="descripcion">
                                <?php 
                                $contenido = htmlspecialchars($pub['Contenido'] ?? '');
                                if (strlen($contenido) > 100) {
                                    $contenido = substr($contenido, 0, 401) . '...';
                                }
                                echo $contenido;
                                ?>
                            </p>
                            <a href="/PRODCONS/PI2do/postWeb/ver-articulo-usuario.php?ID_Articulo=<?php echo htmlspecialchars($pub['ID_Articulo'] ?? ''); ?>" class="post-link">Leer más...</a>
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
                            <span> | Por <?php echo htmlspecialchars($pub['autor_nombre'] ?? ''); ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <button class="prev" aria-label="Publicación anterior">‹</button>
            <button class="next" aria-label="Publicación siguiente">›</button>
        </div>
    <?php else: ?>
        <div class="no-posts">
            <p>No hay publicaciones disponibles en este momento. ¡Vuelve pronto!</p>
        </div>
    <?php endif; ?>
</section>                    </div>
</section>

    <h3 class="apubli"> MIRA MAS DE NUESTRO CONTENIDO </h3>

    <section class="post-list">
        <div class="content">
            <?php foreach ($publicaciones as $post): 
                // Asegurar que el ID del artículo sea numérico
                $article_id = isset($post['ID_Articulo']) ? (int)$post['ID_Articulo'] : 0;
                
                // Check if images data is available before exploding
                $imagenes_string = $post['imagenes'] ?? '';
                $imagenes = !empty($imagenes_string) ? explode(',', $imagenes_string) : [];
                $imagen_principal = !empty($imagenes[0]) ? $imagenes[0] : '/PRODCONS/PI2do/imagenes/default-post.jpg';
                
                // Asegurar que el ID se use en el botón de like y comentarios
                $like_id = $article_id;
                $comment_id = $article_id;
            ?>
                <article class="post" data-post-id="<?php echo $article_id; ?>">
                <div class="post-header">
                        <img src="<?php echo htmlspecialchars($imagen_principal); ?>" alt="<?php echo htmlspecialchars($post['Titulo'] ?? ''); ?>" class="post-img">
                </div>
                <div class="post-body">
                        <h2><?php echo htmlspecialchars($post['Titulo'] ?? ''); ?></h2>
                        <p class="descripcion"><?php 
                            $contenido = htmlspecialchars($post['Contenido'] ?? '');
                            // Truncar contenido a aproximadamente 401 caracteres para coincidir con el carrusel
                            if (strlen($contenido) > 401) {
                                $contenido = substr($contenido, 0, 401) . '...';
                            }
                            echo $contenido;
                        ?></p>
                        
                    <div class="post-footer">
                            <div class="post-actions">
                                <a href="/PRODCONS/PI2do/postWeb/ver-articulo-usuario.php?ID_Articulo=<?php echo $article_id; ?>" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                                    <button class="like-button-small" data-post-id="<?php echo $article_id; ?>" title="Me gusta">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                        <span class="likes-count">0</span>
                            </button>
                                    <button class="comment-toggle-small" data-post-id="<?php echo $article_id; ?>" title="Comentarios">
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
                                <!-- Los comentarios se mostrarán aquí via AJAX -->
                        </div>
                        <div class="add-comment">
                                <textarea placeholder="Escribe tu comentario..."></textarea>
                                <button>Publicar</button>
                        </div>
                    </div>

                        <span style="font-size: 12px; line-height: 1.2;">Publicado el <?php 
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
                        <span>| Por <?php echo htmlspecialchars($post['autor_nombre'] ?? ''); ?></span>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>
    </main>

    <!-- Banner de Cookies -->
    <div class="cookie-banner" id="cookieBanner">
        <div class="cookie-content">
            <p>Utilizamos cookies para mejorar tu experiencia en nuestro sitio web. Al hacer clic en "Aceptar todas", aceptas el uso de todas las cookies. Sin embargo, puedes visitar "Configuración de cookies" para proporcionar un consentimiento controlado.</p>
            <div class="cookie-buttons">
                <button id="rejectCookies" class="cookie-btn reject-btn">Rechazar</button>
                <button id="acceptCookies" class="cookie-btn accept-btn">Aceptar</button>
                </div>
                </div>
            </div>

<script>
        // Función para alternar el idioma
        function alternarIdioma() {
            const idiomasOpciones = document.getElementById('idiomasOpciones');
            idiomasOpciones.style.display = idiomasOpciones.style.display === 'block' ? 'none' : 'block';
        }

        // Función para cambiar el idioma
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

        // Código para el carrusel
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.querySelector('.carousel');
            const prev = document.querySelector('.prev');
            const next = document.querySelector('.next');
            let currentSlide = 0;

            function slideTo(index) {
                if (index < 0) {
                    currentSlide = carousel.children.length - 1;
                } else if (index >= carousel.children.length) {
                    currentSlide = 0;
                } else {
                    currentSlide = index;
                }
                carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
            }

            prev.addEventListener('click', () => slideTo(currentSlide - 1));
            next.addEventListener('click', () => slideTo(currentSlide + 1));

            // Cambiar automáticamente cada 5 segundos
            setInterval(() => slideTo(currentSlide + 1), 5000);
        });

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
    <script src="/PRODCONS/carousel.js"></script>
    <script src='/PRODCONS/Header_visitantes/barra_principal.js'></script>

</body>
</html>
