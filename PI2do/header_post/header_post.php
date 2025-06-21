<?php
// Incluir el CSS del header
?>
<link href="/PRODCONS/PI2do/header_post/header_post.css" rel="stylesheet" />

<header>
    <div class="header-contenedor">
        <i class="flecha_left" onclick="window.history.back()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
        </i>

        <div class="principal">
            <a class="navlink" href="/PRODCONS/PI2do/empresas_responsables/empresasr.php">EMPRESAS RESPONSABLES</a>

            <!-- Selector visible -->
            <div id="idiomaToggle">
                <img class="españa" id="banderaIdioma" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()" />
            </div>

            <!-- Opciones ocultas desplegables -->
            <div id="idiomasOpciones" style="display: none;">
                <img class="ingles" src="/PRODCONS/PI2do/imagenes/logos/ingles.png" onclick="cambiarIdioma('ingles')" alt="Cambiar a inglés" />
                <img class="españa" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" onclick="cambiarIdioma('espanol')" alt="Cambiar a español" />
            </div>
        </div>
    </div>

    <!-- Second row of the fixed header -->
    <div class="header-second-row">
        <img src="/PRODCONS/PI2do/imagenes/logos/prodcons_logo.png" alt="PRODCONS Logo" class="logo-header">
        <div class="main-title">
            PRODUCCIÓN RESPONSABLE CONSUMO RESPONSABLE
        </div>
    </div>
</header>



<!-- Scripts necesarios para el header -->
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
<script src="/PRODCONS/translate.js"></script>
<script src="/PRODCONS/PI2do/header_post/header_post.js"></script>
