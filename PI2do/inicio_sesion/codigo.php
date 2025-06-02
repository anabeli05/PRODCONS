<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require '../vendor/autoload.php';
include '../Base de datos/conexion.php';

// Manejo de mensajes de error
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
} else {
    $error = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['correo'])) {
    $correo = $_POST['correo'];
    
    // Validar que sea un correo de Gmail
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || !str_ends_with(strtolower($correo), '@gmail.com')) {
        die("Solo se aceptan correos de Gmail");
    }

    // Crear instancia de la conexión
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    // Generar código
    $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['codigo_recuperacion'] = $codigo;
    $_SESSION['correo_recuperacion'] = $correo;
    $_SESSION['codigo_expiracion'] = time() + 1800; // 30 minutos

    // Configurar correo usando PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.sendgrid.net';
        $mail->SMTPAuth = true;
        $mail->Username = 'apikey'; // SendGrid API Key username is always 'apikey'
        $mail->Password = 'TU_SENDGRID_API_KEY'; // Reemplaza con tu API Key de SendGrid
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente (debe ser un email verificado en SendGrid)
        $mail->setFrom('tu_email_verificado@tudominio.com', 'Sistema PRODCONS');
        
        // Destinatario
        $mail->addAddress($correo);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Código de recuperación de contraseña';
        $mail->Body = "<h2>Recuperación de contraseña</h2>
                       <p>Tu código de verificación es: <strong>$codigo</strong></p>
                       <p>Válido por 30 minutos</p>
                       <hr>
                       <p><small>Si no solicitaste este cambio, ignora este mensaje.</small></p>";

        $mail->send();
        header("Location: nueva_contraseña.php");
        exit();

    } catch (Exception $e) {
        die("Error al enviar el correo: ". $mail->ErrorInfo);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Recuperar Contraseña</title>
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
    </style>
</head>
<body>
    <div class="background-animation">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
    </div>
    
    <section class="contenedor-main">
        <section class="wrapper">
            <div class="form" id="recuperacion-form">
                <h1>RECUPERAR CONTRASEÑA</h1>
                
                <!-- Mostrar mensaje de error si existe -->
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="instrucciones">
                    <p>Ingresa tu correo electrónico gmail y te enviaremos un código para restablecer tu contraseña.</p>
                </div>

                <form action="" method="POST">
                    <div class="buton">
                        <div class="input-area">
                            <input type="email" name="correo" placeholder="Correo Electrónico" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <div class="buton">
                        <input type="submit" value="ENVIAR CÓDIGO">
                    </div>
                    
                    <div class="alternar-form">
                        <p>¿Recordaste tu contraseña? <a href="../inicio_sesion/login.php" id="volver-login">Inicia sesión aquí</a></p>
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
    // Validación de correo en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const correoInput = document.querySelector('input[name="correo"]');
        correoInput.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            if (!value.endsWith('@gmail.com')) {
                this.setCustomValidity('Solo se aceptan correos de Gmail');
            } else {
                this.setCustomValidity('');
            }
        });
    });
    </script>
</body>
</html>
            