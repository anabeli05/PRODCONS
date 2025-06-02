<?php
include_once 'log_utils.php';

// Configuración de errores (ahora en log_utils.php)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Crear archivo de log (ahora en log_utils.php)
// $logFile = __DIR__ . '/debug.log';
// file_put_contents($logFile, "Iniciando sesión: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Función para escribir en el log (ahora en log_utils.php)
// function writeLog($message) {
//     global $logFile;
//     file_put_contents($logFile, $message . "\n", FILE_APPEND);
// }

writeLog("DEBUG: Iniciando proceso de login");

session_start();
include '../Base de datos/conexion.php';

// Manejo de mensajes de error/success
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
} elseif (isset($_SESSION['login_success'])) {
    $success = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
} else {
    $error = null;
    $success = null;
}

// Si hay error en la URL, establecer el mensaje de error
if (isset($_GET['error'])) {
    $_SESSION['login_error'] = "Error: Credenciales incorrectas";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
writeLog("DEBUG: Error message: " . (isset($error) ? $error : "None"));

// Mostrar mensaje de éxito si la contraseña se actualizó exitosamente
$success_message = isset($_GET['success']) && $_GET['success'] == 'true' ? "Contraseña actualizada exitosamente" : "";
writeLog("DEBUG: Success message: " . $success_message);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Configuración básica del documento -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS</title>
    
    <!-- Hojas de estilo -->
    <link rel="stylesheet" href="css/styles.css">
    <link href="login.css" rel="stylesheet">
    <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos para mensajes de error y éxito */
        .error-message {
            font-size: 16px;
            color: #800020; /* Color vino */
            margin: 5px 0;
            padding: 8px;
            border-radius: 4px;
            background-color: #f8d7da;
            font-weight: 600; /* Texto más grueso */
            letter-spacing: 0.5px; /* Espaciado entre letras */
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: 600; /* Texto más grueso */
            letter-spacing: 0.5px; /* Espaciado entre letras */
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- ============================================= -->
    <!-- SECCIÓN DE ANIMACIÓN DE FONDO -->
    <!-- ============================================= -->
    <div class="background-animation">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
    </div>
    
    <!-- ============================================= -->
    <!-- CABECERA PRINCIPAL -->
    <!-- ============================================= -->
    <header> 
        <div class="header-contenedor">
            <div class="principal"></div>
        </div>
    </header>

    <!-- ============================================= -->
    <!-- SECCIÓN DEL LOGO -->
    <!-- ============================================= -->
    <section class="logo"> 
        <div class="header_2">
            <a href="/">           
                 <img class="prodcons" src="../imagenes/prodcon/logoSinfondo.png" alt="Logo"> 
            </a>
        </div>
    </section>

    <!-- ============================================= -->
    <!-- CONTENIDO PRINCIPAL -->
    <!-- ============================================= -->
    <section class="contenedor-main">
        <section class="wrapper">
            <!-- ============================================= -->
            <!-- FORMULARIO DE LOGIN (visible por defecto) -->
            <!-- ============================================= -->
            <div class="form" id="login-form">
                <h1>INGRESAR USUARIO</h1>
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login_var.php">
                    <!-- Campo para el correo electrónico -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="email" placeholder="Correo Electrónico" name="Correo" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <!-- Campo para la contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Contraseña" name="Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Checkbox "Recuérdame" y enlace para recuperar contraseña -->
                    <div class="recuerdame-contenedor">
                        <div class="recuerdame">
                            <input class="C" type="checkbox">
                            <label>Recuérdame</label>
                        </div>
                        <div class="olvido-contrasena">
                            <a href='../inicio_sesion/codigo.php'>¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>
        
                    <!-- Botón de envío -->
                    <input type="submit" name="boton_ingresar" value="INGRESAR"> 
                    
                    <!-- Enlace para alternar al formulario de registro -->
                    <div class="alternar-form">
                        <p>¿No tienes una cuenta? <a href='/PRODCONS/PI2do/inicio_sesion/registro.php' id="mostrar-registro">Regístrate aquí</a></p>
                    </div>
                </form>
            </div>
            
           
            
            <!-- ============================================= -->
            <!-- CONTENEDOR DEL LOGO (lado derecho) -->
            <!-- ============================================= -->
            <div class="contenedor-logo">
                <img src="../imagenes/login.png" alt="Imagen de fondo" class="bg-image">
                <figure>
                    <img src="../imagenes/prodcon/logoSinfondo.png" alt="Logo transparente" class="logo-portada">
                </figure>
            </div>
        </section>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Referencias a los formularios
        const loginForm = document.getElementById('login-form');
        const registroForm = document.getElementById('registro-form');
        const recuperacionForm = document.getElementById('recuperacion-form');
        const nuevaContrasenaForm = document.getElementById('nueva-contrasena-form');

        // Referencias a los enlaces
        const mostrarRegistro = document.getElementById('mostrar-registro');
        const mostrarLogin = document.getElementById('mostrar-login');
        const mostrarRecuperacion = document.getElementById('mostrar-recuperacion');
        const volverLogin = document.getElementById('volver-login');
        const volverLogin2 = document.getElementById('volver-login-2');

        // Función para mostrar un formulario específico
        function ocultarTodosLosFormularios() {
            // Check if the elements exist before trying to access their style property
            if (loginForm) loginForm.style.display = 'none';
            if (registroForm) registroForm.style.display = 'none';
            if (recuperacionForm) recuperacionForm.style.display = 'none';
            if (nuevaContrasenaForm) nuevaContrasenaForm.style.display = 'none';
        }

        // Función para mostrar un formulario específico
        function mostrarFormulario(formulario) {
            ocultarTodosLosFormularios();
            if (formulario) formulario.style.display = 'block';
        }

        // Event Listeners para los enlaces
        // if (mostrarRegistro) {
        //     mostrarRegistro.addEventListener('click', function(e) {
        //         e.preventDefault();
        //         // Assuming registroForm exists based on your HTML structure
        //         mostrarFormulario(document.getElementById('registro-form'));
        //     });
        // }

        if (mostrarLogin) {
            mostrarLogin.addEventListener('click', function(e) {
                e.preventDefault();
                // Assuming loginForm exists
                mostrarFormulario(document.getElementById('login-form'));
            });
        }

        //if (mostrarRecuperacion) {
        //    mostrarRecuperacion.addEventListener('click', function(e) {
        //        e.preventDefault();
        //        // Assuming recuperacionForm exists
        //        mostrarFormulario(document.getElementById('recuperacion-form'));
        //    });
        //}

        if (volverLogin) {
            volverLogin.addEventListener('click', function(e) {
                e.preventDefault();
                // Assuming loginForm exists
                mostrarFormulario(document.getElementById('login-form'));
            });
        }

        if (volverLogin2) {
            volverLogin2.addEventListener('click', function(e) {
                e.preventDefault();
                // Assuming loginForm exists
                mostrarFormulario(document.getElementById('login-form'));
            });
        }


        // Manejo del formulario de login
        if (loginForm) {
            loginForm.querySelector('form').addEventListener('submit', function(e) {
                // Removemos el preventDefault para permitir que el formulario se envíe
                // e.preventDefault();
                // Aquí iría la lógica de autenticación
                console.log('Intentando iniciar sesión...');
            });
        }

    });
    </script>

<?php
    include 'login_var.php';
?>
</body>
</html>
