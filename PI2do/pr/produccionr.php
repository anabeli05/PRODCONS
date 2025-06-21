<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Producción Responsable</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/PRODCONS/PI2do/pr/stylesprodr.css">
  <link rel="stylesheet" href="/PRODCONS/footer/footer/footer.css">
  <link rel="stylesheet" href="/PRODCONS/articulos.css">
  <link rel="stylesheet" href="/PRODCONS/PI2do/header_post/header_post.css">
  <link rel="stylesheet" href="/PRODCONS/PI2do/Carrusel/carrusel.css">
  
  <!-- Scripts de traducción -->
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
  <script src="/PRODCONS/translate.js"></script>
  
  <!-- Scripts del carrusel -->
  <script src="/PRODCONS/carousel.js"></script>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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

<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex-1">
        <header class="fixed w-full z-50">
            <div class="header-contenedor">
                <i class="flecha_left">
                    <a href="/PRODCONS/" title="Regresar a la página principal">
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

        <!-- Contenido principal -->
        <main class="mt-16">
            <!-- Contenedor principal -->
            <div class="flex flex-col md:flex-row w-full mt-6 box-border">
                <!-- Cuadro café -->
                <div style="background-color: #868278;" 
                     class="w-full md:w-1/2 text-white p-8 rounded-md flex flex-col justify-center">
                    <h2 class="text-3xl font-bold mb-6 leading-tight">LA PRODUCCIÓN RESPONSABLE</h2>
                    <p class="text-lg leading-relaxed">
                        Cada producto que consumimos tiene un costo ambiental que muchas veces no vemos. Desde la extracción de materiales hasta su fabricación y distribución, el impacto puede ser enorme si no se hace de manera responsable.
                    </p>
                    <p class="text-lg leading-relaxed mt-4">
                        La producción responsable implica adoptar prácticas que minimicen el impacto ambiental, promuevan el uso eficiente de recursos y garanticen condiciones laborales justas. Es fundamental para construir un futuro sostenible.
                    </p>
                </div>

                <!-- Imagen -->
                <div class="w-full md:w-1/2 flex items-center justify-center mt-6 md:mt-0">
                    <img src="/PRODCONS/PI2do/imagenes/produccion.png" alt="Producción Responsable" class="max-w-full h-auto rounded-md shadow-lg" />
                </div>
            </div>

