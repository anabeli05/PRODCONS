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
  <!-- Scripts de traducción -->
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
  <script src="/PRODCONS/translate.js"></script>
  <link rel="stylesheet" href="empresasr.css">
  <link rel="stylesheet" href="/PRODCONS/footer/footer/footer.css">
  <link rel="stylesheet" href="/PRODCONS/PI2do/footer/Visitante/footer.css">
  
  <style>
    .language-toggle {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 10px 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      z-index: 1003;
      font-family: Arial, sans-serif;
    }
    
    .language-toggle p {
      margin: 0 0 8px 0;
      font-size: 14px;
      font-weight: bold;
    }
    
    .language-toggle .language-buttons {
      display: flex;
      gap: 10px;
    }
    
    .language-toggle button {
      padding: 5px 10px;
      border: none;
      border-radius: 4px;
      background-color: #f0f0f0;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .language-toggle button:hover {
      background-color: #e0e0e0;
    }
    
    .language-toggle button.active {
      background-color: #4CAF50;
      color: white;
    }
    
    .close-button {
      position: absolute;
      top: 0;
      right: 0;
      font-size: 8px;
      background-color: transparent;
      border: none;
      cursor: pointer;
      color: #bbb;
      padding: 6px 8px;
      line-height: 1;
      opacity: 0.7;
      z-index: 1004;
    }
    
    .close-button:hover {
      color: #999;
      opacity: 0.9;
    }
  </style>
</head>
<body class="bg-white text-gray-800">
  <!-- Barra superior con flecha para regresar -->
  <section class="w-full h-12 bg-[rgb(225,216,204)] flex items-center">
