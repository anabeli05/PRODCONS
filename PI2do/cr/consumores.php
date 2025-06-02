<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Consumo responsable</title>
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- Scripts de traducción -->
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
  <script src="/PRODCONS/translate.js"></script>

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
<body class="bg-white m-0 p-0">

  <!-- Barra superior con flecha para regresar -->
  <section class="w-full h-12 bg-[rgb(225,216,204)] flex items-center">
<a href="/PRODCONS/" title="Regresar a la página principal" class="pl-4">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" 
           class="w-6 h-6 fill-current text-gray-700 hover:text-green-600 transition-colors duration-300">
        <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
      </svg>
    </a>
  </section>

  <!-- Línea negra debajo de la barra -->
  <div class="w-full h-[3.5px] bg-black"></div>

  <!-- Contenedor principal -->
  <div class="flex flex-col md:flex-row w-full mt-6 box-border">

    <!-- Cuadro verde -->
    <div style="background-color: rgb(109, 151, 109);" 
         class="w-full md:w-1/2 text-white p-8 rounded-md flex flex-col justify-center">
      <h2 class="text-3xl font-bold mb-6 leading-tight">EL CONSUMO RESPONSABLE</h2>
      <p class="text-lg leading-relaxed">
        El consumo responsable implica elegir productos y servicios que minimicen el impacto ambiental,
        fomenten la economía local y respeten los derechos de los trabajadores. Es una forma de tomar
        decisiones conscientes para reducir el desperdicio y promover un futuro más sostenible.
      </p>
    </div>

    <!-- Imagen -->
    <div class="w-full md:w-1/2 flex items-center justify-center mt-6 md:mt-0">
      <img src="/PRODCONS/PI2do/imagenes/manoConsumo.png" alt="Imagen Principal" class="max-w-full h-auto rounded-md shadow-lg" />
    </div>

  </div>

  <!-- Selector de idioma -->
  <div class="language-toggle" id="language-toggle">
    <button class="close-button" id="close-language-toggle" onclick="document.getElementById('language-toggle').style.display='none'">✕</button>
    <p id="toggle-text">¿Cambiar idioma?</p>
    <div class="language-buttons">
      <button id="btn-es" onclick="cambiarIdioma('espanol')" class="active">Español</button>
      <button id="btn-en" onclick="cambiarIdioma('ingles')">English</button>
    </div>
  </div>

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
