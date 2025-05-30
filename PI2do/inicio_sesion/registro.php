<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
;
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
            <!-- ============================================= -->
            <!-- FORMULARIO DE REGISTRO -->
            <!-- ============================================= -->
            <div class="form" id="registro-form">
                <h1>REGISTRAR USUARIO</h1>
                <form method="POST" action="registro.php">
                    <!-- Campo para el nombre completo -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="text" placeholder="Nombre Completo" name="Nombre" required>
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

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
                        <p>¿Ya tienes una cuenta? <a href="#" id="mostrar-login">Inicia sesión aquí</a></p>
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
            