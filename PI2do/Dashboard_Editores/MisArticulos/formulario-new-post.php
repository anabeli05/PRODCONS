<?php
session_start();
include '../../Base de datos/conexion.php';
global $conn;

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../login.php');
    exit();
}

// Obtener token CSRF existente o generar uno nuevo
if (!isset($_SESSION['csrf_token'])) {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
} else {
    $csrf_token = $_SESSION['csrf_token'];
}

// Obtener errores de sesión si existen
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : [];
unset($_SESSION['errores']);

// Obtener mensaje de éxito si existe
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : null;
unset($_SESSION['mensaje']);

// Función para validar el formulario
function validarFormulario($titulo, $contenido, $imagenes) {
    $errores = [];
    
    // Validar título
    if (strlen($titulo) < 5) {
        $errores[] = 'El título debe tener al menos 5 caracteres';
    } elseif (strlen($titulo) > 100) {
        $errores[] = 'El título no puede exceder 100 caracteres';
    }

    // Validar contenido
    if (strlen($contenido) < 50) {
        $errores[] = 'El contenido debe tener al menos 50 caracteres';
    }

    // Validar imágenes
    if (isset($imagenes)) {
        $max_imagenes = 5;
        $max_tamano = 5 * 1024 * 1024; // 5MB
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (count($imagenes['name']) > $max_imagenes) {
            $errores[] = 'Solo puedes subir hasta 5 imágenes';
        } else {
            foreach ($imagenes['name'] as $key => $nombre) {
                if (!in_array($imagenes['type'][$key], $tipos_permitidos)) {
                    $errores[] = 'Solo se permiten imágenes JPG, PNG y GIF';
                    break;
                }
                if ($imagenes['size'][$key] > $max_tamano) {
                    $errores[] = 'Alguna imagen excede el tamaño máximo de 5MB';
                    break;
                }
            }
        }
    }

    return $errores;
}

