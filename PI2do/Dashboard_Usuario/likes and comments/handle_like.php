<?php
session_start();
require_once '../../Base de datos/conexion.php'; // Ajusta la ruta si es necesario

header('Content-Type: application/json'); // Responder siempre con JSON

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    exit();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit();
}

// Normalizar el ID del usuario logueado
$usuario_id_logueado = $_SESSION['Usuario_ID'];

// Obtener el ID del post desde la solicitud POST
$post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);

// Validar el post_id
if ($post_id === false || $post_id === null) {
    echo json_encode(['success' => false, 'message' => 'ID de post inválido.']);
    exit();
}

// Conectar a la base de datos
$conexion = new Conexion();
$conn = $conexion->conexion; // Obtener la conexión mysqli

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . $conn->connect_error]);
    exit();
}

try {
    // 1. Verificar si el usuario ya ha dado like a este post
    $stmt = $conn->prepare("SELECT id FROM likes WHERE ID_Articulo = ? AND Usuario_ID = ?");
    if (!$stmt) {
        throw new Exception("Error preparando consulta de verificación de like: " . $conn->error);
    }
    $stmt->bind_param("ii", $post_id, $usuario_id_logueado);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // El usuario ya dio like
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'Ya has dado like a esta publicación.']);
        exit();
    }
    $stmt->close();

    // 2. Insertar el nuevo like
    $stmt = $conn->prepare("INSERT INTO likes (ID_Articulo, Usuario_ID, fecha) VALUES (?, ?, NOW())");
    if (!$stmt) {
        throw new Exception("Error preparando consulta de inserción de like: " . $conn->error);
    }
    $stmt->bind_param("ii", $post_id, $usuario_id_logueado);
    $stmt->execute();
    $stmt->close();

    // 3. Obtener información del artículo y el usuario para la notificación
    $stmt = $conn->prepare("SELECT a.Usuario_ID, a.Titulo, u.Nombre 
                           FROM articulos a 
                           JOIN usuarios u ON u.Usuario_ID = ? 
                           WHERE a.ID_Articulo = ?");
    if (!$stmt) {
        throw new Exception("Error preparando consulta para obtener información: " . $conn->error);
    }
    $stmt->bind_param("ii", $usuario_id_logueado, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $info = $result->fetch_assoc();
    $stmt->close();

    if ($info) {
        // 4. Insertar notificación
        $mensaje = $info['Nombre'] . " le ha dado 'Me gusta' a tu publicación '" . $info['Titulo'] . "'";
        $stmt = $conn->prepare("INSERT INTO noti_box (Usuario_ID, Mensaje, Fecha, Leido) VALUES (?, ?, NOW(), 0)");
        if (!$stmt) {
            throw new Exception("Error preparando consulta de notificación: " . $conn->error);
        }
        $stmt->bind_param("is", $info['Usuario_ID'], $mensaje);
        $stmt->execute();
        $stmt->close();
    }

    // Si todo fue bien
    echo json_encode(['success' => true, 'message' => 'Me gusta registrado.']);

} catch (Exception $e) {
    // Log the error for debugging
    error_log("Error en handle_like.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al procesar el like.']);
} finally {
    // Cerrar conexión (si $conn fue inicializada)
    if ($conn) {
        $conn->close();
    }
}

?> 