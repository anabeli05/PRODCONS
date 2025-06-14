<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_SESSION['registro_error'])) {
    $error = $_SESSION['registro_error'];
    unset($_SESSION['registro_error']);
} elseif (isset($_SESSION['registro_success'])) {
    $success = $_SESSION['registro_success'];
    unset($_SESSION['registro_success']);
} else {
    $error = null;
    $success = null;
}

include_once 'log_utils.php';
writeLog("DEBUG PHP: Script registro.php started. Error: " . (isset($error) ? $error : "None"));

include '../Base de datos/conexion.php';   
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registro'])) {
    try {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Error: Solicitud no válida");
        }

        $nombre = filter_var(trim($_POST['Nombre']), FILTER_SANITIZE_STRING);
        if (empty($nombre)) {
            throw new Exception("Error: El nombre es requerido");
        }

        $correo = filter_var(trim($_POST['Correo']), FILTER_SANITIZE_EMAIL);
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Error: El correo electrónico no es válido");
        }

        $password = $_POST['Contraseña'];
        $confirmar_password = $_POST['confirmar_password'];

        if (empty($password)) {
            throw new Exception("Error: La contraseña es requerida");
        }
        if (strlen($password) < 8) {
            throw new Exception("Error: La contraseña debe tener al menos 8 caracteres");
        }

        if ($password !== $confirmar_password) {
            throw new Exception("Error: Las contraseñas no coinciden");
        }

        writeLog("DEBUG: Checking for duplicate email: " . $correo);
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            writeLog("DEBUG: Duplicate email found.");
            throw new Exception("Error: El correo ya está registrado");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $rol = "Usuario";
        $estado = "Activo";
        
        $stmt = $conn->prepare("INSERT INTO usuarios (Nombre, Correo, Contraseña, Rol, Estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $correo, $hash, $rol, $estado);

        if ($stmt->execute()) { 
            $_SESSION['Nombre'] = $nombre;
            $_SESSION['Rol'] = $rol;
            $_SESSION['Estado'] = $estado;
            $_SESSION['registro_success'] = "Registro exitoso.";
            header("Location: https://localhost/PRODCONS/PI2do/Bienvenida/Bienvenida.html");
            exit();
        } else {
            throw new Exception("Error: No se pudo registrar el usuario");
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        writeLog("DEBUG: Exception caught: " . $errorMessage);
        $_SESSION['registro_error'] = $errorMessage;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
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
            <div class="form" id="registro-form">
                <h1>REGISTRAR USUARIO</h1>
                <?php if (isset($success)): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
                <?php elseif (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="registro.php" onsubmit="return validarFormulario()">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="buton">
                        <div class="input-area">
                            <input type="text" placeholder="Nombre Completo" name="Nombre" required>
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="error-message" id="nombre-error"></div>
                    <div class="buton">
                        <div class="input-area">
                            <input type="email" placeholder="Correo Electrónico" name="Correo" required>
                            <i class="fas fa-envelope"></i>
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
                    <div class="terminos">
                        <input class="C" type="checkbox" required>
                        <label>Acepto los<a href="/PRODCONS/footer/parafooter/term-condi/term-condi.html">términos y condiciones</a></label>
                    </div>
                    <input type="submit" name="registro" value="REGISTRARSE"> 
                    <div class="alternar-form">
                        <label>¿Ya tienes una cuenta? <a href='../inicio_sesion/login.php' id="mostrar-login">Inicia sesión aquí</a></label>
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
        const registroForm = document.getElementById('registro-form');
        const registroPassword = registroForm.querySelector('input[placeholder="Contraseña"]');
        const registroConfirmPassword = registroForm.querySelector('input[placeholder="Confirmar Contraseña"]');
        const nombreInput = registroForm.querySelector('input[placeholder="Nombre Completo"]');
        const correoInput = registroForm.querySelector('input[placeholder="Correo Electrónico"]');
        const terminosCheckbox = registroForm.querySelector('.C');
        const nombreError = document.getElementById('nombre-error');

        registroPassword.addEventListener('input', function() {
            if (this.value.length < 8) {
                this.setCustomValidity('La contraseña debe tener al menos 8 caracteres');
            } else {
                this.setCustomValidity('');
            }
        });

        registroConfirmPassword.addEventListener('input', function() {
            if (this.value !== registroPassword.value) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });

        nombreInput.addEventListener('input', function() {
            if (this.value.length < 3) {
                this.setCustomValidity('El nombre debe tener al menos 3 caracteres');
            } else {
                this.setCustomValidity('');
            }
        });

        correoInput.addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.value)) {
                this.setCustomValidity('El correo electrónico no es válido');
            } else {
                this.setCustomValidity('');
            }
        });

        registroForm.addEventListener('submit', function(e) {
            if (!terminosCheckbox.checked) {
                e.preventDefault();
                alert('Debes aceptar los términos y condiciones');
            }
            nombreInput.reportValidity();
            correoInput.reportValidity();
            registroPassword.reportValidity();
            registroConfirmPassword.reportValidity();
        });

        const phpError = "<?php echo isset($error) ? htmlspecialchars($error) : ''; ?>";
        writeLog('DEBUG JS Consolidated: phpError variable value: ' + phpError);

        document.addEventListener('DOMContentLoaded', function() {
            writeLog('DEBUG JS Consolidated: DOMContentLoaded listener started.');
            const registroForm = document.getElementById('registro-form');
            const correoInput = registroForm.querySelector('input[placeholder="Correo Electrónico"]');
            
            if (phpError === 'Error: El correo ya está registrado') {
                writeLog('DEBUG JS Consolidated: Inside duplicate email error display block.');
                if (correoInput) {
                    writeLog('DEBUG JS Consolidated: Correo input found.');
                    correoInput.setCustomValidity('El correo ya está registrado');
                    correoInput.reportValidity();
                    
                    correoInput.style.borderColor = '#dc3545';
                    correoInput.style.boxShadow = '0 0 0 2px rgba(220, 53, 69, 0.2)';
                    correoInput.style.color = '#dc3545';
                    
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.textContent = 'El correo ya está registrado';
                    correoInput.parentElement.appendChild(errorDiv);
                }
            }

            const phpSuccess = "<?php echo isset($success) ? htmlspecialchars($success) : ''; ?>";
            if (phpSuccess) {
                 writeLog('DEBUG JS Consolidated: Showing success message.');
                 alert(phpSuccess);
                 window.location.href = "/PRODCONS/PI2do/Dashboard_Usuario/Inicio/usuario.php";
            }
        });
    });
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
        .error, .success {
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .error {
            background-color: #f8d7da;
            border: none;
            color: #800020;
            font-size: 16px;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            font-weight: bold;
        }
        
        .success {
            background-color: #d4edda;
            border: 2px solid #155724;
            color: #155724;
            box-shadow: 0 2px 4px rgba(21, 87, 36, 0.1);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
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
</style>

</body>
</html>