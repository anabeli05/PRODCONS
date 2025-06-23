<<<<<<< HEAD
<!DOCTYPE html>
<html>
<head>
    <title>Verificar Tablas</title>
</head>
<body>
    <h1>Verificación de Tablas de Comentarios</h1>
    <pre>
<?php
require_once __DIR__ . '/../Base de datos/conexion_ajax.php';

try {
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    // Mostrar todas las tablas que contienen "comentario"
    $result = $conn->query("SHOW TABLES LIKE '%comentario%'");
    echo "Tablas que contienen 'comentario':\n";
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "\n";
    }

    // Mostrar todas las tablas que contienen "comment"
    $result = $conn->query("SHOW TABLES LIKE '%comment%'");
    echo "\nTablas que contienen 'comment':\n";
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "\n";
    }

    // Mostrar estructura de la tabla comentarios_autor si existe
    $result = $conn->query("SHOW TABLES LIKE 'comentarios_autor'");
    if ($result->num_rows > 0) {
        echo "\nEstructura de comentarios_autor:\n";
        $result = $conn->query("DESCRIBE comentarios_autor");
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "\nLa tabla comentarios_autor NO existe.\n";
        
        // Buscar otras posibles tablas
        echo "\nTodas las tablas en la base de datos:\n";
        $result = $conn->query("SHOW TABLES");
        while ($row = $result->fetch_array()) {
            if (stripos($row[0], 'comment') !== false || stripos($row[0], 'comentario') !== false) {
                echo "*** " . $row[0] . " ***\n";
            } else {
                echo "- " . $row[0] . "\n";
            }
        }
    }

    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
    </pre>
</body>
</html>
=======
<!DOCTYPE html>
<html>
<head>
    <title>Verificar Tablas</title>
</head>
<body>
    <h1>Verificación de Tablas de Comentarios</h1>
    <pre>
<?php
require_once __DIR__ . '/../Base de datos/conexion_ajax.php';

try {
    $conexion = new Conexion();
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    // Mostrar todas las tablas que contienen "comentario"
    $result = $conn->query("SHOW TABLES LIKE '%comentario%'");
    echo "Tablas que contienen 'comentario':\n";
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "\n";
    }

    // Mostrar todas las tablas que contienen "comment"
    $result = $conn->query("SHOW TABLES LIKE '%comment%'");
    echo "\nTablas que contienen 'comment':\n";
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "\n";
    }

    // Mostrar estructura de la tabla comentarios_autor si existe
    $result = $conn->query("SHOW TABLES LIKE 'comentarios_autor'");
    if ($result->num_rows > 0) {
        echo "\nEstructura de comentarios_autor:\n";
        $result = $conn->query("DESCRIBE comentarios_autor");
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "\nLa tabla comentarios_autor NO existe.\n";
        
        // Buscar otras posibles tablas
        echo "\nTodas las tablas en la base de datos:\n";
        $result = $conn->query("SHOW TABLES");
        while ($row = $result->fetch_array()) {
            if (stripos($row[0], 'comment') !== false || stripos($row[0], 'comentario') !== false) {
                echo "*** " . $row[0] . " ***\n";
            } else {
                echo "- " . $row[0] . "\n";
            }
        }
    }

    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
    </pre>
</body>
</html>
>>>>>>> main
