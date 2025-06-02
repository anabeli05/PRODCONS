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
    $stmt = $conn->prepare("SELECT id FROM likes WHERE post_id = ? AND usuario_id = ?");
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
    $stmt = $conn->prepare("INSERT INTO likes (post_id, usuario_id) VALUES (?, ?)");
    if (!$stmt) {
        throw new Exception("Error preparando consulta de inserción de like: " . $conn->error);
    }
    $stmt->bind_param("ii", $post_id, $usuario_id_logueado);
    $stmt->execute();
    $stmt->close();

    // 3. Obtener el ID del editor propietario del post y el título del post
    $stmt = $conn->prepare("SELECT usuario_id, Titulo FROM posts WHERE id = ?"); // Asumiendo que la tabla se llama 'posts' y tiene columnas 'usuario_id' y 'Titulo'
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

         // 4. Insertar notificación para el editor
         // Obtener el nombre del usuario que dio like para el mensaje de notificación
         $stmt_user_like = $conn->prepare("SELECT Nombre FROM usuarios WHERE Usuario_ID = ?");
         if (!$stmt_user_like) {
             throw new Exception("Error preparando consulta para obtener nombre de usuario: " . $conn->error);
         }
         $stmt_user_like->bind_param("i", $usuario_id_logueado);
         $stmt_user_like->execute();
         $result_user_like = $stmt_user_like->get_result();
         $user_like_info = $result_user_like->fetch_assoc();
         $stmt_user_like->close();

         $nombre_usuario_like = $user_like_info['Nombre'] ?? 'Alguien';

         $mensaje_notificacion = "$nombre_usuario_like le ha dado 'Me gusta' a tu publicación '$post_titulo'.";

         $stmt_notif = $conn->prepare("INSERT INTO notificaciones (usuario_id, tipo, mensaje, post_id, fecha) VALUES (?, ?, ?, ?, NOW())");
         if (!$stmt_notif) {
             throw new Exception("Error preparando consulta de inserción de notificación: " . $conn->error);
         }
         $tipo_notificacion = 'like';
         $stmt_notif->bind_param("issi", $editor_id, $tipo_notificacion, $mensaje_notificacion, $post_id);
         $stmt_notif->execute();
         $stmt_notif->close();
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