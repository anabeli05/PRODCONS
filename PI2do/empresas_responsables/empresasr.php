<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Empresas Responsables</title>
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
  </style>
</head>

<body class="bg-gray-50">
    <header>
        <div class="header-contenedor">
        <i class="flecha_left" title="Regresar a la página anterior">
            <button onclick="window.history.back()" class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-6 h-6">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
              </svg>
            </button>
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

  <!-- Introducción -->
  <section class="px-4 py-8 max-w-4xl mx-auto">
    <h1 class="text-5xl font-bold mb-6">Empresas Responsables</h1>
    <p class="mb-6 text-lg">En México, varias empresas han adoptado prácticas alineadas con el ODS 12, ya sea a través de la reducción de residuos, el uso eficiente de recursos, la implementación de energías renovables o la promoción de la economía circular.</p>
    <p class="text-lg">A continuación, se mencionan algunas empresas mexicanas y multinacionales con presencia en México que han demostrado compromiso con este objetivo:</p>
  </section>

    <!-- Empresas -->
    <section class="px-4 py-8 grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 max-w-7xl mx-auto">
      <!-- Cemex -->
      <div class="border rounded-xl shadow p-4">
        <h2 class="text-2xl font-semibold text-green-700 mb-3">Cemex</h2>
        <p class="text-base font-semibold">Sector constructor</p>
        <p class="text-base mb-4">Programas de economía circular, uso de residuos industriales, eficiencia energética y reducción de emisiones de carbono.</p>
        <img src="/PRODCONS/PI2do/imagenes/empresas/cemex.png" alt="Cemex" class="w-full h-40 object-contain" />
      </div>

    <!-- Bimbo -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">Bimbo</h2>
      <p class="text-base font-semibold">Sector alimenticio</p>
      <p class="text-base mb-4">Energía renovable, reducción de plásticos, programas de reciclaje.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/bimbo.png" alt="Bimbo" class="w-full h-40 object-contain" />
    </div>

    <!-- Coca-Cola FEMSA -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">Coca-Cola FEMSA</h2>
      <p class="text-base font-semibold">Sector bebidas</p>
      <p class="text-base mb-4">Reducción de agua, reciclaje, economía circular.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/coca-colaFemsa.png" alt="FEMSA" class="w-full h-40 object-contain" />
    </div>

    <!-- Grupo Modelo -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">Grupo Modelo</h2>
      <p class="text-base font-semibold">Sector bebidas</p>
      <p class="text-base mb-4">Huella de carbono, uso eficiente del agua, envases reciclables.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/GrupoModelo.png" alt="Grupo Modelo" class="w-full h-40 object-contain" />
    </div>

    <!-- Alsea -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">Alsea</h2>
      <p class="text-base font-semibold">Sector restaurantes y entretenimiento</p>
      <p class="text-base mb-4">Reducción de desperdicio, eliminación de plásticos de un solo uso.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Alsea.png" alt="Alsea" class="w-full h-40 object-contain" />
    </div>

    <!-- Grupo Herdez -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">Grupo Herdez</h2>
      <p class="text-base font-semibold">Sector alimenticio</p>
      <p class="text-base mb-4">Producción sostenible, reducción de residuos.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Herdez.png" alt="Grupo Herdez" class="w-full h-40 object-contain" />
    </div>

    <!-- Unilever -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">Unilever</h2>
      <p class="text-base font-semibold">Sector bienes de consumo</p>
      <p class="text-base mb-4">Reducción de plásticos, reciclaje, consumo responsable.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Unilever.png" alt="Unilever" class="w-full h-40 object-contain" />
    </div>

    <!-- Nestlé -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">Nestlé</h2>
      <p class="text-base font-semibold">Sector alimenticio</p>
      <p class="text-base mb-4">Reducción de residuos, uso eficiente del agua, empaques reciclables.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Nestle.png" alt="Nestlé" class="w-full h-40 object-contain" />
    </div>

    <!-- Walmart -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">Walmart de México</h2>
      <p class="text-base font-semibold">Sector retail</p>
      <p class="text-base mb-4">Reducción de residuos, eficiencia energética, productos sostenibles.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/WalmartMexico.png" alt="Walmart" class="w-full h-40 object-contain" />
    </div>

    <!-- PepsiCo -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">PepsiCo México</h2>
      <p class="text-base font-semibold">Sector bebidas y alimentos</p>
      <p class="text-base mb-4">Reducción de plásticos, energías renovables, agricultura sostenible.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/PepsiCo.png" alt="PepsiCo" class="w-full h-40 object-contain" />
    </div>

    <!-- Coca-Cola México -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">Coca-Cola México</h2>
      <p class="text-base font-semibold">Sector bebidas</p>
      <p class="text-base mb-4">Reciclaje de envases, retorno de agua usada.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/coca-cola.png" alt="Coca-Cola" class="w-full h-40 object-contain" />
    </div>

    <!-- Sidikai -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">Sidikai</h2>
      <p class="text-base font-semibold">Sector textil</p>
      <p class="text-base mb-4">Telas 100% sostenibles, filosofía "Cero Desperdicio".</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/sidikai.png" alt="Sidikai" class="w-full h-40 object-contain" />
    </div>

    <!-- Pangaia -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-2xl font-semibold text-green-700 mb-3">Pangaia</h2>
      <p class="text-base font-semibold">Sector textil</p>
      <p class="text-base mb-4">Fibras biológicas, materiales reciclados.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Pangaia.png" alt="Pangaia" class="w-full h-40 object-contain" />
    </div>
  </section>

  <!-- Footer -->
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