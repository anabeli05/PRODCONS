<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Incluir los archivos necesarios de PHPMailer
require __DIR__ . '/../vendor/phpmailer/PHPMailer-6.8.0/src/PHPMailer.php';
require __DIR__ . '/../vendor/phpmailer/PHPMailer-6.8.0/src/SMTP.php';
require __DIR__ . '/../vendor/phpmailer/PHPMailer-6.8.0/src/Exception.php';

// Incluir la conexión a la base de datos
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
    
    // Validar que sea un correo válido
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Por favor ingresa un correo electrónico válido";
        header("Location: codigo.php");
        exit();
    }

    // Generar código de recuperación
    $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['codigo_recuperacion'] = $codigo;
    $_SESSION['correo_recuperacion'] = $correo;
    $_SESSION['codigo_expiracion'] = time() + 1800; // 30 minutos

    try {
        // Crear instancia de PHPMailer
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fernandobenitezastudillo@gmail.com';
        $mail->Password = 'olnk sdkl dzmo otza';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        // Configuraciones adicionales
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Configuración de autenticación
        $mail->set('X-Mailer', 'PHP/' . phpversion());
        $mail->set('X-PHP-Originating-Script', get_current_user() . ':' . __FILE__);
        
        // Habilitar depuración
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        
        // Remitente y destinatario
        $mail->setFrom('fernandobenitezastudillo@gmail.com', 'Sistema PRODCONS');
        $mail->addAddress($correo);
        
        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Código de recuperación de contraseña - PRODCONS';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #333;'>Recuperación de contraseña</h2>
                <p>Hemos recibido una solicitud para restablecer tu contraseña en PRODCONS.</p>
                <p>Tu código de verificación es: <strong style='font-size: 24px; color: #007bff;'>$codigo</strong></p>
                <p>Este código es válido por 30 minutos.</p>
                <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='font-size: 12px; color: #6c757d;'>Si no solicitaste este cambio, por favor ignora este mensaje.</p>
            </div>
        ";
        
        // Enviar correo
        $mail->send();
        
        // Limpiar la salida
        ob_end_clean();
        
        // Verificar si la redirección funciona
        if (!headers_sent()) {
            header("Location: nueva_contraseña.php");
            exit();
        } else {
            echo "<script>window.location.href='nueva_contraseña.php';</script>";
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al enviar el correo: " . $e->getMessage();
        header("Location: codigo.php");
        exit();
    }
} else {
    // Si no es una solicitud POST, mostramos el formulario
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
            .back-arrow {
                position: absolute;
                top: 20px;
                right: 20px;
                font-size: 24px;
                color: #333;
                cursor: pointer;
                z-index: 1000;
                background: rgba(255, 255, 255, 0.7);
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }
            .back-arrow:hover {
                background: rgba(255, 255, 255, 0.9);
                transform: scale(1.1);
            }
        </style>
    </head>
    <body>
        <!-- Flecha de retroceso -->
        <div class="back-arrow" onclick="window.history.back();">
            <i class="fas fa-arrow-left"></i>
        </div>

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
            // Función para manejar el clic en la flecha de retroceso
            document.querySelector('.back-arrow').addEventListener('click', function() {
                window.history.back();
            });
        </script>
    </body>
    </html>
    <?php
}
?>