<?php 
session_start();
include '../../Base de datos/conexion.php';

// Crear instancia de Conexion y abrir conexión
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

$Usuario_ID = $_SESSION['Usuario_ID'];

// Get notifications and count unread ones
$stmt = $conn->prepare("(
    SELECT 'comentarios_autor' as tipo, c.ComentarioUsuario_ID as ID, c.Comentario, c.Fecha, c.Usuario_ID, u.Nombre as autor_nombre, c.visto as visto
    FROM comentarios_autor c
    JOIN usuarios u ON c.Usuario_ID = u.Usuario_ID
    WHERE c.Usuario_ID = ?
    UNION ALL
    SELECT 'likes' as tipo, l.Like_ID as ID, NULL as Comentario, l.Fecha, l.Usuario_ID, u.Nombre as autor_nombre, l.visto as visto
    FROM likes l
    JOIN usuarios u ON l.Usuario_ID = u.Usuario_ID
    WHERE l.Usuario_ID = ?
) ORDER BY Fecha DESC");

// Count unread notifications
$stmt_count = $conn->prepare("SELECT COUNT(*) as count 
    FROM (
        SELECT c.ComentarioUsuario_ID as ID, c.visto as visto
        FROM comentarios_autor c
        WHERE c.Usuario_ID = ? AND c.visto = 0
        UNION ALL
        SELECT l.Like_ID as ID, l.visto as visto
        FROM likes l
        WHERE l.Usuario_ID = ? AND l.visto = 0
    ) as notificaciones");
$stmt_count->bind_param("ii", $Usuario_ID, $Usuario_ID);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_no_vistas = $result_count->fetch_assoc()['count'];
$stmt_count->close();

$stmt->bind_param("ii", $Usuario_ID, $Usuario_ID);
$stmt->execute();
$result = $stmt->get_result();
$notificaciones = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Notificaciones</title>
    <link href='/PRODCONS/PI2do/Dashboard_Editores/Notibox/noti-box.css' rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- CSS Y JS DE HEADER-->
    <link href='/PRODCONS/PI2do/Dashboard_Editores/Dashboard/sidebar.css' rel="stylesheet">
    <script src='/PRODCONS/PI2do/Dashboard_Editores/Dashboard/barra-nav.js' defer></script>
    <style>
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ff0000;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            display: <?php echo $total_no_vistas > 0 ? 'block' : 'none'; ?>;
        }
        .notification-icon {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        .notification-icon i {
            font-size: 20px;
        }
        .notification-type {
            font-weight: bold;
            color: #666;
        }
        .comment-text {
            margin-top: 5px;
            padding: 8px;
            background-color: #f5f5f5;
            border-radius: 4px;
            font-style: italic;
        }
    </style>
</head>
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

    <!-- NOTIFICACIONES -->
    <section class="notificaciones">
        <div class="notifications-container">
            <div class="notifications-header">
                <h2>Notificaciones</h2>
                <div class="badge-container">
                    <div class="notification-badge"><?php echo $total_no_vistas; ?></div>
                </div>
            </div>

            <?php if (empty($notificaciones)): ?>
                <div class="notification">No tienes notificaciones nuevas.</div>
            <?php else: ?>
                <?php foreach ($notificaciones as $noti): ?>
                    <div class="notification" data-post-id="<?= $noti['ID'] ?>">
                        <?php if ($noti['tipo'] === 'likes'): ?>
                            <div class="notification-icon">
                                <i class="fas fa-heart"></i>
                                <span class="notification-type">Nuevo like</span>
                            </div>
                            <div class="content">
                                <div class="notification-text">
                                    <span class="author-name"><?= htmlspecialchars($noti['autor_nombre']) ?></span>
                                    te ha dado like a tu publicación
                                </div>
                                <a href="ver-publicacion.php?id=<?= $noti['ID'] ?>" class="view-post-button">Ver publicación</a>
                                <div class="time"><?= htmlspecialchars($noti['Fecha']) ?></div>
                            </div>
                        <?php elseif ($noti['tipo'] === 'comentarios_autor'): ?>
                            <div class="notification-icon">
                                <i class="fas fa-comment"></i>
                                <span class="notification-type">Nuevo comentario</span>
                            </div>
                            <div class="content">
                                <div class="notification-text">
                                    <span class="author-name"><?= htmlspecialchars($noti['autor_nombre']) ?></span>
                                    ha comentado en tu publicación:
                                    <div class="comment-text"><?= htmlspecialchars($noti['Comentario']) ?></div>
                                </div>
                                <a href="ver-publicacion.php?id=<?= $noti['ID'] ?>" class="view-post-button">Ver publicación</a>
                                <div class="time"><?= htmlspecialchars($noti['Fecha']) ?></div>
                            </div>
                        <?php elseif ($noti['tipo'] === 'aceptada'): ?>
                            <div class="avatar" style="background-image: url('perfil.png')"></div>
                            <div class="content">
                                <div class="status-accepted">Tu publicación fue aceptada y ya está en línea</div>
                                <a href="ver-publicacion.php?id=<?= $noti['Articulo_ID'] ?>" class="view-post-button">Ver publicación</a>
                                <div class="time"><?= htmlspecialchars($noti['Fecha']) ?></div>
                            </div>
                        <?php elseif ($noti['tipo'] === 'rechazada'): ?>
                            <div class="avatar" style="background-image: url('perfil.png')"></div>
                            <div class="content">
                                <div class="status-rejected">Tu publicación fue rechazada</div>
                                <div class="admin-comment">Motivo: "<?= htmlspecialchars($noti['Comentario']) ?>"</div>
                                <a href="ver-publicacion.php?id=<?= $noti['Articulo_ID'] ?>" class="view-post-button">Ver publicación</a>
                                <div class="time"><?= htmlspecialchars($noti['Fecha']) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <script>
        // Marcar todas las notificaciones como vistas cuando se abra el modal
        document.addEventListener('DOMContentLoaded', function() {
            const notificationContainer = document.querySelector('.notifications-container');
            const notificationBadge = document.querySelector('.notification-badge');
            
            if (notificationContainer && notificationBadge) {
                notificationContainer.addEventListener('click', function() {
                    fetch('marcar_vistas.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'Usuario_ID=' + <?php echo $Usuario_ID; ?>
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            notificationBadge.style.display = 'none';
                        } else {
                            console.error('Error al marcar como vistas:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error en la petición:', error);
                    });
                });
            }
        });
    </script>
</body>
</html>