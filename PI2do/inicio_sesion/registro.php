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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS</title>
    <style>
        .error, .success {
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        .input-area input:valid {
            border-color: #155724;
            box-shadow: 0 0 0 2px rgba(21, 87, 36, 0.2);
        }

        .input-area input:invalid::-webkit-validation-bubble-message {
            background-color: #f8d7da;
            color: #dc3545;
            font-size: 14px;
            padding: 10px;
            border-radius: 4px;
        }

        .input-area input:invalid::-webkit-validation-bubble-arrow {
            border-color: #dc3545;
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

        .error-message {
            font-size: 16px;
            color: #800020;
            margin: 5px 0;
            padding: 8px;
            border-radius: 4px;
            background-color: #f8d7da;
        }

        .success {
            background-color: #d4edda;
            border: 2px solid #155724;
            color: #155724;
            box-shadow: 0 2px 4px rgba(21, 87, 36, 0.1);
        }

        .error-message {
            color: #dc3545;
            font-size: 10px;
            margin-top: 5px;
            padding: 0;
            text-align: center; 
            width: 100%;
            box-sizing: border-box;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
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
    <link rel="stylesheet" href="css/styles.css">
    <link href="login.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                            <input type="password" placeholder="Contraseña" name="Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Confirmar Contraseña" name="confirmar_password" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <div class="terminos">
                        <input class="C" type="checkbox" required>
                        <label>Acepto los <a href="/PRODCONS/footer/parafooter/term-condi/term-condi.html">términos y condiciones</a></label>
                    </div>
                    <input type="submit" name="registro" value="REGISTRARSE"> 
                    <div class="alternar-form">
                        <p>¿Ya tienes una cuenta? <a href='../inicio_sesion/login.php' id="mostrar-login">Inicia sesión aquí</a></p>
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
    </script>
</body>
</html>