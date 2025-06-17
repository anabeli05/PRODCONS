<?php
session_start();
header('Content-Type: application/json');

// Habilitar el registro de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/logs/error.log');

require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion_ajax.php';

// Debug: Mostrar datos recibidos
error_log("DEBUG - verificar-like.php: Datos recibidos - ID_Articulo: " . (isset($_POST['ID_Articulo']) ? $_POST['ID_Articulo'] : 'null'));
error_log("DEBUG - verificar-like.php: Datos recibidos - Usuario_ID: " . (isset($_POST['Usuario_ID']) ? $_POST['Usuario_ID'] : 'null'));

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    error_log("DEBUG - verificar-like.php: Usuario no logueado");
    echo json_encode([
        'success' => false, 
        'message' => 'No autorizado'
    ]);
    exit();
}

// Obtener datos del formulario
$ID_Articulo = isset($_POST['ID_Articulo']) ? intval($_POST['ID_Articulo']) : 0;
$Usuario_ID = isset($_POST['Usuario_ID']) ? intval($_POST['Usuario_ID']) : 0;

if (!$ID_Articulo || !$Usuario_ID) {
    error_log("DEBUG - verificar-like.php: Datos inválidos");
    echo json_encode([
        'success' => false, 
        'message' => 'Datos inválidos'
    ]);
    exit();
}

try {
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    if (!$conn) {
        error_log("DEBUG - verificar-like.php: Error de conexión: " . mysqli_connect_error());
        throw new Exception("Error de conexión: " . mysqli_connect_error());
    }
    
    // Verificar si la conexión está activa
    if (!$conn->ping()) {
        error_log("DEBUG - verificar-like.php: Conexión inactiva");
        throw new Exception("La conexión a la base de datos está inactiva");
    }

    // Verificar si el usuario ya ha dado like
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM likes WHERE ID_Articulo = ? AND Usuario_ID = ?");
    if (!$stmt) {
        error_log("DEBUG - verificar-like.php: Error preparando consulta");
        throw new Exception("Error preparando consulta: " . $conn->error);
    }

    $stmt->bind_param("ii", $ID_Articulo, $Usuario_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $response = [
        'success' => true,
        'liked' => $row['total'] > 0
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    error_log("DEBUG - verificar-like.php: Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al verificar el like'
    ]);
}
?>
