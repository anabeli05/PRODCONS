<<<<<<< HEAD
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$admin_nombre = '';
if (!isset($_SESSION['Usuario_ID']) || $_SESSION['Rol'] !== 'Super Admin') {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Conexión a la base de datos
require_once __DIR__ . '/../../Base de datos/conexion.php';
$conexion = new Conexion();

try {
    $conexion->abrir_conexion();
    // Obtener nombre del admin
    $sql = "SELECT Nombre FROM usuarios WHERE Usuario_ID = ? AND rol = 'Super Admin'";
    $stmt = $conexion->conexion->prepare($sql);
    $stmt->bind_param("i", $_SESSION['Usuario_ID']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $admin_nombre = htmlspecialchars($admin['Nombre']);
    }
} catch (Exception $e) {
    error_log("Error al obtener nombre del admin: " . $e->getMessage());
} finally {
    if (isset($conexion)) {
        $conexion->cerrar_conexion();
    }
}

// Conexión a la base de datos
require_once __DIR__ . '/../../Base de datos/conexion.php';
$conexion = new Conexion();

try {
    $conexion->abrir_conexion();
    $mes_seleccionado = isset($_GET['mes']) ? $_GET['mes'] : 'febrero';
    $meses = [
        'enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04',
        'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08',
        'septiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'
    ];
    $anio_actual = date('Y');
    // Buscar la columna que contiene el ID del artículo en la tabla likes
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
    
    $query = "SELECT a.ID_Articulo, a.Titulo, a.Contenido, a.Imagen, a.Fecha, 
              COUNT(l.{$articuloIdColumn}) as likes
              FROM articulos a
              LEFT JOIN likes l ON a.ID_Articulo = l.{$articuloIdColumn}
              WHERE MONTH(a.Fecha) = ? 
              AND YEAR(a.Fecha) = ?
              GROUP BY a.ID_Articulo
              ORDER BY likes DESC 
              LIMIT 6";
    $stmt = $conexion->conexion->prepare($query);
    $stmt->bind_param("ss", $meses[$mes_seleccionado], $anio_actual);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $hay_articulos = $resultado->num_rows > 0;
} catch (Exception $e) {
    error_log("Error en inicioSA: " . $e->getMessage());
    $hay_articulos = false;
} finally {
    if (isset($conexion)) {
        $conexion->cerrar_conexion();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="inicioSA.css">
    <link rel="stylesheet" href="../Dashboard/sidebar.css">
    <script src='../Dashboard/barra-nav-copy.js' defer></script>
</head>
<body>

    <!--Head er importado-->
    <?php include('../Dashboard/sidebar.php'); ?>

    <section class="contenedor-principal">
        <div class="rec-admin">
            <div class="formato-txt">
                <h2>¡Hola <?php echo htmlspecialchars($admin_nombre); ?>!</h2>
                <p>Un blog exitoso se construye post a post. ¡Sigue adelante!</p>
            </div>
            <div class="admin-img">
                <img src="../../imagenes/chicaLaptop.png" alt="Admin Ilustración">
            </div>
        </div>

        <div class="contenedor-articulo">
            <div class="encabezado-articulos">
                <h2>ARTICULOS MAS POPULARES</h2>
                <div class="selector-meses">
                    <form method="get" style="margin:0;">
                        <select id="selectorMes" class="estilo-selector" name="mes" onchange="this.form.submit()">
                            <?php foreach($meses as $nombre_mes => $numero_mes): ?>
                                <option value="<?php echo $nombre_mes; ?>" <?php if($nombre_mes === $mes_seleccionado) echo 'selected'; ?>><?php echo ucfirst($nombre_mes); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="flecha-personalizada" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 11l8 3l8 -3" />
                        </svg>
                    </form>
                </div>
            </div>
            <div class="vista-articulos">
                <?php
                if ($hay_articulos) {
                    $contador = 1;
                    while($articulo = mysqli_fetch_assoc($resultado)) {
                        $fecha = date('d M Y', strtotime($articulo['fecha_publicacion']));
                        $numero = str_pad($contador, 2, '0', STR_PAD_LEFT);
                ?>
                <div class="articulo" data-mes="<?php echo strtolower(date('F', strtotime($articulo['fecha_publicacion']))); ?>">
                    <div class="numero"><?php echo $numero; ?></div>
                    <div class="contenido-articulo">
                        <div class="imagen-articulo">
                            <img src="<?php echo htmlspecialchars($articulo['imagen']); ?>" alt="<?php echo htmlspecialchars($articulo['titulo']); ?>">
                        </div>
                        <div class="texto-articulo">
                            <h3><?php echo htmlspecialchars($articulo['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($articulo['contenido']); ?></p>
                            <span class="fecha">Publicado el <?php echo $fecha; ?></span>
                        <span class="likes">Likes: <?php echo $articulo['likes']; ?></span>
                        </div>
                    </div>
                </div>
                <?php
                        $contador++;
                    }
                } else {
                    echo '<div class="no-articulos">No hay artículos disponibles en este momento.</div>';
                }
                ?>
            </div>
        </div>
    </section>
    <div id="overlay" class="hidden"></div>
</body>
</html>
=======
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Usuario_ID']) || $_SESSION['Rol'] !== 'Super Admin') {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Conexión a la base de datos
require_once __DIR__ . '/../../Base de datos/conexion.php';
$conexion = new Conexion();

try {
    $conexion->abrir_conexion();
    $mes_seleccionado = isset($_GET['mes']) ? $_GET['mes'] : 'febrero';
    $meses = [
        'enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04',
        'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08',
        'septiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'
    ];
    $anio_actual = date('Y');
    $query = "SELECT ID_Articulo, Titulo, Contenido, Imagen, Fecha, Vistas 
              FROM articulos 
              WHERE MONTH(Fecha) = ? 
              AND YEAR(Fecha) = ?
              ORDER BY Vistas DESC 
              LIMIT 6";
    $stmt = $conexion->conexion->prepare($query);
    $stmt->bind_param("ss", $meses[$mes_seleccionado], $anio_actual);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $hay_articulos = $resultado->num_rows > 0;
} catch (Exception $e) {
    error_log("Error en inicioSA: " . $e->getMessage());
    $hay_articulos = false;
} finally {
    if (isset($conexion)) {
        $conexion->cerrar_conexion();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="inicioSA.css">
    <link rel="stylesheet" href="../Dashboard/sidebar.css">
    <script src='../Dashboard/barra-nav-copy.js' defer></script>
</head>
<body>

    <!--Head er importado-->
    <?php include('../Dashboard/sidebar.php'); ?>

    <section class="contenedor-principal">
        <div class="rec-admin">
            <div class="formato-txt">
                <h2>¡Hola SuperAdmin!</h2>
                <p>Un blog exitoso se construye post a post. ¡Sigue adelante!</p>
            </div>
            <div class="admin-img">
                <img src="../../imagenes/chicaLaptop.png" alt="Admin Ilustración">
            </div>
        </div>

        <div class="contenedor-articulo">
            <div class="encabezado-articulos">
                <h2>ARTICULOS MAS VISTOS</h2>
                <div class="selector-meses">
                    <form method="get" style="margin:0;">
                        <select id="selectorMes" class="estilo-selector" name="mes" onchange="this.form.submit()">
                            <?php foreach($meses as $nombre_mes => $numero_mes): ?>
                                <option value="<?php echo $nombre_mes; ?>" <?php if($nombre_mes === $mes_seleccionado) echo 'selected'; ?>><?php echo ucfirst($nombre_mes); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="flecha-personalizada" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 11l8 3l8 -3" />
                        </svg>
                    </form>
                </div>
            </div>
            <div class="vista-articulos">
                <?php
                if ($hay_articulos) {
                    $contador = 1;
                    while($articulo = mysqli_fetch_assoc($resultado)) {
                        $fecha = date('d M Y', strtotime($articulo['fecha_publicacion']));
                        $numero = str_pad($contador, 2, '0', STR_PAD_LEFT);
                ?>
                <div class="articulo" data-mes="<?php echo strtolower(date('F', strtotime($articulo['fecha_publicacion']))); ?>">
                    <div class="numero"><?php echo $numero; ?></div>
                    <div class="contenido-articulo">
                        <div class="imagen-articulo">
                            <img src="<?php echo htmlspecialchars($articulo['imagen']); ?>" alt="<?php echo htmlspecialchars($articulo['titulo']); ?>">
                        </div>
                        <div class="texto-articulo">
                            <h3><?php echo htmlspecialchars($articulo['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($articulo['contenido']); ?></p>
                            <span class="fecha">Publicado el <?php echo $fecha; ?></span>
                        </div>
                    </div>
                </div>
                <?php
                        $contador++;
                    }
                } else {
                    echo '<div class="no-articulos">No hay artículos disponibles en este momento.</div>';
                }
                ?>
            </div>
        </div>
    </section>
    <div id="overlay" class="hidden"></div>
</body>
</html>
>>>>>>> 1c69748dd375ae7c49913422a4d68ee974457a24
