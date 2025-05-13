<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
include '../Base de datos/conexion.php'; // Ajusta la ruta si es necesario

// Recibe los datos del formulario
$email = $_POST['email'];
$password = $_POST['password'];

// Consulta para buscar el usuario
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
    // Si tus contraseñas están hasheadas, usa password_verify:
    // if (password_verify($password, $usuario['contraseña'])) {
    // Si NO están hasheadas, usa:
    if ($password == $usuario['contraseña']) {
        // Login exitoso
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
        header("Location: ../dashboard.php"); // Cambia la ruta según tu estructura
        exit();
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "Usuario no encontrado.";
}
?>
