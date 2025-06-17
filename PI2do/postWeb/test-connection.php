<?php
session_start();
header('Content-Type: application/json');

// Habilitar el registro de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/logs/error.log');

require_once $_SERVER['DOCUMENT_ROOT'] . '/PRODCONS/PI2do/Base de datos/conexion_ajax.php';

try {
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    if (!$conn) {
        throw new Exception("Error de conexión: " . mysqli_connect_error());
    }
    
    // Verificar si la tabla comentarios_autor existe
    $checkTable = $conn->query("SHOW TABLES LIKE 'comentarios_autor'");
    if (!$checkTable || $checkTable->num_rows == 0) {
        throw new Exception("La tabla comentarios_autor no existe en la base de datos");
    }

    // Verificar la estructura de la tabla
    $tableInfo = $conn->query("DESCRIBE comentarios_autor");
    $columns = [];
    while ($row = $tableInfo->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    echo json_encode([
        'success' => true,
        'message' => 'Conexión y tabla verificadas correctamente',
        'debug' => [
            'table_exists' => true,
            'columns' => $columns
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'connection_error' => mysqli_connect_error()
        ]
    ]);
}
?>
