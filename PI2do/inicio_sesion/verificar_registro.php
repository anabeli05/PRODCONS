<?php
session_start();

// Función para verificar si el usuario está registrado
function verificarRegistro() {
    // Verificar si el usuario está registrado
    if (!isset($_SESSION['Rol'])) {
        $_SESSION['login_error'] = "Error: Debes estar registrado para acceder a esta página";
        header("Location: /PRODCONS/PI2do/inicio_sesion/login.php");
        exit();
    }

    // Verificar si el rol es válido
    $rol_valido = false;
    $dashboard = '';
    
    // Definir los roles permitidos y sus dashboards correspondientes
    $roles_permitidos = [
        'Usuario' => '/PRODCONS/PI2do/Dashboard_Usuario/Inicio/usuario.php',
        'Editor' => '/PRODCONS/PI2do/Dashboard_Editor/inicio/inicio.php',
        'SuperAdmin' => '/PRODCONS/PI2do/Dashboard_SuperAdmin/inicio/inicioSA.php'
    ];

    // Verificar si el rol existe y obtener su dashboard
    if (isset($roles_permitidos[$_SESSION['Rol']])) {
        $rol_valido = true;
        $dashboard = $roles_permitidos[$_SESSION['Rol']];
    }

    // Si el rol no es válido
    if (!$rol_valido) {
        $_SESSION['login_error'] = "Error: No tienes permisos para acceder a esta página";
        header("Location: /PRODCONS/PI2do/inicio_sesion/login.php");
        exit();
    }

    // Verificar si el usuario existe en la base de datos
    include '../../Base de datos/conexion.php';
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    if (!$conn) {
        $_SESSION['login_error'] = "Error: No se pudo conectar a la base de datos";
        header("Location: /PRODCONS/PI2do/inicio_sesion/login.php");
        exit();
    }

    // Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Correo = ?");
    if (!$stmt) {
        $_SESSION['login_error'] = "Error: No se pudo verificar el usuario";
        header("Location: /PRODCONS/PI2do/inicio_sesion/login.php");
        exit();
    }

    $stmt->bind_param("s", $_SESSION['Correo']);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        $_SESSION['login_error'] = "Error: Tu cuenta no existe, por favor regístrate";
        header("Location: /PRODCONS/PI2do/inicio_sesion/login.php");
        exit();
    }

    $stmt->close();
    $conexion->cerrar_conexion();

    // Redirigir al dashboard correspondiente al rol
    header("Location: {$dashboard}");
    exit();
}

// Ejecutar la verificación
verificarRegistro();
?>
?>
