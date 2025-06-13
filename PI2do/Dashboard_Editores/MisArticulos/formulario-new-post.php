<?php
session_start();
include '../../Base de datos/conexion.php';

// Crear instancia de la clase Conexion y establecer la conexión
try {
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

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
    } elseif (strlen($titulo) > 255) {
        $errores[] = 'El título no puede exceder 255 caracteres';
    }

    // Validar contenido
    if (strlen($contenido) < 50) {
        $errores[] = 'El contenido debe tener al menos 50 caracteres';
    }

    // Validar imágenes
    if (isset($imagenes)) {
        $max_imagenes = 6;
        $max_tamano = 5 * 1024 * 1024; // 5MB
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (count($imagenes['name']) > $max_imagenes) {
            $errores[] = 'Solo puedes subir hasta 6 imágenes';
        } else {
            foreach ($imagenes['name'] as $key => $nombre) {
                if (!empty($nombre) && !in_array($imagenes['type'][$key], $tipos_permitidos)) {
                    $errores[] = 'Solo se permiten imágenes JPG, PNG y GIF';
                    break;
                }
                if (!empty($nombre) && $imagenes['size'][$key] > $max_tamano) {
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
        'introduccion' => $_POST['introduccion'] ?? '',
        'color' => $_POST['color'] ?? '',
        'contenido' => $_POST['contenido'] ?? '',
        'conclusion' => $_POST['conclusion'] ?? '',
        'etiquetas' => $_POST['etiquetas'] ?? '',
        'comentario_autor' => $_POST['comentario_autor'] ?? '',
        'bibliografias' => $_POST['bibliografias'] ?? ''
    ];

    if (empty($errores)) {
        // Verificar que el Usuario_ID esté disponible en la sesión
        if (!isset($_SESSION['Usuario_ID']) || empty($_SESSION['Usuario_ID'])) {
            $_SESSION['errores'][] = "Error: Sesión de usuario no válida. Por favor, inicia sesión nuevamente.";
            header('Location: ../../inicio_sesion/login.php');
            exit();
        }
        
        $titulo = trim($_POST['titulo']);
        $introduccion = trim($_POST['introduccion'] ?? '');
        $color = $_POST['color'] ?? '#ffffff';
        $contenido = trim($_POST['contenido']);
        $conclusion = trim($_POST['conclusion'] ?? '');
        $usuario_id = $_SESSION['Usuario_ID'];
        $fecha_publicacion = date('Y-m-d');
        $estado = 'Borrador';
        $motivo_rechazo = '';
        $bibliografias = trim($_POST['bibliografias'] ?? '');
        
        // Debug: Verificar el valor del usuario_id
        error_log("Usuario_ID en sesión: " . $usuario_id);

        // Manejo de imágenes - se guardarán después de insertar el artículo
        $imagenes_data = [];
        if (isset($_FILES['imagenes']) && isset($_FILES['imagenes']['tmp_name']) && is_array($_FILES['imagenes']['tmp_name'])) {
            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['imagenes']['error'][$key] === 0 && !empty($tmp_name)) {
                    $imagen_contenido = file_get_contents($tmp_name);
                    $descripcion = $_POST['descripcion_imagen'][$key] ?? '';
                    $orden = $_POST['orden_imagen'][$key] ?? ($key + 1);
                    
                    $imagenes_data[] = [
                        'contenido' => $imagen_contenido,
                        'descripcion' => $descripcion,
                        'orden' => $orden
                    ];
                }
            }
        }

        // Prepara el statement con todos los campos requeridos
        $stmt = $conn->prepare("INSERT INTO articulos (
            Titulo, 
            Introduccion,
            Color,
            Contenido, 
            Bibliografias,
            Conclusion,
            Usuario_ID, 
            `Fecha de Creacion`,
            `Fecha de Publicacion`,
            Estado,
            `Motivo de Rechazo`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)");
        
        if (!$stmt) {
            $_SESSION['errores'][] = "Error en prepare: " . $conn->error;
            header('Location: formulario-new-post.php');
            exit();
        }

        $stmt->bind_param(
            "ssssssssss",
            $titulo,
            $introduccion,
            $color,
            $contenido,
            $bibliografias,
            $conclusion,
            $usuario_id,
            $fecha_publicacion,
            $estado,
            $motivo_rechazo
        );

        if ($stmt->execute()) {
            $articulo_id = $conn->insert_id;
            
            // Insertar las imágenes en la tabla imagenes_articulos
            if (!empty($imagenes_data)) {
                $stmt_img = $conn->prepare("INSERT INTO imagenes_articulos (Articulo_ID, Url_Imagen, Descripcion, Orden_Imagen) VALUES (?, ?, ?, ?)");
                
                foreach ($imagenes_data as $imagen) {
                    $stmt_img->bind_param("isss", 
                        $articulo_id,
                        $imagen['contenido'],
                        $imagen['descripcion'],
                        $imagen['orden']
                    );
                    $stmt_img->execute();
                }
                $stmt_img->close();
            }
            
            $_SESSION['mensaje'] = 'Post creado exitosamente';
            unset($_SESSION['datos_formulario']);
            $stmt->close();
            header('Location: mis-articulos.php');
            exit();
        } else {
            $_SESSION['errores'][] = "Error al guardar el post: " . $stmt->error;
            $stmt->close();
            header('Location: formulario-new-post.php');
            exit();
        }
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

    <header> 
        <div class="header-contenedor">
            <div class="principal"></div>
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
                    <a href='../inicio/inicio.php'>
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
                       minlength="5" maxlength="255"
                       value="<?php echo isset($_SESSION['datos_formulario']['titulo']) ? htmlspecialchars($_SESSION['datos_formulario']['titulo']) : ''; ?>"
                       oninput="validarTitulo(this)">
                <span class="error-message" id="titulo-error"></span>
            </div>
            
            <div class="form-group">
                <label for="introduccion">Introducción:</label>
                <textarea id="introduccion" name="introduccion" rows="3" maxlength="700"
                          placeholder="Breve introducción del artículo..."><?php echo isset($_SESSION['datos_formulario']['introduccion']) ? htmlspecialchars($_SESSION['datos_formulario']['introduccion']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="color">Color del tema:</label>
                <input type="color" id="color" name="color" 
                       value="<?php echo isset($_SESSION['datos_formulario']['color']) ? htmlspecialchars($_SESSION['datos_formulario']['color']) : '#ffffff'; ?>">
            </div>

            <div class="form-group">
                <label for="contenido">Contenido:</label>
                <textarea id="contenido" name="contenido" rows="8" required
                          minlength="50" oninput="validarContenido(this)"><?php echo isset($_SESSION['datos_formulario']['contenido']) ? htmlspecialchars($_SESSION['datos_formulario']['contenido']) : ''; ?></textarea>
                <span class="error-message" id="contenido-error"></span>
            </div>
            
            <div class="form-group">
                <label>Imágenes del Artículo:</label>
                <div id="imagen-inputs">
                    <div class="imagen-input">
                        <input type="file" name="imagenes[]" accept="image/*">
                        <input type="text" name="descripcion_imagen[]" placeholder="Descripción de la imagen" maxlength="200">
                        <select name="orden_imagen[]">
                            <option value="1">Imagen 1</option>
                            <option value="2">Imagen 2</option>
                            <option value="3">Imagen 3</option>
                            <option value="4">Imagen 4</option>
                            <option value="5">Imagen 5</option>
                            <option value="6">Imagen 6</option>
                        </select>
                        <button type="button" onclick="this.parentElement.remove()">Eliminar</button>
                    </div>
                </div>
                <button type="button" onclick="agregarImagenInput()">+ Agregar otra imagen</button>
            </div>

            <div class="form-group">
                <label for="conclusion">Conclusión:</label>
                <textarea id="conclusion" name="conclusion" rows="3" maxlength="700"
                          placeholder="Conclusión del artículo..."><?php echo isset($_SESSION['datos_formulario']['conclusion']) ? htmlspecialchars($_SESSION['datos_formulario']['conclusion']) : ''; ?></textarea>
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

    <script>
    function agregarImagenInput() {
        const container = document.getElementById('imagen-inputs');
        const imagenes = container.querySelectorAll('.imagen-input');
        
        if (imagenes.length >= 6) {
            alert('Máximo 6 imágenes permitidas');
            return;
        }
        
        const div = document.createElement('div');
        div.className = 'imagen-input';
        div.innerHTML = `
            <input type="file" name="imagenes[]" accept="image/*">
            <input type="text" name="descripcion_imagen[]" placeholder="Descripción de la imagen" maxlength="200">
            <select name="orden_imagen[]">
                <option value="1">Imagen 1</option>
                <option value="2">Imagen 2</option>
                <option value="3">Imagen 3</option>
                <option value="4">Imagen 4</option>
                <option value="5">Imagen 5</option>
                <option value="6">Imagen 6</option>
            </select>
            <button type="button" onclick="this.parentElement.remove()">Eliminar</button>
        `;
        container.appendChild(div);
    }

    function validarTitulo(input) {
        const error = document.getElementById('titulo-error');
        if (input.value.length < 5) {
            error.textContent = 'El título debe tener al menos 5 caracteres';
        } else if (input.value.length > 255) {
            error.textContent = 'El título no puede exceder 255 caracteres';
        } else {
            error.textContent = '';
        }
    }

    function validarContenido(input) {
        const error = document.getElementById('contenido-error');
        if (input.value.length < 50) {
            error.textContent = 'El contenido debe tener al menos 50 caracteres';
        } else {
            error.textContent = '';
        }
    }
    </script>

</body>
</html>

<?php
// Cerrar la conexión al final del script
if (isset($conexion)) {
    $conexion->cerrar_conexion();
}
?>
