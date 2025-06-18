<?php
session_start();
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion_ajax.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    echo json_encode(['success' => false, 'liked' => false, 'message' => 'No autorizado']);
    exit();
}

// Obtener datos
$article_id = isset($_POST['ID_Articulo']) ? intval($_POST['ID_Articulo']) : 0;
$user_id = $_SESSION['Usuario_ID'];

if (!$article_id) {
    echo json_encode([
        'success' => false, 
        'liked' => false, 
        'message' => 'ID del artículo inválido'
    ]);
    exit();
}

// Conexión a la base de datos
$conexion = new Conexion();
try {
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    if (!$conn) {
        throw new Exception("Error de conexión: " . mysqli_connect_error());
    }

    // Verificar si el usuario ya ha dado like
    $stmt = $conn->prepare("SELECT Usuario_ID FROM likes WHERE ID_Articulo = ? AND Usuario_ID = ?");
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $article_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($conn->error) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    
    $like_status = $result->fetch_assoc();
    $has_liked = $like_status ? true : false;
    
    // Obtener el total_likes del artículo
    $stmt = $conn->prepare("SELECT COUNT(Usuario_ID) as total_likes FROM likes WHERE ID_Articulo = ?");
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($conn->error) {
        throw new Exception("Error obteniendo total de likes: " . $conn->error);
    }
    
    $like_count = $result->fetch_assoc();
    $total_likes = $like_count['total_likes'] ?? 0;
    
    echo json_encode([
        'success' => true,
        'liked' => $has_liked,
        'total_likes' => $total_likes,
        'debug' => [
            'query_error' => $conn->error,
            'article_id' => $article_id,
            'user_id' => $user_id
        ]
    ]);
} catch (Exception $e) {
    error_log("Error en check-like.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'liked' => false,
        'total_likes' => 0,
        'error' => 'Error al verificar el estado del like: ' . $e->getMessage()
    ]);
}
?>
