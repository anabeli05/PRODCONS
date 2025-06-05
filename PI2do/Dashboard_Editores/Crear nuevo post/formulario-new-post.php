<?php
session_start();
include '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$errores = [];
$mensaje = null;

function validarFormulario($titulo, $contenido, $imagenes) {
    $errores = [];
    if (strlen($titulo) < 5 || strlen($titulo) > 100) {
        $errores[] = 'El título debe tener entre 5 y 100 caracteres';
    }
    if (strlen($contenido) < 50) {
        $errores[] = 'El contenido debe tener al menos 50 caracteres';
    }
    // Validar imágenes
    if (!empty($imagenes['name'][0])) {
        foreach ($imagenes['name'] as $key => $value) {
            $tipo = $imagenes['type'][$key];
            $tamano = $imagenes['size'][$key];
            if (!in_array($tipo, ['image/jpeg', 'image/png', 'image/gif'])) {
                $errores[] = 'Solo se permiten imágenes JPG, PNG y GIF';
            }
            if ($tamano > 5242880) { // 5MB
                $errores[] = 'Las imágenes no deben superar 5MB';
            }
        }
    }
    return $errores;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errores[] = 'Error de seguridad: token CSRF inválido';
    } else {
        $titulo = $_POST['titulo'] ?? '';
        $contenido = $_POST['contenido'] ?? '';
        $etiquetas = $_POST['etiquetas'] ?? '';
        $comentario_autor = $_POST['comentario_autor'] ?? '';
        $imagenes = $_FILES['imagenes'] ?? [];
        $errores = validarFormulario($titulo, $contenido, $imagenes);
        if (empty($errores)) {
            try {
                $conn->beginTransaction();
                $stmt = $conn->prepare("INSERT INTO posts (titulo, contenido, etiquetas, comentario_autor, usuario_id, fecha_creacion) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$titulo, $contenido, $etiquetas, $comentario_autor, $_SESSION['usuario_id']]);
                $post_id = $conn->lastInsertId();
                // Procesar imágenes
                if (!empty($imagenes['name'][0])) {
                    $upload_dir = '../../imagenes/posts/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    foreach ($imagenes['name'] as $key => $value) {
                        $file_name = uniqid() . '_' . basename($imagenes['name'][$key]);
                        $file_path = $upload_dir . $file_name;
                        if (move_uploaded_file($imagenes['tmp_name'][$key], $file_path)) {
                            $stmt = $conn->prepare("INSERT INTO imagenes_posts (post_id, ruta_imagen) VALUES (?, ?)");
                            $stmt->execute([$post_id, $file_path]);
                        }
                    }
                }
                $conn->commit();
                $mensaje = 'Post creado exitosamente';
            } catch (Exception $e) {
                $conn->rollBack();
                $errores[] = 'Error al crear el post: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Posts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link href="formulario-new-post.css" rel="stylesheet">
    <script src="../Dashboard/barra-nav.js" defer></script>
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
        <?php if ($mensaje): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <?php if (!empty($errores)): ?>
            <div class="mensaje-error">
                <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <label for="titulo">Título del Post:</label>
            <input type="text" id="titulo" name="titulo" required minlength="5" maxlength="100" value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>">
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="8" required minlength="50"><?php echo isset($_POST['contenido']) ? htmlspecialchars($_POST['contenido']) : ''; ?></textarea>
            <label for="imagenes">Subir Imágenes:</label>
            <input type="file" id="imagenes" name="imagenes[]" multiple accept="image/*">
            <label for="etiquetas">Etiquetas (separadas por comas):</label>
            <input type="text" id="etiquetas" name="etiquetas" value="<?php echo isset($_POST['etiquetas']) ? htmlspecialchars($_POST['etiquetas']) : ''; ?>">
            <label for="comentario_autor">Comentario del Autor:</label>
            <textarea id="comentario_autor" name="comentario_autor" rows="4"><?php echo isset($_POST['comentario_autor']) ? htmlspecialchars($_POST['comentario_autor']) : ''; ?></textarea>
            <button type="submit">Publicar</button>
        </form>
    </div>



</body>
</html>