<!-- Carrusel destacado -->
<section class="carrusel-destacado">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/PRODCONS/PI2do/Carrusel/carrusel.php'; ?>
</section>
    

    <h3 class="apubli"> MIRA MAS DE NUESTRO CONTENIDO </h3>

    <section class="post-list">
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'].'/PRODCONS/PI2do/Base de datos/conexion.php';
        
        // Función para traducir el nombre del mes a español
        function traducirMesEspanol($mesIngles) {
            $meses = [
                'January' => 'Enero',
                'February' => 'Febrero',
                'March' => 'Marzo',
                'April' => 'Abril',
                'May' => 'Mayo',
                'June' => 'Junio',
                'July' => 'Julio',
                'August' => 'Agosto',
                'September' => 'Septiembre',
                'October' => 'Octubre',
                'November' => 'Noviembre',
                'December' => 'Diciembre'
            ];
            return $meses[$mesIngles] ?? $mesIngles; // Devuelve el mes traducido o el original si no se encuentra
        }

        $conexion = new Conexion();
        $conexion->abrir_conexion();
        $conn = $conexion->conexion;

        // Obtener los posts publicados desde la base de datos
        $stmt = $conn->prepare("SELECT a.*, u.Nombre as autor_nombre, 
                               GROUP_CONCAT(ia.Url_Imagen) as imagenes
                               FROM articulos a 
                               JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                               LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
                               WHERE a.Estado = 'Publicado' 
                               GROUP BY a.ID_Articulo
                               ORDER BY a.`Fecha de Creacion` DESC");

        if (!$stmt) {
            die("Error en la preparación de la consulta de artículos: " . $conn->error);
        }

        $stmt->execute();
        $publicaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conexion->cerrar_conexion();

        foreach ($publicaciones as $post): 
            // Check if images data is available before exploding
            $imagenes_string = $post['imagenes'] ?? '';
            $imagenes = !empty($imagenes_string) ? explode(',', $imagenes_string) : [];
            $imagen_principal = !empty($imagenes[0]) ? $imagenes[0] : '/PRODCONS/PI2do/imagenes/default-post.jpg';
        ?>
            <article class="post" data-post-id="<?php echo htmlspecialchars($post['ID_Articulo'] ?? ''); ?>">
                <div class="post-header">
                    <img src="<?php echo htmlspecialchars($imagen_principal ?? ''); ?>" alt="<?php echo htmlspecialchars($post['Titulo'] ?? ''); ?>" class="post-img">
                </div>
                <div class="post-body">
                    <h2><?php echo htmlspecialchars($post['Titulo'] ?? ''); ?></h2>
                    <p class="descripcion"><?php 
                        $contenido = htmlspecialchars($post['Contenido'] ?? '');
                        // Truncar contenido a aproximadamente 100 caracteres si es más largo
                        if (strlen($contenido) > 100) {
                            $contenido = substr($contenido, 0, 401) . '...';
                        }
                        echo $contenido;
                    ?></p>
                    <a href="/PRODCONS/PI2do/postWeb/ver-articulo.php?id=<?php echo htmlspecialchars($post['ID_Articulo'] ?? ''); ?>" class="post-link">Leer más...</a>
                    <span>Publicado el <?php 
                         $fecha_timestamp = strtotime($post['Fecha de Publicacion'] ?? '');
                         if ($fecha_timestamp !== false) {
                             $dia = date('d', $fecha_timestamp);
                             $mes_ingles = date('F', $fecha_timestamp);
                             $mes_espanol = traducirMesEspanol($mes_ingles);
                             $año = date('Y', $fecha_timestamp);
                             echo htmlspecialchars("$dia de $mes_espanol de $año");
                         } else {
                             echo "Fecha desconocida";
                         }
                    ?></span>
                    <span> | Por   <?= htmlspecialchars($post['autor_nombre'] ?? '') ?></span>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
    </main>
    
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

        localStorage.setItem('preferredLanguage', idioma);
        translateContent(idioma);
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
        cambiarIdioma(nuevoIdioma);
    }

    // Función para normalizar y eliminar acentos
    function normalizeText(text) {
        return text.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    }

    // Función para escapar caracteres especiales de regex
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${"}]/g, '\\$&');
    }

    // Función para buscar artículos
    function buscarArticulos() {
        const busqueda = document.getElementById('busqueda').value.trim();
        const articles = document.querySelectorAll('.post');
        const resultados = [];

        if (!busqueda) {
            articles.forEach(article => article.style.display = '');
            return;
        }

        const busquedaNormalized = normalizeText(busqueda);
        const busquedaRegex = new RegExp(escapeRegExp(busquedaNormalized), 'i');

        articles.forEach(article => {
            const title = article.querySelector('h2');
            const description = article.querySelector('.descripcion');
            if (!title) return;

            const titleNormalized = normalizeText(title.textContent);
            const descriptionNormalized = description ? normalizeText(description.textContent) : '';

            const matchesTitle = busquedaRegex.test(titleNormalized);
            const matchesDescription = busquedaRegex.test(descriptionNormalized);

            if (matchesTitle || matchesDescription) {
                resultados.push(article);
                article.style.display = '';
            } else {
                article.style.display = 'none';
            }
        });

        // Si no hay resultados, mostrar mensaje
        const noResults = document.getElementById('noResults');
        if (noResults) {
            noResults.style.display = resultados.length === 0 ? 'block' : 'none';
        }
    }
</script>


<?php include $_SERVER['DOCUMENT_ROOT'].'/PRODCONS/footer/footer/footer.php'; ?>
<script src='/PRODCONS/Header visitantes/barra_principal.js'></script>

</body>
</html>