<<<<<<< HEAD
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['codigo_recuperacion'])) {
    header("Location: codigo.php");
    exit();
}

if (time() > $_SESSION['codigo_expiracion']) {
    session_unset();
    session_destroy();
    $_SESSION['error'] = "El código ha expirado. Por favor solicita uno nuevo.";
    header("Location: codigo.php");
    exit();
}

include '../Base de datos/conexion.php';
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_ingresado = $_POST['codigo'];
    $nueva_password = $_POST['nueva_password'];
    $confirmar_password = $_POST['confirmar_password'];
    
    if ($codigo_ingresado != $_SESSION['codigo_recuperacion']) {
        $error = "Código de verificación incorrecto";
    }
    elseif ($nueva_password != $confirmar_password) {
        $error = "Las contraseñas no coinciden";
    }
    else {
        $hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET Contraseña = ? WHERE Correo = ?");
        $stmt->bind_param("ss", $hash, $_SESSION['correo_recuperacion']);
        
        if ($stmt->execute()) {
            // Password updated successfully
            $stmt->close();

            // --- Log password change in historial de datos usuario table ---
            // Get Usuario_ID from the usuarios table using the email
            $stmt_user_id = $conn->prepare("SELECT Usuario_ID FROM usuarios WHERE Correo = ?");
            if ($stmt_user_id) {
                $stmt_user_id->bind_param("s", $_SESSION['correo_recuperacion']);
                $stmt_user_id->execute();
                $result_user_id = $stmt_user_id->get_result();
                $user_data = $result_user_id->fetch_assoc();
                $stmt_user_id->close();

                if ($user_data && isset($user_data['Usuario_ID'])) {
                    $usuario_id_log = $user_data['Usuario_ID'];
                    $campo_cambiado = 'Contraseña';
                    $valor_anterior = NULL; // We don't store the old password/hash
                    $valor_nuevo = '[Actualizado]'; // Indicate password was updated
                    $tipo_cambio = 'Actualización de contraseña';

                    // Insert into historial de datos usuario
                    $stmt_historial = $conn->prepare("INSERT INTO historial_de_datos_usuario (Usuario_ID, Campo, Valor_Anterior, Valor_Nuevo, Fecha_Cambio, Tipo_Cambio) VALUES (?, ?, ?, ?, NOW(), ?)");
                    if ($stmt_historial) {
                        $stmt_historial->bind_param("issss", $usuario_id_log, $campo_cambiado, $valor_anterior, $valor_nuevo, $tipo_cambio);
                        $stmt_historial->execute();
                        $stmt_historial->close();
                        // Log success or handle error if needed, but don't interrupt user flow for history log failure
                    } else {
                        error_log("Error preparando consulta para historial de datos: " . $conn->error);
                    }
                } else {
                    error_log("Usuario_ID no encontrado para el correo: " . $_SESSION['correo_recuperacion']);
                }
            } else {
                error_log("Error preparando consulta para obtener Usuario_ID: " . $conn->error);
            }
            // --- End logging ---
            
            session_unset();
            session_destroy();
            
            $_SESSION['success'] = "Contraseña actualizada correctamente. Ahora puedes iniciar sesión.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Error al actualizar la contraseña";
        }
    }
}

