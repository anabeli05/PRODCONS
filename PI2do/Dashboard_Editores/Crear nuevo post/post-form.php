<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion.php'; // Ruta absoluta a tu archivo de conexión

// You can add your database interaction code here, for example, to handle form submissions.

//function rgbToName($rgbInput) {
// Diccionario básico de colores
//   $colorNames = [
//     "black" => "rgb(0, 0, 0)",
//     "white" => "rgb(255, 255, 255)",
//     "red" => "rgb(255, 0, 0)",
//     "lime" => "rgb(0, 255, 0)",
//     "blue" => "rgb(0, 0, 255)",
//     "yellow" => "rgb(255, 255, 0)",
//     "cyan" => "rgb(0, 255, 255)",
//     "magenta" => "rgb(255, 0, 255)",
//     "silver" => "rgb(192, 192, 192)",
//     "gray" => "rgb(128, 128, 128)",
//     "maroon" => "rgb(128, 0, 0)",
//     "olive" => "rgb(128, 128, 0)",
//     "green" => "rgb(0, 128, 0)",
//     "purple" => "rgb(128, 0, 128)",
//     "teal" => "rgb(0, 128, 128)",
//     "navy" => "rgb(0, 0, 128)"
//  ];

//  $rgbInput = str_replace(' ', '', strtolower($rgbInput)); // Normaliza

//  foreach ($colorNames as $name => $rgb) {
//    if (str_replace(' ', '', strtolower($rgb)) === $rgbInput) {
//      return $name;
//    }
//  }

//  return "desconocido";
//}
?>
<?php
//if ($_SERVER["REQUEST_METHOD"] === "POST") {
//  $rgb = $_POST['color']; // Por ejemplo: "rgb(255, 0, 0)"
//
//  require_once 'colores.php'; // Donde está la función rgbToName()
//
//  $nombreColor = rgbToName($rgb);
//
//  echo "El color es: $rgb y su nombre es: $nombreColor";

  // Luego puedes guardarlo en la base de datos si lo deseas
//}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Nuevo Post</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../Crear nuevo Post/post-form.css">
  
  <!--herramientas QUILLJS-->
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

  <!-- CSS Y JS DE HEADER-->
  <link href='../Dashboard/sidebar.css' rel="stylesheet">
  <script src='../Dashboard/barra-nav.js' defer></script>
  <!-- Tailwind CSS-->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- CSS de traduccion -->
  <link rel="stylesheet" href="../../Dashboard_Editores/Dashboard/traduccion.css">
  <!-- Scripts de traducción -->
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
  <script src="/PRODCONS/translate.js"></script>
</head>
<body> 
    <header>
        <a href="javascript:history.back()" title="Regresar a la página principal" class="flex m-6 pl-4 ">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" 
            class="w-10 h-10 fill-current text-gray-700 hover:text-green-600 transition-colors duration-300">
            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>
        </a>
        <div class="header-contenedor">
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

    <section class="logo"> 
        <div class="header_2">
            <img class="prodcons" src='../../imagenes/prodcon/logoSinfondo.png' alt="Logo">

            <div class="admin-controls">
                <button class="search-toggle-btn"></button>
                <div class="search-bar hidden">
                    <button class="search-close-btn"></button>
                </div>
                <!--Botón de notificaciones-->
                <a href='/PRODCONS/PI2do/Dashboard_Editores/Notibox/noti-box.php' class="notif-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notif-badge"></span>
                </a>

                <!-- Botón Admin con avatar -->
                <div class="admin-btn">
                    <span><?php echo htmlspecialchars($_SESSION['Nombre'] ?? 'Admin'); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 11l8 3l8 -3" />
                    </svg>
                    <img src='/PRODCONS/PI2do/imagenes/logos/perfil.png' alt="Admin" class="admin-avatar">
                </div>
            </div>

            <!-- Sidebar -->
            <div class="admin-sidebar" id="adminSidebar">
                <div class="sidebar-header">
                    <h3>ADMIN</h3>
                    <button class="close-sidebar">
                        <img src='../../imagenes/logos/perfil.png' alt="Admin" class="admin-avatar">
                    </button>
                </div>
                
                <nav class="sidebar-menu">

                <a href='../inicio/inicio.php'><!----cambiar la ruta a inicio---->
                        <span>Inicio</span>
                        <i class="fas fa-file-alt"></i>
                    </a>

                <a href='../MisArticulos/mis-articulos.php'>
                        <span>Mis Artículos</span>
                        <i class="fas fa-file-alt"></i>
                    </a>

                    <a href="../Crear nuevo post/post-form.html">
                        <span>Crear Post</span>
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href='../PostPlaneados/post-planeados.php'>
                        <span>Post Planeados</span>
                        <i class="fas fa-calendar"></i>
                    </a>
                                        
                    <a href='../Estadisticas/estadisticas-adm.php'>
                        <span>Estadísticas</span>
                        <i class="fas fa-chart-bar"></i>
                    </a>
                    
                    <a href='../Configuracion/configuracion.php'>
                        <span>Configuración</span>
                        <i class="fas fa-cog"></i>
                    </a>
                
                <div class="sidebar-footer">
                    <a href='../../inicio_sesion/logout.php' class="logout-btn">
                        Cerrar Sesión
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
                </nav>
            </div>
        </div>
    </section>
    
