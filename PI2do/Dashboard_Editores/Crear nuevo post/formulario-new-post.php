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
    <link href="formulario-new-post.css" rel="stylesheet">
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
            <img class="prodcons" src="../../imagenes/prodcon/logoSinfondo.png" alt="Logo">

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
                <button class="notif-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notif-badge">1</span>
                </button>

                <!-- Botón Admin con avatar -->
                <div class="admin-btn">
                    <span>Admin</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 11l8 3l8 -3" />
                    </svg>
                    <img src="../../imagenes/logos/perfil.png" alt="Admin" class="admin-avatar">
                </div>
            </div>

            <!----- sidebar ----->
            <div class="admin-sidebar" id="adminSidebar">
                <div class="sidebar-header">
                    <h3>ADMIN</h3>
                    <button class="close-sidebar">
                        <img src="../../imagenes/logos/perfil.png" alt="Admin" class="admin-avatar">
                    </button>
                </div>
                
                <nav class="sidebar-menu">
                    <a href="../Dashboard_Usuario/mis-articulos.php">
                        <span>Mis Artículos</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </a>
                    
                    <a href="#">
                        <span>Configuración</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                    </a>
                    
                    <a href="#">
                        <span>Post Planeados</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </a>
                    
                    <a href="#">
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