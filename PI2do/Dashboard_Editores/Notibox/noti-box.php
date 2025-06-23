<<<<<<< HEAD
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
    <!-- <link href='/PRODCONS/PI2do/Dashboard_Editores/Notibox/noti-box.css' rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- CSS Y JS DE HEADER-->
    <link href='/PRODCONS/PI2do/Dashboard_Editores/Dashboard/sidebar.css' rel="stylesheet">
    <script src='/PRODCONS/PI2do/Dashboard_Editores/Dashboard/barra-nav.js' defer></script>

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

    <!-- NOTIFICACIONES -->
    <section class="font-sans bg-[#f5f7fa] py-10 px-4 flex justify-center">
        <div class="notifications-container w-full max-w-[600px] bg-white rounded-[12px] overflow-hidden">
            <div class="dflex items-center justify-between px-5 py-4 border-b">
                <h2 class="text-[18px] font-semibold">Notificaciones</h2>
                <div class="relative">
                    <div class="notification-badge bg-red-600 text-white text-[12px] w-6 h-6 rounded-full flex items-center justify-center font-bold"><?php echo $total_no_vistas; ?></div>
                </div>
            </div>

            <?php if (empty($notificaciones)): ?>
                <div  class="text-center text-gray-500 text-[15px] px-5 py-6">No tienes notificaciones nuevas.</div>
            <?php else: ?>
                <?php foreach ($notificaciones as $noti): ?>
                    <div class="border-b px-5 py-4 flex gap-4 items-start text-[16px]" data-post-id="<?= $noti['ID'] ?>">
                        <?php if ($noti['tipo'] === 'likes'): ?>
                            <div class="flex items-center gap-2">
                                <i  class="fas fa-heart text-[20px] text-[#e0245e] mt-1"></i>
                                <span class="font-semibold text-gray-700">Nuevo like</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-gray-800">
                                    <span class="font-bold"><?= htmlspecialchars($noti['autor_nombre']) ?></span>
                                    te ha dado like a tu publicación
                                </div>
                                <a href="ver-publicacion.php?id=<?= $noti['ID'] ?>" class="inline-block mt-2 bg-blue-600 text-white text-sm px-3 py-1.5 rounded hover:bg-blue-700 transition">Ver publicación</a>
                                <div class="text-[14px] text-gray-500 mt-1"><?= htmlspecialchars($noti['Fecha']) ?></div>
                            </div>
                        <?php elseif ($noti['tipo'] === 'comentarios_autor'): ?>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-comment text-[20px] text-[#007bff] mt-1"></i>
                                <span class="font-semibold text-gray-700">Nuevo comentario</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-gray-800">
                                    <span class="font-bold"><?= htmlspecialchars($noti['autor_nombre']) ?></span>
                                    ha comentado en tu publicación:
                                    <div class="mt-1 p-2 bg-[#f5f5f5] rounded text-gray-600 italic"><?= htmlspecialchars($noti['Comentario']) ?></div>
                                </div>
                                <a href="ver-publicacion.php?id=<?= $noti['ID'] ?>" class="inline-block mt-2 bg-blue-600 text-white text-sm px-3 py-1.5 rounded hover:bg-blue-700 transition">Ver publicación</a>
                                <div class="text-[14px] text-gray-500 mt-1"><?= htmlspecialchars($noti['Fecha']) ?></div>
                            </div>
                        <?php elseif ($noti['tipo'] === 'aceptada'): ?>
                            <div class="w-10 h-10 rounded-full bg-cover bg-center flex-shrink-0" style="background-image: url('perfil.png')"></div>
                            <div class="flex-1">
                                <div class="text-green-600 font-bold text-[17px]">Tu publicación fue aceptada y ya está en línea</div>
                                <a href="ver-publicacion.php?id=<?= $noti['Articulo_ID'] ?>" class="inline-block mt-2 bg-blue-600 text-white text-sm px-3 py-1.5 rounded hover:bg-blue-700 transition">Ver publicación</a>
                                <div class="text-[14px] text-gray-500 mt-1"><?= htmlspecialchars($noti['Fecha']) ?></div>
                            </div>
                        <?php elseif ($noti['tipo'] === 'rechazada'): ?>
                            <div class="w-10 h-10 rounded-full bg-cover bg-center flex-shrink-0" style="background-image: url('perfil.png')"></div>
                            <div class="flex-1">
                                <div class="text-red-600 font-bold text-[17px]">Tu publicación fue rechazada</div>
                                <div class="text-blue-600 italic mt-1">Motivo: "<?= htmlspecialchars($noti['Comentario']) ?>"</div>
                                <a href="ver-publicacion.php?id=<?= $noti['Articulo_ID'] ?>" class="inline-block mt-2 bg-blue-600 text-white text-sm px-3 py-1.5 rounded hover:bg-blue-700 transition">Ver publicación</a>
                                <div class="text-[14px] text-gray-500 mt-1"><?= htmlspecialchars($noti['Fecha']) ?></div>
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
=======
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
    <!-- <link href='/PRODCONS/PI2do/Dashboard_Editores/Notibox/noti-box.css' rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- CSS Y JS DE HEADER-->
    <link href='/PRODCONS/PI2do/Dashboard_Editores/Dashboard/sidebar.css' rel="stylesheet">
    <script src='/PRODCONS/PI2do/Dashboard_Editores/Dashboard/barra-nav.js' defer></script>

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

    <!-- NOTIFICACIONES -->
    <section class="font-sans bg-[#f5f7fa] py-10 px-4 flex justify-center">
        <div class="notifications-container w-full max-w-[600px] bg-white rounded-[12px] overflow-hidden">
            <div class="dflex items-center justify-between px-5 py-4 border-b">
                <h2 class="text-[18px] font-semibold">Notificaciones</h2>
                <div class="relative">
                    <div class="notification-badge bg-red-600 text-white text-[12px] w-6 h-6 rounded-full flex items-center justify-center font-bold"><?php echo $total_no_vistas; ?></div>
                </div>
            </div>

            <?php if (empty($notificaciones)): ?>
                <div  class="text-center text-gray-500 text-[15px] px-5 py-6">No tienes notificaciones nuevas.</div>
            <?php else: ?>
                <?php foreach ($notificaciones as $noti): ?>
                    <div class="border-b px-5 py-4 flex gap-4 items-start text-[16px]" data-post-id="<?= $noti['ID'] ?>">
                        <?php if ($noti['tipo'] === 'likes'): ?>
                            <div class="flex items-center gap-2">
                                <i  class="fas fa-heart text-[20px] text-[#e0245e] mt-1"></i>
                                <span class="font-semibold text-gray-700">Nuevo like</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-gray-800">
                                    <span class="font-bold"><?= htmlspecialchars($noti['autor_nombre']) ?></span>
                                    te ha dado like a tu publicación
                                </div>
                                <a href="ver-publicacion.php?id=<?= $noti['ID'] ?>" class="inline-block mt-2 bg-blue-600 text-white text-sm px-3 py-1.5 rounded hover:bg-blue-700 transition">Ver publicación</a>
                                <div class="text-[14px] text-gray-500 mt-1"><?= htmlspecialchars($noti['Fecha']) ?></div>
                            </div>
                        <?php elseif ($noti['tipo'] === 'comentarios_autor'): ?>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-comment text-[20px] text-[#007bff] mt-1"></i>
                                <span class="font-semibold text-gray-700">Nuevo comentario</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-gray-800">
                                    <span class="font-bold"><?= htmlspecialchars($noti['autor_nombre']) ?></span>
                                    ha comentado en tu publicación:
                                    <div class="mt-1 p-2 bg-[#f5f5f5] rounded text-gray-600 italic"><?= htmlspecialchars($noti['Comentario']) ?></div>
                                </div>
                                <a href="ver-publicacion.php?id=<?= $noti['ID'] ?>" class="inline-block mt-2 bg-blue-600 text-white text-sm px-3 py-1.5 rounded hover:bg-blue-700 transition">Ver publicación</a>
                                <div class="text-[14px] text-gray-500 mt-1"><?= htmlspecialchars($noti['Fecha']) ?></div>
                            </div>
                        <?php elseif ($noti['tipo'] === 'aceptada'): ?>
                            <div class="w-10 h-10 rounded-full bg-cover bg-center flex-shrink-0" style="background-image: url('perfil.png')"></div>
                            <div class="flex-1">
                                <div class="text-green-600 font-bold text-[17px]">Tu publicación fue aceptada y ya está en línea</div>
                                <a href="ver-publicacion.php?id=<?= $noti['Articulo_ID'] ?>" class="inline-block mt-2 bg-blue-600 text-white text-sm px-3 py-1.5 rounded hover:bg-blue-700 transition">Ver publicación</a>
                                <div class="text-[14px] text-gray-500 mt-1"><?= htmlspecialchars($noti['Fecha']) ?></div>
                            </div>
                        <?php elseif ($noti['tipo'] === 'rechazada'): ?>
                            <div class="w-10 h-10 rounded-full bg-cover bg-center flex-shrink-0" style="background-image: url('perfil.png')"></div>
                            <div class="flex-1">
                                <div class="text-red-600 font-bold text-[17px]">Tu publicación fue rechazada</div>
                                <div class="text-blue-600 italic mt-1">Motivo: "<?= htmlspecialchars($noti['Comentario']) ?>"</div>
                                <a href="ver-publicacion.php?id=<?= $noti['Articulo_ID'] ?>" class="inline-block mt-2 bg-blue-600 text-white text-sm px-3 py-1.5 rounded hover:bg-blue-700 transition">Ver publicación</a>
                                <div class="text-[14px] text-gray-500 mt-1"><?= htmlspecialchars($noti['Fecha']) ?></div>
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
>>>>>>> main
</html>