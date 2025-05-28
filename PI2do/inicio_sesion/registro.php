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