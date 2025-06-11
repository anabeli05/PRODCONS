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
                            <input type="password" placeholder="Contraseña" name="Contraseña" required id="password-login">
                            <i class="fas fa-lock input-icon"></i>
                            <span class="toggle-password" onclick="togglePassword('password-login', this)">
                                <i class="fas fa-eye"></i>
                            </span>
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
                        <label>¿No tienes una cuenta? <a href='/PRODCONS/PI2do/inicio_sesion/registro.php' id="mostrar-registro">Regístrate aquí</a></label>
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

<?php
    include 'login_var.php';
?>
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
        /* Estilos de la barra de navegación */
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



/* Estilo para el hover del icono */
.toggle-password:hover {
    color: #333;
}
</style>

</body>
</html>