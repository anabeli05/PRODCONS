<?php
session_start();
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion_ajax.php';

// Obtener el ID del artículo usando el mismo método que en el HTML
$ID_Articulo = isset($_POST['ID_Articulo']) ? intval($_POST['ID_Articulo']) : 0;

if (!$ID_Articulo || $ID_Articulo <= 0) {

    echo json_encode([
        'success' => false, 
        'message' => 'ID del artículo inválido',
        'debug' => [
            'ID_Articulo' => $ID_Articulo,
            'received_data' => $_POST
        ]
    ]);
    exit();
}

try {
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    if (!$conn) {
    
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos',
            'error' => mysqli_connect_error()
        ]);
        exit;
    }
    
    // Verificar si la conexión está activa
    if (!$conn->ping()) {
    
        echo json_encode([
            'success' => false,
            'message' => 'La conexión a la base de datos está inactiva'
        ]);
        exit;
    }

    // Debug: Estado de la conexión

    
    // Obtener los 6 comentarios más recientes
    // Verificar si la tabla comentarios_autor existe
    $checkTable = $conn->query("SHOW TABLES LIKE 'comentarios_autor'");
    if (!$checkTable || $checkTable->num_rows == 0) {
    
        throw new Exception("La tabla comentarios_autor no existe en la base de datos");
    }

    $stmt = $conn->prepare("
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
    
    if (!$stmt) {
    
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    

    $stmt->bind_param("i", $ID_Articulo);
    
    if (!$stmt->execute()) {
    
        throw new Exception("Error en execute: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($stmt->error) {
    
        throw new Exception("Error en get_result: " . $stmt->error);
    }

    // Obtener el total de comentarios (sin filtrar por visto)
    $totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM comentarios_autor WHERE ID_Articulo = ?");
    $totalStmt->bind_param("i", $ID_Articulo);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    $totalComments = $totalRow ? $totalRow['total'] : 0;

    // Cerrar la consulta total
    $totalStmt->close();
    $totalResult->close();

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        // Asegurarse de que todos los campos existen
        $comments[] = [
            'id' => $row['id'] ?? null,
            'Usuario_Nombre' => htmlspecialchars($row['Usuario_Nombre'] ?? 'Usuario Anónimo'),
            'foto_perfil' => $row['foto_perfil'] ?? '/PRODCONS/PI2do/imagenes/logos/perfil.png',
            'Comentario' => htmlspecialchars($row['Comentario'] ?? ''),
            'Fecha' => $row['Fecha'] ?? date('d/m/Y')
        ];
    }

    // Debug: Resultado de la consulta
    error_log("DEBUG - get-comments.php: Comentarios encontrados: " . count($comments));

    echo json_encode([
        'success' => true,
        'comments' => $comments,
        'total_comments' => $totalComments
    ]);
} catch (Exception $e) {

    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'article_id' => $ID_Articulo
        ]
    ]);
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
