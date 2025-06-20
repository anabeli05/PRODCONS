<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="footer/footer/footer.css">
    <link rel="stylesheet" href="articulos.css">
    <link rel="stylesheet" href="PI2do/Header visitantes/barra_principal.css">
    <link rel="stylesheet" href="PI2do/Carrusel/carrusel.css">

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
            max-width: 900px;
            margin: 40px auto 40px auto;
            padding: 0 10px;
            display: flex;
            justify-content: center;
            align-items: center;
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
<a href='/PRODCONS/PI2do/pr/produccionr.php'>PRODUCCIÓN RESPONSABLE</a>
        <a href='/PRODCONS/PI2do/cr/consumores.php'>CONSUMO RESPONSABLE</a>
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

    <!-- <script>
      const btnLupa = document.getElementById('btnLupa');
      const barraBusqueda = document.getElementById('barraBusqueda');

      btnLupa.addEventListener('click', () => {
        barraBusqueda.classList.toggle('activa');
        if (barraBusqueda.classList.contains('activa')) {
          barraBusqueda.focus();
        }
      });
    </script> -->

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
        <div class="logo">
            <img src='/PRODCONS/PI2do/imagenes/prodcon/logoSinfondo.png' alt="Logo PRODCONS">
        </div>
        <div class="principal">
            <a class="navlink" href='/PRODCONS/PI2do/empresas_responsables/empresasr.php'>EMPRESAS RESPONSABLES</a>
            <a href="/PRODCONS/PI2do/inicio_sesion/login.php" class="link-login">INICIAR SESIÓN</a>
            <div id="idiomaToggle">
                <img class="españa" id="banderaIdioma" src="./PI2do/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
            </div>
            <div id="idiomasOpciones">
                <img class="ingles" src="./PI2do/imagenes/logos/ingles.png" onclick="cambiarIdioma('ingles')" alt="Cambiar a inglés">
                <img class="españa" src="./PI2do/imagenes/logos/espanol.png" onclick="cambiarIdioma('espanol')" alt="Cambiar a español">
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

const btnIdioma = document.getElementById('btnIdioma');
if (btnIdioma) {
    btnIdioma.addEventListener('click', function() {
        const currentLang = localStorage.getItem('preferredLanguage') || 'es';
        const newLang = currentLang === 'es' ? 'en' : 'es';
        localStorage.setItem('preferredLanguage', newLang);
        translateContent(newLang);
        updateIdiomaButtonText(newLang);
    });
}

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
<a href='/PRODCONS/PI2do/pr/produccionr.php'>PRODUCCIÓN RESPONSABLE</a>
                <a href='/PRODCONS/PI2do/cr/consumores.php'>CONSUMO RESPONSABLE</a>

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

    <h3 class="apubli"> MIRA MAS DE NUESTRO CONTENIDO </h3>

    <section class="post-list">
        <?php
        require_once 'PI2do/Base de datos/conexion.php';
        
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

        $conexion = new Conexion();
        $conexion->abrir_conexion();
        $conn = $conexion->conexion;

        // Obtener los posts publicados desde la base de datos
        $stmt = $conn->prepare("SELECT a.*, u.Nombre as autor_nombre, 
                               GROUP_CONCAT(ia.Url_Imagen) as imagenes
                               FROM articulos a 
                               JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                               LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
                               WHERE a.Estado = 'Publicado' 
                               GROUP BY a.ID_Articulo
                               ORDER BY a.`Fecha de Creacion` DESC");

        if (!$stmt) {
            die("Error en la preparación de la consulta de artículos: " . $conn->error);
        }

        $stmt->execute();
        $publicaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        foreach ($publicaciones as $post): 
            // Check if images data is available before exploding
            $imagenes_string = $post['imagenes'] ?? '';
            $imagenes = !empty($imagenes_string) ? explode(',', $imagenes_string) : [];
            $imagen_principal = !empty($imagenes[0]) ? $imagenes[0] : '/PRODCONS/PI2do/imagenes/default-post.jpg';
        ?>
            <article class="post" data-post-id="<?php echo htmlspecialchars($post['ID_Articulo'] ?? ''); ?>">
                <div class="post-header">
                    <img src="<?php echo htmlspecialchars($imagen_principal ?? ''); ?>" alt="<?php echo htmlspecialchars($post['Titulo'] ?? ''); ?>" class="post-img">
                </div>
                <div class="post-body">
                    <h2><?php echo htmlspecialchars($post['Titulo'] ?? ''); ?></h2>
                    <p class="descripcion"><?php 
                        $contenido = htmlspecialchars($post['Contenido'] ?? '');
                        // Truncar contenido a aproximadamente 100 caracteres si es más largo
                        if (strlen($contenido) > 100) {
                            $contenido = substr($contenido, 0, 401) . '...';
                        }
                        echo $contenido;
                    ?></p>
                    <a href="/PRODCONS/PI2do/postWeb/ver-articulo.php?id=<?php echo htmlspecialchars($post['ID_Articulo'] ?? ''); ?>" class="post-link">Leer más...</a>
                    <span>Publicado el <?php 
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
                    <span> | Por   <?= htmlspecialchars($post['autor_nombre'] ?? '') ?></span>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
    
    <!-- Carrusel destacado -->
    <section class="carrusel-destacado">
        <?php 
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

        if (!empty($publicaciones_carousel)): ?>
            <div class="carousel-container">
                <div class="carousel">
                    <?php foreach ($publicaciones_carousel as $pub): 
                        $article_id = isset($pub['ID_Articulo']) ? (int)$pub['ID_Articulo'] : 0;
                        $imagen_principal_carousel = $pub['imagenes'] ?? '/PRODCONS/PI2do/imagenes/default-post.jpg';
                    ?>
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
                                <a href="/PRODCONS/PI2do/postWeb/ver-articulo.php?id=<?php echo htmlspecialchars($pub['ID_Articulo'] ?? ''); ?>" class="post-link">Leer más...</a>
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
    <script src="/PRODCONS/carousel.js"></script>

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
        // Script para activar/desactivar la barra de búsqueda
        const btnLupa = document.getElementById('btnLupa');
        const barraBusqueda = document.getElementById('barraBusqueda');

        if (btnLupa) {
            btnLupa.addEventListener('click', () => {
                if (barraBusqueda) {
                    barraBusqueda.classList.toggle('activa');
                    if (barraBusqueda.classList.contains('activa')) {
                        barraBusqueda.focus();
                    }
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
    </script>


    <?php include $_SERVER['DOCUMENT_ROOT'].'/PRODCONS/footer/footer/footer.php'; ?>
    <script src='/PRODCONS/Header visitantes/barra_principal.js'></script>

</body>
</html>


