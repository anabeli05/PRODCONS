<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reduciendo residuos en el hogar</title>
    <link rel="stylesheet" href="/PRODCONS/PI2do/postWeb/code5.css">
    
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
<body>
    <!-- Barra para regresar -->
    <section class="barra_left">
        <i class="flecha_left">
<a href="/PRODCONS/" title="Regresar a la página principal">
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
        <h1>Crea tu propio huerto y sus ventajas</h1>
        <div class="contenedor-imagenes">
            <div class="texto">
                <p>Tener un huerto permite tener la diversidad y disponibilidad de alimentos tales como: vegetales y frutas.</p>
                <p>Es una manera cuidadosa e higiénica para mantener una mejor calidad en la nutrición y salud de nuestro organismo.</p>
                <p>Los huertos ecológicos son la principal semilla de la agricultura sostenible, aquella que usa abonos orgánicos en lugar de fertilizantes químicos, mejora la fertilidad del suelo, mantiene la calidad del agua y protege la biodiversidad. Una tendencia que no para de crecer.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/personasComprando.png" alt="Personas comprando productos frescos" class="imagen-grande">
            </div>
        </div>
    </section>

    <main>
        <section class="contenedor-imagenes">
            <div class="texto">
                <h2>VENTAJAS DE TENER UN HUERTO EN CASA</h2>
                <h3>Reduce la huella de carbono</h3>
                <p>Cultivar tus propios alimentos disminuye la necesidad de transportarlos desde grandes distancias, lo que reduce las emisiones de CO₂ generadas por camiones, barcos y aviones.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/huellaCo2.png" alt="Huella de carbono">
            </div>
        </section>

        <section class="contenedor-imagenes">
            <div class="texto">
                <h3>Menos residuos plásticos</h3>
                <p>Los productos que compras en supermercados suelen venir en envases de plástico. Con un huerto en casa, reduces el consumo de envases desechables y contribuyes a disminuir la contaminación por plásticos.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/plastico.png" alt="Residuos plásticos">
            </div>
        </section>

        <section class="contenedor-imagenes">
            <div class="texto">
                <h3>Fomenta la biodiversidad</h3>
                <p>Los huertos caseros pueden atraer polinizadores como abejas y mariposas, lo que ayuda a mantener el equilibrio ecológico y mejorar la salud de los ecosistemas locales.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/biodiversidad.png" alt="Biodiversidad">
            </div>
        </section>

        <section class="contenedor-imagenes">
            <div class="texto">
                <h3>Mejora la calidad del aire</h3>
                <p>Las plantas absorben dióxido de carbono (CO₂) y liberan oxígeno, ayudando a purificar el aire en un entorno. Además, algunas especies pueden capturar partículas contaminantes.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/arbolManos.png" alt="Calidad del aire">
            </div>
        </section>

        <section class="contenedor-imagenes">
            <div class="texto">
                <h3>Fomenta una alimentación sostenible</h3>
                <p>Al cultivar tus propios alimentos, reduces la demanda de productos agrícolas industriales, que muchas veces utilizan pesticidas y fertilizantes químicos dañinos para el suelo y el agua.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/comprando.png" alt="Alimentación sostenible">
            </div>
        </section>

        <section class="contenedor-imagenes">
            <div class="texto">
                <h3>Ahorro y conservación del agua</h3>
                <p>Los huertos caseros pueden diseñarse con sistemas de riego eficientes, como el riego por goteo o la recolección de agua de lluvia, reduciendo el desperdicio de este recurso.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/arbolPlaneta.png" alt="Ahorro de agua">
            </div>
        </section>

        <section class="contenedor-imagenes">
            <div class="texto">
                <h3>Reducción de desechos orgánicos</h3>
                <p>Puedes hacer compost con los restos de frutas, verduras y otros residuos orgánicos, generando abono natural para tu huerto y reduciendo la cantidad de basura enviada a los vertederos.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/composta.png" alt="Reducción de desechos orgánicos">
            </div>
        </section>

        <section>
            <h2>Blogs que te pueden apoyar a crear tu huerto</h2>
            <ul>
                <li><strong>Appalearra.com</strong><br>Ofrece una guía en 10 pasos para montar un huerto en casa, ideal para principiantes.<br><a href="http://appalearra.com">appalearra.com</a></li>
                <li><strong>Vida-materatable.com</strong><br>Proporciona una guía completa para crear un huerto orgánico en casa, con consejos expertos y pasos detallados.<br><a href="http://vida-materatable.com">vida-materatable.com</a></li>
                <li><strong>Plantamanda.com</strong><br>Brinda consejos para crear y cuidar un huerto en casa, desde la elección del lugar hasta el mantenimiento adecuado.<br><a href="http://plantamanda.com">plantamanda.com</a></li>
            </ul>
        </section>

        <section>
            <p>En conclusión, tener un huerto en tu hogar apoya a nuestra tierra y a nuestra alimentación, beneficia nuestro cuerpo y medio ambiente.</p>
        </section>
    </main>
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
<?php include '/xampp/htdocs/PRODCONS/PI2do/footer/Visitante/footer.php'; ?>

</body>
</html>
