<?php
include_once 'log_utils.php';

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

if (isset($_GET['error'])) {
    $_SESSION['login_error'] = "Error: Credenciales incorrectas";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
writeLog("DEBUG: Error message: " . (isset($error) ? $error : "None"));

if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
} elseif (isset($_GET['success']) && $_GET['success'] == 'password_changed') {
    $success_message = "Contraseña actualizada exitosamente. Ahora puedes iniciar sesión.";
} else {
    $success_message = null;
}
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
            font-size: 16px;
            color: #800020;
            margin: 5px 0;
            padding: 8px;
            border-radius: 4px;
            background-color: #f8d7da;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: 600;
            letter-spacing: 0.5px;
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
                    <div class="buton">
                        <div class="input-area">
                            <input type="email" placeholder="Correo Electrónico" name="Correo" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Contraseña" name="Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <div class="recuerdame-contenedor">
                        <div class="recuerdame">
                            <input class="C" type="checkbox">
                            <label>Recuérdame</label>
                        </div>
                        <div class="olvido-contrasena">
                            <a href='../inicio_sesion/codigo.php'>¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>
        
                    <input type="submit" name="boton_ingresar" value="INGRESAR"> 
                    
                    <div class="alternar-form">
                        <p>¿No tienes una cuenta? <a href='/PRODCONS/PI2do/inicio_sesion/registro.php' id="mostrar-registro">Regístrate aquí</a></p>
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
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('login-form');
        const registroForm = document.getElementById('registro-form');
        const recuperacionForm = document.getElementById('recuperacion-form');
        const nuevaContrasenaForm = document.getElementById('nueva-contrasena-form');

        const mostrarRegistro = document.getElementById('mostrar-registro');
        const mostrarLogin = document.getElementById('mostrar-login');
        const mostrarRecuperacion = document.getElementById('mostrar-recuperacion');
        const volverLogin = document.getElementById('volver-login');
        const volverLogin2 = document.getElementById('volver-login-2');

        function ocultarTodosLosFormularios() {
            if (loginForm) loginForm.style.display = 'none';
            if (registroForm) registroForm.style.display = 'none';
            if (recuperacionForm) recuperacionForm.style.display = 'none';
            if (nuevaContrasenaForm) nuevaContrasenaForm.style.display = 'none';
        }

        function mostrarFormulario(formulario) {
            ocultarTodosLosFormularios();
            if (formulario) formulario.style.display = 'block';
        }

        if (mostrarLogin) {
            mostrarLogin.addEventListener('click', function(e) {
                e.preventDefault();
                mostrarFormulario(document.getElementById('login-form'));
            });
        }

        if (volverLogin) {
            volverLogin.addEventListener('click', function(e) {
                e.preventDefault();
                mostrarFormulario(document.getElementById('login-form'));
            });
        }

        if (volverLogin2) {
            volverLogin2.addEventListener('click', function(e) {
                e.preventDefault();
                mostrarFormulario(document.getElementById('login-form'));
            });
        }

        if (loginForm) {
            loginForm.querySelector('form').addEventListener('submit', function(e) {
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