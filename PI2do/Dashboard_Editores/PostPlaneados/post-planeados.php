<<<<<<< HEAD
<?php
session_start();

include '../../Base de datos/conexion.php';

// Crear instancia de la conexión
$conexion = new Conexion();
try {
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;
} catch (Exception $e) {
    die("Error de conexión a la base de datos");
}

if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../inicio_sesion/login.php');
    exit();
}

$Usuario_ID = $_SESSION['Usuario_ID'];

try {
    $stmt = $conn->prepare("SELECT * FROM articulos WHERE Usuario_ID = ? ORDER BY `Fecha de Publicacion` ASC");
    
    $stmt->bind_param("i", $Usuario_ID);
    
    $stmt->execute();
    
    $result = $stmt->get_result();
    $planeados = [];
    while ($row = $result->fetch_assoc()) {
        $planeados[] = $row;
    }
} catch (Exception $e) {
    die("Error al obtener los posts planeados");
}

// Extraer los días con posts programados
$dias_con_post = [];
foreach ($planeados as $post) {
    $fecha = date('Y-m-d', strtotime($post['Fecha de Publicacion']));
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
$nombre_mes = date('F', mktime(0, 0, 0, $mes_actual, 1, $anio_actual));

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

    <!-- Tailwind CSS y font -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- CSS de traduccion -->
    <link rel="stylesheet" href="../../Dashboard_Editores/Dashboard/traduccion.css">
    <!-- Scripts de traducción -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>
</head>
<body>
<body>
    <header>
        <a href="javascript:history.back()" title="Regresar a la página principal" class="flex m-6 pl-4 ">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" 
            class="w-10 h-10 fill-current text-gray-700 hover:text-green-600 transition-colors duration-300">
            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>
        </a>
        <div class="header-contenedor">
            <div class="principal">
                <!-- Selector de bandera para cambio de idioma -->
                <div id="idiomaToggle" style="display: inline-block; margin-left: 15px;">
                    <img class="españa" id="banderaIdioma" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
                </div>
                <!-- Opciones de banderas desplegables -->
                <div id="idiomasOpciones" style="display: none;">
                    <img class="ingles" src="/PRODCONS/PI2do/imagenes/logos/ingles.png" onclick="cambiarIdioma('ingles')" alt="Cambiar a inglés" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                    <img class="españa" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" onclick="cambiarIdioma('espanol')" alt="Cambiar a español" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                </div>
            </div>
        </div>
    </header>

    <section class="logo"> 
        <div class="header_2">
            <a href="../inicio/inicio.php">
                <img class="prodcons" src='../../imagenes/prodcon/logoSinfondo.png' alt="Logo">
            </a>

            <div class="admin-controls">
                <button class="search-toggle-btn"></button>
                <div class="search-bar hidden">
                    <button class="search-close-btn"></button>
                </div>
                <!--Botón de notificaciones-->
                <a href='/PRODCONS/PI2do/Dashboard_Editores/Notibox/noti-box.php' class="notif-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notif-badge"></span>
                </a>

                <!-- Botón Admin con avatar -->
                <div class="admin-btn">
                    <span><?php echo htmlspecialchars($_SESSION['Nombre'] ?? 'Editor'); ?></span>
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
            <h1 class="text-[28px] font-semibold">Post Planeados</h1>
        </div>

         <div class="division-dos">
             <div class="contenedor-cajas">

                <div class="programa">
                    <p>¡Programa tus post desde antes!</p>
                    <a href='../PostPlaneados/planeados-form.html' class="mas">+</a>
                    <img src='/PRODCONS/PI2do/imagenes/plantita.png' class="decoracion hojas-izq" width="80">
                    <img src='/PRODCONS/PI2do/imagenes/planta.png' class="decoracion hojas-der" width="80">
                </div>
                
                <div class="contenedor-calendario">
                    <div class="titulo-calendario" style="display:flex;align-items:center;justify-content:center;gap:20px;font-size:px;font-weight:bold;">
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
                <h2 class="text-[30px] font-semibold">Post Planeados</h2>
                <div class="text">
                    <?php if (empty($planeados)): ?>
                        <p>No tienes ningún post programado</p>
                    <?php else: ?>
                        <?php foreach ($planeados as $post): ?>
                            <div class="post-planeado">
                                <div class="post-day"><?= date('d', strtotime($post['Fecha de Publicacion'] ?? date('Y-m-d'))) ?></div>
                                <div class="post-content-wrapper">
                                    <?php if (isset($post['Imagen']) && $post['Imagen']): ?>
                                        <img src="<?= htmlspecialchars($post['Imagen']) ?>" alt="Imagen del post" class="post-image">
                                    <?php endif; ?>
                                    <div class="post-text">
                                        <strong class="post-title"><?= htmlspecialchars($post['Titulo'] ?? '') ?></strong><br>
                                        <span class="post-date">Será publicado el <?= date('d', strtotime($post['Fecha de Publicacion'] ?? date('Y-m-d'))) ?> de <?= date('F', strtotime($post['Fecha de Publicacion'] ?? date('Y-m-d'))) ?> de <?= date('Y', strtotime($post['Fecha de Publicacion'] ?? date('Y-m-d'))) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
         
    </div>
</body>
=======
<?php
session_start();

include '../../Base de datos/conexion.php';

// Crear instancia de la conexión
$conexion = new Conexion();
try {
    $conexion->abrir_conexion();
    $conn = $conexion->conexion;
} catch (Exception $e) {
    die("Error de conexión a la base de datos");
}

if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../inicio_sesion/login.php');
    exit();
}

