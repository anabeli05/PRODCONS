<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../../Base de datos/conexion.php'; // Ajusta la ruta si es necesario

header('Content-Type: application/json'); // Responder siempre con JSON

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    exit();
}

// Obtener los datos de la solicitud
$Articulo_ID = filter_input(INPUT_POST, 'Articulo_ID', FILTER_VALIDATE_INT);
$Usuario_ID = filter_input(INPUT_POST, 'Usuario_ID', FILTER_VALIDATE_INT);

// Verificar si los datos son válidos
if (!$Articulo_ID || !$Usuario_ID) {
    echo json_encode([
        'success' => false, 
        'message' => 'Datos inválidos.',
        'debug' => [
            'Articulo_ID' => $Articulo_ID,
            'Usuario_ID' => $Usuario_ID
        ]
    ]);
    exit();
}

// Crear una nueva conexión
$conexion = new Conexion();
try {
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;
    
    if (!$conn) {
        throw new Exception("Conexión a la base de datos fallida");
    }

    // Verificar si ya existe el like
    $stmt = $conn->prepare("SELECT Like_ID FROM likes WHERE Articulo_ID = ? AND Usuario_ID = ?");
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    $stmt->bind_param("ii", $Articulo_ID, $Usuario_ID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Ya has dado like a esta publicación.',
            'debug' => [
                'query' => $stmt->error
            ]
        ]);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Insertar el nuevo like
    $stmt = $conn->prepare("INSERT INTO likes (Articulo_ID, Usuario_ID, Fecha, visto, total_likes) VALUES (?, ?, NOW(), 0, 1)");
    if (!$stmt) {
        throw new Exception("Error preparando inserción: " . $conn->error);
    }
    $stmt->bind_param("ii", $Articulo_ID, $Usuario_ID);
    
    if ($stmt->execute()) {
        // Obtener el total de likes
        $stmt = $conn->prepare("SELECT COUNT(*) as total_likes FROM likes WHERE Articulo_ID = ?");
        if (!$stmt) {
            throw new Exception("Error preparando consulta de total likes: " . $conn->error);
        }
        $stmt->bind_param("i", $Articulo_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $total_likes = $result->fetch_assoc()['total_likes'];
        $stmt->close();

        // Si todo fue bien
        echo json_encode([
            'success' => true, 
            'message' => 'Me gusta registrado exitosamente.',
            'total_likes' => $total_likes
        ]);
    } else {
        throw new Exception("Error al ejecutar la inserción: " . $stmt->error);
    }

} catch (Exception $e) {
    // Log the error for debugging
    error_log("Error en handle_like.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error al procesar el like: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
} finally {
    // Cerrar conexión
    if ($conexion) {
        $conexion->cerrar_conexion();
    }
}
?> 