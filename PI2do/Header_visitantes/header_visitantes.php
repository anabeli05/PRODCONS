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