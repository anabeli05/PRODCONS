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
    <link rel="stylesheet" href='../../Dashboard_Editores/Dashboard/sidebar.css'>
    <link rel="stylesheet" href='../../Dashboard_Editores/Configuracion/configuracion.css'>
    
    
    <link rel="stylesheet" href='../Dashboard/sidebar.css'>
    <script src="../Dashboard/barra-nav.js" defer></script>
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

<div class="contenedor-configuracion">
    <div class="config-titulo">
        <h1>Configuración de Perfil</h1>
    </div>

    <div class="main-content">
        <div class="secciones">
            <div class="section-title">Perfil</div>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-success"><?php echo $mensaje; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" class="profile-item">
                
                <label for="Foto de Perfil" class="avatar-selector">
                    <?php if (!empty($usuario['Foto de Perfil'])): ?>
                        <img src="<?php echo htmlspecialchars($usuario['Foto de Perfil']); ?>" alt="Foto de perfil actual" class="admin-avatar" />
                    <?php else: ?>
                        <img src="/PRODCONS-main/PI2do/Vista-Admin/UsuarioAdmin/img-vista-admin/removebg-preview.png" alt="Default avatar" class="admin-avatar" />
                    <?php endif; ?>
                    <p>Foto de perfil</p>
                </label>
                <input type="file" id="Foto de Perfil" name="Foto de Perfil" accept="image/*" style="display: none;">

                <div class="nombre-container">
                    <label for="Nombre" class="profile-label">Nombre:</label>
                    <input type="text" id="Nombre" name="Nombre" value="<?php echo htmlspecialchars($usuario['Nombre'] ?? ''); ?>" class="profile-name-input" required>
                </div>

                <div class="nombre-container">
                    <label for="Contraseña" class="profile-label">Contraseña:</label>
                    <input type="password" id="Contraseña" name="Contraseña" value="<?php echo htmlspecialchars($usuario['Contraseña'] ?? ''); ?>" class="profile-name-input" required>
                </div>

                <div class="actualizar-container" style="margin-top: 15px;">
                    <button type="submit" name="actualizar_perfil" class="logout-button">Actualizar Perfil</button>
                </div>
            </form>
        </div>
    </div>

    <div class="seccion-sesion">
        <div class="seguridad-contenido">
            <div class="section-title">Seguridad</div>

            <div class="security-item">
                <svg xmlns="http://www.w3.org/2000/svg" class="lock-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <a href="../cambiar contraseña/cambiar.php" class="change-password">Cambiar Contraseña</a>
            </div>
        </div>

        <button id="cancelarSuscripcion" class="logout-button">Cancelar Suscripción</button>
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
    <link rel="stylesheet" href='../../Dashboard_Editores/Dashboard/sidebar.css'>
    <style>
        .config-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-warning {
            background: #ffc107;
            color: #000;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
    <script src="barra-nav.js" defer></script>
</head>
<body>


    <div class="config-container">
        <h1>Configuración de Perfil</h1>
        
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="Nombre">Nombre:</label>
                <input type="text" id="Nombre" name="Nombre" value="<?php echo htmlspecialchars($usuario['Nombre'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="Contraseña">Contraseña:</label>
                <input type="password" id="Contraseña" name="Contraseña" value="<?php echo htmlspecialchars($usuario['Contraseña'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="Foto de Perfil">Foto de Perfil:</label>
                <input type="file" id="Foto de Perfil" name="Foto de Perfil" accept="image/*">
                <?php if (!empty($usuario['Foto de Perfil'])): ?>
                    <img src="<?php echo htmlspecialchars($usuario['Foto de Perfil']); ?>" alt="Foto de perfil actual" style="max-width: 200px; margin-top: 10px;">
                <?php endif; ?>
            </div>

            <button type="submit" name="actualizar_perfil" class="btn btn-primary">Actualizar Perfil</button>
        </form>

        <div class="mt-4">
            <a href="../cambiar contraseña/cambiar_contraseña.php" class="btn btn-warning">Cambiar Contraseña</a>
        </div>

        <div class="mt-4">
            <button id="cancelarSuscripcion" class="btn btn-danger">Cancelar Suscripción</button>
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