<a href="/PRODCONS/PI2do/Dashboard_Usuario/Inicio/usuario.php" title="Regresar a la página principal" class="pl-4">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" 
           class="w-6 h-6 fill-current text-gray-700 hover:text-green-600 transition-colors duration-300">
        <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
      </svg>
    </a>
  </section>

  <!-- Línea negra debajo de la barra -->
  <div class="w-full h-[3.5px] bg-black"></div>
  
  <!-- Selector de idioma -->
  <div id="language-toggle" class="language-toggle">
    <button class="close-button" id="close-language-toggle" onclick="document.getElementById('language-toggle').style.display='none'">✕</button>
    <p id="toggle-text">¿Cambiar idioma?</p>
    <div class="language-buttons">
      <button id="btn-es" onclick="cambiarIdioma('espanol')" class="active">Español</button>
      <button id="btn-en" onclick="cambiarIdioma('ingles')">English</button>
    </div>
  </div>

  <!-- Introducción -->
  <section class="px-4 py-8 max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-4">Empresas Responsables</h1>
    <p class="mb-4">En México, varias empresas han adoptado prácticas alineadas con el ODS 12, ya sea a través de la reducción de residuos, el uso eficiente de recursos, la implementación de energías renovables o la promoción de la economía circular.</p>
    <p>A continuación, se mencionan algunas empresas mexicanas y multinacionales con presencia en México que han demostrado compromiso con este objetivo:</p>
  </section>

  <!-- Empresas -->
  <section class="px-4 py-8 grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 max-w-7xl mx-auto">
    <!-- Cemex -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Cemex</h2>
      <p class="text-sm">Sector constructor</p>
      <p class="text-sm mb-2">Programas de economía circular, uso de residuos industriales, eficiencia energética y reducción de emisiones de carbono.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/cemex.png" alt="Cemex" class="w-full h-40 object-contain" />
    </div>

    <!-- Bimbo -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Bimbo</h2>
      <p class="text-sm">Sector alimenticio</p>
      <p class="text-sm mb-2">Energía renovable, reducción de plásticos, programas de reciclaje.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/bimbo.png" alt="Bimbo" class="w-full h-40 object-contain" />
    </div>

    <!-- Coca-Cola FEMSA -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Coca-Cola FEMSA</h2>
      <p class="text-sm">Sector bebidas</p>
      <p class="text-sm mb-2">Reducción de agua, reciclaje, economía circular.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/coca-cola femsa.png" alt="FEMSA" class="w-full h-40 object-contain" />
    </div>

    <!-- Grupo Modelo -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Grupo Modelo</h2>
      <p class="text-sm">Sector bebidas</p>
      <p class="text-sm mb-2">Huella de carbono, uso eficiente del agua, envases reciclables.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Grupo Modelo.png" alt="Grupo Modelo" class="w-full h-40 object-contain" />
    </div>

    <!-- Alsea -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Alsea</h2>
      <p class="text-sm">Sector restaurantes y entretenimiento</p>
      <p class="text-sm mb-2">Reducción de desperdicio, eliminación de plásticos de un solo uso.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Alsea.png" alt="Alsea" class="w-full h-40 object-contain" />
    </div>

    <!-- Grupo Herdez -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Grupo Herdez</h2>
      <p class="text-sm">Sector alimenticio</p>
      <p class="text-sm mb-2">Producción sostenible, reducción de residuos.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Herdez.png" alt="Grupo Herdez" class="w-full h-40 object-contain" />
    </div>

    <!-- Unilever -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Unilever</h2>
      <p class="text-sm">Sector bienes de consumo</p>
      <p class="text-sm mb-2">Reducción de plásticos, reciclaje, consumo responsable.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Unilever.png" alt="Unilever" class="w-full h-40 object-contain" />
    </div>

    <!-- Nestlé -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Nestlé</h2>
      <p class="text-sm">Sector alimenticio</p>
      <p class="text-sm mb-2">Reducción de residuos, uso eficiente del agua, empaques reciclables.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Nestle.png" alt="Nestlé" class="w-full h-40 object-contain" />
    </div>

    <!-- Walmart -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Walmart de México</h2>
      <p class="text-sm">Sector retail</p>
      <p class="text-sm mb-2">Reducción de residuos, eficiencia energética, productos sostenibles.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Walmart Mexico.png" alt="Walmart" class="w-full h-40 object-contain" />
    </div>

    <!-- PepsiCo -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">PepsiCo México</h2>
      <p class="text-sm">Sector bebidas y alimentos</p>
      <p class="text-sm mb-2">Reducción de plásticos, energías renovables, agricultura sostenible.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/PepsiCo.png" alt="PepsiCo" class="w-full h-40 object-contain" />
    </div>

    <!-- Coca-Cola México -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Coca-Cola México</h2>
      <p class="text-sm">Sector bebidas</p>
      <p class="text-sm mb-2">Reciclaje de envases, retorno de agua usada.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/coca-cola.png" alt="Coca-Cola" class="w-full h-40 object-contain" />
    </div>

    <!-- Sidikai -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Sidikai</h2>
      <p class="text-sm">Sector textil</p>
      <p class="text-sm mb-2">Telas 100% sostenibles, filosofía "Cero Desperdicio".</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/sidikai.png" alt="Sidikai" class="w-full h-40 object-contain" />
    </div>

    <!-- Pangaia -->
    <div class="border rounded-xl shadow p-4">
      <h2 class="text-xl font-semibold text-green-700 mb-2">Pangaia</h2>
      <p class="text-sm">Sector textil</p>
      <p class="text-sm mb-2">Fibras biológicas, materiales reciclados.</p>
      <img src="/PRODCONS/PI2do/imagenes/empresas/Pangaia.png" alt="Pangaia" class="w-full h-40 object-contain" />
    </div>
  </section>

  <!-- Footer -->
  <footer class="text-center text-sm text-gray-500 py-8">© 2025 PRODCONS</footer>

  <!-- Script para el idioma -->
  <script>
    function updateLanguageButtons() {
      const btnEs = document.getElementById('btn-es');
      const btnEn = document.getElementById('btn-en');
      const toggleText = document.getElementById('toggle-text');
      
      const currentLang = localStorage.getItem('preferredLanguage') || 'es';
      
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
    
    document.addEventListener('DOMContentLoaded', function() {
      updateLanguageButtons();
      
      const observer = new MutationObserver(function(mutations) {
        updateLanguageButtons();
      });
      
      observer.observe(document.documentElement, { attributes: true, attributeFilter: ['lang'] });
      
      document.getElementById('close-language-toggle').addEventListener('click', function() {
        document.getElementById('language-toggle').style.display = 'none';
      });
    });
    
    const originalCambiarIdioma = window.cambiarIdioma;
    window.cambiarIdioma = function(idioma) {
      if (typeof originalCambiarIdioma === 'function') {
        originalCambiarIdioma(idioma);
      } else {
        translateContent(idioma === 'ingles' ? 'en' : 'es');
      }
      
      setTimeout(updateLanguageButtons, 100);
    };
  </script>


</body>
</html>
