<?php
$host = "gondola.proxy.rlwy.net";
$port = "49128";
$user = "root";
$password = "XSvZgbZFpXXcKskSSjFyvBmASVZeCXcM"; // pon aquí tu contraseña real de Railway
$dbname = "railway";

// Crear conexión
$conn = new mysqli($host, $user, $password, $dbname, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Opcional: establecer el charset a utf8
$conn->set_charset("utf8");
?>