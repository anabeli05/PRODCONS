<?php
session_start();
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion_ajax.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'No autorizado',
        'debug' => 'Usuario no logueado'
    ]);
    exit();
}

// Obtener datos del formulario usando los nombres correctos de las columnas
$ID_Articulo = isset($_POST['ID_Articulo']) ? intval($_POST['ID_Articulo']) : 0;
$Comentario = isset($_POST['Comentario']) ? trim($_POST['Comentario']) : '';

// Usar el ID del usuario de la sesión
$Usuario_ID = isset($_SESSION['Usuario_ID']) ? intval($_SESSION['Usuario_ID']) : 0;

if (!$ID_Articulo || !$Usuario_ID || empty($Comentario)) {
    error_log("DEBUG - comentarios.php: Datos inválidos");
    echo json_encode([
        'success' => false, 
        'message' => 'Datos inválidos',
        'debug' => [
            'ID_Articulo' => $ID_Articulo,
            'Usuario_ID' => $Usuario_ID,
            'Comentario' => $Comentario
        ]
    ]);
    exit();
}

try {
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    if (!$conn) {

        throw new Exception("Error de conexión: " . mysqli_connect_error());
    }



    // Insertar el nuevo comentario usando los nombres correctos de las columnas
    $stmt = $conn->prepare("INSERT INTO comentarios_autor (ID_Articulo, Usuario_ID, Comentario, Fecha, visto) VALUES (?, ?, ?, NOW(), 1)");
    if (!$stmt) {

        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("iis", $ID_Articulo, $Usuario_ID, $Comentario);
    
    if (!$stmt->execute()) {

        throw new Exception("Error ejecutando consulta: " . $stmt->error);
    }



    // Obtener el ID del nuevo comentario
    $ComentarioUsuario_ID = $conn->insert_id;

    // Obtener los datos del usuario
    $stmt = $conn->prepare("SELECT Nombre, `Foto de Perfil` as foto_perfil FROM usuarios WHERE Usuario_ID = ?");
    if (!$stmt) {

        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $Usuario_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Obtener el total de comentarios para este artículo (solo los vistos)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM comentarios_autor WHERE ID_Articulo = ? AND visto = 1");
    if (!$stmt) {
        error_log("DEBUG - comentarios.php: Error preparando consulta de total");
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    // Obtener los 6 comentarios más recientes para mostrar (solo los vistos)
    $stmtComments = $conn->prepare("
        SELECT 
            c.ComentarioUsuario_ID as id,
            u.Nombre as Usuario_Nombre,
            u.`Foto de Perfil` as foto_perfil,
            c.Comentario as Comentario,
            DATE_FORMAT(c.Fecha, '%d/%m/%Y') as Fecha
        FROM comentarios_autor c
        JOIN usuarios u ON c.Usuario_ID = u.Usuario_ID
        WHERE c.ID_Articulo = ? AND c.visto = 1
        ORDER BY c.Fecha DESC
        LIMIT 6
    ");
    if (!$stmtComments) {
        error_log("DEBUG - comentarios.php: Error preparando consulta de comentarios");
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    if (!$stmt) {
        error_log("DEBUG - comentarios.php: Error preparando consulta de total");
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $ID_Articulo);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];

    // Ejecutar las consultas
    $stmt->bind_param("i", $ID_Articulo);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'] ?? 0;
    
    $stmtComments->bind_param("i", $ID_Articulo);
    $stmtComments->execute();
    $commentsResult = $stmtComments->get_result();
    $comments = [];
    while ($comment = $commentsResult->fetch_assoc()) {
        $comments[] = $comment;
    }

    // Preparar la respuesta completa
    $response = [
        'success' => true,
        'usuario' => [
            'id' => $Usuario_ID,
            'usuario' => $user['Nombre'] ?? 'Usuario Anónimo',
            'foto_perfil' => $user['foto_perfil'] ?? '/PRODCONS/PI2do/imagenes/logos/perfil.png',
            'comentario' => $Comentario,
            'fecha' => date('d/m/Y')
        ],
        'total_comentarios' => $total,
        'comments' => $comments
    ];

    // Debug: Datos del comentario
    error_log("DEBUG - comentarios.php: Nuevo comentario - ID: " . $ComentarioUsuario_ID);

    // Cerrar conexión
    $stmt->close();
    $conn->close();
    
    echo json_encode($response);
    exit();
} catch (Exception $e) {
    error_log("DEBUG - comentarios.php: Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'article_id' => $ID_Articulo,
            'user_id' => $Usuario_ID
        ]
    ], JSON_UNESCAPED_UNICODE);
} finally {
    // Asegurarse de cerrar la conexión y el statement
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
