<<<<<<< HEAD
<?php
session_start();
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion_ajax.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    echo json_encode(['success' => false, 'favorited' => false, 'message' => 'No autorizado']);
    exit();
}

// Obtener datos
$article_id = isset($_POST['ID_Articulo']) ? intval($_POST['ID_Articulo']) : 0;
$user_id = $_SESSION['Usuario_ID'];

if (!$article_id) {
    echo json_encode([
        'success' => false, 
        'favorited' => false, 
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

    // Verificar si el usuario ya ha marcado como favorito
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM favoritos WHERE Articulo_ID = ? AND Usuario_ID = ?");
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $article_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($conn->error) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    
    $row = $result->fetch_assoc();
    $favorited = $row['count'] > 0;
    
    echo json_encode([
        'success' => true,
        'favorited' => $favorited
    ]);
} catch (Exception $e) {
    error_log("Error en check-favorite.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'favorited' => false,
        'message' => 'Error al verificar el estado del favorito: ' . $e->getMessage()
    ]);
}
=======
<?php
session_start();
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion_ajax.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    echo json_encode(['success' => false, 'favorited' => false, 'message' => 'No autorizado']);
    exit();
}

// Obtener datos
$article_id = isset($_POST['ID_Articulo']) ? intval($_POST['ID_Articulo']) : 0;
$user_id = $_SESSION['Usuario_ID'];

if (!$article_id) {
    echo json_encode([
        'success' => false, 
        'favorited' => false, 
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

    // Verificar si el usuario ya ha marcado como favorito
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM favoritos WHERE Articulo_ID = ? AND Usuario_ID = ?");
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $article_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($conn->error) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    
    $row = $result->fetch_assoc();
    $favorited = $row['count'] > 0;
    
    echo json_encode([
        'success' => true,
        'favorited' => $favorited
    ]);
} catch (Exception $e) {
    error_log("Error en check-favorite.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'favorited' => false,
        'message' => 'Error al verificar el estado del favorito: ' . $e->getMessage()
    ]);
}
>>>>>>> main
