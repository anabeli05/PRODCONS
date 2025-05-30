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
            <!-- FORMULARIO DE RECUPERACIÓN  -->
            <!-- ============================================= -->
            <div class="form" id="recuperacion-form">
                <h1>RECUPERAR CONTRASEÑA</h1>
                <form action='../inicio_sesion/nueva contraseña.php' method="POST">
                    <!-- Instrucciones -->
                    <div class="instrucciones">
                        <p>Ingresa tu correo electrónico gmail y te enviaremos un código para restablecer tu contraseña.</p>
                    </div>

                    <!-- Campo para el correo electrónico -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="email" name="correo" placeholder="Correo Electrónico" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <!-- Botón de envío -->
                    <input type="submit" value="ENVIAR CÓDIGO">
                    
                    <!-- Enlace para volver al login -->
                    <div class="alternar-form">
                        <p>¿Recordaste tu contraseña? <a href="../inicio_sesion/login.php" id="volver-login">Inicia sesión aquí</a></p>
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
        registroConfirmPassword.addEventListener('input', function() {
            if (this.value !== registroPassword.value) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
    });
    </script>
</body>
</html>
            