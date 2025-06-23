<<<<<<< HEAD
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menos Plásticos, Más Vida</title>
    <link rel="stylesheet" href="/PRODCONS/PI2do/postWeb/code.css">
     <link rel="stylesheet" href="/PRODCONS/PI2do/header_post/header_post.css">
    
    <!-- =====================================================================
    SCRIPTS DE TRADUCCIÓN - REQUERIDOS
    No eliminar estas líneas, son necesarias para la funcionalidad de traducción
    ===================================================================== -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>
    
    <!-- =====================================================================
    ESTILOS PARA EL CUADRO DE IDIOMA - PERSONALIZABLE
    Puedes modificar los estilos para cambiar la apariencia del cuadro de idioma
    
    - El botón X permite cerrar el selector cuando no se necesita
    ===================================================================== -->
        <style>        .language-toggle {            position: fixed;          /* Posición fija en la pantalla */            top: 20px;                /* Distancia desde la parte superior - PERSONALIZABLE */            right: 20px;              /* Distancia desde la derecha - PERSONALIZABLE */            background-color: #fff;   /* Color de fondo - PERSONALIZABLE */            border: 1px solid #ddd;   /* Borde - PERSONALIZABLE */            border-radius: 8px;       /* Bordes redondeados - PERSONALIZABLE */            padding: 10px 15px;       /* Espaciado interno - PERSONALIZABLE */            box-shadow: 0 2px 10px rgba(0,0,0,0.1); /* Sombra - PERSONALIZABLE */            z-index: 1000;            /* Capa de visualización - MANTENER ALTO */            font-family: Arial, sans-serif; /* Fuente - PERSONALIZABLE */        }                .language-toggle p {            margin: 0 0 8px 0;            font-size: 14px;          /* Tamaño de texto - PERSONALIZABLE */            font-weight: bold;        /* Negrita - PERSONALIZABLE */        }                .language-toggle .language-buttons {            display: flex;            gap: 10px;                /* Espacio entre botones - PERSONALIZABLE */        }                .language-toggle button {            padding: 5px 10px;        /* Espaciado interno de botones - PERSONALIZABLE */            border: none;             /* Sin borde - PERSONALIZABLE */            border-radius: 4px;       /* Bordes redondeados - PERSONALIZABLE */            background-color: #f0f0f0; /* Color de fondo - PERSONALIZABLE */            cursor: pointer;            transition: background-color 0.2s;        }                .language-toggle button:hover {            background-color: #e0e0e0; /* Color al pasar el mouse - PERSONALIZABLE */        }                .language-toggle button.active {            background-color: #4CAF50; /* Color del botón activo - PERSONALIZABLE */            color: white;             /* Color de texto del botón activo - PERSONALIZABLE */        }                /* Estilos para el botón de cerrar (X) */        .close-button {            position: absolute;       /* Posición absoluta dentro del contenedor */            top: 0;                   /* Distancia desde arriba - PERSONALIZABLE */            right: 0;                 /* Distancia desde la derecha - PERSONALIZABLE */            font-size: 8px;           /* Tamaño de la X más pequeño - PERSONALIZABLE */            background-color: transparent; /* Fondo completamente transparente */            border: none;             /* Sin borde */                        cursor: pointer;          /* Cursor tipo mano al pasar por encima */                        color: #bbb;              /* Color más claro para la X */                        padding: 6px 8px;         /* Padding más grande para aumentar el área de clic */            line-height: 1;           /* Altura de línea ajustada */            opacity: 0.7;             /* Ligeramente transparente */            z-index: 1001;            /* Se asegura que esté por encima para poder hacer clic */        }                .close-button:hover {            color: #999;              /* Color al pasar el ratón más sutil - PERSONALIZABLE */            opacity: 0.9;             /* Aumenta ligeramente la opacidad al pasar el ratón */        }
    </style>
</head>
<body>
    <!-- Barra para regresar y header principal -->
    <section class="barra_left">
        <i class="flecha_left">
<a href="/PRODCONS/" title="Regresar a la página principal">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                </svg>
            </a>
        </i>
    </section>

    <!-- =====================================================================    CUADRO DE SELECCIÓN DE IDIOMA - NO MODIFICAR LA ESTRUCTURA    Puedes modificar el texto, pero mantén los IDs y la estructura        - El botón X permite cerrar/ocultar el selector de idioma    ===================================================================== -->
    <!-- <div class="language-toggle" id="language-toggle">
        <button class="close-button" id="close-language-toggle" onclick="document.getElementById('language-toggle').style.display='none'">✕</button>
        <p id="toggle-text">¿Cambiar idioma?</p>
        <div class="language-buttons">
            <button id="btn-es" onclick="cambiarIdioma('espanol')" class="active">Español</button>
            <button id="btn-en" onclick="cambiarIdioma('ingles')">English</button>
        </div>
    </div> -->

    <!-- Header convertido en section -->
    <section class="header-section">
        <h1>Menos plásticos, más vida</h1>
        <div class="contenedor-imagenes">
            <div class="texto">
                <p>El plástico está por todas partes: en nuestras casas, en los supermercados, incluso en nuestros océanos. Aunque es un material práctico, su impacto en el medio ambiente es devastador. La buena noticia es que podemos hacer algo al respecto. Con pequeñas decisiones conscientes, podemos reducir significativamente nuestro uso de plásticos y contribuir a un mundo más limpio. ¿Listo para empezar?</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/personaBotella.png" alt="Persona con botella reutilizable" class="imagen-primera">
            </div>
        </div>
    </section>

    <main>
        <section>
            <h2>REDUCCIÓN DE PLÁSTICOS</h2>
            <h3>¿Por qué reducir el uso de plásticos?</h3>
            <p>El plástico tarda cientos de años en descomponerse, y gran parte termina en vertederos, ríos y océanos, afectando gravemente a la fauna y contaminando ecosistemas:</p>
            <ul>
                <li>Cada año, 8 millones de toneladas de plástico llegan a los océanos.</li>
                <li>Millones de animales marinos mueren al confundir el plástico con comida o quedar atrapados en él.</li>
                <li>Los microplásticos (pequeñas partículas de plástico) ya están presentes en nuestra agua, aire y alimentos.</li>
            </ul>
        </section>

        <section>
            <h3>Consejos prácticos para reducir el plástico en tu día a día</h3>
            <h4>En casa:</h4>
            <ul>
                <li>Dile adiós a los plásticos de un solo uso: Reemplaza pajitas, cubiertos y platos desechables por alternativas reutilizables hechas de bambú, metal o vidrio.</li>
                <li>Compra a granel: Lleva tus propios recipientes cuando compres alimentos como arroz, legumbres o cereales.</li>
            </ul>

            <h4>En el supermercado:</h4>
            <ul>
                <li>Lleva tus propias bolsas reutilizables. Deja las bolsas de plástico en el pasado y opta por bolsas de tela o redes.</li>
                <li>Evita productos con exceso de empaques. Elige frutas y verduras sueltas en lugar de las preenvasadas.</li>
                <li>Busca alternativas sostenibles: Por ejemplo, compra leche en envases de vidrio retornable o snacks envasados en papel.</li>
            </ul>

            <h4>En la calle:</h4>
            <ul>
                <li>Lleva una botella reutilizable. Olvídate de comprar agua embotellada y lleva siempre contigo una botella de acero inoxidable o vidrio.</li>
                <li>Di "no gracias" a los popotes: Si realmente necesitas uno, opta por opciones de metal, bambú o silicona.</li>
                <li>Lleva tu propio kit reutilizable: Incluye utensilios, servilletas de tela y un recipiente pequeño para evitar usar plásticos en restaurantes o food trucks.</li>
            </ul>
        </section>
    </main>

    <!-- Footer convertido en section -->
    <section class="footer-section">
        <div class="contenedor-imagenes">
            <div class="texto">
                <p>Reducir el uso de plásticos no es solo una tendencia; es una necesidad urgente. No se trata de ser perfecto, sino de tomar pequeños pasos hacia un estilo de vida más consciente. Desde llevar tus propias bolsas al supermercado hasta decirle adiós a los popotes de plástico, cada acción cuenta. Juntos podemos construir un mundo donde el plástico no sea un problema, sino una excepción. ¿Qué cambio harás hoy?</p>
                <p><strong>Dato curioso:</strong> Se estima que cada año terminan en los océanos más de 8 millones de toneladas de plástico. ¡Reducir su uso puede salvar a miles de animales marinos!</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/bolsaProtcons.png" alt="Bolsa reutilizable Protcons">
                <div class="publicado">PUBLICADO EL 14 FEBRERO DEL 2025   |
                       POR JUAN PABLO MANCILLA RODRIGUEZ</div>
            </div>
        </div>
    </section>

    <!-- Nueva sección para el autor y bibliografías -->
    <section class="autor-bibliografias">
        <h3>Bibliografías</h3>
        <ul>
            <li>Ritchie, H., & Roser, M. (2018). Plastic pollution. Our World in Data. Recuperado de <a href="https://ourworldindata.org/plastic-pollution">https://ourworldindata.org/plastic-pollution</a></li>
            <li>Secretaría de Medio Ambiente y Recursos Naturales. (s.f.). Estrategias para reducir el uso de plásticos. Recuperado de <a href="https://www.gob.mx/semarnat">https://www.gob.mx/semarnat</a></li>
            <li>Greenpeace México. (s.f.). Cómo reducir el uso de plásticos de un solo uso. Recuperado de <a href="https://www.greenpeace.org/mexico">https://www.greenpeace.org/mexico</a></li>
            <li>Plastic Pollution Coalition. (s.f.). Guía para reducir el uso de plásticos. Recuperado de <a href="https://www.plasticpollutioncoalition.org">https://www.plasticpollutioncoalition.org</a></li>
            <li>OpenAI. (2023). ChatGPT (versión febrero 2023) Modelo de lenguaje. <a href="https://chat.openai.com">https://chat.openai.com</a></li>
        </ul>
    </section>

<!-- =====================================================================
SCRIPT PARA ACTUALIZAR BOTONES DE IDIOMA - NO MODIFICAR
Este script mantiene sincronizada la interfaz de idioma
 
- El botón X permite ocultar el selector de idioma cuando no se necesita
===================================================================== -->
<script>
    // Function to update button states based on current language    function updateLanguageButtons() {        const btnEs = document.getElementById('btn-es');        const btnEn = document.getElementById('btn-en');        const toggleText = document.getElementById('toggle-text');                // Get current language from localStorage or default to Spanish        const currentLang = localStorage.getItem('preferredLanguage') || 'es';                // Update active button        if (currentLang === 'en') {            btnEs.classList.remove('active');            btnEn.classList.add('active');            toggleText.innerText = 'Change language?';        } else {            btnEn.classList.remove('active');            btnEs.classList.add('active');            toggleText.innerText = '¿Cambiar idioma?';        }    }
    
    // Call this function on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateLanguageButtons();
        
        // Add event listener to update buttons when language changes
        const observer = new MutationObserver(function(mutations) {
            updateLanguageButtons();
        });
        
        // Start observing the document with the configured parameters
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['lang'] });
        
        // Add event listener for the close button
        document.getElementById('close-language-toggle').addEventListener('click', function() {
            document.getElementById('language-toggle').style.display = 'none';
        });
    });
    
    // Override the cambiarIdioma function to update button states
    const originalCambiarIdioma = window.cambiarIdioma;
    window.cambiarIdioma = function(idioma) {
        if (typeof originalCambiarIdioma === 'function') {
            originalCambiarIdioma(idioma);
        } else {
            // Fallback if the original function isn't available
            translateContent(idioma === 'ingles' ? 'en' : 'es');
        }
        
        // Update button states
        setTimeout(updateLanguageButtons, 100);
    };
