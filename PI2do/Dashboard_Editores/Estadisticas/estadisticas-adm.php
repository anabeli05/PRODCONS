<<<<<<< HEAD
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Agregar verificación de rol
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'Editor') {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Base de datos/conexion.php';

// Crear una instancia de la clase Conexion
$conexion = new Conexion();

// Obtener el ID del editor logueado
$editor_id = $_SESSION['Usuario_ID'];

// Validar que editor_id sea numérico
if (!is_numeric($editor_id)) {
    $_SESSION['error'] = "ID de editor inválido";
    header('Location: error.php');
    exit();
}

// Inicializar variables
$totalPosts = 0;
$totalVisitas = 0;
$totalLikes = 0;
$totalComentarios = 0;
$postsPopulares = [];

// Consulta para obtener estadísticas de los posts del editor
try {
    // Abrir conexión
    $conexion->abrir_conexion();
    
    // Total de posts del editor
    $sqlPosts = "SELECT COUNT(*) as total FROM articulos WHERE Usuario_ID = ?";
    $stmt = $conexion->conexion->prepare($sqlPosts);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultPosts = $stmt->get_result();
    $totalPosts = $resultPosts->fetch_assoc()['total'];
    
    // Total de visitas de los posts del editor
    $sqlVisitas = "SELECT COUNT(*) as total_visitas FROM estadisticas_vistas sv INNER JOIN articulos a ON sv.Articulo_ID = a.ID_Articulo WHERE a.Usuario_ID = ?";
    $stmt = $conexion->conexion->prepare($sqlVisitas);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultVisitas = $stmt->get_result();
    $totalVisitas = $resultVisitas->fetch_assoc()['total_visitas'] ?? 0;
    
    // Total de likes de los posts del editor
    $sqlLikes = "SELECT COUNT(*) as total_likes FROM likes l 
                 INNER JOIN articulos a ON l.Articulo_ID = a.ID_Articulo 
                 WHERE a.Usuario_ID = ?";
    $stmt = $conexion->conexion->prepare($sqlLikes);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultLikes = $stmt->get_result();
    $totalLikes = $resultLikes->fetch_assoc()['total_likes'];
    
    // Total de comentarios en los posts del editor
    $sqlComentarios = "SELECT COUNT(*) as total_comentarios FROM comentarios_autor c 
                      INNER JOIN articulos a ON c.Articulo_ID = a.ID_Articulo 
                      WHERE a.Usuario_ID = ?";
    $stmt = $conexion->conexion->prepare($sqlComentarios);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultComentarios = $stmt->get_result();
    $totalComentarios = $resultComentarios->fetch_assoc()['total_comentarios'];
    
    // Obtener posts más populares del editor
    // Calcular visitas de estadisticas_vistas usando un subquery
    $sqlPopulares = "SELECT a.ID_Articulo, a.Titulo, a.`Fecha de Publicacion`, COALESCE(v.visitas, 0) as visitas
                     FROM articulos a
                     LEFT JOIN (
                         SELECT Articulo_ID, COUNT(Vista_ID) as visitas
                         FROM estadisticas_vistas
                         GROUP BY Articulo_ID
                     ) v ON a.ID_Articulo = v.Articulo_ID
                     WHERE a.Usuario_ID = ?
                     ORDER BY visitas DESC
                     LIMIT 4";
    $stmt = $conexion->conexion->prepare($sqlPopulares);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultPopulares = $stmt->get_result();
    $postsPopulares = $resultPopulares->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    $_SESSION['error'] = "Error al obtener estadísticas: " . $e->getMessage();
    // header('Location: error.php');
    // exit();
    echo "<div class=\'error-message\'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit();
} finally {
    // Cerrar conexión
    if (isset($conexion)) {
        $conexion->cerrar_conexion();
    }
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>PRODCONS - Mis Estadísticas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="estadisticas-adm.css" />
    <link rel="stylesheet" href='../Dashboard/sidebar.css'>
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
            <a href="../inicio/inicio.php">
                <img class="prodcons" src='../../imagenes/prodcon/logoSinfondo.png' alt="Logo">
            </a>

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
                    <a href='../inicio/inicio.php'>
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

    <div class="contenedor-estadisticas">
        <div class="titulo-estadisticas"><h1>Mis Estadísticas</h1></div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <section class="statistics">
            <div class="stat">
                <h1><i class="fas fa-file-alt"></i> Mis Posts</h1>
                <p class="stat-number"><?php echo number_format($totalPosts, 0, ',', '.'); ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-eye"></i> Vistas Totales</h1>
                <p class="stat-number"><?php echo number_format($totalVisitas, 0, ',', '.'); ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-heart"></i> Likes Totales</h1>
                <p class="stat-number"><?php echo number_format($totalLikes, 0, ',', '.'); ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-comments"></i> Comentarios Totales</h1>
                <p class="stat-number"><?php echo number_format($totalComentarios, 0, ',', '.'); ?></p>
            </div>
        </section>

        <section class="popular-posts">
            <h2>Posts Más Populares</h2>
            <div class="posts-grid">
                <?php foreach ($postsPopulares as $index => $post): ?>
                    <div class="post-card">
                        <div class="post-number"><?php echo sprintf('%02d', $index + 1); ?></div>
                        <div class="post-details">
                            <!-- Espacio para la imagen -->
                            <div class="post-image"><!-- Imagen del artículo si está disponible --></div>
                            <div class="post-info">
                                <h3><?php echo htmlspecialchars($post['Titulo']); ?></h3>
                                <p class="publish-date">Publicado el: <?php echo htmlspecialchars(date('d M Y', strtotime($post['Fecha de Publicacion']))); ?></p>
                                <p class="views"><i class="fas fa-eye"></i> <?php echo number_format($post['visitas'], 0, ',', '.'); ?> vistas</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</body>
=======
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Agregar verificación de rol
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'Editor') {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Base de datos/conexion.php';

// Crear una instancia de la clase Conexion
$conexion = new Conexion();

// Obtener el ID del editor logueado
$editor_id = $_SESSION['Usuario_ID'];

// Validar que editor_id sea numérico
if (!is_numeric($editor_id)) {
    $_SESSION['error'] = "ID de editor inválido";
    header('Location: error.php');
    exit();
}

// Inicializar variables
$totalPosts = 0;
$totalVisitas = 0;
$totalLikes = 0;
$totalComentarios = 0;
$postsPopulares = [];

// Consulta para obtener estadísticas de los posts del editor
try {
    // Abrir conexión
    $conexion->abrir_conexion();
    
    // Total de posts del editor
    $sqlPosts = "SELECT COUNT(*) as total FROM articulos WHERE Usuario_ID = ?";
    $stmt = $conexion->conexion->prepare($sqlPosts);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultPosts = $stmt->get_result();
    $totalPosts = $resultPosts->fetch_assoc()['total'];
    
    // Total de visitas de los posts del editor
    $sqlVisitas = "SELECT COUNT(*) as total_visitas FROM estadisticas_vistas sv INNER JOIN articulos a ON sv.Articulo_ID = a.ID_Articulo WHERE a.Usuario_ID = ?";
    $stmt = $conexion->conexion->prepare($sqlVisitas);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultVisitas = $stmt->get_result();
    $totalVisitas = $resultVisitas->fetch_assoc()['total_visitas'] ?? 0;
    
    // Total de likes de los posts del editor
    $sqlLikes = "SELECT COUNT(*) as total_likes FROM likes l 
                 INNER JOIN articulos a ON l.Articulo_ID = a.ID_Articulo 
                 WHERE a.Usuario_ID = ?";
    $stmt = $conexion->conexion->prepare($sqlLikes);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultLikes = $stmt->get_result();
    $totalLikes = $resultLikes->fetch_assoc()['total_likes'];
    
    // Total de comentarios en los posts del editor
    $sqlComentarios = "SELECT COUNT(*) as total_comentarios FROM comentarios_autor c 
                      INNER JOIN articulos a ON c.Articulo_ID = a.ID_Articulo 
                      WHERE a.Usuario_ID = ?";
    $stmt = $conexion->conexion->prepare($sqlComentarios);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultComentarios = $stmt->get_result();
    $totalComentarios = $resultComentarios->fetch_assoc()['total_comentarios'];
    
    // Obtener posts más populares del editor
    // Calcular visitas de estadisticas_vistas usando un subquery
    $sqlPopulares = "SELECT a.ID_Articulo, a.Titulo, a.`Fecha de Publicacion`, COALESCE(v.visitas, 0) as visitas
                     FROM articulos a
                     LEFT JOIN (
                         SELECT Articulo_ID, COUNT(Vista_ID) as visitas
                         FROM estadisticas_vistas
                         GROUP BY Articulo_ID
                     ) v ON a.ID_Articulo = v.Articulo_ID
                     WHERE a.Usuario_ID = ?
                     ORDER BY visitas DESC
                     LIMIT 4";
    $stmt = $conexion->conexion->prepare($sqlPopulares);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultPopulares = $stmt->get_result();
    $postsPopulares = $resultPopulares->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    $_SESSION['error'] = "Error al obtener estadísticas: " . $e->getMessage();
    // header('Location: error.php');
    // exit();
    echo "<div class=\'error-message\'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit();
} finally {
    // Cerrar conexión
    if (isset($conexion)) {
        $conexion->cerrar_conexion();
    }
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>PRODCONS - Mis Estadísticas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="estadisticas-adm.css" />
    <link rel="stylesheet" href='../Dashboard/sidebar.css'>
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
            <a href="../inicio/inicio.php">
                <img class="prodcons" src='../../imagenes/prodcon/logoSinfondo.png' alt="Logo">
            </a>

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
                    <a href='../inicio/inicio.php'>
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

    <div class="contenedor-estadisticas">
        <div class="titulo-estadisticas"><h1>Mis Estadísticas</h1></div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <section class="statistics">
            <div class="stat">
                <h1><i class="fas fa-file-alt"></i> Mis Posts</h1>
                <p class="stat-number"><?php echo number_format($totalPosts, 0, ',', '.'); ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-eye"></i> Vistas Totales</h1>
                <p class="stat-number"><?php echo number_format($totalVisitas, 0, ',', '.'); ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-heart"></i> Likes Totales</h1>
                <p class="stat-number"><?php echo number_format($totalLikes, 0, ',', '.'); ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-comments"></i> Comentarios Totales</h1>
                <p class="stat-number"><?php echo number_format($totalComentarios, 0, ',', '.'); ?></p>
            </div>
        </section>

        <section class="popular-posts">
            <h2>Posts Más Populares</h2>
            <div class="posts-grid">
                <?php foreach ($postsPopulares as $index => $post): ?>
                    <div class="post-card">
                        <div class="post-number"><?php echo sprintf('%02d', $index + 1); ?></div>
                        <div class="post-details">
                            <!-- Espacio para la imagen -->
                            <div class="post-image"><!-- Imagen del artículo si está disponible --></div>
                            <div class="post-info">
                                <h3><?php echo htmlspecialchars($post['Titulo']); ?></h3>
                                <p class="publish-date">Publicado el: <?php echo htmlspecialchars(date('d M Y', strtotime($post['Fecha de Publicacion']))); ?></p>
                                <p class="views"><i class="fas fa-eye"></i> <?php echo number_format($post['visitas'], 0, ',', '.'); ?> vistas</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</body>
>>>>>>> main
</html>