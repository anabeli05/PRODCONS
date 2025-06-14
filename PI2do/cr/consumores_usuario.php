<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Consumo Responsable</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/PRODCONS/PI2do/header_post/header_post.css">
  
  <!-- Scripts de traducción -->
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
  <script src="/PRODCONS/translate.js"></script>

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

    .header-section {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 2rem;
    }

    .contenedor-imagenes {
        display: flex;
        gap: 2rem;
        align-items: flex-start;
        margin-top: 2rem;
    }

    .texto {
        flex: 1;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .imagenes {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .imagen-primera {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
        .contenedor-imagenes {
            flex-direction: column;
        }
    }
  </style>
</head>

<body class="bg-gray-50">
    <header>
        <div class="header-contenedor">
            <i class="flecha_left">
                <a href="/PRODCONS/PI2do/Dashboard_Usuario/inicio/usuario.php" title="Regresar a la página principal">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-6 h-6">
                        <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                    </svg>
                </a>
            </i>
            <div class="principal">
                <!-- Selector de bandera para cambio de idioma -->
                <div id="idiomaToggle" style="display: inline-block; margin-left: 15px;">
                    <img class="españa" id="banderaIdioma" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
                </div>
                <!-- Opciones de banderas desplegables -->
                <div id="idiomasOpciones" style="display: none;">
                    <img class="ingles" src="/PRODCONS/PI2do/imagenes/logos/ingles.png" onclick="cambiarIdioma('ingles')" alt="Cambiar a inglés" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                    <img class="españa" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" onclick="cambiarIdioma('espanol')" alt="Cambiar a español" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                </div>
            </div>
        </div>
    </header>

    <!-- Contenedor principal -->
    <div class="flex flex-col md:flex-row w-full mt-6 box-border">
        <!-- Cuadro verde -->
        <div style="background-color: rgb(109, 151, 109);" 
             class="w-full md:w-1/2 text-white p-8 rounded-md flex flex-col justify-center">
            <h2 class="text-3xl font-bold mb-6 leading-tight">EL CONSUMO RESPONSABLE</h2>
            <p class="text-lg leading-relaxed">
                El consumo responsable implica elegir productos y servicios que minimicen el impacto ambiental, fomenten la economía local y respeten los derechos de los trabajadores. Es una forma de tomar decisiones conscientes para reducir el desperdicio y promover un futuro más sostenible.
            </p>
            <p class="text-lg leading-relaxed mt-4">
                Cada decisión de compra que tomamos tiene un impacto en el medio ambiente y en la sociedad. Al elegir productos locales, duraderos y éticos, contribuimos a crear un mundo más justo y sostenible para las futuras generaciones.
            </p>
        </div>

        <!-- Imagen -->
        <div class="w-full md:w-1/2 flex items-center justify-center mt-6 md:mt-0">
            <img src="/PRODCONS/PI2do/imagenes/manoConsumo.png" alt="Consumo Responsable" class="max-w-full h-auto rounded-md shadow-lg" />
        </div>
    </div>

    <footer class="text-center text-sm text-gray-500 py-8">© 2025 PRODCONS</footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const savedLanguage = localStorage.getItem('preferredLanguage') || 'es';
            const bandera = document.getElementById('banderaIdioma');
            bandera.src = savedLanguage === 'en'
                ? '/PRODCONS/PI2do/imagenes/logos/ingles.png'
                : '/PRODCONS/PI2do/imagenes/logos/espanol.png';
            bandera.setAttribute('data-idioma', savedLanguage);
            translateContent(savedLanguage);
        });

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
</body>
</html>
