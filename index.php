<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS</title>
    <link rel="stylesheet" href="/PRODCONS/styles.css">
    <link rel="stylesheet" href="/PRODCONS/footer/footer/footer.css">
    <link rel="stylesheet" href="/PRODCONS/articulos.css">
    <link rel="stylesheet" href="/PRODCONS/PI2do/Header visitantes/barra_principal.css">

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
        <div class="principal">
<a class="navlink" href='/PRODCONS/PI2do/empresas_responsables/empresasr.php'>EMPRESAS RESPONSABLES</a>
            <a href="/PRODCONS/PI2do/inicio_sesion/login.php" class="link-login">INICIAR SESIÓN</a>
             <!-- =====================================================================
                SELECTOR DE BANDERA PARA CAMBIO DE IDIOMA - PERSONALIZABLE
                Estos elementos controlan la selección de idioma en la página principal
                ===================================================================== -->
                <!-- Bandera principal visible - Puedes cambiar la imagen por defecto aquí -->
                <div id="idiomaToggle">
                    <img class="españa" id="banderaIdioma" src="./PI2do/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
                </div>
                <!-- Opciones de banderas desplegables - Puedes cambiar las imágenes aquí -->
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

<!-- Carrusel destacado -->
<section class="carrusel-destacado">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/PRODCONS/PI2do/Carrusel/carrusel.php'; ?>
</section>
    

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
<a href='/PRODCONS/PI2do/postWeb/articulo1.php' class="post-link">Leer más...</a>
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
<a href='/PRODCONS/PI2do/postWeb/articulo2.php' class="post-link">Leer más...</a>
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
<a href='/PRODCONS/PI2do/postWeb/articulo3.php' class="post-link">Leer más...</a>
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
<a href='/PRODCONS/PI2do/postWeb/articulo4.php' class="post-link">Leer más...</a>
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
                    <p class="descripcion">Consumimos sin medida, sin pensar en el impacto. Es momento de tomar decisiones responsables y reducir nuestra huella ecológica. Cada elección cuenta. ¿Qué harás hoy por un futuro más verde? </p>
<a href='/PRODCONS/PI2do/postWeb/articulo5.php' class="post-link">Leer más...</a>
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
<a href='/PRODCONS/PI2do/postWeb/articulo6.php' class="post-link">Leer más...</a>
                    <span>Publicado el 21 de Febrero del 2025 </span>
                    <span>| Isabela Monserrat Vidrio Camarena</span>

                </div>
            </article>
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
        // Script para activar/desactivar la barra de búsqueda
        const btnLupa = document.getElementById('btnLupa');
        const barraBusqueda = document.getElementById('barraBusqueda');

        btnLupa.addEventListener('click', () => {
            barraBusqueda.classList.toggle('activa');
            if (barraBusqueda.classList.contains('activa')) {
                barraBusqueda.focus();
            }
        });

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


