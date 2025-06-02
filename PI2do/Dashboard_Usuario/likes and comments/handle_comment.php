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
if (!isset($_SESSION['Usuario_ID']) && !isset($_SESSION['usuario_id'])) { // Verificar ambos posibles nombres de sesión
     echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
     exit();
}

// Normalizar el ID del usuario logueado
$usuario_id_logueado = $_SESSION['Usuario_ID'] ?? $_SESSION['usuario_id'];

// Obtener el ID del post y el contenido del comentario desde la solicitud POST
$post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
$contenido_comentario = filter_input(INPUT_POST, 'comment_text', FILTER_SANITIZE_STRING); // Sanitizar el comentario

// Validar los datos
if ($post_id === false || $post_id === null || empty($contenido_comentario)) {
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
    // 1. Insertar el nuevo comentario en la tabla de comentarios de usuario
    // Asumiendo una tabla 'comentarios' con columnas 'post_id', 'usuario_id', 'contenido', 'fecha'
    $stmt = $conn->prepare("INSERT INTO comentarios (post_id, usuario_id, contenido, fecha) VALUES (?, ?, ?, NOW())");
    if (!$stmt) {
        throw new Exception("Error preparando consulta de inserción de comentario: " . $conn->error);
    }
    $stmt->bind_param("iis", $post_id, $usuario_id_logueado, $contenido_comentario);
    $stmt->execute();
    $stmt->close();

    // 2. Obtener el ID del editor propietario del post y el título del post
    // Asumiendo que la tabla se llama 'posts' y tiene columnas 'usuario_id' y 'Titulo'
    $stmt = $conn->prepare("SELECT usuario_id, Titulo FROM posts WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Error preparando consulta para obtener info del post: " . $conn->error);
    }
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post_info = $result->fetch_assoc();
    $stmt->close();

    if ($post_info && $post_info['usuario_id']) {
        $editor_id = $post_info['usuario_id'];
        $post_titulo = $post_info['Titulo'] ?? 'una publicación'; // Fallback si no hay título

        // 3. Insertar notificación para el editor
        // Obtener el nombre del usuario que comentó para el mensaje de notificación
        $stmt_user_comment = $conn->prepare("SELECT Nombre FROM usuarios WHERE Usuario_ID = ?");
        if (!$stmt_user_comment) {
            throw new Exception("Error preparando consulta para obtener nombre de usuario comentador: " . $conn->error);
        }
        $stmt_user_comment->bind_param("i", $usuario_id_logueado);
        $stmt_user_comment->execute();
        $result_user_comment = $stmt_user_comment->get_result();
        $user_comment_info = $result_user_comment->fetch_assoc();
        $stmt_user_comment->close();

        $nombre_usuario_comentario = $user_comment_info['Nombre'] ?? 'Alguien';

        $mensaje_notificacion = "ha comentado en tu publicación '$post_titulo'.";

        $stmt_notif = $conn->prepare("INSERT INTO notificaciones (usuario_id, tipo, mensaje, autor, post_id, fecha) VALUES (?, ?, ?, ?, ?, NOW())");
        if (!$stmt_notif) {
            throw new Exception("Error preparando consulta de inserción de notificación: " . $conn->error);
        }
        $tipo_notificacion = 'comentario';
        $stmt_notif->bind_param("isssi", $editor_id, $tipo_notificacion, $mensaje_notificacion, $nombre_usuario_comentario, $post_id);
        $stmt_notif->execute();
        $stmt_notif->close();
    }

    // Si todo fue bien
    echo json_encode(['success' => true, 'message' => 'Comentario agregado.']);

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