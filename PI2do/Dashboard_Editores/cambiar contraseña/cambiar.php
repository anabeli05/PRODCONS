<<<<<<< HEAD
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

$Usuario_ID = $_SESSION['Usuario_ID'];
$mensaje_exito = '';
$errores = [];

// Función para validar el formulario
function validarFormulario($currentPassword, $newPassword, $confirmPassword) {
    $errores = [];
    
    // Validar que las nuevas contraseñas coincidan
    if ($newPassword !== $confirmPassword) {
        $errores[] = 'Las nuevas contraseñas no coinciden';
    }
    
    // Validar fortaleza de la contraseña (puedes ajustar los criterios)
    if (strlen($newPassword) < 8) {
        $errores[] = 'La nueva contraseña debe tener al menos 8 caracteres';
    }
    // Puedes añadir más validaciones como caracteres especiales, números, mayúsculas, etc.
    
    return $errores;
}

// Manejar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['nueva_password'] ?? '';
    $confirmPassword = $_POST['confirmar_password'] ?? '';
    
    // Validar el formulario básico
    $errores = validarFormulario($currentPassword, $newPassword, $confirmPassword);
    
    // Si no hay errores de formato, validar la contraseña actual contra la base de datos
    if (empty($errores)) {
        $conexion = new Conexion();
        try {
            $conexion->abrir_conexion();
            $conn = $conexion->conexion;

        $stmt = $conn->prepare("SELECT Contraseña FROM usuarios WHERE Usuario_ID = ?");
            if (!$stmt) {
                 throw new Exception("Error preparando consulta: " . $conn->error);
            }
            $stmt->bind_param("i", $Usuario_ID);
        $stmt->execute();
        $stmt->bind_result($hash_actual);
            
        if ($stmt->fetch()) {
            if (!password_verify($currentPassword, $hash_actual)) {
                $errores[] = 'La contraseña actual es incorrecta';
                } else if ($newPassword === $currentPassword) { // Validar también si la nueva es igual a la actual después de verificar la actual
                     $errores[] = 'La nueva contraseña debe ser diferente a la actual';
            }
        } else {
                $errores[] = 'Usuario no encontrado en la base de datos'; // Esto no debería pasar si está logueado, pero es una seguridad
        }
        $stmt->close();
    
    // Si no hay errores, proceder con el cambio de contraseña
    if (empty($errores)) {
        $hash_nueva = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt_update = $conn->prepare("UPDATE usuarios SET Contraseña = ? WHERE Usuario_ID = ?");
                if (!$stmt_update) {
                    throw new Exception("Error preparando actualización: " . $conn->error);
                }
                $stmt_update->bind_param("si", $hash_nueva, $Usuario_ID);
                
                if ($stmt_update->execute()) {
                    // Registrar el cambio en el historial (opcional)
            $accion = "Cambio de contraseña";
            $fecha = date('Y-m-d H:i:s');
            $stmt_hist = $conn->prepare("INSERT INTO historial_datos_usuario (Usuario_ID, Accion, Fecha) VALUES (?, ?, ?)");
                     if ($stmt_hist) {
                         $stmt_hist->bind_param("iss", $Usuario_ID, $accion, $fecha);
            $stmt_hist->execute();
            $stmt_hist->close();
                     }

                    // Opcional: Cerrar sesión después de cambiar contraseña por seguridad
                    // session_destroy(); 
                    // header('Location: ../../inicio_sesion/login.php?success=password_changed');
                    // exit();
                    
                    $mensaje_exito = 'Contraseña actualizada exitosamente.';

        } else {
                    $errores[] = 'Error al actualizar la contraseña en la base de datos.';
                }
                 if ($stmt_update) $stmt_update->close();
            }

        } catch (Exception $e) {
            $errores[] = 'Error de base de datos: ' . $e->getMessage();
             error_log("Error en cambiar contraseña: " . $e->getMessage());
        } finally {
             if ($conexion && $conexion->conexion) {
                 $conexion->cerrar_conexion();
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
    <title>PRODCONS - Cambiar Contraseña</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../inicio_sesion/login.css">
    
    <!--Tailwind CSS-->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- CSS de traduccion -->
    <link rel="stylesheet" href="../../Dashboard_Editores/Dashboard/traduccion.css">
    <!-- Scripts de traducción -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>

    <style>
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
        .success-message {
            color: #28a745;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
    </style>
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
        </div>
    </section>

    <div class="background-animation">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
    </div>
    
    <section class="contenedor-main">
        <section class="wrapper">
            <div class="form" id="cambiar-password-form">
        <h1>CAMBIAR CONTRASEÑA</h1>
        
                <?php if (!empty($errores)): ?>
            <?php foreach ($errores as $error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
                <?php if ($mensaje_exito): ?>
                    <div class="success-message"><?php echo htmlspecialchars($mensaje_exito); ?></div>
                <?php endif; ?>

            <div class="instrucciones">
                <p>Ingresa tu contraseña actual y tu nueva contraseña.</p>
            </div>

                <form action="" method="POST">
            <div class="buton">
                <div class="input-area">
                            <input type="password" name="currentPassword" placeholder="Contraseña Actual" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <div class="buton">
                <div class="input-area">
                            <input type="password" name="nueva_password" placeholder="Nueva Contraseña" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <div class="buton">
                <div class="input-area">
                            <input type="password" name="confirmar_password" placeholder="Confirmar Nueva Contraseña" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <input type="submit" value="CAMBIAR CONTRASEÑA">
            
            <div class="alternar-form">
                        <p><a href="../../Dashboard_Editores/inicio/inicio.php">Volver al Panel</a></p>
            </div>
        </form>
    </div>

            <div class="contenedor-logo">
                <img src="../../imagenes/login.png" alt="Imagen de fondo" class="bg-image">
                <figure>
                    <img src="../../imagenes/prodcon/logoSinfondo.png" alt="Logo transparente" class="logo-portada">
                </figure>
            </div>
        </section>
    </section>
</body>
=======
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

$Usuario_ID = $_SESSION['Usuario_ID'];
$mensaje_exito = '';
$errores = [];

// Función para validar el formulario
function validarFormulario($currentPassword, $newPassword, $confirmPassword) {
    $errores = [];
    
    // Validar que las nuevas contraseñas coincidan
    if ($newPassword !== $confirmPassword) {
        $errores[] = 'Las nuevas contraseñas no coinciden';
    }
    
    // Validar fortaleza de la contraseña (puedes ajustar los criterios)
    if (strlen($newPassword) < 8) {
        $errores[] = 'La nueva contraseña debe tener al menos 8 caracteres';
    }
    // Puedes añadir más validaciones como caracteres especiales, números, mayúsculas, etc.
    
    return $errores;
}

// Manejar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['nueva_password'] ?? '';
    $confirmPassword = $_POST['confirmar_password'] ?? '';
    
    // Validar el formulario básico
    $errores = validarFormulario($currentPassword, $newPassword, $confirmPassword);
    
    // Si no hay errores de formato, validar la contraseña actual contra la base de datos
    if (empty($errores)) {
        $conexion = new Conexion();
        try {
            $conexion->abrir_conexion();
            $conn = $conexion->conexion;

        $stmt = $conn->prepare("SELECT Contraseña FROM usuarios WHERE Usuario_ID = ?");
            if (!$stmt) {
                 throw new Exception("Error preparando consulta: " . $conn->error);
            }
            $stmt->bind_param("i", $Usuario_ID);
        $stmt->execute();
        $stmt->bind_result($hash_actual);
            
        if ($stmt->fetch()) {
            if (!password_verify($currentPassword, $hash_actual)) {
                $errores[] = 'La contraseña actual es incorrecta';
                } else if ($newPassword === $currentPassword) { // Validar también si la nueva es igual a la actual después de verificar la actual
                     $errores[] = 'La nueva contraseña debe ser diferente a la actual';
            }
        } else {
                $errores[] = 'Usuario no encontrado en la base de datos'; // Esto no debería pasar si está logueado, pero es una seguridad
        }
        $stmt->close();
    
    // Si no hay errores, proceder con el cambio de contraseña
    if (empty($errores)) {
        $hash_nueva = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt_update = $conn->prepare("UPDATE usuarios SET Contraseña = ? WHERE Usuario_ID = ?");
                if (!$stmt_update) {
                    throw new Exception("Error preparando actualización: " . $conn->error);
                }
                $stmt_update->bind_param("si", $hash_nueva, $Usuario_ID);
                
                if ($stmt_update->execute()) {
                    // Registrar el cambio en el historial (opcional)
            $accion = "Cambio de contraseña";
            $fecha = date('Y-m-d H:i:s');
            $stmt_hist = $conn->prepare("INSERT INTO historial_datos_usuario (Usuario_ID, Accion, Fecha) VALUES (?, ?, ?)");
                     if ($stmt_hist) {
                         $stmt_hist->bind_param("iss", $Usuario_ID, $accion, $fecha);
            $stmt_hist->execute();
            $stmt_hist->close();
                     }

                    // Opcional: Cerrar sesión después de cambiar contraseña por seguridad
                    // session_destroy(); 
                    // header('Location: ../../inicio_sesion/login.php?success=password_changed');
                    // exit();
                    
                    $mensaje_exito = 'Contraseña actualizada exitosamente.';

        } else {
                    $errores[] = 'Error al actualizar la contraseña en la base de datos.';
                }
                 if ($stmt_update) $stmt_update->close();
            }

        } catch (Exception $e) {
            $errores[] = 'Error de base de datos: ' . $e->getMessage();
             error_log("Error en cambiar contraseña: " . $e->getMessage());
        } finally {
             if ($conexion && $conexion->conexion) {
                 $conexion->cerrar_conexion();
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
    <title>PRODCONS - Cambiar Contraseña</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../inicio_sesion/login.css">
    
    <!--Tailwind CSS-->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- CSS de traduccion -->
    <link rel="stylesheet" href="../../Dashboard_Editores/Dashboard/traduccion.css">
    <!-- Scripts de traducción -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>

    <style>
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
        .success-message {
            color: #28a745;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
    </style>
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
        </div>
    </section>

    <div class="background-animation">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
    </div>
    
    <section class="contenedor-main">
        <section class="wrapper">
            <div class="form" id="cambiar-password-form">
        <h1>CAMBIAR CONTRASEÑA</h1>
        
                <?php if (!empty($errores)): ?>
            <?php foreach ($errores as $error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
                <?php if ($mensaje_exito): ?>
                    <div class="success-message"><?php echo htmlspecialchars($mensaje_exito); ?></div>
                <?php endif; ?>

            <div class="instrucciones">
                <p>Ingresa tu contraseña actual y tu nueva contraseña.</p>
            </div>

                <form action="" method="POST">
            <div class="buton">
                <div class="input-area">
                            <input type="password" name="currentPassword" placeholder="Contraseña Actual" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <div class="buton">
                <div class="input-area">
                            <input type="password" name="nueva_password" placeholder="Nueva Contraseña" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <div class="buton">
                <div class="input-area">
                            <input type="password" name="confirmar_password" placeholder="Confirmar Nueva Contraseña" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <input type="submit" value="CAMBIAR CONTRASEÑA">
            
            <div class="alternar-form">
                        <p><a href="../../Dashboard_Editores/inicio/inicio.php">Volver al Panel</a></p>
            </div>
        </form>
    </div>

            <div class="contenedor-logo">
                <img src="../../imagenes/login.png" alt="Imagen de fondo" class="bg-image">
                <figure>
                    <img src="../../imagenes/prodcon/logoSinfondo.png" alt="Logo transparente" class="logo-portada">
                </figure>
            </div>
        </section>
    </section>
</body>
>>>>>>> main
</html>