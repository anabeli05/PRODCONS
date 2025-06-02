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

// Obtener el ID del post y el contenido del comentario desde la solicitud POST
$post_id = filter_input(INPUT_POST, 'ID_Articulo', FILTER_VALIDATE_INT);
$comentario = filter_input(INPUT_POST, 'Comentario', FILTER_SANITIZE_STRING); // Sanitizar el comentario

// Validar los datos
if ($post_id === false || $post_id === null || empty($comentario)) {
    echo json_encode(['success' => false, 'message' => 'Datos de comentario inválidos.']);
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
    // 1. Insertar el comentario
    $stmt = $conn->prepare("INSERT INTO comentarios_autor (ID_Articulo, Usuario_ID, Comentario, Fecha) VALUES (?, ?, ?, NOW())");
    if (!$stmt) {
        throw new Exception("Error preparando consulta de inserción de comentario: " . $conn->error);
    }
    $stmt->bind_param("iis", $post_id, $usuario_id_logueado, $comentario);
    $stmt->execute();
    $stmt->close();

    // 2. Obtener información del artículo y el usuario para la notificación
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
        // 3. Insertar notificación
        $mensaje = $info['Nombre'] . " ha comentado en tu publicación '" . $info['Titulo'] . "': '" . $comentario . "'";
        $stmt = $conn->prepare("INSERT INTO noti_box (Usuario_ID, Mensaje, Fecha, Leido) VALUES (?, ?, NOW(), 0)");
        if (!$stmt) {
            throw new Exception("Error preparando consulta de notificación: " . $conn->error);
        }
        $stmt->bind_param("is", $info['Usuario_ID'], $mensaje);
        $stmt->execute();
        $stmt->close();
    }

    echo json_encode(['success' => true, 'message' => 'Comentario publicado.']);

} catch (Exception $e) {
    // Log the error for debugging
    error_log("Error en handle_comment.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al procesar el comentario.']);
} finally {
    // Cerrar conexión (si $conn fue inicializada)
    if ($conn) {
        $conn->close();
    }
}

?> 