<div class="contenedor-formulario">
  <h1 class="text-[20px] font-semibold mb-5">Crear Nuevo Post</h1>

  <form id="form-post" onsubmit="return guardarContenido()" action="/guardar-post" method="POST" enctype="multipart/form-data">

    <!-- Sección 1: Introducción -->
    <div class="seccion intro">
      <h2 class="text-[12px]">Introducción del Artículo</h2>

      <label for="intro_imagen">Imagen:</label>
      <input type="file" id="intro_imagen" name="intro_imagen" accept="image/*" required>

      <label for="intro_titulo">Título:</label>
      <input type="text" id="intro_titulo" name="intro_titulo" required>

      <label for="intro_editor">Texto de Introducción:</label>
      <div id="intro_editor" class="quill-editor"></div>
      <input type="hidden" name="intro_texto" id="intro_texto_hidden">

      <label for="intro_color">Color de Fondo:</label>
      <input type="color" id="intro_color" name="intro_color" value="#ffffff" required>
    </div>

<!-- Sección 2: Contenido -->
<div class="seccion">
  <h2 class="text-[12px]">Contenido del Artículo</h2>
  <div id="contenedores">
    <!-- Primer bloque obligatorio -->
    <div class="bloque">
      <button type="button" class="eliminar-bloque" onclick="this.parentElement.remove()">❌</button>

      <label>Título:</label>
      <input type="text" name="titulo_bloques[]" required>

      <label>Subtítulo:</label>
      <input type="text" name="subtitulo_bloques[]" required>

      <label>Contenido:</label>
      <div class="quill-editor" id="bloque_editor_0"></div>
      <input type="hidden" name="contenido_bloques[]" id="bloque_hidden_0">
    </div>
  </div>

  <button type="button" onclick="agregarBloque()">+ Agregar otro bloque</button>
</div>

    <!-- Sección 3: Conclusión -->
    <div class="seccion">
      <h2 class="text-[12px]">Conclusión</h2>
      <div id="conclusion_editor" class="quill-editor"></div>
      <input type="hidden" name="conclusion">
    </div>

    <!-- Sección 4: Bibliografía -->
    <div class="seccion">
      <h2 class="text-[12px]">Bibliografía</h2>
      <div id="bibliografia_editor" class="quill-editor"></div>
      <input type="hidden" name="bibliografia">
    </div>

    <button type="submit" class="submit-btn">Publicar Artículo</button>
  </form>

<!--JavaScript-->
<script>
  let editors = {}; // Para guardar instancias de Quill
  let bloqueCount = 1;

  // Inicializar editores
  const initQuill = (id) => {
    return new Quill(`#${id}`, {
      theme: 'snow',
      placeholder: 'Escribe aquí...',
      modules: {
        toolbar: [
          ['bold', 'italic', 'underline'],
          [{ 'color': [] }, { 'background': [] }],
          [{ 'list': 'ordered' }, { 'list': 'bullet' }],
          ['link', 'image']
        ]
      }
    });
  };

  // Editores estáticos
  editors.intro = initQuill('intro_editor');
  editors.conclusion = initQuill('conclusion_editor');
  editors.bibliografia = initQuill('bibliografia_editor');
  editors["bloque_0"] = initQuill('bloque_editor_0');

  function agregarBloque() {
  const contenedor = document.getElementById("contenedores");
  const index = bloqueCount++;

  const bloque = document.createElement("div");
  bloque.className = "bloque";
  bloque.innerHTML = `
    <button type="button" class="eliminar-bloque" onclick="this.parentElement.remove()">❌</button>

    <label>Título:</label>
    <input type="text" name="titulo_bloques[]">

    <label>Subtítulo:</label>
    <input type="text" name="subtitulo_bloques[]">

    <label>Contenido:</label>
    <div class="quill-editor" id="bloque_editor_${index}"></div>
    <input type="hidden" name="contenido_bloques[]" id="bloque_hidden_${index}">
  `;
  contenedor.appendChild(bloque);

  editors[`bloque_${index}`] = initQuill(`bloque_editor_${index}`);
}

  function guardarContenido() {
    // Extraer contenido del editor de introducción
    document.getElementById("intro_texto_hidden").value = editors.intro.root.innerHTML;

    // Conclusión y bibliografía
    document.querySelector('[name="conclusion"]').value = editors.conclusion.root.innerHTML;
    document.querySelector('[name="bibliografia"]').value = editors.bibliografia.root.innerHTML;

    // Bloques dinámicos
    Object.keys(editors).forEach((key) => {
      if (key.startsWith("bloque_")) {
        const index = key.split("_")[1];
        document.getElementById(`bloque_hidden_${index}`).value = editors[key].root.innerHTML;
      }
    });
    return true;
  }
</script>


</body>
</html>
