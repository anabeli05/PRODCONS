<?php
// Incluir conexión a la base de datos
require_once __DIR__ . '/conexion.php';

try {
    // Crear una instancia de la clase Conexion
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    // Obtener la estructura de la tabla articulos
    $sql = "DESCRIBE articulos";
    $result = $conn->query($sql);

    echo "<h2>Estructura de la tabla articulos:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conexion->cerrar_conexion();
}
?>