// Close the database connection after all operations
$conexion->cerrar_conexion();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Nueva Contraseña</title>
    <style>
        /* Estilos de la barra de navegación */
        .barra-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: rgb(225, 216, 204);
            display: flex;
            justify-content: flex-start;
            align-items: center;
            padding-left: 20px;
            border-bottom: 4px solid black;
            z-index: 1000;
        }
        
        .flecha-nav {
            margin-left: 0;
        }
        
        .flecha-nav a {
            display: flex;
            align-items: center;
            height: 50px;
        }
        
        .flecha-nav svg {
            width: 32px;
            height: 32px;
            fill: #000;
            cursor: pointer;
            margin: 0;
            padding: 0;
            transition: transform 0.2s;
        }
        
        .flecha-nav svg:hover {
            transform: scale(1.1);
            fill: #4CAF50;
        }
        
        /* Ajuste para el contenido principal */
        .contenedor-main {
            margin-top: 70px;
        }
        
        /* Estilos existentes */
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
    </style>
    <link rel="stylesheet" href="css/styles.css">
    <link href="login.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="barra-nav">
        <div class="flecha-nav">
            <a onclick="window.history.back()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                </svg>
            </a>
        </div>
    </nav>

    <div class="background-animation">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
    </div>

    <section class="contenedor-main">
        <section class="wrapper">
            <div class="form" id="nueva-contrasena-form">
                <h1>RESTABLECER CONTRASEÑA</h1>
                
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="buton">
                        <div class="input-area">
                            <input type="text" name="codigo" placeholder="Código de verificación" required>
                            <i class="fas fa-key"></i>
                        </div>
                    </div>

                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Contraseña" name="Contraseña" required id="password-registro">
                            <i class="fas fa-lock input-icon"></i>
                            <span class="toggle-password" onclick="togglePassword('password-registro', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Confirmar Contraseña" name="confirmar_password" required id="confirm-password-registro">
        <i class="fas fa-lock input-icon"></i>
        <span class="toggle-password" onclick="togglePassword('confirm-password-registro', this)">
            <i class="fas fa-eye"></i>
        </span>
    </div>
</div>

                    <input type="submit" value="CAMBIAR CONTRASEÑA"> 
                    
                    <div class="alternar-form">
                        <p>¿Recordaste tu contraseña? <a href='login.php'>Inicia sesión aquí</a></p>
                    </div>
                </form>
            </div>
            
            <div class="contenedor-logo">
                <img src="../imagenes/login.png" alt="Imagen de fondo" class="bg-image">
                <figure>
                    <img src="../imagenes/prodcon/logoSinfondo.png" alt="Logo transparente" class="logo-portada">
                </figure>
            </div>
        </section>
    </section>
    <script>
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<style>
.input-area {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #777;
}

.toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #777;
    z-index: 2;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
</body>
=======
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['codigo_recuperacion'])) {
    header("Location: codigo.php");
    exit();
}

if (time() > $_SESSION['codigo_expiracion']) {
    session_unset();
    session_destroy();
    $_SESSION['error'] = "El código ha expirado. Por favor solicita uno nuevo.";
    header("Location: codigo.php");
    exit();
}

include '../Base de datos/conexion.php';
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_ingresado = $_POST['codigo'];
    $nueva_password = $_POST['nueva_password'];
    $confirmar_password = $_POST['confirmar_password'];
    
    if ($codigo_ingresado != $_SESSION['codigo_recuperacion']) {
        $error = "Código de verificación incorrecto";
    }
    elseif ($nueva_password != $confirmar_password) {
        $error = "Las contraseñas no coinciden";
    }
    else {
        $hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET Contraseña = ? WHERE Correo = ?");
        $stmt->bind_param("ss", $hash, $_SESSION['correo_recuperacion']);
        
        if ($stmt->execute()) {
            // Password updated successfully
            $stmt->close();

            // --- Log password change in historial de datos usuario table ---
            // Get Usuario_ID from the usuarios table using the email
            $stmt_user_id = $conn->prepare("SELECT Usuario_ID FROM usuarios WHERE Correo = ?");
            if ($stmt_user_id) {
                $stmt_user_id->bind_param("s", $_SESSION['correo_recuperacion']);
                $stmt_user_id->execute();
                $result_user_id = $stmt_user_id->get_result();
                $user_data = $result_user_id->fetch_assoc();
                $stmt_user_id->close();

                if ($user_data && isset($user_data['Usuario_ID'])) {
                    $usuario_id_log = $user_data['Usuario_ID'];
                    $campo_cambiado = 'Contraseña';
                    $valor_anterior = NULL; // We don't store the old password/hash
                    $valor_nuevo = '[Actualizado]'; // Indicate password was updated
                    $tipo_cambio = 'Actualización de contraseña';

                    // Insert into historial de datos usuario
                    $stmt_historial = $conn->prepare("INSERT INTO historial_de_datos_usuario (Usuario_ID, Campo, Valor_Anterior, Valor_Nuevo, Fecha_Cambio, Tipo_Cambio) VALUES (?, ?, ?, ?, NOW(), ?)");
                    if ($stmt_historial) {
                        $stmt_historial->bind_param("issss", $usuario_id_log, $campo_cambiado, $valor_anterior, $valor_nuevo, $tipo_cambio);
                        $stmt_historial->execute();
                        $stmt_historial->close();
                        // Log success or handle error if needed, but don't interrupt user flow for history log failure
                    } else {
                        error_log("Error preparando consulta para historial de datos: " . $conn->error);
                    }
                } else {
                    error_log("Usuario_ID no encontrado para el correo: " . $_SESSION['correo_recuperacion']);
                }
            } else {
                error_log("Error preparando consulta para obtener Usuario_ID: " . $conn->error);
            }
            // --- End logging ---
            
            session_unset();
            session_destroy();
            
            $_SESSION['success'] = "Contraseña actualizada correctamente. Ahora puedes iniciar sesión.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Error al actualizar la contraseña";
        }
    }
}

