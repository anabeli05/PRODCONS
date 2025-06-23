<<<<<<< HEAD
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Consumo Digital y Producción Responsable</title>
    <link rel="stylesheet" href="/PRODCONS/PI2do/postWeb/code6.css" />
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
        <h1>Consumo Digital y Producción Responsable</h1>
        <div class="contenedor-imagenes">
            <div class="texto">
                <p>El consumo digital y la producción responsable están estrechamente relacionados, ya que nuestras decisiones digitales pueden influir en el impacto ambiental, social y económico.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/telefono.png" alt="Persona utilizando un teléfono móvil" class="imagen-primera" />
            </div>
        </div>
    </section>

    <main>
        <section class="contenedor-imagenes">
            <div class="texto">
                <h2>Consumo Energético</h2>
                <p>El uso de internet, servidores y centros de datos consume mucha energía. Reducir el tiempo en plataformas digitales y optar por servicios con políticas sostenibles ayuda a disminuir la huella de carbono.</p>

                <h3>Obsolescencia Programada</h3>
                <p>La compra frecuente de dispositivos electrónicos (como smartphones, computadoras o tabletas) impulsa la producción masiva y el desecho tecnológico. Optar por reparar, reutilizar y reciclar dispositivos promueve un consumo más responsable.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/tecladoPlanta.png" alt="Teclado con una planta encima" />
            </div>
        </section>

        <section class="contenedor-imagenes">
            <div class="texto">
                <h2>Huella Ecológica de la Tecnología</h2>
                <p>La extracción de minerales para dispositivos electrónicos y su proceso de fabricación tienen un alto costo ambiental. Elegir productos electrónicos de marcas con prácticas éticas y sostenibles puede marcar la diferencia.</p>

                <h3>Consumo de Contenidos</h3>
                <p>La transmisión de videos y el uso excesivo de plataformas digitales también genera un impacto. Por ejemplo, ver videos en baja resolución o descargar en lugar de hacer streaming continuo reduce la demanda energética.</p>

                <h3>E-commerce y Publicidad Digital</h3>
                <p>Las compras en línea pueden incentivar el consumismo. Sin embargo, pueden ser responsables si se priorizan productos locales, sostenibles y de comercio justo.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/Huella.png" alt="Huella ecológica representada en una imagen" class="imagen-huella" />
            </div>
        </section>

        <section class="contenedor-imagenes laptop">
            <div class="texto">
                <h2>¿Cómo Promover un Consumo Digital Responsable?</h2>
                <ul>
                    <li><strong>Optimizar el uso de dispositivos:</strong> Apagar equipos cuando no se usan, reducir el brillo de las pantallas y desactivar funciones innecesarias.</li>
                    <li><strong>Seleccionar plataformas responsables:</strong> Apoyar servicios digitales que usen energías renovables o tengan programas de reciclaje.</li>
                    <li><strong>Educar sobre el impacto digital:</strong> Crear contenido en tu blog que informe a las personas sobre cómo sus acciones digitales afectan el medio ambiente y la sociedad.</li>
                </ul>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/laptop.png" alt="Persona utilizando una laptop" />
            </div>
        </section>

        <section class="contenedor-imagenes mano-planta">
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/manoDePlanta.png" alt="Mano sosteniendo una planta" />
            </div>
            <div class="texto">
                <p>Las tecnologías digitales tienen sus ventajas, pero el auge de comercio electrónico que vivimos podría dañar gravemente el medio ambiente ya que la economía digital aumenta significativamente el consumo de energía, además de que genera muchos desechos, advierte la conferencia para el comercio, e insta a invertir en energías renovables para un futuro energético sostenible.</p>
            </div>
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
=======
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Consumo Digital y Producción Responsable</title>
    <link rel="stylesheet" href="/PRODCONS/PI2do/postWeb/code6.css" />
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
        <h1>Consumo Digital y Producción Responsable</h1>
        <div class="contenedor-imagenes">
            <div class="texto">
                <p>El consumo digital y la producción responsable están estrechamente relacionados, ya que nuestras decisiones digitales pueden influir en el impacto ambiental, social y económico.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/telefono.png" alt="Persona utilizando un teléfono móvil" class="imagen-primera" />
            </div>
        </div>
    </section>

    <main>
        <section class="contenedor-imagenes">
            <div class="texto">
                <h2>Consumo Energético</h2>
                <p>El uso de internet, servidores y centros de datos consume mucha energía. Reducir el tiempo en plataformas digitales y optar por servicios con políticas sostenibles ayuda a disminuir la huella de carbono.</p>

                <h3>Obsolescencia Programada</h3>
                <p>La compra frecuente de dispositivos electrónicos (como smartphones, computadoras o tabletas) impulsa la producción masiva y el desecho tecnológico. Optar por reparar, reutilizar y reciclar dispositivos promueve un consumo más responsable.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/tecladoPlanta.png" alt="Teclado con una planta encima" />
            </div>
        </section>

        <section class="contenedor-imagenes">
            <div class="texto">
                <h2>Huella Ecológica de la Tecnología</h2>
                <p>La extracción de minerales para dispositivos electrónicos y su proceso de fabricación tienen un alto costo ambiental. Elegir productos electrónicos de marcas con prácticas éticas y sostenibles puede marcar la diferencia.</p>

                <h3>Consumo de Contenidos</h3>
                <p>La transmisión de videos y el uso excesivo de plataformas digitales también genera un impacto. Por ejemplo, ver videos en baja resolución o descargar en lugar de hacer streaming continuo reduce la demanda energética.</p>

                <h3>E-commerce y Publicidad Digital</h3>
                <p>Las compras en línea pueden incentivar el consumismo. Sin embargo, pueden ser responsables si se priorizan productos locales, sostenibles y de comercio justo.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/Huella.png" alt="Huella ecológica representada en una imagen" class="imagen-huella" />
            </div>
        </section>

        <section class="contenedor-imagenes laptop">
            <div class="texto">
                <h2>¿Cómo Promover un Consumo Digital Responsable?</h2>
                <ul>
                    <li><strong>Optimizar el uso de dispositivos:</strong> Apagar equipos cuando no se usan, reducir el brillo de las pantallas y desactivar funciones innecesarias.</li>
                    <li><strong>Seleccionar plataformas responsables:</strong> Apoyar servicios digitales que usen energías renovables o tengan programas de reciclaje.</li>
                    <li><strong>Educar sobre el impacto digital:</strong> Crear contenido en tu blog que informe a las personas sobre cómo sus acciones digitales afectan el medio ambiente y la sociedad.</li>
                </ul>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/laptop.png" alt="Persona utilizando una laptop" />
            </div>
        </section>

        <section class="contenedor-imagenes mano-planta">
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/manoDePlanta.png" alt="Mano sosteniendo una planta" />
            </div>
            <div class="texto">
                <p>Las tecnologías digitales tienen sus ventajas, pero el auge de comercio electrónico que vivimos podría dañar gravemente el medio ambiente ya que la economía digital aumenta significativamente el consumo de energía, además de que genera muchos desechos, advierte la conferencia para el comercio, e insta a invertir en energías renovables para un futuro energético sostenible.</p>
            </div>
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
>>>>>>> main
