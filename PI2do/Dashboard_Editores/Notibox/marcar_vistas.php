<?php
session_start();
include '../Base de datos/conexion.php';

$conexion = new Conexion();
$conn = $conexion->conexion;
$Usuario_ID = $_SESSION['Usuario_ID'];

try {
    // Iniciar transacción
    $conn->begin_transaction();
    
    // Actualizar comentarios_autor
    $stmt1 = $conn->prepare("UPDATE comentarios_autor SET visto = 1 WHERE Usuario_ID = ? AND visto = 0");
    $stmt1->bind_param("i", $Usuario_ID);
    $stmt1->execute();
    
    // Actualizar likes
    $stmt2 = $conn->prepare("UPDATE likes SET visto = 1 WHERE Usuario_ID = ? AND visto = 0");
    $stmt2->bind_param("i", $Usuario_ID);
    $stmt2->execute();
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
