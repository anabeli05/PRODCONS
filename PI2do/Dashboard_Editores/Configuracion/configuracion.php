<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Incluir la conexión a la base de datos
include '../../Base de datos/conexion.php';
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = '';
    $error = '';

    try {
        // Actualizar información del perfil
        if (isset($_POST['actualizar_perfil'])) {
            $Nombre = filter_input(INPUT_POST, 'Nombre', FILTER_SANITIZE_STRING);
            $Contraseña = filter_input(INPUT_POST, 'Contraseña', FILTER_SANITIZE_STRING);
            $Foto_Perfil = filter_input(INPUT_POST, 'Foto de Perfil', FILTER_SANITIZE_STRING);
            
            $stmt = $conn->prepare("UPDATE usuarios SET Nombre = ?, Contraseña = ?, `Foto de Perfil` = ? WHERE Usuario_ID = ?");
            $stmt->bind_param("sssi", $Nombre, $Contraseña, $Foto_Perfil, $_SESSION['Usuario_ID']);
            $stmt->execute();
            
            $mensaje = 'Perfil actualizado correctamente';
        }
    } catch (Exception $e) {
        $error = 'Error al actualizar el perfil: ' . $e->getMessage();
    }
}

// Obtener información actual del usuario
try {
    $stmt = $conn->prepare("SELECT Nombre, Contraseña, `Foto de Perfil` FROM usuarios WHERE Usuario_ID = ?");
    $stmt->bind_param("i", $_SESSION['Usuario_ID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
} catch (Exception $e) {
    $error = 'Error al obtener información del usuario: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - PRONCONS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="stylesheet" href='../Dashboard/sidebar.css'>
    <script src="../Dashboard/barra-nav.js" defer></script>

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
                    <a href='../inicio/inicio.php' data-no-translate>
                            <span>Inicio</span>
                        <i class="fas fa-file-alt"></i>
                    </a>
                    
                    <a href='../MisArticulos/mis-articulos.php' data-no-translate>
                        <span>Mis Artículos</span>
                        <i class="fas fa-file-alt"></i>
                    </a>
                    
                    <a href="../Crear nuevo post/formulario-new-post.php" data-no-translate>
                        <span>Crear Post</span>
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href='../PostPlaneados/post-planeados.php' data-no-translate>
                        <span>Post Planeados</span>
                        <i class="fas fa-calendar"></i>
                    </a>
                                        
                    <a href='../Estadisticas/estadisticas-adm.php' data-no-translate>
                        <span>Estadísticas</span>
                        <i class="fas fa-chart-bar"></i>
                    </a>
                    
                    <a href='../Configuracion/configuracion.php' data-no-translate>
                        <span>Configuración</span>
                        <i class="fas fa-cog"></i>
                    </a>
                    
                    <a href='../../inicio_sesion/logout.php' class="logout-btn" data-no-translate>
                        <span>Cerrar Sesión</span>
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </nav>
            </div>
        </div>
    </section>

<div class="font-sans m-20 bg-[#fdfdfd]">
    <div class="flex justify-between items-center mb-5">
        <h1 class="text-[28px] font-semibold m-0">Configuración de Perfil</h1>
    </div>

    <div class="flex gap-5 mx-5">
        <div class="flex-1 bg-white p-4 rounded-lg">
            <div class="font-bold mb-5 text-[22px]">Perfil</div>

            <?php if (isset($mensaje)): ?>
                <div class="p-2 mb-4 rounded bg-[#d4edda] text-[#155724] border border-[#c3e6cb]"><?php echo $mensaje; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="p-2 mb-4 rounded bg-[#f8d7da] text-[#721c24] border border-[#f5c6cb]"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" class="flex flex-col gap-2">
                <label for="Foto de Perfil" class="flex items-center cursor-pointer gap-2 select-none">
                    <?php if (!empty($usuario['Foto de Perfil'])): ?>
                        <img src="<?php echo htmlspecialchars($usuario['Foto de Perfil']); ?>" alt="Foto de perfil actual" class="w-10 h-10 rounded-full object-cover" />
                    <?php else: ?>
                        <img src="/PRODCONS/PI2do/imagenes/logos/perfil.png" alt="Default avatar" class="w-12 h-12 mb-5 rounded-full object-cover" />
                    <?php endif; ?>
                    <p class="mb-5 text-[13px]">Foto de perfil</p>
                </label>
                <input type="file" id="Foto de Perfil" name="Foto de Perfil" accept="image/*" class="hidden">

                <div class="flex items-center gap-2 ml-14">
                    <label for="Nombre" class="font-bold text-[12px]">Nombre:</label>
                    <input type="text" id="Nombre" name="Nombre" value="<?php echo htmlspecialchars($usuario['Nombre'] ?? ''); ?>" class="px-3 py-2 border border-gray-300 rounded w-52 text-base" required>
                </div>

                <div class="flex items-center gap-2 mt-4 ml-auto">
                    <button type="submit" name="actualizar_perfil" class="ml-auto mr-11 w-[150px] h-[45px] bg-[#aedA97] border-none rounded-[15px] text-white cursor-pointer text-[15px] font-serif hover:underline">
                        Actualizar Perfil
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="flex items-end justify-between ml-8 gap-5 mb-10 pb-10 mr-8">
        <div class="flex flex-col gap-2">
            <div class="mb-5 font-bold text-[22px]">Seguridad</div>
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-[25px] h-[25px] mr-3 stroke-black" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <a href="../cambiar_contraseña/cambiar.php" class="text-[15px] text-black hover:underline">Cambiar Contraseña</a>
            </div>
        </div>

        <button id="cancelarSuscripcion" class="ml-auto mr-11 w-[150px] h-[45px] bg-[#aedA97] border-none rounded-[15px] text-white cursor-pointer text-[15px] font-serif hover:underline">
            Cancelar Suscripción
        </button>
    </div>
</div>
<script>
    document.getElementById('cancelarSuscripcion').addEventListener('click', function() {
        if (confirm('¿Estás seguro de que deseas cancelar tu suscripción? Se enviará una notificación al SuperAdmin para su aprobación.')) {
            fetch('../Notibox/notificar_superadmin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mensaje: 'El Editor ha solicitado cancelar su suscripción. Por favor, revisa y aprueba o rechaza la solicitud.'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Se ha enviado una solicitud al SuperAdmin para cancelar tu suscripción.');
                } else {
                     alert('Error al enviar la solicitud. Por favor, intenta de nuevo.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al enviar la solicitud. Por favor, intenta de nuevo.');
            });
        }
    });
</script>
</body>
</html> 