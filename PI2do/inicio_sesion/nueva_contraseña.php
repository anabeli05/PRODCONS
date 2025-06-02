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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_ingresado = $_POST['codigo'];
    $nueva_password = $_POST['nueva_password'];
    $confirmar_password = $_POST['confirmar_password'];
    
    // Verificar que el código coincida
    if ($codigo_ingresado != $_SESSION['codigo_recuperacion']) {
        die("Error: Código de verificación incorrecto");
    }
    
    // Verificar que las contraseñas coincidan
    if ($nueva_password != $confirmar_password) {
        die("Error: Las contraseñas no coinciden");
    }
    
    // Actualizar la contraseña en la base de datos
    $hash = password_hash($nueva_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET Contraseña = ? WHERE Correo = ?");
    $stmt->bind_param("ss", $hash, $_SESSION['correo_recuperacion']);
    
    if ($stmt->execute()) {
        // Limpiar variables de sesión
        unset($_SESSION['codigo_recuperacion']);
        unset($_SESSION['correo_recuperacion']);
        
        header("Location: login.php?success=password_changed");
        exit();
    } else {
        die("Error al actualizar la contraseña");
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
            <div class="form" id="nueva-contrasena-form">
                <h1>RESTABLECER CONTRASEÑA</h1>
                <form method="POST" action="">
                    <div class="instrucciones">
                        <p>Ingresa el código que recibiste y tu nueva contraseña.</p>
                    </div>

                    <!-- Campo para el código -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="text" name="codigo" placeholder="Código de verificación" required>
                            <i class="fas fa-key"></i>
                        </div>
                    </div>

                    <!-- Campo para la nueva contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" name="nueva_password" placeholder="Nueva Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Campo para confirmar la nueva contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" name="confirmar_password" placeholder="Confirmar Nueva Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Botón de envío -->
                    <input type="submit" value="CAMBIAR CONTRASEÑA"> 
                    
                    <!-- Enlace para volver al login -->
                    <div class="alternar-form">
                        <p>¿Recordaste tu contraseña? <a href='login.php'>Inicia sesión aquí</a></p>
                    </div>
                </form>
            </div>
            
            <!-- Contenedor del logo (lado derecho) -->
            <div class="contenedor-logo">
                <img src="../imagenes/login.png" alt="Imagen de fondo" class="bg-image">
                <figure>
                    <img src="../imagenes/prodcon/logoSinfondo.png" alt="Logo transparente" class="logo-portada">
                </figure>
            </div>
        </section>
    </section>
</body>
</html>
            