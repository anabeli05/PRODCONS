<<<<<<< HEAD
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Incluir conexión a la base de datos
// La ruta puede necesitar ajuste dependiendo de dónde se coloque este script
require_once __DIR__ . '/../../Base de datos/conexion.php';

// Crear instancia de la conexión
$conexion = new Conexion();
$conn = null; // Inicializar $conn a null

try {
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    // Obtener la fecha y hora actual en el formato de la base de datos
    $fecha_hora_actual = date('Y-m-d H:i:s');

    // Consulta para seleccionar artículos programados para publicación
    // Consideramos estados que no son finales (Publicado, Eliminado, Rechazado)
    $sql = "SELECT ID_Articulo, `Fecha de Publicacion`, Estado FROM articulos 
            WHERE (`Estado` = 'Borrador' OR `Estado` = 'Programado') 
            AND `Fecha de Publicacion` <= ?";
    
    $stmt = $conn->prepare($sql);
    
    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt === false) {
        throw new Exception('Error en la preparación de la consulta: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $fecha_hora_actual);
    
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($result === false) {
         throw new Exception('Error al obtener resultados: ' . $stmt->error);
    }

    $articulos_a_publicar = [];
    while ($row = $result->fetch_assoc()) {
        $articulos_a_publicar[] = $row['ID_Articulo'];
    }

    // Actualizar el estado de los artículos encontrados a 'Publicado'
    if (!empty($articulos_a_publicar)) {
        // Crear una lista de placeholders '?' para la cláusula IN
        $placeholders = implode(',', array_fill(0, count($articulos_a_publicar), '?'));
        
        $sql_update = "UPDATE articulos SET Estado = 'Publicado' WHERE ID_Articulo IN ($placeholders)";
        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update === false) {
            throw new Exception('Error en la preparación de la consulta de actualización: ' . $conn->error);
        }

        // Construir el string de tipos para bind_param (todos son 'i' para ID_Articulo INT)
        $types = str_repeat('i', count($articulos_a_publicar));
        
        // bind_param requiere referencias, usamos ... para unpack el array con referencias
        $stmt_update->bind_param($types, ...$articulos_a_publicar);
        
        $stmt_update->execute();

        if ($stmt_update->affected_rows === false) {
             throw new Exception('Error al actualizar artículos: ' . $stmt_update->error);
        }
        
        echo "Se publicaron " . $stmt_update->affected_rows . " artículo(s) programado(s).
";
    } else {
        echo "No hay artículos programados para publicar en este momento.
";
    }

    $stmt->close();
    if (isset($stmt_update)) {
        $stmt_update->close();
    }

} catch (Exception $e) {
    error_log("Error en el script de publicación automática: " . $e->getMessage());
    echo "Ocurrió un error al intentar publicar artículos programados: " . $e->getMessage() . "
";
} finally {
    // Cerrar conexión
    if ($conexion) {
        $conexion->cerrar_conexion();
    }
}

=======
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Incluir conexión a la base de datos
// La ruta puede necesitar ajuste dependiendo de dónde se coloque este script
require_once __DIR__ . '/../../Base de datos/conexion.php';

// Crear instancia de la conexión
$conexion = new Conexion();
$conn = null; // Inicializar $conn a null

try {
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;

    // Obtener la fecha y hora actual en el formato de la base de datos
    $fecha_hora_actual = date('Y-m-d H:i:s');

    // Consulta para seleccionar artículos programados para publicación
    // Consideramos estados que no son finales (Publicado, Eliminado, Rechazado)
    $sql = "SELECT ID_Articulo, `Fecha de Publicacion`, Estado FROM articulos 
            WHERE (`Estado` = 'Borrador' OR `Estado` = 'Programado') 
            AND `Fecha de Publicacion` <= ?";
    
    $stmt = $conn->prepare($sql);
    
    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt === false) {
        throw new Exception('Error en la preparación de la consulta: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $fecha_hora_actual);
    
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($result === false) {
         throw new Exception('Error al obtener resultados: ' . $stmt->error);
    }

    $articulos_a_publicar = [];
    while ($row = $result->fetch_assoc()) {
        $articulos_a_publicar[] = $row['ID_Articulo'];
    }

    // Actualizar el estado de los artículos encontrados a 'Publicado'
    if (!empty($articulos_a_publicar)) {
        // Crear una lista de placeholders '?' para la cláusula IN
        $placeholders = implode(',', array_fill(0, count($articulos_a_publicar), '?'));
        
        $sql_update = "UPDATE articulos SET Estado = 'Publicado' WHERE ID_Articulo IN ($placeholders)";
        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update === false) {
            throw new Exception('Error en la preparación de la consulta de actualización: ' . $conn->error);
        }

        // Construir el string de tipos para bind_param (todos son 'i' para ID_Articulo INT)
        $types = str_repeat('i', count($articulos_a_publicar));
        
        // bind_param requiere referencias, usamos ... para unpack el array con referencias
        $stmt_update->bind_param($types, ...$articulos_a_publicar);
        
        $stmt_update->execute();

        if ($stmt_update->affected_rows === false) {
             throw new Exception('Error al actualizar artículos: ' . $stmt_update->error);
        }
        
        echo "Se publicaron " . $stmt_update->affected_rows . " artículo(s) programado(s).
";
    } else {
        echo "No hay artículos programados para publicar en este momento.
";
    }

    $stmt->close();
    if (isset($stmt_update)) {
        $stmt_update->close();
    }

} catch (Exception $e) {
    error_log("Error en el script de publicación automática: " . $e->getMessage());
    echo "Ocurrió un error al intentar publicar artículos programados: " . $e->getMessage() . "
";
} finally {
    // Cerrar conexión
    if ($conexion) {
        $conexion->cerrar_conexion();
    }
}

>>>>>>> main
?> 