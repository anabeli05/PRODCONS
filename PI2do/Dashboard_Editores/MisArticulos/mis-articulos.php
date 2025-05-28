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
    <link rel="stylesheet" href="styles.css">
    <link href='mis-articulos.css' rel="stylesheet">
    
    <link href='../Dashboard/sidebar.css' rel="stylesheet">
  
    <!--javascript-->
    <script src='../Dashboard/barra-nav.js' defer></script>
</head>
<body>
    <header> 
        <div class="header-contenedor">
            <div class="principal"></div>
        </div>
    </header>

    <section class="logo"> 
        <div class="header_2">
            <img class="prodcons" src='../imagenes/prodcon/logoSinfondo.png' alt="Logo">

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
                <a href='../Dashboard_Editores/Notibox/noti-box.php' class="notif-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notif-badge">1</span>
                </a>

                <!-- Botón Admin con avatar -->
                <div class="admin-btn">
                    <span>Admin</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 11l8 3l8 -3" />
                    </svg>
                    <img src='../imagenes/logos/perfil.png' alt="Admin" class="admin-avatar">
                </div>
            </div>

            <!----- sidebar ----->
            <div class="admin-sidebar" id="adminSidebar">
                <div class="sidebar-header">
                    <h3>ADMIN</h3>
                    <button class="close-sidebar">
                        <img src='../imagenes/logos/perfil.png' alt="Admin" class="admin-avatar">
                    </button>
                </div>
                
                <nav class="sidebar-menu">
                    <a href='mis-articulos.php'>
                        <span>Mis Artículos</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </a>
                    
                    <a href='../Configuracion/config.php'>
                        <span>Configuración</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                    </a>
                    
                    <a href='../PostPlaneados/post-planeados.php'>
                        <span>Post Planeados</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </a>
                    
                    <a href='../Estadisticas/estadisticas-adm.php'>
                        <span>Estadísticas</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="20" x2="12" y2="10"/>
                            <line x1="18" y1="20" x2="18" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="16"/>
                        </svg>
                    </a>
                </nav>
                
                <div class="sidebar-footer">
                    <button class="logout-btn">Cerrar Sesión</button>
                </div>

            </div>

        </div>
    </section>

    <div class="contenedor-mis-articulos">
        <div class="header-articulos">
            <h1>Mis Artículos</h1>
            <a href="formulario-new-post.php" class="btn-nuevo-post">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Crear Nuevo Post
            </a>
        </div>
        <div class="ordenar-wrapper">
            <a href="?orden=<?php echo $orden === 'desc' ? 'asc' : 'desc'; ?>" class="ordenar">
                ORDENAR POR RECIENTES <span><?php echo $orden_icono; ?></span>
            </a>
        </div>        
        
        <div class="contenedor-principal">
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="mensaje-exito">
                    <?php 
                    echo $_SESSION['mensaje'];
                    unset($_SESSION['mensaje']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mensaje-error">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="grid-articulos">
                <?php if (empty($posts)): ?>
                    <div class="no-articulos">
                        <p>No tienes artículos publicados aún.</p>
                        <a href="formulario-new-post.php" class="btn-nuevo-post">Crear mi primer artículo</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <article class="articulo-card">
                            <div class="articulo-imagen">
                                <?php if ($post['imagenes']): ?>
                                    <img src="<?php echo '/PRODCONS/PI2do/imagenes/articulos/' . explode(',', $post['imagenes'])[0]; ?>" alt="<?php echo htmlspecialchars($post['Titulo']); ?>">
                                <?php else: ?>
                                    <div class="no-imagen">Sin imagen</div>
                                <?php endif; ?>
                            </div>
                            <div class="articulo-contenido">
                                <h2><?php echo htmlspecialchars($post['Titulo']); ?></h2>
                                <p class="fecha"><?php echo date('d/m/Y', strtotime($post['Fecha de Creacion'])); ?></p>
                                <p class="resumen"><?php echo substr(strip_tags($post['Contenido']), 0, 150) . '...'; ?></p>
                                <div class="articulo-acciones">
                                    <a href="editar-post.php?id=<?php echo $post['ID_Articulo']; ?>" class="btn-editar">Editar</a>
                                    <button onclick="confirmarEliminar(<?php echo $post['ID_Articulo']; ?>)" class="btn-eliminar">Eliminar</button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="motivacion-container">
                <div class="motivacion">
                    <p>Sigue creando post, informa al mundo de los nuevos sucesos!</p>
                    <a href='formulario-new-post.php' class="mas">+</a>
                    <img src='../imagenes/plantita.png' class="decoracion hojas-izq" width="80">
                    <img src='../imagenes/planta.png' class="decoracion hojas-der" width="80">
                </div>
                    <img src='../imagenes/maceta-verde.png' class="maceta" width="80">
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
</html>