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
        
        // Redireccionar
        header("Location: nueva_contraseña.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al enviar el correo: " . $e->getMessage();
        header("Location: codigo.php");
        exit();
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

       <!-- Google Cloud Translation API -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <!-- Script de traducción global -->
    <script src='/PRODCONS/translate.js'></script>

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

          <!-- Bandera actual --> 
           <div id="idiomaToggle" style="display: inline-block; margin-left: 15px;">
                    <img class="españa" id="banderaIdioma" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
                </div>
                <!-- Opciones de banderas desplegables - Puedes cambiar las imágenes aquí -->
                <div id="idiomasOpciones" style="display: none;">
                    <img class="ingles" src="/PRODCONS/PI2do/imagenes/logos/ingles.png" onclick="cambiarIdioma('ingles')" alt="Cambiar a inglés" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                    <img class="españa" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" onclick="cambiarIdioma('espanol')" alt="Cambiar a español" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                </div>
    </nav>

    <script>
        // Funciones para el cambio de idioma (copiadas de index.php)
        function cambiarIdioma(idioma) {
            const banderaPrincipal = document.getElementById('banderaIdioma');
            const banderaIngles = document.querySelector('.ingles');
            const banderaEspana = document.querySelector('.españa');
            
            if (banderaPrincipal) {
                banderaPrincipal.src = idioma === 'ingles' 
                    ? "/PRODCONS/PI2do/imagenes/logos/ingles.png" 
                    : "/PRODCONS/PI2do/imagenes/logos/espanol.png";
            }
            
            if (banderaIngles && banderaEspana) {
                banderaIngles.style.display = idioma === 'espanol' ? 'none' : 'block';
                banderaEspana.style.display = idioma === 'espanol' ? 'block' : 'none';
            }
            
            currentLanguage = idioma === 'ingles' ? 'en' : 'es';
            translateContent(currentLanguage);
            
            const opciones = document.getElementById('idiomasOpciones');
            if (opciones) {
                opciones.style.display = 'none';
            }
        }

        function alternarIdioma() {
            const bandera = document.getElementById('banderaIdioma');
            let idiomaActual = bandera.getAttribute('data-idioma') || 'es';
            let nuevoIdioma, nuevaBandera;

            if (idiomaActual === 'es') {
                nuevoIdioma = 'en';
                nuevaBandera = '/PRODCONS/PI2do/imagenes/logos/ingles.png';
            } else {
                nuevoIdioma = 'es';
                nuevaBandera = '/PRODCONS/PI2do/imagenes/logos/espanol.png';
            }

            bandera.src = nuevaBandera;
            bandera.setAttribute('data-idioma', nuevoIdioma);

            translateContent(nuevoIdioma);
            localStorage.setItem('preferredLanguage', nuevoIdioma);
        }
    </script>

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
                        <label>¿Recordaste tu contraseña? <a href="../inicio_sesion/login.php" id="volver-login">Inicia sesión aquí</a></label>
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
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    border-bottom: 4px solid black;
    z-index: 1000;
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

/* Contenedor para la bandera alineada a la derecha */
.bandera-container {
    margin-left: auto;
}

/* BANDERA */
#banderaIdioma {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid black;
    cursor: pointer;
    transition: transform 0.2s;
}

#banderaIdioma:hover {
    transform: scale(1.1);
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

</body>
</html>