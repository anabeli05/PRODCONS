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
    <title>PRODCONS</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="login.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
        
        .back-arrow {
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 32px;
            cursor: pointer;
            color: #333;
            background: rgba(255, 255, 255, 0.8);
            padding: 15px;
            border-radius: 50%;
            z-index: 1000;
            transition: all 0.3s ease;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .back-arrow:hover {
            background: rgba(255, 255, 255, 1);
            transform: scale(1.1);
        }
        
        .back-arrow i {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Flecha de regreso -->
    <div class="back-arrow" onclick="window.history.back();">
        <i class="fas fa-arrow-left"></i>
    </div>

    <div class="background-animation">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
    </div>
    
    <header> 
        <div class="header-contenedor">
            <div class="principal"></div>
        </div>
    </header>

    <section class="logo"> 
        <div class="header_2">
            <a href="/">           
                 <img class="prodcons" src="../imagenes/prodcon/logoSinfondo.png" alt="Logo"> 
            </a>
        </div>
    </section>

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
</body>
</html>