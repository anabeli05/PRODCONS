<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Validaciones de seguridad
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Manejo de mensajes de error/success
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

writeLog("DEBUG PHP: Script registro.php started. Error: " . (isset($error) ? $error : "None")); // Log para verificar si $error tiene valor

// Inicia la conexion con la base de datos
include '../Base de datos/conexion.php';   
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registro'])) {
    try {
        // Validar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Error: Solicitud no válida");
        }

        // Sanitizar y validar inputs
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

        // Validar contraseña
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
            $_SESSION['registro_success'] = "Registro exitoso. Redirigiendo al dashboard...";
            // header("Location: /PRODCONS/PI2do/Dashboard_Usuario/Inicio/usuario.php");
            // exit();
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
    <!-- Configuración básica del documento -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS</title>
    
    <!-- Estilos para mensajes -->
    <style>
        .error, .success {
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Estilos para campos inválidos */
        .input-area input:invalid {
            /* border-color: #dc3545; */
            /* box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2); */
        }

        /* Estilos para campos válidos */
        .input-area input:valid {
            border-color: #155724;
            box-shadow: 0 0 0 2px rgba(21, 87, 36, 0.2);
        }



        /* Estilos para el mensaje de error nativo */
        .input-area input:invalid::-webkit-validation-bubble-message {
            background-color: #f8d7da;
            color: #dc3545;
            font-size: 14px;
            padding: 10px;
            border-radius: 4px;
        }

        /* Estilos para el icono de error */
        .input-area input:invalid::-webkit-validation-bubble-arrow {
            border-color: #dc3545;
        }

        .error {
            background-color: #f8d7da;
            border: none;
            color: #800020; /* Color vino */
            font-size: 16px;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            font-weight: bold;
        }

        /* Estilo para el mensaje de error de correo duplicado */
        .error-message {
            font-size: 16px;
            color: #800020; /* Color vino */
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
    </style>
    
    <!-- Hojas de estilo -->
    <link rel="stylesheet" href="css/styles.css">
    <link href="login.css" rel="stylesheet">
    <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <!-- FORMULARIO DE REGISTRO -->
            <!-- ============================================= -->
            <div class="form" id="registro-form">
                <h1>REGISTRAR USUARIO</h1>
                <?php if (isset($success)): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
                <?php elseif (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="registro.php" onsubmit="return validarFormulario()">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <!-- Campo para el nombre completo -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="text" placeholder="Nombre Completo" name="Nombre" required>
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <!-- Mensaje de validación del nombre -->
                    <div class="error-message" id="nombre-error"></div>
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
                    <!-- Campo para confirmar la contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Confirmar Contraseña" name="confirmar_password" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <!-- Checkbox de términos y condiciones -->
                    <div class="terminos">
                        <input class="C" type="checkbox" required>
                        <label>Acepto los <a href="/PRODCONS/footer/parafooter/term-condi/term-condi.html">términos y condiciones</a></label>
                    </div>
                    <!-- Botón de envío -->
                    <input type="submit" name="registro" value="REGISTRARSE"> 
                    <!-- Enlace para alternar al formulario de login -->
                    <div class="alternar-form">
                        <p>¿Ya tienes una cuenta? <a href='../inicio_sesion/login.php' id="mostrar-login">Inicia sesión aquí</a></p>
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
    // Validación de contraseñas en el formulario de registro
    document.addEventListener('DOMContentLoaded', function() {
        const registroForm = document.getElementById('registro-form');
        const registroPassword = registroForm.querySelector('input[placeholder="Contraseña"]');
        const registroConfirmPassword = registroForm.querySelector('input[placeholder="Confirmar Contraseña"]');
        const nombreInput = registroForm.querySelector('input[placeholder="Nombre Completo"]');
        const correoInput = registroForm.querySelector('input[placeholder="Correo Electrónico"]');
        const terminosCheckbox = registroForm.querySelector('.C');
        const nombreError = document.getElementById('nombre-error');

        // Validación de contraseñas en tiempo real
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

        // Validación de nombre
        nombreInput.addEventListener('input', function() {
            if (this.value.length < 3) {
                this.setCustomValidity('El nombre debe tener al menos 3 caracteres');
            } else {
                this.setCustomValidity('');
            }
        });

        // Validación de correo
        correoInput.addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.value)) {
                this.setCustomValidity('El correo electrónico no es válido');
            } else {
                this.setCustomValidity('');
            }
        });

        // Validación de términos y condiciones
        registroForm.addEventListener('submit', function(e) {
            if (!terminosCheckbox.checked) {
                e.preventDefault();
                alert('Debes aceptar los términos y condiciones');
            }
            // También validamos otros campos al intentar enviar para mostrar las burbujas
            nombreInput.reportValidity();
            correoInput.reportValidity();
            registroPassword.reportValidity();
            registroConfirmPassword.reportValidity();
        });

        // --- Manejo de errores de sesión desde PHP ---
        // Pass PHP error to JavaScript
        const phpError = "<?php echo isset($error) ? htmlspecialchars($error) : ''; ?>";
        writeLog('DEBUG JS Consolidated: phpError variable value: ' + phpError); // Log point 6 (Consolidated)

        document.addEventListener('DOMContentLoaded', function() {
            writeLog('DEBUG JS Consolidated: DOMContentLoaded listener started.'); // Log point 7 (Consolidated)
            const registroForm = document.getElementById('registro-form');
            const correoInput = registroForm.querySelector('input[placeholder="Correo Electrónico"]');
            
            // Display duplicate email error if present from PHP
            if (phpError === 'Error: El correo ya está registrado') {
                writeLog('DEBUG JS Consolidated: Inside duplicate email error display block.'); // Log point 8 (Consolidated)
                if (correoInput) {
                    writeLog('DEBUG JS Consolidated: Correo input found.'); // Log point 5 (Consolidated)
                    correoInput.setCustomValidity('El correo ya está registrado');
                    correoInput.reportValidity();
                    
                    // Aseguramos que el mensaje se muestre inmediatamente
                    correoInput.style.borderColor = '#dc3545';
                    correoInput.style.boxShadow = '0 0 0 2px rgba(220, 53, 69, 0.2)';
                    correoInput.style.color = '#dc3545';
                    
                    // Mostramos el mensaje en un div adicional si no se muestra la burbuja
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.textContent = 'El correo ya está registrado';
                    correoInput.parentElement.appendChild(errorDiv);
                }
            }

            // Show success message in a pop-up and then redirect if present from PHP
            const phpSuccess = "<?php echo isset($success) ? htmlspecialchars($success) : ''; ?>";
            if (phpSuccess) {
                 writeLog('DEBUG JS Consolidated: Showing success message.');
                 alert(phpSuccess);
                 window.location.href = "/PRODCONS/PI2do/Dashboard_Usuario/Inicio/usuario.php";
            }
             // --- Fin Manejo de errores de sesión desde PHP ---

        });

        // Clear the session error after displaying it via JS on the next page load
        // This PHP code is outside the DOMContentLoaded to ensure it runs immediately
        <?php 
        // Ya no necesitamos limpiar aquí, se limpia después de la asignación en PHP arriba
        // unset($_SESSION['registro_error']);
        // unset($_SESSION['registro_success']);
        ?>

    });

    </script>
</body>
</html>
            