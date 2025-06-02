<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu puedes hacer la diferencia</title>
    <link rel="stylesheet" href="/PRODCONS/PI2do/postWeb/code2.css">
    
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

        .imagenes-articulo2 {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .imagenes-articulo2 img {
            width: 350px;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            object-fit: cover;
            background: #fff;
        }
    </style>
</head>
<body>
    <!-- Barra para regresar -->
    <section class="barra_left">
        <i class="flecha_left">
<a href="/PRODCONS/PI2do/Dashboard_Usuario/Inicio/usuario.php" title="Regresar a la página principal">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                </svg>
            </a>
        </i>
    </section>

    <!-- Selector de idioma -->
    <div class="language-toggle" id="language-toggle">
        <button class="close-button" id="close-language-toggle" onclick="document.getElementById('language-toggle').style.display='none'">✕</button>
        <p id="toggle-text">¿Cambiar idioma?</p>
        <div class="language-buttons">
            <button id="btn-es" onclick="cambiarIdioma('espanol')" class="active">Español</button>
            <button id="btn-en" onclick="cambiarIdioma('ingles')">English</button>
        </div>
    </div>

    <!-- Contenido principal -->
    <section class="header-section">
        <h1>Tú puedes hacer la diferencia</h1>
        <div class="contenedor-imagenes">
            <div class="texto">
                <p>Las empresas no son las únicas responsables de los cambios que está enfrentando nuestro planeta, nosotros tenemos una gran responsabilidad de esos cambios.</p>
                <p>Toda pequeña acción puede lograr un gran cambio.</p>
                <p>En este apartado hablaremos en qué consiste el consumo responsable y de pequeñas acciones que puedes hacer en tu día a día, las cuales en un futuro mostrarán un gran cambio en la sociedad.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/manosApuntando.png" alt="Manos apuntando hacia un mensaje de responsabilidad">
            </div>
        </div>
    </section>

    <main>
        <section class="contenedor-imagenes">
            <div class="texto">
                <h2>El consumo responsable</h2>
                <p>El consumo responsable es el uso adecuado que le damos a los productos y a los servicios con el fin de satisfacer una necesidad, cambiando de esta manera nuestra forma de consumir.</p>
                <p>Para practicar el consumo responsable podemos comenzar con preguntarnos si de verdad es necesario comprar el producto o no, también ver si es un producto el cual fue creado con materiales que cuidan al medio ambiente y no causan daño.</p>
            </div>
        </section>
        <div class="imagenes-articulo2">
            <img src="/PRODCONS/PI2do/imagenes/comprandoFruta.png" alt="Persona comprando frutas">
            <img src="/PRODCONS/PI2do/imagenes/foco.png" alt="Foco representando ideas sostenibles">
        </div>

        <section class="contenedor-imagenes">
            <div class="texto">
                <h2>TIPS PARA PONER EN PRÁCTICA:</h2>
                <ul>
                    <li><strong>Comprar solo los alimentos necesarios y menos procesados</strong></li>
                    <li><strong>Usar de manera adecuada los servicios</strong></li>
                    <li><strong>Usar un medio de transporte el cual no contamine</strong></li>
                    <li><strong>Apoyar a las empresas que buscan cuidar al medio ambiente y trabajan basándose en los ODS</strong></li>
                </ul>
            </div>
        </section>
        <div class="imagenes-articulo2">
            <img src="/PRODCONS/PI2do/imagenes/montarBicicletas.png" alt="Personas montando bicicletas">
            <img src="/PRODCONS/PI2do/imagenes/rompecabezas.png" alt="Rompecabezas representando la colaboración">
        </div>

        <section class="contenedor-imagenes">
            <div class="texto">
                <h3>Algunas empresas son:</h3>
                <ul>
                    <li>Grupo Modelo</li>
                    <li>Unilever</li>
                    <li>Walmart de México</li>
                    <li>PepsiCo México</li>
                </ul>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <!-- Remove the footer section here to avoid duplication -->
    <!-- Footer -->
    <!-- Remove the footer section here to avoid duplication -->
    <?php include '/xampp/htdocs/PRODCONS/PI2do/footer/Visitante/footer.php'; ?>

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
