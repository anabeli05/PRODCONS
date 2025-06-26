<?php
session_start();

// Debug: Mostrar información de la sesión
echo "Debug - Session variables:<br>";
echo "Usuario_ID: " . (isset($_SESSION['Usuario_ID']) ? $_SESSION['Usuario_ID'] : 'No definido') . "<br>";
echo "Rol: " . (isset($_SESSION['Rol']) ? $_SESSION['Rol'] : 'No definido') . "<br>";

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Verificar si el usuario es Super Admin
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'Super Admin') {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Incluir conexión a la base de datos
require_once '../../Base de datos/conexion.php';

// Crear una instancia de la clase Conexion
$conexion = new Conexion();

// Consulta para obtener estadísticas
try {
    // Abrir conexión
    $conexion->abrir_conexion();
    
    // Total de posts
    $sqlPosts = "SELECT COUNT(*) as total FROM articulos";
    $conexion->sentencia = $sqlPosts;
    $stmt = $conexion->conexion->prepare($sqlPosts);
    $stmt->execute();
    $resultPosts = $stmt->get_result();
    $totalPosts = $resultPosts->fetch_assoc()['total'];
    
    // Total de likes
    $sqlLikes = "SELECT COUNT(*) as total_likes FROM likes";
    $conexion->sentencia = $sqlLikes;
    $stmt = $conexion->conexion->prepare($sqlLikes);
    $stmt->execute();
    $resultLikes = $stmt->get_result();
    $totalLikes = $resultLikes->fetch_assoc()['total_likes'];
    
    // Total de comentarios
    $sqlComentarios = "SELECT COUNT(*) as total_comentarios FROM comentarios_autor";
    $conexion->sentencia = $sqlComentarios;
    $stmt = $conexion->conexion->prepare($sqlComentarios);
    $stmt->execute();
    $resultComentarios = $stmt->get_result();
    $totalComentarios = $resultComentarios->fetch_assoc()['total_comentarios'];
    
    // Obtener posts más populares por likes
    try {
        // Verificar columnas de la tabla likes
        $sqlColumns = "SHOW COLUMNS FROM likes";
        $stmt = $conexion->conexion->prepare($sqlColumns);
        $stmt->execute();
        $resultColumns = $stmt->get_result();
        $columns = $resultColumns->fetch_all(MYSQLI_ASSOC);
        
        // Buscar la columna que contiene el ID del artículo
        $articuloIdColumn = null;
        foreach ($columns as $column) {
            if (stripos($column['Field'], 'articulo') !== false) {
                $articuloIdColumn = $column['Field'];
                break;
            }
        }
        
        if (!$articuloIdColumn) {
            throw new Exception("No se encontró la columna del ID del artículo en la tabla likes");
        }
        
        // Obtener posts más populares
        $sqlPopulares = "SELECT a.Titulo as titulo, COALESCE(l.likes, 0) as likes
                         FROM articulos a
                         LEFT JOIN (
                             SELECT {$articuloIdColumn} as Articulo_ID, COUNT(*) as likes
                             FROM likes
                             GROUP BY {$articuloIdColumn}
                         ) l ON a.ID_Articulo = l.Articulo_ID
                         ORDER BY likes DESC";
        $stmt = $conexion->conexion->prepare($sqlPopulares);
        $stmt->execute();
        $resultPopulares = $stmt->get_result();
        $postsPopulares = $resultPopulares->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Error al obtener posts populares: " . $e->getMessage());
        $postsPopulares = array(
            array('titulo' => 'Post de ejemplo 1', 'likes' => 0)
        );
    }
} catch (Exception $e) {
    echo "Error al obtener las estadísticas: " . $e->getMessage();
    exit();
} finally {
    // Cerrar conexión
    $conexion->cerrar_conexion();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>PRODCONS - Estadísticas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="estadisticas-adm.css" />
    <link rel="stylesheet" href="../../Dashboard_SuperAdmin/Dashboard/sidebar.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../Dashboard_SuperAdmin/Dashboard/barra-nav-copy.js" defer></script>
    <style>
        .chart-wrapper {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .bar-label {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .bar-title {
            flex: 1;
            font-weight: bold;
            color: #333;
            margin-right: 20px;
        }
        .bar-container {
            flex: 2;
            height: 20px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        .bar {
            height: 100%;
            background: #007bff;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <section class="logo"> 
        <?php include('../../Dashboard_SuperAdmin/Dashboard/sidebar.php'); ?>
    </section>

    <div class="contenedor-estadisticas">
        <div class="titulo-estadisticas"><h1>Estadísticas Generales</h1></div>
        <section class="statistics">
            <div class="stat">
                <h1><i class="fas fa-file-alt"></i> Posts</h1>
                <p class="stat-number"><?php echo number_format($totalPosts, 0, ',', '.'); ?></p>
            </div>
            <div class="stat">
                <h1><i class="fas fa-heart" style="color: #e4405f;"></i> Likes</h1>
                <p class="stat-number"><?php echo number_format($totalLikes, 0, ',', '.'); ?></p>
            </div>
            <div class="stat">
                <h1><i class="fas fa-comment"></i> Comentarios</h1>
                <p class="stat-number"><?php echo number_format($totalComentarios, 0, ',', '.'); ?></p>
            </div>
        </section>

        <div class="titulo-estadisticas"><h1>Posts Populares</h1></div>
        <div class="contenedor-ok">
            <section class="chart">
                <div class="chart-wrapper">
                    <div class="bars">
                        <?php if (isset($postsPopulares)): ?>
                            <?php foreach ($postsPopulares as $index => $post): ?>
                                <div class="bar-label">
                                    <div class="bar-title"><?php echo htmlspecialchars($post['titulo']); ?></div>
                                    <div class="bar-container">
                                        <div class="bar" style="width: <?php echo min(100, ($post['likes'] / max(array_column($postsPopulares, 'likes'))) * 100); ?>%">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-data">No hay datos disponibles</div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
