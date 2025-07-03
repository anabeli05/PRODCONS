<?php
session_start();

// Función para verificar si el usuario está registrado
function verificarRegistro() {
    // Verificar si el usuario está registrado
    if (!isset($_SESSION['Rol'])) {
        $_SESSION['login_error'] = "Error: Debes estar registrado para acceder a esta página"; // Mensaje de error con estilo
        header("Location: /PRODCONS/PI2do/inicio_sesion/login.php?error=1");
        exit();
    }

    // Verificar si el rol es válido
    $rol_valido = false;
    $dashboard = '';
    
    // Definir los roles permitidos y sus dashboards correspondientes
    $roles_permitidos = [
        'Usuario' => '/PRODCONS/PI2do/Dashboard_Usuario/inicio/usuario.php',
        'Editor' => '/PRODCONS/PI2do/Dashboard_Editores/inicio/inicio.php',
        'SuperAdmin' => '/PRODCONS/PI2do/Dashboard_SuperAdmin/inicio/inicioSA.php'
    ];

    // Verificar si el rol existe y obtener su dashboard
    if (isset($roles_permitidos[$_SESSION['Rol']])) {
        $rol_valido = true;
        $dashboard = $roles_permitidos[$_SESSION['Rol']];
    }

    // Si el rol no es válido
    if (!$rol_valido) {
        $_SESSION['login_error'] = "Error: No tienes permisos para acceder a esta página"; // Mensaje de error con estilo
        header("Location: /PRODCONS/PI2do/inicio_sesion/login.php?error=1");
        exit();
    }

    // Verificar si el usuario existe en la base de datos
    include '../../Base de datos/conexion.php';
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    // Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Correo = ?");
    $stmt->bind_param("s", $_SESSION['Correo']);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        $_SESSION['login_error'] = "Error: Tu cuenta no existe, por favor regístrate"; // Mensaje de error con estilo
        header("Location: /PRODCONS/PI2do/inicio_sesion/login.php?error=1");
        exit();
    }

    // Redirigir al dashboard correspondiente al rol
    header("Location: {$dashboard}");
    exit();
}

// Ejecutar la verificación
verificarRegistro();
?>
?>
