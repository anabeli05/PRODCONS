<?php
session_start();
include '../../Base de datos/conexion.php';

if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../inicio_sesion/login.php');
    exit();
}

$Usuario_ID = $_SESSION['Usuario_ID'];
$stmt = $conn->prepare("SELECT * FROM posts_planeados WHERE Usuario_ID = ? ORDER BY Fecha_Programada ASC");
$stmt->execute([$Usuario_ID]);
$planeados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extraer los días con posts programados
$dias_con_post = [];
foreach ($planeados as $post) {
    $fecha = date('Y-m-d', strtotime($post['Fecha_Programada']));
    $dias_con_post[$fecha] = true;
}

// Determinar el mes y año a mostrar (por GET, o actual por defecto)
$mes_actual = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');
$anio_actual = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

// Calcular mes anterior y siguiente
$mes_anterior = $mes_actual - 1;
$anio_anterior = $anio_actual;
if ($mes_anterior < 1) {
    $mes_anterior = 12;
    $anio_anterior--;
}
$mes_siguiente = $mes_actual + 1;
$anio_siguiente = $anio_actual;
if ($mes_siguiente > 12) {
    $mes_siguiente = 1;
    $anio_siguiente++;
}

// Formato para el título del mes
setlocale(LC_TIME, 'es_ES.UTF-8');
$nombre_mes = strftime('%B', mktime(0, 0, 0, $mes_actual, 1, $anio_actual));

$primer_dia = "$anio_actual-$mes_actual-01";
$ultimo_dia = date('t', strtotime($primer_dia));
$dia_semana = date('w', strtotime($primer_dia)); // 0=Domingo, 1=Lunes, ...

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Post Planeados</title>
    <link href='post-planeados.css'	 rel="stylesheet">
    <link href='../Dashboard/sidebar.css' rel="stylesheet">
    <script src='../Dashboard/barra-nav.js' defer></script>

</head>
<body>
<body>
    <header> 
        <div class="header-contenedor">
            <div class="principal"></div>
        </div>
    </header>

    <section class="logo"> 
        <div class="header_2">
            <img class="prodcons" src='../../imagenes/prodcon/logoSinfondo.png' alt="Logo">

            <div class="admin-controls">
                <!-- Botón de búsqueda-->
                <button class="search-toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>

                <!--Barra de búsqueda-->
                <div class="search-bar hidden">
                    <input type="text" placeholder="Buscar...">
                    <button class="search-close-btn">&times;</button>
                </div>

                <!--Botón de notificaciones-->
                <a href='/PRODCONS/PI2do/Dashboard_Editores/Notibox/noti-box.php' class="notif-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notif-badge">1</span>
                </a>

                <!-- Botón Admin con avatar -->
                <div class="admin-btn">
                    <span><?php echo htmlspecialchars($_SESSION['Nombre'] ?? 'Admin'); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 11l8 3l8 -3" />
                    </svg>
                    <img src='/PRODCONS/PI2do/imagenes/logos/perfil.png' alt="Admin" class="admin-avatar">
                </div>
            </div>

            <!-- Sidebar -->
            <div class="admin-sidebar" id="adminSidebar">
                <div class="sidebar-header">
                    <h3>ADMIN</h3>
                    <button class="close-sidebar">
                        <img src='../../imagenes/logos/perfil.png' alt="Admin" class="admin-avatar">
                    </button>
                </div>
                
                <nav class="sidebar-menu">

                <a href='../inicio/inicio.php'><!----cambiar la ruta a inicio---->
                        <span>Inicio</span>
                        <i class="fas fa-file-alt"></i>
                    </a>

                <a href='../MisArticulos/mis-articulos.php'>
                        <span>Mis Artículos</span>
                        <i class="fas fa-file-alt"></i>
                    </a>

                    <a href="../Crear nuevo post/post-form.html">
                        <span>Crear Post</span>
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href='../PostPlaneados/post-planeados.php'>
                        <span>Post Planeados</span>
                        <i class="fas fa-calendar"></i>
                    </a>
                                        
                    <a href='../Estadisticas/estadisticas-adm.php'>
                        <span>Estadísticas</span>
                        <i class="fas fa-chart-bar"></i>
                    </a>
                    
                    <a href='../Configuracion/configuracion.php'>
                        <span>Configuración</span>
                        <i class="fas fa-cog"></i>
                    </a>
                
                <div class="sidebar-footer">
                    <a href='../../inicio_sesion/logout.php' class="logout-btn">
                        Cerrar Sesión
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
                </nav>
            </div>
        </div>
    </section>

    <div class="contenedor-planeados">
        <div class="titulo-planeados">
            <h1>Post Planeados</h1>
        </div>

         <div class="division-dos">
             <div class="contenedor-cajas">

                <div class="programa">
                    <p>¡Programa tus post desde antes!</p>
                    <a href='../PostPlaneados/planeados-form.html' class="mas">+</a>
                    <img src='../imagenes/plantita.png' class="decoracion hojas-izq" width="80">
                    <img src='../imagenes/planta.png' class="decoracion hojas-der" width="80">
                </div>
                
                <div class="contenedor-calendario">
                    <div class="titulo-calendario" style="display:flex;align-items:center;justify-content:center;gap:20px;">
                        <a href="?mes=<?= $mes_anterior ?>&anio=<?= $anio_anterior ?>" class="flecha-mes" style="font-size:2rem;text-decoration:none;">&#8592;</a>
                        <h2 id="mes-actual" style="margin:0;"><?= ucfirst($nombre_mes) . " $anio_actual" ?></h2>
                        <a href="?mes=<?= $mes_siguiente ?>&anio=<?= $anio_siguiente ?>" class="flecha-mes" style="font-size:2rem;text-decoration:none;">&#8594;</a>
                    </div>
                    <div class="calendario">
                        <div class="dias-semana">
                            <div class="dia">D</div>
                            <div class="dia">L</div>
                            <div class="dia">M</div>
                            <div class="dia">M</div>
                            <div class="dia">J</div>
                            <div class="dia">V</div>
                            <div class="dia">S</div>
                        </div>
                        <div class="contenedores" style="flex-wrap: wrap;">
                            <?php
                            // Espacios en blanco antes del primer día
                            for ($i = 0; $i < $dia_semana; $i++) {
                                echo '<div class="cuadrito" style="background:transparent;box-shadow:none"></div>';
                            }
                            // Días del mes
                            for ($dia = 1; $dia <= $ultimo_dia; $dia++) {
                                $fecha_actual = sprintf('%04d-%02d-%02d', $anio_actual, $mes_actual, $dia);
                                $tiene_post = isset($dias_con_post[$fecha_actual]);
                                echo '<div class="cuadrito">';
                                echo $dia;
                                if ($tiene_post) {
                                    echo ' <span class="punto-post"></span>';
                                }
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="contenedor-postit">
                <h2>Post Planeados</h2>
                <div class="text">
                    <?php if (empty($planeados)): ?>
                        <p>No tienes ningún post programado</p>
                    <?php else: ?>
                        <?php foreach ($planeados as $post): ?>
                            <div class="post-planeado">
                                <strong><?= htmlspecialchars($post['Titulo']) ?></strong><br>
                                <span><?= date('d/m/Y H:i', strtotime($post['Fecha_Programada'])) ?></span>
                                <p><?= nl2br(htmlspecialchars($post['Contenido'])) ?></p>
                                <?php if ($post['Imagen']): ?>
                                    <img src="<?= htmlspecialchars($post['Imagen']) ?>" alt="Imagen del post" style="max-width:100px;">
                                <?php endif; ?>
                                <hr>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
         
    </div>
    

</body>
</html>