</script>

<?php include '/xampp/htdocs/PRODCONS/PI2do/footer/Visitante/footer.php'; ?>
<script src='/PRODCONS/PI2do/header_post/header_post.js'></script>

</body>
</html>
=======
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menos Plásticos, Más Vida</title>
    <link rel="stylesheet" href="/PRODCONS/PI2do/postWeb/code.css">
     <link rel="stylesheet" href="/PRODCONS/PI2do/header_post/header_post.css">
    
    <!-- =====================================================================
    SCRIPTS DE TRADUCCIÓN - REQUERIDOS
    No eliminar estas líneas, son necesarias para la funcionalidad de traducción
    ===================================================================== -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>
    
    <!-- =====================================================================
    ESTILOS PARA EL CUADRO DE IDIOMA - PERSONALIZABLE
    Puedes modificar los estilos para cambiar la apariencia del cuadro de idioma
    
    - El botón X permite cerrar el selector cuando no se necesita
    ===================================================================== -->
        <style>        .language-toggle {            position: fixed;          /* Posición fija en la pantalla */            top: 20px;                /* Distancia desde la parte superior - PERSONALIZABLE */            right: 20px;              /* Distancia desde la derecha - PERSONALIZABLE */            background-color: #fff;   /* Color de fondo - PERSONALIZABLE */            border: 1px solid #ddd;   /* Borde - PERSONALIZABLE */            border-radius: 8px;       /* Bordes redondeados - PERSONALIZABLE */            padding: 10px 15px;       /* Espaciado interno - PERSONALIZABLE */            box-shadow: 0 2px 10px rgba(0,0,0,0.1); /* Sombra - PERSONALIZABLE */            z-index: 1000;            /* Capa de visualización - MANTENER ALTO */            font-family: Arial, sans-serif; /* Fuente - PERSONALIZABLE */        }                .language-toggle p {            margin: 0 0 8px 0;            font-size: 14px;          /* Tamaño de texto - PERSONALIZABLE */            font-weight: bold;        /* Negrita - PERSONALIZABLE */        }                .language-toggle .language-buttons {            display: flex;            gap: 10px;                /* Espacio entre botones - PERSONALIZABLE */        }                .language-toggle button {            padding: 5px 10px;        /* Espaciado interno de botones - PERSONALIZABLE */            border: none;             /* Sin borde - PERSONALIZABLE */            border-radius: 4px;       /* Bordes redondeados - PERSONALIZABLE */            background-color: #f0f0f0; /* Color de fondo - PERSONALIZABLE */            cursor: pointer;            transition: background-color 0.2s;        }                .language-toggle button:hover {            background-color: #e0e0e0; /* Color al pasar el mouse - PERSONALIZABLE */        }                .language-toggle button.active {            background-color: #4CAF50; /* Color del botón activo - PERSONALIZABLE */            color: white;             /* Color de texto del botón activo - PERSONALIZABLE */        }                /* Estilos para el botón de cerrar (X) */        .close-button {            position: absolute;       /* Posición absoluta dentro del contenedor */            top: 0;                   /* Distancia desde arriba - PERSONALIZABLE */            right: 0;                 /* Distancia desde la derecha - PERSONALIZABLE */            font-size: 8px;           /* Tamaño de la X más pequeño - PERSONALIZABLE */            background-color: transparent; /* Fondo completamente transparente */            border: none;             /* Sin borde */                        cursor: pointer;          /* Cursor tipo mano al pasar por encima */                        color: #bbb;              /* Color más claro para la X */                        padding: 6px 8px;         /* Padding más grande para aumentar el área de clic */            line-height: 1;           /* Altura de línea ajustada */            opacity: 0.7;             /* Ligeramente transparente */            z-index: 1001;            /* Se asegura que esté por encima para poder hacer clic */        }                .close-button:hover {            color: #999;              /* Color al pasar el ratón más sutil - PERSONALIZABLE */            opacity: 0.9;             /* Aumenta ligeramente la opacidad al pasar el ratón */        }
    </style>
</head>
<body>
    <!-- Barra para regresar y header principal -->
    <section class="barra_left">
        <i class="flecha_left">
<a href="/PRODCONS/" title="Regresar a la página principal">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                </svg>
            </a>
        </i>
    </section>

    <!-- =====================================================================    CUADRO DE SELECCIÓN DE IDIOMA - NO MODIFICAR LA ESTRUCTURA    Puedes modificar el texto, pero mantén los IDs y la estructura        - El botón X permite cerrar/ocultar el selector de idioma    ===================================================================== -->
    <!-- <div class="language-toggle" id="language-toggle">
        <button class="close-button" id="close-language-toggle" onclick="document.getElementById('language-toggle').style.display='none'">✕</button>
        <p id="toggle-text">¿Cambiar idioma?</p>
        <div class="language-buttons">
            <button id="btn-es" onclick="cambiarIdioma('espanol')" class="active">Español</button>
            <button id="btn-en" onclick="cambiarIdioma('ingles')">English</button>
        </div>
    </div> -->

    <!-- Header convertido en section -->
    <section class="header-section">
        <h1>Menos plásticos, más vida</h1>
        <div class="contenedor-imagenes">
            <div class="texto">
                <p>El plástico está por todas partes: en nuestras casas, en los supermercados, incluso en nuestros océanos. Aunque es un material práctico, su impacto en el medio ambiente es devastador. La buena noticia es que podemos hacer algo al respecto. Con pequeñas decisiones conscientes, podemos reducir significativamente nuestro uso de plásticos y contribuir a un mundo más limpio. ¿Listo para empezar?</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/personaBotella.png" alt="Persona con botella reutilizable" class="imagen-primera">
            </div>
        </div>
    </section>

    <main>
        <section>
            <h2>REDUCCIÓN DE PLÁSTICOS</h2>
            <h3>¿Por qué reducir el uso de plásticos?</h3>
            <p>El plástico tarda cientos de años en descomponerse, y gran parte termina en vertederos, ríos y océanos, afectando gravemente a la fauna y contaminando ecosistemas:</p>
            <ul>
                <li>Cada año, 8 millones de toneladas de plástico llegan a los océanos.</li>
                <li>Millones de animales marinos mueren al confundir el plástico con comida o quedar atrapados en él.</li>
                <li>Los microplásticos (pequeñas partículas de plástico) ya están presentes en nuestra agua, aire y alimentos.</li>
            </ul>
        </section>

        <section>
            <h3>Consejos prácticos para reducir el plástico en tu día a día</h3>
            <h4>En casa:</h4>
            <ul>
                <li>Dile adiós a los plásticos de un solo uso: Reemplaza pajitas, cubiertos y platos desechables por alternativas reutilizables hechas de bambú, metal o vidrio.</li>
                <li>Compra a granel: Lleva tus propios recipientes cuando compres alimentos como arroz, legumbres o cereales.</li>
            </ul>

            <h4>En el supermercado:</h4>
            <ul>
                <li>Lleva tus propias bolsas reutilizables. Deja las bolsas de plástico en el pasado y opta por bolsas de tela o redes.</li>
                <li>Evita productos con exceso de empaques. Elige frutas y verduras sueltas en lugar de las preenvasadas.</li>
                <li>Busca alternativas sostenibles: Por ejemplo, compra leche en envases de vidrio retornable o snacks envasados en papel.</li>
            </ul>

            <h4>En la calle:</h4>
            <ul>
                <li>Lleva una botella reutilizable. Olvídate de comprar agua embotellada y lleva siempre contigo una botella de acero inoxidable o vidrio.</li>
                <li>Di "no gracias" a los popotes: Si realmente necesitas uno, opta por opciones de metal, bambú o silicona.</li>
                <li>Lleva tu propio kit reutilizable: Incluye utensilios, servilletas de tela y un recipiente pequeño para evitar usar plásticos en restaurantes o food trucks.</li>
            </ul>
        </section>
    </main>

    <!-- Footer convertido en section -->
    <section class="footer-section">
        <div class="contenedor-imagenes">
            <div class="texto">
                <p>Reducir el uso de plásticos no es solo una tendencia; es una necesidad urgente. No se trata de ser perfecto, sino de tomar pequeños pasos hacia un estilo de vida más consciente. Desde llevar tus propias bolsas al supermercado hasta decirle adiós a los popotes de plástico, cada acción cuenta. Juntos podemos construir un mundo donde el plástico no sea un problema, sino una excepción. ¿Qué cambio harás hoy?</p>
                <p><strong>Dato curioso:</strong> Se estima que cada año terminan en los océanos más de 8 millones de toneladas de plástico. ¡Reducir su uso puede salvar a miles de animales marinos!</p>
            </div>
            <div class="imagenes">
                <img src="/PRODCONS/PI2do/imagenes/bolsaProtcons.png" alt="Bolsa reutilizable Protcons">
                <div class="publicado">PUBLICADO EL 14 FEBRERO DEL 2025   |
                       POR JUAN PABLO MANCILLA RODRIGUEZ</div>
            </div>
        </div>
    </section>

    <!-- Nueva sección para el autor y bibliografías -->
    <section class="autor-bibliografias">
        <h3>Bibliografías</h3>
        <ul>
            <li>Ritchie, H., & Roser, M. (2018). Plastic pollution. Our World in Data. Recuperado de <a href="https://ourworldindata.org/plastic-pollution">https://ourworldindata.org/plastic-pollution</a></li>
            <li>Secretaría de Medio Ambiente y Recursos Naturales. (s.f.). Estrategias para reducir el uso de plásticos. Recuperado de <a href="https://www.gob.mx/semarnat">https://www.gob.mx/semarnat</a></li>
            <li>Greenpeace México. (s.f.). Cómo reducir el uso de plásticos de un solo uso. Recuperado de <a href="https://www.greenpeace.org/mexico">https://www.greenpeace.org/mexico</a></li>
            <li>Plastic Pollution Coalition. (s.f.). Guía para reducir el uso de plásticos. Recuperado de <a href="https://www.plasticpollutioncoalition.org">https://www.plasticpollutioncoalition.org</a></li>
            <li>OpenAI. (2023). ChatGPT (versión febrero 2023) Modelo de lenguaje. <a href="https://chat.openai.com">https://chat.openai.com</a></li>
        </ul>
    </section>

<!-- =====================================================================
SCRIPT PARA ACTUALIZAR BOTONES DE IDIOMA - NO MODIFICAR
Este script mantiene sincronizada la interfaz de idioma
 
- El botón X permite ocultar el selector de idioma cuando no se necesita
===================================================================== -->
<script>
    // Function to update button states based on current language    function updateLanguageButtons() {        const btnEs = document.getElementById('btn-es');        const btnEn = document.getElementById('btn-en');        const toggleText = document.getElementById('toggle-text');                // Get current language from localStorage or default to Spanish        const currentLang = localStorage.getItem('preferredLanguage') || 'es';                // Update active button        if (currentLang === 'en') {            btnEs.classList.remove('active');            btnEn.classList.add('active');            toggleText.innerText = 'Change language?';        } else {            btnEn.classList.remove('active');            btnEs.classList.add('active');            toggleText.innerText = '¿Cambiar idioma?';        }    }
    
    // Call this function on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateLanguageButtons();
        
        // Add event listener to update buttons when language changes
        const observer = new MutationObserver(function(mutations) {
            updateLanguageButtons();
        });
        
        // Start observing the document with the configured parameters
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['lang'] });
        
        // Add event listener for the close button
        document.getElementById('close-language-toggle').addEventListener('click', function() {
            document.getElementById('language-toggle').style.display = 'none';
        });
    });
    
    // Override the cambiarIdioma function to update button states
    const originalCambiarIdioma = window.cambiarIdioma;
    window.cambiarIdioma = function(idioma) {
        if (typeof originalCambiarIdioma === 'function') {
            originalCambiarIdioma(idioma);
        } else {
            // Fallback if the original function isn't available
            translateContent(idioma === 'ingles' ? 'en' : 'es');
        }
        
        // Update button states
        setTimeout(updateLanguageButtons, 100);
    };
</script>

<?php include '/xampp/htdocs/PRODCONS/PI2do/footer/Visitante/footer.php'; ?>
<script src='/PRODCONS/PI2do/header_post/header_post.js'></script>

</body>
</html>
>>>>>>> main
