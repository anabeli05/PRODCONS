<?php
session_start();
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion_ajax.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Obtener datos
$article_id = isset($_POST['ID_Articulo']) ? intval($_POST['ID_Articulo']) : 0;
$user_id = $_SESSION['Usuario_ID'];
$hasLiked = isset($_POST['hasLiked']) ? filter_var($_POST['hasLiked'], FILTER_VALIDATE_BOOLEAN) : false;

if (!$article_id) {
    echo json_encode(['success' => false, 'message' => 'ID del artículo inválido']);
    exit();
}

try {
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    if (!$conn) {
        throw new Exception("Error de conexión: " . mysqli_connect_error());
    }

    // Verificar si el like ya existe
    $checkStmt = $conn->prepare("SELECT Usuario_ID FROM likes WHERE ID_Articulo = ? AND Usuario_ID = ?");
    if (!$checkStmt) {
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $checkStmt->bind_param("ii", $article_id, $user_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $existingLike = $result->fetch_assoc();
    $checkStmt->close();

    if ($existingLike) {
        // Si existe y se quiere quitar el like, eliminar el registro
        if (!$hasLiked) {
            $deleteStmt = $conn->prepare("DELETE FROM likes WHERE ID_Articulo = ? AND Usuario_ID = ?");
            $deleteStmt->bind_param("ii", $article_id, $user_id);
            $success = $deleteStmt->execute();
            $deleteStmt->close();
        } else {
            // Si existe y se quiere dar like, actualizar la fecha
            $updateStmt = $conn->prepare("UPDATE likes SET Fecha = NOW() WHERE ID_Articulo = ? AND Usuario_ID = ?");
            $updateStmt->bind_param("ii", $article_id, $user_id);
            $success = $updateStmt->execute();
            $updateStmt->close();
        }
    } else {
        // Si no existe y se quiere dar like, crear nuevo registro
        if ($hasLiked) {
            $insertStmt = $conn->prepare("INSERT INTO likes (ID_Articulo, Usuario_ID, Fecha) VALUES (?, ?, NOW())");
            $insertStmt->bind_param("ii", $article_id, $user_id);
            $success = $insertStmt->execute();
            $insertStmt->close();
        }
    }

    if (!$success) {
        throw new Exception("Error en la operación: " . $conn->error);
    }

    // Obtener el nuevo total de likes en una sola consulta
    $stmt = $conn->prepare("SELECT COUNT(Usuario_ID) as total_likes FROM likes WHERE ID_Articulo = ?");
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalLikes = $result->fetch_assoc();
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => $hasLiked ? 'Like agregado' : 'Like removido',
        'total_likes' => $totalLikes['total_likes']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conexion)) {
        $conexion->cerrar_conexion();
    }
}
exit();
