<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Verificar si el usuario es un editor
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'Editor') {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Incluir conexión a la base de datos
require_once '../../Base de datos/conexion.php';

// Crear una instancia de la clase Conexion
$conexion = new Conexion();

// Obtener el ID del editor logueado
$editor_id = $_SESSION['Usuario_ID'];

// Obtener el mes seleccionado (por defecto febrero)
$mes_seleccionado = isset($_GET['mes']) ? $_GET['mes'] : 'febrero';

// Mapeo de nombres de meses en español a números
$meses = [
    'enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04',
    'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08',
    'septiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'
];

// Obtener el año actual
$anio_actual = date('Y');

try {
    // Abrir conexión
    $conexion->abrir_conexion();
    
    // Consulta para obtener los artículos más vistos del mes seleccionado
    $sql = "SELECT a.ID_Articulo, a.Titulo, a.Contenido, a.`Fecha de Creacion`, 
               GROUP_CONCAT(ia.Url_Imagen) as imagenes
        FROM articulos a
        LEFT JOIN imagenes_articulos ia ON a.ID_Articulo = ia.Articulo_ID
        WHERE MONTH(a.`Fecha de Creacion`) = ? 
          AND YEAR(a.`Fecha de Creacion`) = ?
          AND a.Usuario_ID = ?
        GROUP BY a.ID_Articulo
        ORDER BY a.ID_Articulo DESC 
        LIMIT 6";
            
    $stmt = $conexion->conexion->prepare($sql);
    $stmt->bind_param("ssi", $meses[$mes_seleccionado], $anio_actual, $editor_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    // Array para almacenar los artículos
    $articulos = [];
    while ($row = $resultado->fetch_assoc()) {
        $articulos[] = $row;
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = "Error al cargar los artículos: " . $e->getMessage();
} finally {
    $conexion->cerrar_conexion();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- CSS Y JS DE HEADER-->
    <link rel="stylesheet" href='../Dashboard/sidebar.css'>
    <script src='../Dashboard/barra-nav.js' defer></script>
    
    <!-- CSS de traduccion -->
    <link rel="stylesheet" href="../../Dashboard_Editores/Dashboard/traduccion.css">
    <!-- Scripts de traducción -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>

</head>
<body>
    <header>
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
            <img class="prodcons" src='../../imagenes/prodcon/logoSinfondo.png' alt="Logo">
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
                    <a href='../inicio/inicio.php' data-no-translate>
                            <span>Inicio</span>
                        <i class="fas fa-file-alt"></i>
                    </a>
                    
                    <a href='../MisArticulos/mis-articulos.php' data-no-translate>
                        <span>Mis Artículos</span>
                        <i class="fas fa-file-alt"></i>
                    </a>
                    
                    <a href="../Crear nuevo post/formulario-new-post.php" data-no-translate>
                        <span>Crear Post</span>
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href='../PostPlaneados/post-planeados.php' data-no-translate>
                        <span>Post Planeados</span>
                        <i class="fas fa-calendar"></i>
                    </a>
                                        
                    <a href='../Estadisticas/estadisticas-adm.php' data-no-translate>
                        <span>Estadísticas</span>
                        <i class="fas fa-chart-bar"></i>
                    </a>
                    
                    <a href='../Configuracion/configuracion.php' data-no-translate>
                        <span>Configuración</span>
                        <i class="fas fa-cog"></i>
                    </a>
                    
                    <a href='../../inicio_sesion/logout.php' class="logout-btn" data-no-translate>
                        <span>Cerrar Sesión</span>
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </nav>
            </div>
        </div>
    </section>

    <!---------Saludo Admin--------->
    <section class="w-full max-w-[1200px] mx-auto my-[10px] px-5 flex flex-col items-center">
        <div class="relative w-[90%] max-w-[900px] min-h-[250px] p-[30px] flex flex-wrap justify-between items-start bg-[#9fd984] rounded-[40px] overflow-hidden">
            <div class="flex-1 min-w-[300px] p-5 m-0 z-10">
              <h2 class="text-[clamp(28px,3vw,40px)] mb-[15px] font-bold">¡Hola <?php echo htmlspecialchars($_SESSION['Nombre'] ?? 'Admin'); ?>!</h2>
                <p class="text-[clamp(13px,1.5vw,15px)] mb-5">Un blog exitoso se construye post a post. ¡Sigue adelante!</p>
                <a href='../Crear nuevo post/formulario-new-post.php' 
                    class="min-w-[250px] h-20 border-none rounded-[30px] bg-[#1b641b] text-white font-semibold text-[clamp(14px,1.5vw,16px)] cursor-pointer transition duration-300 mt-[25px] hover:bg-[#145014] inline-flex items-center justify-center">
                    ESCRIBE UN NUEVO POST
                </a>
            </div>
            <div class="w-full flex justify-center items-center mt-8 md:mt-0 md:absolute md:right-[30px] md:bottom-[10px] md:top-[10px] md:w-[30%] md:h-auto z-0">
                <img src='/PRODCONS/PI2do/imagenes/chicaLaptop.png' class="max-h-[200px] h-auto w-auto object-contain">
            </div>
        </div>
        
        <!-- contenedor articulos -->
        <div class="w-[90%] max-w-[900px] bg-[#f8f9fa] rounded-[15px] p-[30px] mt-[30px] shadow-[0_2px_10px_rgba(0,0,0,0.08)]">
            <div class="flex justify-between items-center mb-[25px] flex-wrap gap-[15px]">
                <h2 class="text-[2rem] text-[#333] mb-[25px] text-left font-bold uppercase tracking-[1px]">ARTICULOS MAS VISTOS</h2>
                <!-------selector de meses------->
                <div class="relative">
                    <form method="GET" action="">
                        <select name="mes" id="selectorMes" onchange="this.form.submit()" class="appearance-none px-[15px] pr-[35px] py-[10px] border-none rounded-[30px] bg-white font-sans text-[15px] text-[#333] cursor-pointer outline-none transition-all duration-300 hover:shadow-[0_0_0_3px_rgba(76,175,80,0.2)]">
                            <?php
                            $meses_nombres = [
                                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
                            ];
                            foreach ($meses_nombres as $mes) {
                                $selected = ($mes === $mes_seleccionado) ? 'selected' : '';
                                echo "<option value=\"$mes\" $selected>" . ucfirst($mes) . "</option>";
                            }
                            ?>
                        </select>
                    </form>
                    <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none transition-transform duration-300 peer-focus:rotate-180 text-[#242524]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 11l8 3l8 -3" />
                    </svg>
                </div>
            </div>

            <!-------ARTICULOS------>
            <div class="vista-articulos">
                <?php
                if (!empty($articulos)) {
                    $contador = 1;
                    foreach ($articulos as $articulo) {
                        $imagenes = explode(',', $articulo['imagenes']);
                        $imagen_principal = !empty($imagenes[0]) ? $imagenes[0] : '/PI2do/Vista-Admin/img-vista-admin/default-post.jpg';
                        ?>
                        <div class="flex items-center mb-[25px] gap-[15px]" data-mes="<?php echo $mes_seleccionado; ?>">
                            <div class="text-[3rem] font-bold text-[#242524] min-w-[40px] pt-[5px]"><?php echo str_pad($contador, 2, '0', STR_PAD_LEFT); ?></div>
                            <div class="flex gap-[20px] w-full items-center">
                                <div class="w-[180px] h-[120px] min-w-[180px] rounded-[8px] overflow-hidden shadow-[0_3px_6px_rgba(0,0,0,0.1)]">
                                    <img src="<?php echo htmlspecialchars($imagen_principal); ?>" alt="<?php echo htmlspecialchars($articulo['Titulo']); ?>" class="w-full h-full object-cover transition-transform duration-400 ease-in-out hover:scale-[1.03]">
                                </div>
                                <div class="flex-1 pr-[10px]">
                                    <h3 class="font-serif text-[2rem] text-[#333] mb-2"><?php echo htmlspecialchars($articulo['Titulo']); ?></h3>
                                    <p class="text-[0.95rem] text-[#555] mb-2 leading-[1.5]"><?php echo htmlspecialchars(substr($articulo['Contenido'], 0, 100)) . '...'; ?></p>
                                    <span  class="text-[1rem] italic text-black">Publicado el <?php echo date('d \d\e F \d\e Y', strtotime($articulo['Fecha de Creacion'])); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php
                        $contador++;
                    }
                } else {
                    echo '<div class="text-2xl text-center mt-5 text-gray-600" >No hay artículos disponibles para este mes.</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="error-message">
        <?php 
        echo htmlspecialchars($_SESSION['error']);
        unset($_SESSION['error']);
        ?>
    </div>
    <?php endif; ?>

    <script>
    // Script para manejar el selector de meses
    document.getElementById('selectorMes').addEventListener('change', function() {
        this.form.submit();
    });

    //Script para el idioma -->
    function updateLanguageButtons() {
      const btnEs = document.getElementById('btn-es');
      const btnEn = document.getElementById('btn-en');
      const toggleText = document.getElementById('toggle-text');
      
      const currentLang = localStorage.getItem('preferredLanguage') || 'es';
      
      if (currentLang === 'en') {
        btnEs.classList.remove('active');
        btnEn.classList.add('active');
        toggleText.innerText = 'Change language?';
      } else {
        btnEn.classList.remove('active');
        btnEs.classList.add('active');
        toggleText.innerText = '¿Cambiar idioma?';
      }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
      updateLanguageButtons();
      
      const observer = new MutationObserver(function(mutations) {
        updateLanguageButtons();
      });
      
      observer.observe(document.documentElement, { attributes: true, attributeFilter: ['lang'] });
      
      document.getElementById('close-language-toggle').addEventListener('click', function() {
        document.getElementById('language-toggle').style.display = 'none';
      });
    });
    
    const originalCambiarIdioma = window.cambiarIdioma;
    window.cambiarIdioma = function(idioma) {
      if (typeof originalCambiarIdioma === 'function') {
        originalCambiarIdioma(idioma);
      } else {
        translateContent(idioma === 'ingles' ? 'en' : 'es');
      }
      setTimeout(updateLanguageButtons, 100);
    };
    </script>

</body>
</html> 