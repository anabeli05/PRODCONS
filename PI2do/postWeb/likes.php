<?php
session_start();
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Obtener datos
$article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

if (!$article_id || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

// Conexión a la base de datos
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit();
}

try {
    // Verificar si ya existe un like
    $stmt = $conn->prepare("SELECT * FROM likes WHERE Articulo_ID = ? AND Usuario_ID = ?");
    $stmt->bind_param("ii", $article_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Si existe, eliminar el like
        $stmt = $conn->prepare("DELETE FROM likes WHERE Articulo_ID = ? AND Usuario_ID = ?");
    } else {
        // Si no existe, crear el like
        $stmt = $conn->prepare("INSERT INTO likes (Articulo_ID, Usuario_ID) VALUES (?, ?)");
    }
    
    $stmt->bind_param("ii", $article_id, $user_id);
    $stmt->execute();
    
    // Obtener el nuevo total de likes
    $stmt = $conn->prepare("SELECT COUNT(*) as total_likes FROM likes WHERE Articulo_ID = ?");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $like_count = $result->fetch_assoc();
    
    echo json_encode(['success' => true, 'total_likes' => $like_count['total_likes']]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
