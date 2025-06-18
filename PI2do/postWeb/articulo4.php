<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reduciendo Residuos en el Hogar</title>
    <link rel="stylesheet" href="/PRODCONS/PI2do/postWeb/code4.css">
    
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
        <h1>Reduciendo Residuos en el Hogar: 10 Consejos Prácticos</h1>
        <div class="contenedor-imagenes">
            <div class="texto">
                <p>En nuestra vida diaria compramos y consumimos cosas y productos de manera descontrolada y desmedida. Nosotros como individuos debemos ser conscientes de que debemos tomar medidas urgentes para reducir nuestra huella ecológica.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/composta.png" alt="Composta de residuos orgánicos">
            </div>
        </div>
    </section>

    <main>
        <section class="contenedor-imagenes">
            <div class="texto">
                <p>El consumo desmedido de los productos de "moda" o "temporada" también motiva a las marcas a realizar una producción desmedida de productos desechables que duran a lo mucho una temporada o 4 meses máximo de uso para después ser inútiles por pasar a usar la siguiente "moda de temporada". Ese consumo y producción desmedido produce una contaminación desmedida, tanto de parte de nosotros los consumidores como de las empresas que las fabrican.</p>
                <p>En cambio, es preferible comprar productos locales, ya sea en la compra de ropa elaborada de manera local como en la compra de productos para nuestro consumo de manera controlada. Debemos comprar solo la comida necesaria para no generar desperdicios innecesarios, también evitar a toda costa el comprar productos que son considerados "de temporada" para igual mostrar una desaprobación a aquellas prácticas.</p>
                <p>De esta manera y muchas otras formas sencillas, podemos cambiar nuestra forma de vida teniendo un consumo responsable y reduciendo nuestra huella ecológica para tener un mejor futuro.</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/planeta.png" alt="Planeta Tierra">
            </div>
        </section>

        <section>
            <h2>Consejos Prácticos</h2>
            <ul>
                <li><strong>Compra a granel:</strong> Minimiza los empaques comprando productos a granel. Usa tus propios recipientes y bolsas reutilizables de tela.</li>
                <li><strong>Reutiliza y recicla:</strong> Busca formas innovadoras de reutilizar objetos antes de deshacerte de ellos. Recicla los materiales adecuados y sigue las normas locales de reciclaje.</li>
                <li><strong>Composta tus desechos orgánicos:</strong> Convierte los restos de comida y otros desechos orgánicos en compost para enriquecer tu jardín.</li>
                <li><strong>Elige productos con menos embalaje:</strong> Prefiere productos que tengan poco o ningún embalaje. Prioriza las marcas que usan materiales biodegradables o reciclables.</li>
                <li><strong>Usa botellas y tazas reutilizables:</strong> Evita las botellas y tazas desechables llevando siempre contigo una botella y una taza reutilizables.</li>
                <li><strong>Dona y repara:</strong> Antes de desechar ropa u otros objetos, piensa en donarlos o repararlos.</li>
                <li><strong>Compra de segunda mano:</strong> Fomenta la economía circular comprando en tiendas de segunda mano o intercambiando objetos con amigos y familiares.</li>
                <li><strong>Elabora tus propios productos de limpieza:</strong> Haz tus propios productos de limpieza con ingredientes naturales como vinagre, bicarbonato de sodio y aceites esenciales.</li>
                <li><strong>Planifica tus comidas:</strong> Reduce el desperdicio de alimentos planificando tus comidas y comprando solo lo necesario.</li>
                <li><strong>Participa en iniciativas comunitarias:</strong> Únete a grupos locales que promuevan la reducción de residuos y la sostenibilidad.</li>
            </ul>
        </section>
    </main>

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
