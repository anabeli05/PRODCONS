<?php
session_start();

// Verificar si el usuario está logueado y es Super Admin
if (!isset($_SESSION['Usuario_ID']) || $_SESSION['Rol'] !== 'Super Admin') {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Verificar token CSRF
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: editores.php?error=2');
    exit();
}

// Verificar si se recibió el ID del editor
if (!isset($_GET['id'])) {
    header('Location: editores.php?error=3');
    exit();
}

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Base de datos/conexion.php';

try {
    // Crear una instancia de la clase Conexion
    $conexion = new Conexion();
    $conexion->abrir_conexion();

    // Obtener el ID del editor
    $editor_id = intval($_GET['id']);

    // Verificar si el editor existe y es un editor
    $stmt = $conexion->conexion->prepare("SELECT Usuario_ID, Nombre FROM usuarios WHERE Usuario_ID = ? AND rol = 'Editor'");
    $stmt->bind_param("i", $editor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("El editor no existe o no es un editor válido");
    }

    // Obtener el nombre del editor
    $editor = $result->fetch_assoc();
    $nombre_editor = $editor['Nombre'];

    // Actualizar los artículos del editor para que muestren "Usuario eliminado"
    $stmt = $conexion->conexion->prepare("UPDATE articulos SET Usuario_ID = NULL WHERE Usuario_ID = ?");
    $stmt->bind_param("i", $editor_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al actualizar los artículos: " . $conexion->conexion->error);
    }

    // Eliminar el editor de la tabla usuarios
    $stmt = $conexion->conexion->prepare("DELETE FROM usuarios WHERE Usuario_ID = ? AND rol = 'Editor'");
    $stmt->bind_param("i", $editor_id);
    
    if ($stmt->execute()) {
        header('Location: editores.php?success=1');
    } else {
        throw new Exception("Error al eliminar el editor: " . $conexion->conexion->error);
    }

} catch (Exception $e) {
    // Registrar el error en un archivo de log
    $error_log = __DIR__ . '/error_log.txt';
    $error_msg = date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n";
    file_put_contents($error_log, $error_msg, FILE_APPEND);
    
    // Redirigir con un código de error específico
    header('Location: editores.php?error=4');
} finally {
    if (isset($conexion)) {
        $conexion->cerrar_conexion();
    }
}
exit();
