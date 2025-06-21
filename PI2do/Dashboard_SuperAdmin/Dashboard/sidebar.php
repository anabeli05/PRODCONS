<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado y es Super Admin
if (!isset($_SESSION['Usuario_ID']) || $_SESSION['Rol'] !== 'Super Admin') {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Base de datos/conexion.php';

// Crear una instancia de la clase Conexion
$conexion = new Conexion();

$admin_nombre = '';
$admin_foto = '../../imagenes/logos/perfil.png'; // Imagen por defecto

try {
    $conexion->abrir_conexion();
    // Obtener datos del super admin, incluyendo la foto de perfil
    $sql = "SELECT Usuario_ID, Nombre, `Foto de Perfil` FROM usuarios WHERE Usuario_ID = ? AND rol = 'Super Admin'";
    $stmt = $conexion->conexion->prepare($sql);
    $stmt->bind_param("i", $_SESSION['Usuario_ID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
        $admin_nombre = htmlspecialchars($admin['Nombre']);
        // Usar la ruta de la base de datos si existe y no está vacía
        if (!empty($admin['Foto de Perfil'])) {
            $admin_foto = htmlspecialchars($admin['Foto de Perfil']);
        }
    } else {
         // Redirigir si no se encuentran datos del admin (medida de seguridad)
         header('Location: ../../inicio_sesion/logout.php');
         exit();
    }

} catch (Exception $e) {
    error_log("Error al cargar datos del admin para sidebar: " . $e->getMessage());
    // Puedes manejar el error de forma más amigable si es necesario
} finally {
    $conexion->cerrar_conexion();
}
?>

    <header> 
        <div class="header-contenedor">
            <div class="principal"></div>
        </div>
    </header>
    <!-- Scripts de traducción -->
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
  <script src="/PRODCONS/translate.js"></script>

<section class="logo"> 
        <div class="header_2">
            <img class="prodcons" src="../../imagenes/prodcon/logoSinfondo.png" alt="Logo">
            <div class="admin-controls"><!--
                <button class="search-toggle-btn">
                    <i class="fas fa-search"></i>
                </button>-->
                <div class="search-bar hidden">
                    <input type="text" placeholder="Buscar...">
                    <button class="search-close-btn">&times;</button>
                </div>
                <a href="../Notibox/noti-box.php" class="notif-btn" aria-label="Notificaciones">
                    <i class="fas fa-bell" aria-hidden="true" data-no-translate></i>
                    <span class="notif-badge">1</span>
                </a>
                <div class="admin-btn" id="sidebarToggle">
                    <span>Super Administrador</span>
                    <i class="fas fa-chevron-down"></i>
                    <img src="../../imagenes/logos/perfil.png" alt="Admin" class="admin-avatar">
                </div><!--
                <div class="idiomaToggle">
                    <img class="españa" id="banderaIdioma" src="../PI2do/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
                </div>-->
            </div>
        </div>
</section>
<div class="sidebar" id="sidebar" aria-hidden="true">
<div id="close-btn" style="text-align: right; padding: 10px; cursor: pointer;">
  <i class="fas fa-times"></i>
</div>
    
    <div class="profile-details">
        <img src="<?php echo $admin_foto; ?>" alt="<?php echo $admin_nombre; ?>" class="profile-img">
        <div class="name-job">
            <div class="profile_name"><?php echo $admin_nombre; ?></div>
            <div class="job">Super Administrador</div>
        </div>
    </div>
    <div class="logo-details">
        <img src="../../imagenes/prodcon/logoSinfondo.png" alt="Logo" class="logo-img">
    </div>
    <ul class="nav-links">
        <li>
            <a href="../inicio/inicioSA.php">
                <i class="fas fa-home"></i>
                <span class="link_name">Inicio</span>
            </a>
        </li>
        <li>
            <a href="../Editores/editores.php">
                <i class="fas fa-users"></i>
                <span class="link_name">Editores</span>
            </a>
        </li>
        <li>
            <a href="../Dashboard_SuperAdmin/Registro de Editores/registro.php">
                <i class="fas fa-user-plus"></i>
                <span class="link_name">Registrar Editor</span>
            </a>
        </li>
        <li>
            <a href="../Estadisticas/estadisticas-adm.php">
                <i class="fas fa-chart-bar"></i>
                <span class="link_name">Estadísticas</span>
            </a>
        </li>
        <li>
            <a href="../Configuracion/configuracion.php">
                <i class="fas fa-key"></i>
                <span class="link_name">Configuración</span>
            </a>
        </li>
        <li class="log_out">
            <a href="../../inicio_sesion/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span class="link_name">Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</div>
<div id="overlay" class="sidebar-overlay hidden"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const closeBtn = document.getElementById('close-btn');
  const overlay = document.getElementById('overlay');

  function openSidebar() {
    sidebar.classList.add('active');
    overlay.classList.remove('hidden');
    sidebar.setAttribute('aria-hidden', 'false');
  }

  function closeSidebar() {
    sidebar.classList.remove('active');
    overlay.classList.add('hidden');
    sidebar.setAttribute('aria-hidden', 'true');
  }

  toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    if (sidebar.classList.contains('active')) {
      closeSidebar();
    } else {
      openSidebar();
    }
  });

  closeBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    closeSidebar();
  });

  overlay.addEventListener('click', () => {
    closeSidebar();
  });

  // Cerrar la barra lateral al hacer clic fuera de ella
  document.addEventListener('click', (event) => {
    if (sidebar.classList.contains('active') &&
        !sidebar.contains(event.target) &&
        !toggleBtn.contains(event.target)) {
      closeSidebar();
    }
  });
});

</script>