// Close the database connection after all operations
$conexion->cerrar_conexion();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Nueva Contraseña</title>
    <style>
        /* Estilos de la barra de navegación */
        .barra-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: rgb(225, 216, 204);
            display: flex;
            justify-content: flex-start;
            align-items: center;
            padding-left: 20px;
            border-bottom: 4px solid black;
            z-index: 1000;
        }
        
        .flecha-nav {
            margin-left: 0;
        }
        
        .flecha-nav a {
            display: flex;
            align-items: center;
            height: 50px;
        }
        
        .flecha-nav svg {
            width: 32px;
            height: 32px;
            fill: #000;
            cursor: pointer;
            margin: 0;
            padding: 0;
            transition: transform 0.2s;
        }
        
        .flecha-nav svg:hover {
            transform: scale(1.1);
            fill: #4CAF50;
        }
        
        /* Ajuste para el contenido principal */
        .contenedor-main {
            margin-top: 70px;
        }
        
        /* Estilos existentes */
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
    </style>
    <link rel="stylesheet" href="css/styles.css">
    <link href="login.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="barra-nav">
        <div class="flecha-nav">
            <a onclick="window.history.back()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                </svg>
            </a>
        </div>
    </nav>

    <div class="background-animation">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
    </div>

    <section class="contenedor-main">
        <section class="wrapper">
            <div class="form" id="nueva-contrasena-form">
                <h1>RESTABLECER CONTRASEÑA</h1>
                
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="buton">
                        <div class="input-area">
                            <input type="text" name="codigo" placeholder="Código de verificación" required>
                            <i class="fas fa-key"></i>
                        </div>
                    </div>

                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Contraseña" name="Contraseña" required id="password-registro">
                            <i class="fas fa-lock input-icon"></i>
                            <span class="toggle-password" onclick="togglePassword('password-registro', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Confirmar Contraseña" name="confirmar_password" required id="confirm-password-registro">
        <i class="fas fa-lock input-icon"></i>
        <span class="toggle-password" onclick="togglePassword('confirm-password-registro', this)">
            <i class="fas fa-eye"></i>
        </span>
    </div>
</div>

                    <input type="submit" value="CAMBIAR CONTRASEÑA"> 
                    
                    <div class="alternar-form">
                        <p>¿Recordaste tu contraseña? <a href='login.php'>Inicia sesión aquí</a></p>
                    </div>
                </form>
            </div>
            
            <div class="contenedor-logo">
                <img src="../imagenes/login.png" alt="Imagen de fondo" class="bg-image">
                <figure>
                    <img src="../imagenes/prodcon/logoSinfondo.png" alt="Logo transparente" class="logo-portada">
                </figure>
            </div>
        </section>
    </section>
    <script>
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<style>
.input-area {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #777;
}

.toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #777;
    z-index: 2;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
</body>
>>>>>>> main
</html>