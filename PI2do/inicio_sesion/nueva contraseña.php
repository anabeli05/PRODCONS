<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// Ensure the database connection is included
include '../Base de datos/conexion.php';

// Initialize the database connection
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registro'])) {
    $nombre = $_POST['Nombre'];
    $correo = $_POST['Correo'];
    $password = $_POST['Contraseña'];
    $confirmar_password = $_POST['confirmar_password'];

    if ($password !== $confirmar_password) {
        die("Error: Las contraseñas no coinciden");
    }

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        die("Error: El correo ya está registrado");
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $rol = "Usuario";
    
    $stmt = $conn->prepare("INSERT INTO usuarios (Nombre, Correo, Contraseña, Rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $correo, $hash, $rol);

    if ($stmt->execute()) {
        $_SESSION['Nombre'] = $nombre;
        $_SESSION['Rol'] = $rol;
        header("Location: /PRODCONS/usuario/usuario.php");
        exit();
    } else {
        die("Error: No se pudo registrar el usuario");
    }

    $stmt->close();
    $conn->close();
}
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
            <!-- FORMULARIO DE NUEVA CONTRASEÑA  -->
            <!-- ============================================= -->
            <div class="form" id="nueva-contrasena-form">
                <h1>RESTABLECER CONTRASEÑA</h1>
                <form action="">
                    <!-- Instrucciones -->
                    <div class="instrucciones">
                        <p>Ingresa el código que recibiste y tu nueva contraseña.</p>
                    </div>

                    <!-- Campo para el código -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="text" placeholder="Código de verificación" required>
                            <i class="fas fa-key"></i>
                        </div>
                    </div>

                    <!-- Campo para la nueva contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Nueva Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Campo para confirmar la nueva contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Confirmar Nueva Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Botón de envío -->
                    <input type="submit" value="CAMBIAR CONTRASEÑA"> 
                    
                    <!-- Enlace para volver al login -->
                    <div class="alternar-form">
                        <p>¿Recordaste tu contraseña? <a href='../inicio_sesion/login.php' id="volver-login-2">Inicia sesión aquí</a></p>
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
        // Validación de contraseñas en el formulario de nueva contraseña
        const nuevaPassword = nuevaContrasenaForm.querySelector('input[placeholder="Nueva Contraseña"]');
        const nuevaConfirmPassword = nuevaContrasenaForm.querySelector('input[placeholder="Confirmar Nueva Contraseña"]');

        nuevaConfirmPassword.addEventListener('input', function() {
            if (this.value !== nuevaPassword.value) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
                // Manejo del formulario de recuperación
                recuperacionForm.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica de recuperación
            console.log('Enviando código de recuperación...');
            // Simular envío exitoso y mostrar formulario de nueva contraseña
            mostrarFormulario(nuevaContrasenaForm);
        });

        // Manejo del formulario de nueva contraseña
        nuevaContrasenaForm.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica de cambio de contraseña
            console.log('Cambiando contraseña...');
            // Simular cambio exitoso y volver al login
            mostrarFormulario(loginForm);
        });
    </script>
</body>
</html>
            