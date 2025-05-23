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
    <style>
        .flecha-mes {
            color: #4CAF50;
            font-weight: bold;
            padding: 0 10px;
            transition: color 0.2s;
        }
        .flecha-mes:hover {
            color: #388e3c;
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
            <img class="prodcons" src='../imagenes/prodcon/logoSinfondo.png' alt="Logo">

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
                <a href='../Notibox/noti-box.php' class="notif-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notif-badge">1</span>
                </a>

                <!-- Botón Admin con avatar -->
                <div class="admin-btn">
                    <span>Admin</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 11l8 3l8 -3" />
                    </svg>
                    <img src='../imagenes/logos/perfil.png' alt="Admin" class="admin-avatar">
                </div>
            </div>

            <!----- sidebar ----->
            <div class="admin-sidebar" id="adminSidebar">
                <div class="sidebar-header">
                    <h3>ADMIN</h3>
                    <button class="close-sidebar">
                        <img src='../imagenes/logos/perfil.png' alt="Admin" class="admin-avatar">
                    </button>
                </div>
                
                <nav class="sidebar-menu">
                    <a href='../MisArticulos/mis-articulos.php'>
                        <span>Mis Artículos</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </a>
                    
                    <a href='../Configuracion/config.php'>
                        <span>Configuración</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                    </a>
                    
                    <a href='../PostPlaneados/post-planeados.php'>
                        <span>Post Planeados</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </a>
                    
                    <a href='../Estadisticas/estadisticas-adm.php'>
                        <span>Estadísticas</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="20" x2="12" y2="10"/>
                            <line x1="18" y1="20" x2="18" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="16"/>
                        </svg>
                    </a>
                </nav>
                
                <div class="sidebar-footer">
                    <button class="logout-btn">Cerrar Sesión</button>
                </div>

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
                    <a href='formulario-nuevo-planeado.php' class="mas">+</a>
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