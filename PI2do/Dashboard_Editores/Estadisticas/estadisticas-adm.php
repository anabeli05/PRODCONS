<?php
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

// Consulta para obtener estadísticas de los posts del editor
try {
    // Abrir conexión
    $conexion->abrir_conexion();
    
    // Total de posts del editor
    $sqlPosts = "SELECT COUNT(*) as total FROM posts WHERE editor_id = ?";
    $conexion->sentencia = $sqlPosts;
    $stmt = $conexion->conexion->prepare($sqlPosts);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultPosts = $stmt->get_result();
    $totalPosts = $resultPosts->fetch_assoc()['total'];
    
    // Total de visitas de los posts del editor
    $sqlVisitas = "SELECT SUM(visitas) as total_visitas FROM posts WHERE editor_id = ?";
    $conexion->sentencia = $sqlVisitas;
    $stmt = $conexion->conexion->prepare($sqlVisitas);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultVisitas = $stmt->get_result();
    $totalVisitas = $resultVisitas->fetch_assoc()['total_visitas'] ?? 0;
    
    // Total de likes de los posts del editor
    $sqlLikes = "SELECT COUNT(*) as total_likes FROM likes l 
                 INNER JOIN posts p ON l.post_id = p.id 
                 WHERE p.editor_id = ?";
    $conexion->sentencia = $sqlLikes;
    $stmt = $conexion->conexion->prepare($sqlLikes);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultLikes = $stmt->get_result();
    $totalLikes = $resultLikes->fetch_assoc()['total_likes'];
    
    // Total de comentarios en los posts del editor
    $sqlComentarios = "SELECT COUNT(*) as total_comentarios FROM comentarios c 
                      INNER JOIN posts p ON c.post_id = p.id 
                      WHERE p.editor_id = ?";
    $conexion->sentencia = $sqlComentarios;
    $stmt = $conexion->conexion->prepare($sqlComentarios);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultComentarios = $stmt->get_result();
    $totalComentarios = $resultComentarios->fetch_assoc()['total_comentarios'];
    
    // Obtener posts más populares del editor
    $sqlPopulares = "SELECT titulo, visitas FROM posts 
                     WHERE editor_id = ? 
                     ORDER BY visitas DESC LIMIT 4";
    $conexion->sentencia = $sqlPopulares;
    $stmt = $conexion->conexion->prepare($sqlPopulares);
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $resultPopulares = $stmt->get_result();
    $postsPopulares = $resultPopulares->fetch_all(MYSQLI_ASSOC);
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    header('Location: error.php');
    exit();
} catch (Exception $e) {
    $_SESSION['error'] = "Error general: " . $e->getMessage();
    header('Location: error.php');
    exit();
} finally {
    // Cerrar conexión
    $conexion->cerrar_conexion();
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
    <link rel="stylesheet" href="../Estadisticas/estadisticas-adm.css" />

    <!-- CSS Y JS DE HEADER-->
    <link rel="stylesheet" href='../Dashboard/sidebar.css'>
    <script src='../Dashboard/barra-nav.js' defer></script>
    <style>

    </style>
</head>
<body>
<body>
    <header> 
        <div class="header-contenedor">
            <div class="principal"></div>
        </div>
    </header>

    <section class="logo"> 
        <div class="header_2">
            <img class="prodcons" src='../../imagenes/prodcon/logoSinfondo.png' alt="Logo">

            <div class="admin-controls">
                <!-- Botón de búsqueda-->
                <button class="search-toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>

                <!--Barra de búsqueda-->
                <div class="search-bar hidden">
                    <input type="text" placeholder="Buscar...">
                    <button class="search-close-btn">&times;</button>
                </div>

                <!--Botón de notificaciones-->
                <a href='/PRODCONS/PI2do/Dashboard_Editores/Notibox/noti-box.php' class="notif-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notif-badge">1</span>
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
                <p class="stat-number"><?php echo isset($totalPosts) ? number_format($totalPosts, 0, ',', '.') : '0'; ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-eye"></i> Vistas Totales</h1>
                <p class="stat-number"><?php echo isset($totalVisitas) ? number_format($totalVisitas, 0, ',', '.') : '0'; ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-heart" style="color: #e4405f;"></i> Likes Recibidos</h1>
                <p class="stat-number"><?php echo isset($totalLikes) ? number_format($totalLikes, 0, ',', '.') : '0'; ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-comment"></i> Comentarios Recibidos</h1>
                <p class="stat-number"><?php echo isset($totalComentarios) ? number_format($totalComentarios, 0, ',', '.') : '0'; ?></p>
            </div>
        </section>

        <div class="titulo-estadisticas"><h1>Mis Posts Más Populares</h1></div>
        
        <div class="contenedor-ok">
            <section class="chart">
                <div class="chart-wrapper">
                    <div class="bars">
                        <?php if (isset($postsPopulares) && !empty($postsPopulares)): ?>
                            <?php foreach ($postsPopulares as $index => $post): ?>
                                <div class="bar-label">
                                    <div class="bar-title"><?php echo htmlspecialchars($post['titulo']); ?></div>
                                    <div class="bar-container">
                                        <div class="bar bar<?php echo $index + 1; ?>" 
                                             data-label="<?php echo number_format($post['visitas'], 0, ',', '.'); ?>"
                                             data-views="<?php echo $post['visitas']; ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-data">No tienes posts publicados aún</div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
