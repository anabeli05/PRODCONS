<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['Usuario_ID'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['articleId']) || !isset($data['userId'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$articleId = $data['articleId'];
$userId = $data['userId'];

// Conexión a la base de datos
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

try {
    // Verificar si el artículo es favorito
    $sql = "SELECT COUNT(*) as isFavorited FROM favoritos WHERE Usuario_ID = ? AND Articulo_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $articleId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    
    echo json_encode(['isFavorited' => $row['isFavorited'] > 0]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al verificar el favorito']);
}

$conexion->cerrar_conexion();
