<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../Base de datos/conexion.php';

// --- REGISTRO ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registro'])) {
    $nombre = $_POST['Nombre'];
    $correo = $_POST['Correo'];
    $password = $_POST['Contraseña'];
    $confirmar_password = $_POST['confirmar_password']; ///777777777777777

    if ($password !== $confirmar_password) {
        $error = "Las contraseñas no coinciden";
    } else {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $error = "El correo ya está registrado";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $rol = "Usuario";
            $stmt = $conn->prepare("INSERT INTO usuarios (Nombre, Correo, Contraseña, Rol) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $correo, $hash, $rol);
            if ($stmt->execute()) {
                $_SESSION['Usuario_ID'] = $stmt->insert_id;
                $_SESSION['Nombre'] = $nombre;
                $_SESSION['Rol'] = $rol;
                header("Location: /PRODCONS/usuario/usuario.php");
                exit();
            } else {
                $error = "Error al registrar el usuario";
            }
        }
        $stmt->close();
    }
    $conn->close();
    // IMPORTANTE: Detener la ejecución aquí para que no siga al login
    exit();
}
?>