$Usuario_ID = $_SESSION['Usuario_ID'];

try {
    $stmt = $conn->prepare("SELECT * FROM articulos WHERE Usuario_ID = ? ORDER BY `Fecha de Publicacion` ASC");
    
    $stmt->bind_param("i", $Usuario_ID);
    
    $stmt->execute();
    
    $result = $stmt->get_result();
    $planeados = [];
    while ($row = $result->fetch_assoc()) {
        $planeados[] = $row;
    }
} catch (Exception $e) {
    die("Error al obtener los posts planeados");
}

// Extraer los días con posts programados
$dias_con_post = [];
foreach ($planeados as $post) {
    $fecha = date('Y-m-d', strtotime($post['Fecha de Publicacion']));
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
$nombre_mes = date('F', mktime(0, 0, 0, $mes_actual, 1, $anio_actual));

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

    <!-- Tailwind CSS y font -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- CSS de traduccion -->
    <link rel="stylesheet" href="../../Dashboard_Editores/Dashboard/traduccion.css">
    <!-- Scripts de traducción -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>
</head>
<body>
<body>
    <header>
        <a href="javascript:history.back()" title="Regresar a la página principal" class="flex m-6 pl-4 ">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" 
            class="w-10 h-10 fill-current text-gray-700 hover:text-green-600 transition-colors duration-300">
            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>
        </a>
        <div class="header-contenedor">
            <div class="principal">
                <!-- Selector de bandera para cambio de idioma -->
                <div id="idiomaToggle" style="display: inline-block; margin-left: 15px;">
                    <img class="españa" id="banderaIdioma" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
                </div>
                <!-- Opciones de banderas desplegables -->
                <div id="idiomasOpciones" style="display: none;">
                    <img class="ingles" src="/PRODCONS/PI2do/imagenes/logos/ingles.png" onclick="cambiarIdioma('ingles')" alt="Cambiar a inglés" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                    <img class="españa" src="/PRODCONS/PI2do/imagenes/logos/espanol.png" onclick="cambiarIdioma('espanol')" alt="Cambiar a español" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid black; cursor: pointer; margin-left: 5px;">
                </div>
            </div>
        </div>
    </header>

    <section class="logo"> 
        <div class="header_2">
            <a href="../inicio/inicio.php">
                <img class="prodcons" src='../../imagenes/prodcon/logoSinfondo.png' alt="Logo">
            </a>

            <div class="admin-controls">
                <button class="search-toggle-btn"></button>
                <div class="search-bar hidden">
                    <button class="search-close-btn"></button>
                </div>
                <!--Botón de notificaciones-->
                <a href='/PRODCONS/PI2do/Dashboard_Editores/Notibox/noti-box.php' class="notif-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notif-badge"></span>
                </a>

                <!-- Botón Admin con avatar -->
                <div class="admin-btn">
                    <span><?php echo htmlspecialchars($_SESSION['Nombre'] ?? 'Editor'); ?></span>
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
            <h1 class="text-[28px] font-semibold">Post Planeados</h1>
        </div>

         <div class="division-dos">
             <div class="contenedor-cajas">

                <div class="programa">
                    <p>¡Programa tus post desde antes!</p>
                    <a href='../PostPlaneados/planeados-form.html' class="mas">+</a>
                    <img src='/PRODCONS/PI2do/imagenes/plantita.png' class="decoracion hojas-izq" width="80">
                    <img src='/PRODCONS/PI2do/imagenes/planta.png' class="decoracion hojas-der" width="80">
                </div>
                
                <div class="contenedor-calendario">
                    <div class="titulo-calendario" style="display:flex;align-items:center;justify-content:center;gap:20px;font-size:px;font-weight:bold;">
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
                <h2 class="text-[30px] font-semibold">Post Planeados</h2>
                <div class="text">
                    <?php if (empty($planeados)): ?>
                        <p>No tienes ningún post programado</p>
                    <?php else: ?>
                        <?php foreach ($planeados as $post): ?>
                            <div class="post-planeado">
                                <div class="post-day"><?= date('d', strtotime($post['Fecha de Publicacion'] ?? date('Y-m-d'))) ?></div>
                                <div class="post-content-wrapper">
                                    <?php if (isset($post['Imagen']) && $post['Imagen']): ?>
                                        <img src="<?= htmlspecialchars($post['Imagen']) ?>" alt="Imagen del post" class="post-image">
                                    <?php endif; ?>
                                    <div class="post-text">
                                        <strong class="post-title"><?= htmlspecialchars($post['Titulo'] ?? '') ?></strong><br>
                                        <span class="post-date">Será publicado el <?= date('d', strtotime($post['Fecha de Publicacion'] ?? date('Y-m-d'))) ?> de <?= date('F', strtotime($post['Fecha de Publicacion'] ?? date('Y-m-d'))) ?> de <?= date('Y', strtotime($post['Fecha de Publicacion'] ?? date('Y-m-d'))) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
         
    </div>
</body>
>>>>>>> main
</html>