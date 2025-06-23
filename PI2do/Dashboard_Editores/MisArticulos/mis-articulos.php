<<<<<<< HEAD
<?php
session_start();
require_once '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../inicio_sesion/login.php');
    exit();
}

// Determinar el orden de los posts
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'desc';
$orden_sql = $orden === 'asc' ? 'ASC' : 'DESC';
$orden_icono = $orden === 'asc' ? '▲' : '▼';

// Conexión MySQLi
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

// Obtener los posts del usuario
try {
    $sql = "
        SELECT a.ID_Articulo, a.Titulo, a.Contenido, a.`Fecha de Creacion`, a.Estado,
               GROUP_CONCAT(ia.Url_Imagen) as imagenes
        FROM articulos a
        LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
        WHERE a.Usuario_ID = ?
        GROUP BY a.ID_Articulo
        ORDER BY a.`Fecha de Creacion` $orden_sql
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['Usuario_ID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    $error = 'Error al cargar los posts: ' . $e->getMessage();
}
$conexion->cerrar_conexion();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Mis Articulos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!--Javascript y Css HEADER-->
    <link href='../Dashboard/sidebar.css' rel="stylesheet">
    <script src='../Dashboard/barra-nav.js' defer></script>

    <!-- Tailwind CSS y font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- CSS de traduccion -->
    <link rel="stylesheet" href="../../Dashboard_Editores/Dashboard/traduccion.css">
    <!-- Scripts de traducción -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>
</head>
<body>
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

                    <a href="../Crear nuevo post/formulario-new-post.php">
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

    <!-- MIs Articulos -->
    <div class="font-sans m-20 bg-[#fdfdfd]">
        <div class="flex justify-between items-center mb-5">
            <h1 class="text-[28px] font-semibold m-0">Mis Artículos</h1>
            <a href="/PRODCONS/PI2do/Dashboard_Editores/Crear nuevo post/formulario-new-post.php" class="flex mr-5 items-center gap-2 bg-[#3F6B55] text-white text-[15px] font-bold italic py-2 px-5 rounded-[12px] cursor-pointer hover:bg-[#2F5443]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Crear Nuevo Post
            </a>
        </div>
        <div class="flex justify-end mt-[10px] mb-5 ">
            <a href="?orden=<?php echo $orden === 'desc' ? 'asc' : 'desc'; ?>" class="flex items-center gap-1 rounded-[12px] bg-[#a6d78e] text-black font-bold text-[16px] py-3 px-5 cursor-pointer hover:bg-[#5f9f51] mr-5">
                ORDENAR POR RECIENTES <span class="text-[10px]  transition-transform hover:scale-110"><?php echo $orden_icono; ?></span>
            </a>
        </div>        
        
        <div class="max-w-[900px] mx-auto">
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="bg-[#dff0d8] text-[#3c763d] p-4 rounded mb-4">
                    <?php 
                    echo $_SESSION['mensaje'];
                    unset($_SESSION['mensaje']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-[#f2dede] text-[#a94442] p-4 rounded mb-4">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="grid-articulos">
                <?php if (empty($posts)): ?>
                    <div class="text-2xl text-center mt-5 text-gray-600">
                        <p class="text-[#666] mb-4">No tienes artículos publicados aún.</p>
                        <a href="formulario-new-post.php" class="inline-block px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700 transition">Crear mi primer artículo</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <article class="bg-[#fceee3] rounded-[12px] p-5 flex w-full max-w-[700px] shadow-md mb-3 gap-3">
                            <div class="mt-4 w-[200px] h-[140px] flex-shrink-0">
                                <?php if ($post['imagenes']): ?>
                                    <img src="<?php echo '/PRODCONS/PI2do/imagenes/articulos/' . explode(',', $post['imagenes'])[0]; ?>" alt="<?php echo htmlspecialchars($post['Titulo']); ?>" class="w-full h-full object-cover rounded-md">
                                <?php else: ?>
                                    <div class="w-full h-full bg-[#f5f5f5] flex items-center justify-center text-[#666]">Sin imagen</div>
                                <?php endif; ?>
                            </div>
                            <div class="p-6">
                                <h2 class="m-0 text-[18px] font-bold font-serif"><?php echo htmlspecialchars($post['Titulo']); ?></h2>
                                <p class="text-[12px] text-gray-500">
                                    <?php
                                    if (!empty($post['Fecha de Creacion'])) {
                                        echo date('d/m/Y', strtotime($post['Fecha de Creacion']));
                                    } else {
                                        echo 'Fecha no disponible'; // O un mensaje similar
                                    }
                                    ?>
                                </p>
                                <p class="text-[14px] text-gray-700"><?php echo substr(strip_tags($post['Contenido']), 0, 150) . '...'; ?></p>
                                <a href="/PRODCONS/PI2do/postWeb/ver-articulo-usuario.php?ID_Articulo=<?php echo $post['ID_Articulo']; ?>" class="text-black italic font-bold rounded text-[13px] hover:text-[#2F5443] transition cursor-pointer">Ver más...</a>
                                <div class="flex gap-4 mt-3">
                                    <a href="editar-post.php?id=<?php echo $post['ID_Articulo']; ?>" class="px-4 py-2 bg-blue-600 font-semibold text-white rounded text-[10px] hover:bg-blue-700 transition">Editar</a>
                                    <button onclick="confirmarEliminar(<?php echo $post['ID_Articulo']; ?>)" class="px-4 py-2 bg-red-600 font-semibold text-white rounded text-[10px] hover:bg-red-700 transition">Eliminar</button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="flex items-end gap-[120px] mt-10 max-w-[895px] relative">
                <div class="w-[120%] bg-[#ece9e5] mr-[35px] rounded-[12px] p-[25px_40px] pt-[60px] relative flex-grow min-h-[150px] flex flex-col justify-center items-center">
                    <p class="m-0 mb-5 text-[15px] text-gray-800 max-w-[80%] z-[1] text-center">Sigue creando post, informa al mundo de los nuevos sucesos!</p>
                    <a href='../Crear nuevo post/formulario-new-post.php' class="pb-5 bg-[#b1dcaa] rounded-full w-[50px] h-[50px] text-[50px] font-bold text-green-900 cursor-pointer z-[2] transition-transform duration-200 flex items-center justify-center hover:scale-110">+</a>
                    <img src='/PRODCONS/PI2do/imagenes/plantita.png' class="absolute z-0 left-[15px] top-[25px]" width="80">
                    <img src='/PRODCONS/PI2do/imagenes/planta.png' class="absolute z-0 right-[15px] top-[25px]" width="80">
                </div>
                    <img src='/PRODCONS/PI2do/imagenes/maceta-verde.png' class="w-[125px] -mr-[90px]" width="80">
            </div>
            
        </div>
    </div>  
    <script>
        function confirmarEliminar(postId) {
            if (confirm('¿Estás seguro de que quieres eliminar este artículo? Esta acción no se puede deshacer.')) {
                window.location.href = `eliminar-post.php?id=${postId}`;
            }
        }
    </script>
</body>
=======
<?php
session_start();
require_once '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../inicio_sesion/login.php');
    exit();
}

// Determinar el orden de los posts
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'desc';
$orden_sql = $orden === 'asc' ? 'ASC' : 'DESC';
$orden_icono = $orden === 'asc' ? '▲' : '▼';

// Conexión MySQLi
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

// Obtener los posts del usuario
try {
    $sql = "
        SELECT a.ID_Articulo, a.Titulo, a.Contenido, a.`Fecha de Creacion`, a.Estado,
               GROUP_CONCAT(ia.Url_Imagen) as imagenes
        FROM articulos a
        LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
        WHERE a.Usuario_ID = ?
        GROUP BY a.ID_Articulo
        ORDER BY a.`Fecha de Creacion` $orden_sql
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['Usuario_ID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    $error = 'Error al cargar los posts: ' . $e->getMessage();
}
$conexion->cerrar_conexion();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Mis Articulos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!--Javascript y Css HEADER-->
    <link href='../Dashboard/sidebar.css' rel="stylesheet">
    <script src='../Dashboard/barra-nav.js' defer></script>

    <!-- Tailwind CSS y font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- CSS de traduccion -->
    <link rel="stylesheet" href="../../Dashboard_Editores/Dashboard/traduccion.css">
    <!-- Scripts de traducción -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>
</head>
<body>
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

                    <a href="../Crear nuevo post/formulario-new-post.php">
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

    <!-- MIs Articulos -->
    <div class="font-sans m-20 bg-[#fdfdfd]">
        <div class="flex justify-between items-center mb-5">
            <h1 class="text-[28px] font-semibold m-0">Mis Artículos</h1>
            <a href="/PRODCONS/PI2do/Dashboard_Editores/Crear nuevo post/formulario-new-post.php" class="flex mr-5 items-center gap-2 bg-[#3F6B55] text-white text-[15px] font-bold italic py-2 px-5 rounded-[12px] cursor-pointer hover:bg-[#2F5443]">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Crear Nuevo Post
            </a>
        </div>
        <div class="flex justify-end mt-[10px] mb-5 ">
            <a href="?orden=<?php echo $orden === 'desc' ? 'asc' : 'desc'; ?>" class="flex items-center gap-1 rounded-[12px] bg-[#a6d78e] text-black font-bold text-[16px] py-3 px-5 cursor-pointer hover:bg-[#5f9f51] mr-5">
                ORDENAR POR RECIENTES <span class="text-[10px]  transition-transform hover:scale-110"><?php echo $orden_icono; ?></span>
            </a>
        </div>        
        
        <div class="max-w-[900px] mx-auto">
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="bg-[#dff0d8] text-[#3c763d] p-4 rounded mb-4">
                    <?php 
                    echo $_SESSION['mensaje'];
                    unset($_SESSION['mensaje']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-[#f2dede] text-[#a94442] p-4 rounded mb-4">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="grid-articulos">
                <?php if (empty($posts)): ?>
                    <div class="text-2xl text-center mt-5 text-gray-600">
                        <p class="text-[#666] mb-4">No tienes artículos publicados aún.</p>
                        <a href="formulario-new-post.php" class="inline-block px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700 transition">Crear mi primer artículo</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <article class="bg-[#fceee3] rounded-[12px] p-5 flex w-full max-w-[700px] shadow-md mb-3 gap-3">
                            <div class="mt-4 w-[200px] h-[140px] flex-shrink-0">
                                <?php if ($post['imagenes']): ?>
                                    <img src="<?php echo '/PRODCONS/PI2do/imagenes/articulos/' . explode(',', $post['imagenes'])[0]; ?>" alt="<?php echo htmlspecialchars($post['Titulo']); ?>" class="w-full h-full object-cover rounded-md">
                                <?php else: ?>
                                    <div class="w-full h-full bg-[#f5f5f5] flex items-center justify-center text-[#666]">Sin imagen</div>
                                <?php endif; ?>
                            </div>
                            <div class="p-6">
                                <h2 class="m-0 text-[18px] font-bold font-serif"><?php echo htmlspecialchars($post['Titulo']); ?></h2>
                                <p class="text-[12px] text-gray-500">
                                    <?php
                                    if (!empty($post['Fecha de Creacion'])) {
                                        echo date('d/m/Y', strtotime($post['Fecha de Creacion']));
                                    } else {
                                        echo 'Fecha no disponible'; // O un mensaje similar
                                    }
                                    ?>
                                </p>
                                <p class="text-[14px] text-gray-700"><?php echo substr(strip_tags($post['Contenido']), 0, 150) . '...'; ?></p>
                                <a href="/PRODCONS/PI2do/postWeb/ver-articulo-usuario.php?ID_Articulo=<?php echo $post['ID_Articulo']; ?>" class="text-black italic font-bold rounded text-[13px] hover:text-[#2F5443] transition cursor-pointer">Ver más...</a>
                                <div class="flex gap-4 mt-3">
                                    <a href="editar-post.php?id=<?php echo $post['ID_Articulo']; ?>" class="px-4 py-2 bg-blue-600 font-semibold text-white rounded text-[10px] hover:bg-blue-700 transition">Editar</a>
                                    <button onclick="confirmarEliminar(<?php echo $post['ID_Articulo']; ?>)" class="px-4 py-2 bg-red-600 font-semibold text-white rounded text-[10px] hover:bg-red-700 transition">Eliminar</button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="flex items-end gap-[120px] mt-10 max-w-[895px] relative">
                <div class="w-[120%] bg-[#ece9e5] mr-[35px] rounded-[12px] p-[25px_40px] pt-[60px] relative flex-grow min-h-[150px] flex flex-col justify-center items-center">
                    <p class="m-0 mb-5 text-[15px] text-gray-800 max-w-[80%] z-[1] text-center">Sigue creando post, informa al mundo de los nuevos sucesos!</p>
                    <a href='../Crear nuevo post/formulario-new-post.php' class="pb-5 bg-[#b1dcaa] rounded-full w-[50px] h-[50px] text-[50px] font-bold text-green-900 cursor-pointer z-[2] transition-transform duration-200 flex items-center justify-center hover:scale-110">+</a>
                    <img src='/PRODCONS/PI2do/imagenes/plantita.png' class="absolute z-0 left-[15px] top-[25px]" width="80">
                    <img src='/PRODCONS/PI2do/imagenes/planta.png' class="absolute z-0 right-[15px] top-[25px]" width="80">
                </div>
                    <img src='/PRODCONS/PI2do/imagenes/maceta-verde.png' class="w-[125px] -mr-[90px]" width="80">
            </div>
            
        </div>
    </div>  
    <script>
        function confirmarEliminar(postId) {
            if (confirm('¿Estás seguro de que quieres eliminar este artículo? Esta acción no se puede deshacer.')) {
                window.location.href = `eliminar-post.php?id=${postId}`;
            }
        }
    </script>
</body>
>>>>>>> main
</html>