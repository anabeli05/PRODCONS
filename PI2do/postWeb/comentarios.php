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
$comment_text = isset($_POST['comment_text']) ? trim($_POST['comment_text']) : '';

if (!$article_id || !$user_id || empty($comment_text)) {
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
    // Insertar el nuevo comentario
    $stmt = $conn->prepare("INSERT INTO comentarios_autor (Articulo_ID, Usuario_ID, Comentario, Fecha) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $article_id, $user_id, $comment_text);
    $stmt->execute();
    
    // Obtener el nuevo total de comentarios
    $stmt = $conn->prepare("SELECT COUNT(*) as total_comments FROM comentarios_autor WHERE Articulo_ID = ?");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $comment_count = $result->fetch_assoc();
    
    echo json_encode(['success' => true, 'total_comments' => $comment_count['total_comments']]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