// Validación del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['errores'][] = "Error de seguridad: token inválido";
        header('Location: formulario-new-post.php');
        exit();
    }

    // Validar el formulario
    $errores = validarFormulario(
        trim($_POST['titulo'] ?? ''),
        trim($_POST['contenido'] ?? ''),
        $_FILES['imagenes'] ?? null
    );

    $_SESSION['datos_formulario'] = [
        'titulo' => $_POST['titulo'] ?? '',
        'contenido' => $_POST['contenido'] ?? '',
        'etiquetas' => $_POST['etiquetas'] ?? '',
        'comentario_autor' => $_POST['comentario_autor'] ?? '',
        'bibliografias' => $_POST['bibliografias'] ?? ''
    ];

    if (empty($errores)) {
        $titulo = trim($_POST['titulo']);
        $contenido = trim($_POST['contenido']);
        $usuario_id = $_SESSION['Usuario_ID'];
        $fecha_creacion = date('Y-m-d H:i:s');
        $fecha_publicacion = date('Y-m-d'); // Usar la fecha actual
        $estado = 'pendiente';
        $motivo_rechazo = null;
        $bibliografias = trim($_POST['bibliografias'] ?? '');

        // Manejo de imágenes
        $imagenes_ruta = [];
        if (isset($_FILES['imagenes']) && isset($_FILES['imagenes']['tmp_name']) && is_array($_FILES['imagenes']['tmp_name'])) {
            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['imagenes']['error'][$key] === 0) {
                    $upload_dir = '../../uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $nombre_archivo = basename($_FILES['imagenes']['name'][$key]);
                    $ruta_destino = $upload_dir . uniqid() . '_' . $nombre_archivo;
                    if (move_uploaded_file($tmp_name, $ruta_destino)) {
                        $imagenes_ruta[] = $ruta_destino;
                    }
                }
            }
        }
        $imagenes_json = json_encode($imagenes_ruta);

        // Prepara el statement
        $stmt = $conn->prepare("INSERT INTO articulos (Titulo, Contenido, Bibliografias, Usuario_ID, `Fecha de Publicacion`) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            $_SESSION['errores'][] = "Error en prepare: " . $conn->error;
            header('Location: formulario-new-post.php');
            exit();
        }

        $stmt->bind_param(
            "sssis",
            $titulo,
            $contenido,
            $bibliografias,
            $usuario_id,
            $fecha_publicacion
        );

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = 'Post creado exitosamente';
            unset($_SESSION['datos_formulario']);
        } else {
            $_SESSION['errores'][] = "Error al guardar el post: " . $stmt->error;
        }
        $stmt->close();
        header('Location: formulario-new-post.php');
        exit();
    } else {
        $_SESSION['errores'] = $errores;
        header('Location: formulario-new-post.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Posts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link href='formulario-new-post.css' rel="stylesheet">
    <link href='../Dashboard/sidebar.css' rel="stylesheet">

    <!--javascript-->
    <script src='../Dashboard/barra-nav.js' defer></script>
</head>
<body>
    <?php if (isset($mensaje) && $mensaje): ?>
        <script>
            alert("<?php echo addslashes($mensaje); ?>");
        </script>
    <?php endif; ?>
    <?php if (!empty($errores)): ?>
        <script>
            alert("<?php echo addslashes(implode('\n', $errores)); ?>");
        </script>
    <?php endif; ?>

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


    <div class="contenedor-formulario">
        <h1>Crear Nuevo Post</h1>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="mensaje-exito">
                <?php 
                echo $_SESSION['mensaje'];
                unset($_SESSION['mensaje']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errores'])): ?>
            <div class="mensaje-error">
                <ul>
                    <?php 
                    foreach ($_SESSION['errores'] as $error) {
                        echo "<li>$error</li>";
                    }
                    unset($_SESSION['errores']);
                    ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="titulo">Título del Post:</label>
                <input type="text" id="titulo" name="titulo" required 
                       minlength="5" maxlength="100"
                       value="<?php echo isset($_SESSION['datos_formulario']['titulo']) ? htmlspecialchars($_SESSION['datos_formulario']['titulo']) : ''; ?>"
                       oninput="validarTitulo(this)">
                <span class="error-message" id="titulo-error"></span>
            </div>
            
            <div class="form-group">
                <label for="contenido">Contenido:</label>
                <textarea id="contenido" name="contenido" rows="8" required
                          minlength="50" oninput="validarContenido(this)"><?php echo isset($_SESSION['datos_formulario']['contenido']) ? htmlspecialchars($_SESSION['datos_formulario']['contenido']) : ''; ?></textarea>
                <span class="error-message" id="contenido-error"></span>
            </div>
            
            <div class="form-group">
                <label for="imagenes">Subir Imágenes:</label>
                <input type="file" id="imagenes" name="imagenes[]" 
                       multiple accept="image/*" onchange="previewImages(this)">
                <div id="image-preview" class="image-preview"></div>
                <span class="error-message" id="imagenes-error"></span>
            </div>
            
            <div class="form-group">
                <label for="etiquetas">Etiquetas (separadas por comas):</label>
                <input type="text" id="etiquetas" name="etiquetas" 
                       value="<?php echo isset($_SESSION['datos_formulario']['etiquetas']) ? htmlspecialchars($_SESSION['datos_formulario']['etiquetas']) : ''; ?>"
                       placeholder="Ej: medio ambiente, reciclaje, energía">
            </div>

            <div class="form-group">
                <label for="bibliografias">Bibliografías:</label>
                <textarea id="bibliografias" name="bibliografias" rows="3"
                    placeholder="Referencias, fuentes, etc."><?php echo isset($_SESSION['datos_formulario']['bibliografias']) ? htmlspecialchars($_SESSION['datos_formulario']['bibliografias']) : ''; ?></textarea>
            </div>
      
            <div class="form-group">
                <label for="comentario_autor">Comentario del Autor:</label>
                <textarea id="comentario_autor" name="comentario_autor" rows="4" 
                          placeholder="Un mensaje personal para los lectores..."><?php echo isset($_SESSION['datos_formulario']['comentario_autor']) ? htmlspecialchars($_SESSION['datos_formulario']['comentario_autor']) : ''; ?></textarea>
            </div>
      
            <div class="publicacion">
                <button type="submit">Publicar</button>
            </div>
        </form>

    </div>

</body>
</html>
