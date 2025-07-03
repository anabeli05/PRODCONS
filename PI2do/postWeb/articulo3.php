<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Revolución de la Moda Sostenible</title>
    <link rel="stylesheet" href="/PRODCONS/PI2do/postWeb/code3.css">
    
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
        <h1>La Revolución de la Moda Sostenible: Cómo Ser un Consumidor Responsable</h1>
        <div class="contenedor-imagenes">
            <div class="texto">
                <p>La moda es una de las industrias más influyentes a nivel mundial, pero también una de las más contaminantes.</p>
                <p>Por lo que la moda sostenible ha estado desarrollándose continuamente y, gracias a la aceptación, tenemos la oportunidad de hacer un cambio. Pero, ¿sabes a qué se debe esto?</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/ropaOtraVez.png" alt="Ropa sostenible" class="imagen-grande">
            </div>
        </div>
    </section>

    <main>
        <section class="contenedor-imagenes">
            <div class="texto">
                <h2>¿Qué es la Moda Sostenible?</h2>
                <p>Es la creación de vestimenta con materiales o procesos que minimicen el impacto ambiental. Cómo por ejemplo:</p>
                <ul>
                    <li><strong>Uso de Materiales Orgánicos y Reciclados</strong></li>
                    <li><strong>Diseños Duraderos para un mayor tiempo de uso</strong></li>
                </ul>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/cajaCosas.png" alt="Caja con materiales sostenibles" class="caja">
                <img src="/PRODCONS/PI2do/imagenes/ropaColgada.png" alt="Ropa colgada">
            </div>
        </section>

        <section class="contenedor-imagenes">
            <div class="texto">
                <h2>Prácticas de Consumo Responsable en la Moda</h2>
                <p>Como consumidores, podemos tomar decisiones que promuevan la sostenibilidad en la moda. Así, tomando decisiones más responsables de nuestras acciones.</p>
                <p>Cada consumidor será diferente, por lo que una recomendación sería buscar vestimentas que tengan la intención de la moda sostenible.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/mujeres.png" alt="Mujeres sostenibles">
                <img src="/PRODCONS/PI2do/imagenes/mujer.png" alt="Mujer sostenible">
            </div>
        </section>

        <section>
            <h2>Personas que Inspiran</h2>
            <p>En el mundo existen personas que desean un mundo más próspero y verde, como por ejemplo:</p>
            <ul>
                <li><strong>Stella McCartney</strong>, una diseñadora de moda, ha liderado el movimiento hacia la moda sostenible, utilizando materiales orgánicos y prácticas éticas en sus colecciones.</li>
                <li><strong>Emma Watson</strong>, actriz y activista, ha promovido el uso de ropa sostenible en la alfombra roja y en su vida diaria.</li>
            </ul>
        </section>

        <section>
            <p>En resumen, la moda sostenible no solo es una tendencia, sino una necesidad para asegurar un futuro más verde y justo. Al tomar decisiones conscientes sobre nuestra ropa, podemos ser parte del cambio hacia una industria de la moda más responsable.</p>
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
