<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
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
    $sqlPosts = "SELECT COUNT(*) as total FROM posts";
    $conexion->sentencia = $sqlPosts;
    $stmt = $conexion->conexion->prepare($sqlPosts);
    $stmt->execute();
    $resultPosts = $stmt->get_result();
    $totalPosts = $resultPosts->fetch_assoc()['total'];
    
    // Total de visitas
    $sqlVisitas = "SELECT SUM(visitas) as total_visitas FROM posts";
    $conexion->sentencia = $sqlVisitas;
    $stmt = $conexion->conexion->prepare($sqlVisitas);
    $stmt->execute();
    $resultVisitas = $stmt->get_result();
    $totalVisitas = $resultVisitas->fetch_assoc()['total_visitas'];
    
    // Total de likes
    $sqlLikes = "SELECT COUNT(*) as total_likes FROM likes";
    $conexion->sentencia = $sqlLikes;
    $stmt = $conexion->conexion->prepare($sqlLikes);
    $stmt->execute();
    $resultLikes = $stmt->get_result();
    $totalLikes = $resultLikes->fetch_assoc()['total_likes'];
    
    // Total de comentarios
    $sqlComentarios = "SELECT COUNT(*) as total_comentarios FROM comentarios";
    $conexion->sentencia = $sqlComentarios;
    $stmt = $conexion->conexion->prepare($sqlComentarios);
    $stmt->execute();
    $resultComentarios = $stmt->get_result();
    $totalComentarios = $resultComentarios->fetch_assoc()['total_comentarios'];
    
    // Obtener posts más populares
    $sqlPopulares = "SELECT titulo, visitas FROM posts ORDER BY visitas DESC LIMIT 4";
    $conexion->sentencia = $sqlPopulares;
    $stmt = $conexion->conexion->prepare($sqlPopulares);
    $stmt->execute();
    $resultPopulares = $stmt->get_result();
    $postsPopulares = $resultPopulares->fetch_all(MYSQLI_ASSOC);
    
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
    <link rel="stylesheet" href="sidebar.css" />
    <script src="barra-nav.js" defer></script>
    <style>
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .stat h1 i {
            margin-right: 8px;
            color: #666;
        }
        .stat {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 10px;
        }
    </style>
</head>
<body>
    <header> 
        <div class="header-contenedor">
            <div class="principal"></div>
        </div>
    </header>

    <section class="logo"> 
        <div class="header_2">
            <img class="prodcons" src="../../imagenes/prodcon/logoSinfondo.png" alt="Logo">
            <div class="admin-controls">
                <!-- Resto del HTML del header -->
            </div>
        </div>
    </section>

    <div class="contenedor-estadisticas">
        <div class="titulo-estadisticas"><h1>Estadísticas Generales</h1></div>
        
        <section class="statistics">
            <div class="stat">
                <h1><i class="fas fa-file-alt"></i> Posts</h1>
                <p class="stat-number"><?php echo isset($totalPosts) ? number_format($totalPosts, 0, ',', '.') : '0'; ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-eye"></i> Vistas</h1>
                <p class="stat-number"><?php echo isset($totalVisitas) ? number_format($totalVisitas, 0, ',', '.') : '0'; ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-heart" style="color: #e4405f;"></i> Likes</h1>
                <p class="stat-number"><?php echo isset($totalLikes) ? number_format($totalLikes, 0, ',', '.') : '0'; ?></p>
            </div>

            <div class="stat">
                <h1><i class="fas fa-comment"></i> Comentarios</h1>
                <p class="stat-number"><?php echo isset($totalComentarios) ? number_format($totalComentarios, 0, ',', '.') : '0'; ?></p>
            </div>
        </section>

        <div class="titulo-estadisticas"><h1>Gráficos de Vistas</h1></div>
        
        <div class="contenedor-ok">
            <section class="chart">
                <div class="chart-wrapper">
                    <div class="bars">
                        <?php if (isset($postsPopulares)): ?>
                            <?php foreach ($postsPopulares as $index => $post): ?>
                                <div class="bar-label">
                                    <div class="bar-title"><?php echo htmlspecialchars($post['titulo']); ?></div>
                                    <div class="bar-container">
                                        <div class="bar bar<?php echo $index + 1; ?>" 
                                             data-label="<?php echo number_format($post['visitas'], 0, ',', '.'); ?>"
                                             data-views="<?php echo $post['visitas']; ?>">
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
