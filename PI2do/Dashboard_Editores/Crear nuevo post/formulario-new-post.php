<<<<<<< HEAD
<?php
session_start();
include '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
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
        $introduccion = $_POST['introduccion'] ?? '';
        $color = $_POST['color'] ?? '';
        $contenido = $_POST['contenido'] ?? '';
        $bibliografias = $_POST['bibliografias'] ?? '';
        $conclusion = $_POST['conclusion'] ?? '';
        $fecha_publicacion = $_POST['fecha_publicacion'] ?? date('Y-m-d');
        $estado = 'Publicado'; // Estado inicial
        $motivo_rechazo = ''; // Inicialmente vacío
        
        // Validación de campos requeridos
        if (empty($titulo)) $errores[] = 'El título es obligatorio';
        if (empty($contenido)) $errores[] = 'El contenido es obligatorio';
        if (empty($bibliografias)) $errores[] = 'La bibliografía es obligatoria';
        if (empty($fecha_publicacion)) $errores[] = 'La fecha de publicación es obligatoria';
        
        // Validar que se haya subido al menos una imagen
        if (empty($_FILES['imagenes']['name'][0])) {
            $errores[] = 'Debe seleccionar al menos una imagen para el artículo';
        }
        
        if (empty($errores)) {
            try {
                $conexion = new Conexion();
                $conexion->abrir_conexion();
                $conexion->conexion->begin_transaction();
                
                // Insertar el artículo
                $sql = "INSERT INTO articulos (
                    Titulo, Introduccion, Color, Contenido, Bibliografias, 
                    Conclusion, Usuario_ID, `Fecha de Creacion`, `Fecha de Publicacion`, 
                    Estado, `Motivo de Rechazo`
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)";
                
                $stmt = $conexion->conexion->prepare($sql);
                $stmt->bind_param(
                    "ssssssssss",
                    $titulo,
                    $introduccion,
                    $color,
                    $contenido,
                    $bibliografias,
                    $conclusion,
                    $_SESSION['Usuario_ID'],
                    $fecha_publicacion,
                    $estado,
                    $motivo_rechazo
                );
                
                if ($stmt->execute()) {
                    $articulo_id = $conexion->conexion->insert_id;
                    
                    // Procesar las imágenes si se han subido
                    if (!empty($_FILES['imagenes']['name'][0])) {
                        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                            if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                                // Generar un nombre único para la imagen
                                $nombre_original = $_FILES['imagenes']['name'][$key];
                                $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
                                $nombre_unico = uniqid() . '.' . $extension;
                                
                                // Ruta donde se guardará la imagen
                                $ruta_destino = '../../uploads/' . $nombre_unico;
                                
                                // Mover la imagen al directorio de uploads
                                if (move_uploaded_file($tmp_name, $ruta_destino)) {
                                    // Guardar la ruta relativa en la base de datos
                                    $ruta_imagen = '/PRODCONS/PI2do/uploads/' . $nombre_unico;
                                    $descripcion = $_POST['descripcion_imagen'][$key] ?? '';
                                    $orden = $_POST['orden_imagen'][$key] ?? '1';
                                    
                                    $sql_imagen = "INSERT INTO imagenes_articulos (Articulo_ID, Url_Imagen, Descripcion, Orden_Imagen) 
                                                 VALUES (?, ?, ?, ?)";
                                    $stmt_imagen = $conexion->conexion->prepare($sql_imagen);
                                    $stmt_imagen->bind_param("isss", 
                                        $articulo_id,
                                        $ruta_imagen,
                                        $descripcion,
                                        $orden
                                    );
                                    
                                    if (!$stmt_imagen->execute()) {
                                        throw new Exception("Error al guardar la imagen: " . $stmt_imagen->error);
                                    }
                                    $stmt_imagen->close();
                                } else {
                                    throw new Exception("Error al mover la imagen al servidor");
                                }
                            }
                        }
                    }
                    
                    $conexion->conexion->commit();
                    // Redirigir a la página de Mis Artículos después de crear exitosamente
                    header('Location: ../MisArticulos/mis-articulos.php');
                    exit();
                } else {
                    throw new Exception("Error al crear el artículo: " . $stmt->error);
                }
                
                $stmt->close();
                $conexion->cerrar_conexion();
                
                
            } catch (Exception $e) {
                $conexion->conexion->rollback();
                $errores[] = 'Error al crear el artículo: ' . $e->getMessage();
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
    <link href="post-form.css" rel="stylesheet">
    
    <!-- CSS Y JS DE HEADER-->
    <link rel="stylesheet" href='../Dashboard/sidebar.css'>
    <script src='../Dashboard/barra-nav.js' defer></script>
    
    <!-- CSS de traduccion -->
    <link rel="stylesheet" href="../../Dashboard_Editores/Dashboard/traduccion.css">
    <!-- Scripts de traducción -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>
</head>

<body>
<header>
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
        <form action="/PRODCONS/PI2do/Dashboard_Editores/Crear%20nuevo%20post/formulario-new-post.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <label for="titulo">Título del Artículo:</label>
            <input type="text" id="titulo" name="titulo" required maxlength="255" 
                value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>">
            
            <label for="introduccion">Introducción:</label>
            <textarea id="introduccion" name="introduccion" maxlength="700"><?php echo isset($_POST['introduccion']) ? htmlspecialchars($_POST['introduccion']) : ''; ?></textarea>
            
            <label for="color">Color:</label>
            <input type="color" id="color" name="color" 
                value="<?php echo isset($_POST['color']) ? htmlspecialchars($_POST['color']) : '#ffffff'; ?>">
            
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" required rows="8"><?php echo isset($_POST['contenido']) ? htmlspecialchars($_POST['contenido']) : ''; ?></textarea>
            
            <label for="bibliografias">Bibliografías:</label>
            <textarea id="bibliografias" name="bibliografias" required rows="4"><?php echo isset($_POST['bibliografias']) ? htmlspecialchars($_POST['bibliografias']) : ''; ?></textarea>
            
            <label for="conclusion">Conclusión:</label>
            <textarea id="conclusion" name="conclusion" maxlength="700"><?php echo isset($_POST['conclusion']) ? htmlspecialchars($_POST['conclusion']) : ''; ?></textarea>
            
            <div class="imagenes-container">
                <label>Imágenes del Artículo: <span style="color: red;">*</span></label>
                <div id="imagen-inputs">
                    <div class="imagen-input">
                        <input type="file" name="imagenes[]" accept="image/*" required>
                        <input type="text" name="descripcion_imagen[]" placeholder="Descripción de la imagen" maxlength="200">
                        <select name="orden_imagen[]">
                            <option value="1">Imagen 1</option>
                            <option value="2">Imagen 2</option>
                            <option value="3">Imagen 3</option>
                            <option value="4">Imagen 4</option>
                            <option value="5">Imagen 5</option>
                            <option value="6">Imagen 6</option>
                        </select>
                    </div>
                </div>
                <button type="button" onclick="agregarImagenInput()">+ Agregar otra imagen</button>
            </div>

            <label for="fecha_publicacion">Fecha de Publicación:</label>
            <input type="date" id="fecha_publicacion" name="fecha_publicacion" required 
                value="<?php echo isset($_POST['fecha_publicacion']) ? htmlspecialchars($_POST['fecha_publicacion']) : date('Y-m-d'); ?>">
            
            <button type="submit">Guardar Artículo</button>

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

            // Validación del formulario antes del envío
            document.querySelector('form').addEventListener('submit', function(e) {
                const imagenes = document.querySelectorAll('input[type="file"]');
                let tieneImagen = false;
                
                imagenes.forEach(input => {
                    if (input.files.length > 0) tieneImagen = true;
                });
                
                if (!tieneImagen) {
                    e.preventDefault();
                    alert('Debe seleccionar al menos una imagen para el artículo');
                    return false;
                }
            });
            </script>
        </form>
    </div>



</body>
=======
<?php
session_start();
include '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
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
        $introduccion = $_POST['introduccion'] ?? '';
        $color = $_POST['color'] ?? '';
        $contenido = $_POST['contenido'] ?? '';
        $bibliografias = $_POST['bibliografias'] ?? '';
        $conclusion = $_POST['conclusion'] ?? '';
        $fecha_publicacion = $_POST['fecha_publicacion'] ?? date('Y-m-d');
        $estado = 'Publicado'; // Estado inicial
        $motivo_rechazo = ''; // Inicialmente vacío
        
        // Validación de campos requeridos
        if (empty($titulo)) $errores[] = 'El título es obligatorio';
        if (empty($contenido)) $errores[] = 'El contenido es obligatorio';
        if (empty($bibliografias)) $errores[] = 'La bibliografía es obligatoria';
        if (empty($fecha_publicacion)) $errores[] = 'La fecha de publicación es obligatoria';
        
        // Validar que se haya subido al menos una imagen
        if (empty($_FILES['imagenes']['name'][0])) {
            $errores[] = 'Debe seleccionar al menos una imagen para el artículo';
        }
        
        if (empty($errores)) {
            try {
                $conexion = new Conexion();
                $conexion->abrir_conexion();
                $conexion->conexion->begin_transaction();
                
                // Insertar el artículo
                $sql = "INSERT INTO articulos (
                    Titulo, Introduccion, Color, Contenido, Bibliografias, 
                    Conclusion, Usuario_ID, `Fecha de Creacion`, `Fecha de Publicacion`, 
                    Estado, `Motivo de Rechazo`
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)";
                
                $stmt = $conexion->conexion->prepare($sql);
                $stmt->bind_param(
                    "ssssssssss",
                    $titulo,
                    $introduccion,
                    $color,
                    $contenido,
                    $bibliografias,
                    $conclusion,
                    $_SESSION['Usuario_ID'],
                    $fecha_publicacion,
                    $estado,
                    $motivo_rechazo
                );
                
                if ($stmt->execute()) {
                    $articulo_id = $conexion->conexion->insert_id;
                    
                    // Procesar las imágenes si se han subido
                    if (!empty($_FILES['imagenes']['name'][0])) {
                        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                            if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                                // Generar un nombre único para la imagen
                                $nombre_original = $_FILES['imagenes']['name'][$key];
                                $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
                                $nombre_unico = uniqid() . '.' . $extension;
                                
                                // Ruta donde se guardará la imagen
                                $ruta_destino = '../../uploads/' . $nombre_unico;
                                
                                // Mover la imagen al directorio de uploads
                                if (move_uploaded_file($tmp_name, $ruta_destino)) {
                                    // Guardar la ruta relativa en la base de datos
                                    $ruta_imagen = '/PRODCONS/PI2do/uploads/' . $nombre_unico;
                                    $descripcion = $_POST['descripcion_imagen'][$key] ?? '';
                                    $orden = $_POST['orden_imagen'][$key] ?? '1';
                                    
                                    $sql_imagen = "INSERT INTO imagenes_articulos (Articulo_ID, Url_Imagen, Descripcion, Orden_Imagen) 
                                                 VALUES (?, ?, ?, ?)";
                                    $stmt_imagen = $conexion->conexion->prepare($sql_imagen);
                                    $stmt_imagen->bind_param("isss", 
                                        $articulo_id,
                                        $ruta_imagen,
                                        $descripcion,
                                        $orden
                                    );
                                    
                                    if (!$stmt_imagen->execute()) {
                                        throw new Exception("Error al guardar la imagen: " . $stmt_imagen->error);
                                    }
                                    $stmt_imagen->close();
                                } else {
                                    throw new Exception("Error al mover la imagen al servidor");
                                }
                            }
                        }
                    }
                    
                    $conexion->conexion->commit();
                    // Redirigir a la página de Mis Artículos después de crear exitosamente
                    header('Location: ../MisArticulos/mis-articulos.php');
                    exit();
                } else {
                    throw new Exception("Error al crear el artículo: " . $stmt->error);
                }
                
                $stmt->close();
                $conexion->cerrar_conexion();
                
                
            } catch (Exception $e) {
                $conexion->conexion->rollback();
                $errores[] = 'Error al crear el artículo: ' . $e->getMessage();
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
    <link href="post-form.css" rel="stylesheet">
    
    <!-- CSS Y JS DE HEADER-->
    <link rel="stylesheet" href='../Dashboard/sidebar.css'>
    <script src='../Dashboard/barra-nav.js' defer></script>
    
    <!-- CSS de traduccion -->
    <link rel="stylesheet" href="../../Dashboard_Editores/Dashboard/traduccion.css">
    <!-- Scripts de traducción -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>
</head>

<body>
<header>
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
        <form action="/PRODCONS/PI2do/Dashboard_Editores/Crear%20nuevo%20post/formulario-new-post.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <label for="titulo">Título del Artículo:</label>
            <input type="text" id="titulo" name="titulo" required maxlength="255" 
                value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>">
            
            <label for="introduccion">Introducción:</label>
            <textarea id="introduccion" name="introduccion" maxlength="700"><?php echo isset($_POST['introduccion']) ? htmlspecialchars($_POST['introduccion']) : ''; ?></textarea>
            
            <label for="color">Color:</label>
            <input type="color" id="color" name="color" 
                value="<?php echo isset($_POST['color']) ? htmlspecialchars($_POST['color']) : '#ffffff'; ?>">
            
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" required rows="8"><?php echo isset($_POST['contenido']) ? htmlspecialchars($_POST['contenido']) : ''; ?></textarea>
            
            <label for="bibliografias">Bibliografías:</label>
            <textarea id="bibliografias" name="bibliografias" required rows="4"><?php echo isset($_POST['bibliografias']) ? htmlspecialchars($_POST['bibliografias']) : ''; ?></textarea>
            
            <label for="conclusion">Conclusión:</label>
            <textarea id="conclusion" name="conclusion" maxlength="700"><?php echo isset($_POST['conclusion']) ? htmlspecialchars($_POST['conclusion']) : ''; ?></textarea>
            
            <div class="imagenes-container">
                <label>Imágenes del Artículo: <span style="color: red;">*</span></label>
                <div id="imagen-inputs">
                    <div class="imagen-input">
                        <input type="file" name="imagenes[]" accept="image/*" required>
                        <input type="text" name="descripcion_imagen[]" placeholder="Descripción de la imagen" maxlength="200">
                        <select name="orden_imagen[]">
                            <option value="1">Imagen 1</option>
                            <option value="2">Imagen 2</option>
                            <option value="3">Imagen 3</option>
                            <option value="4">Imagen 4</option>
                            <option value="5">Imagen 5</option>
                            <option value="6">Imagen 6</option>
                        </select>
                    </div>
                </div>
                <button type="button" onclick="agregarImagenInput()">+ Agregar otra imagen</button>
            </div>

            <label for="fecha_publicacion">Fecha de Publicación:</label>
            <input type="date" id="fecha_publicacion" name="fecha_publicacion" required 
                value="<?php echo isset($_POST['fecha_publicacion']) ? htmlspecialchars($_POST['fecha_publicacion']) : date('Y-m-d'); ?>">
            
            <button type="submit">Guardar Artículo</button>

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

            // Validación del formulario antes del envío
            document.querySelector('form').addEventListener('submit', function(e) {
                const imagenes = document.querySelectorAll('input[type="file"]');
                let tieneImagen = false;
                
                imagenes.forEach(input => {
                    if (input.files.length > 0) tieneImagen = true;
                });
                
                if (!tieneImagen) {
                    e.preventDefault();
                    alert('Debe seleccionar al menos una imagen para el artículo');
                    return false;
                }
            });
            </script>
        </form>
    </div>



</body>
>>>>>>> main
</html>