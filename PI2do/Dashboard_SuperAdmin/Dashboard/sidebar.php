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
<div class="sidebar">
    <div class="profile-details">
        <img src="<?php echo $admin_foto; ?>" alt="<?php echo $admin_nombre; ?>" class="profile-img">
        <div class="name-job">
            <div class="profile_name"><?php echo $admin_nombre; ?></div>
            <div class="job">Super Admin</div>
        </div>
    </div>
    <div class="logo-details">
        <img src="../../imagenes/prodcon/logoSinfondo.png" alt="Logo" class="logo-img">
        <span class="logo_name">PRODCONS</span>
    </div>
    <ul class="nav-links">
        <li>
            <a href="../inicio/inicioSA.php" class="active">
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
            <a href="../Registro de Editores/registro.php">
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
            <a href="../Notibox/noti-box.php">
                <i class="fas fa-bell"></i>
                <span class="link_name">Notificaciones</span>
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
