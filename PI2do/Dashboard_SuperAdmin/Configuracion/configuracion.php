<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Incluir la conexión a la base de datos
include '../../Base de datos/conexion.php';
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = '';
    $error = '';

    try {
        // Actualizar información del perfil
        if (isset($_POST['actualizar_perfil'])) {
            $Nombre = filter_input(INPUT_POST, 'Nombre', FILTER_SANITIZE_STRING);
            $Contraseña = filter_input(INPUT_POST, 'Contraseña', FILTER_SANITIZE_STRING);
            $Foto_Perfil = filter_input(INPUT_POST, 'Foto de Perfil', FILTER_SANITIZE_STRING);
            
            $stmt = $conn->prepare("UPDATE usuarios SET Nombre = ?, Contraseña = ?, `Foto de Perfil` = ? WHERE Usuario_ID = ?");
            $stmt->bind_param("sssi", $Nombre, $Contraseña, $Foto_Perfil, $_SESSION['Usuario_ID']);
            $stmt->execute();
            
            $mensaje = 'Perfil actualizado correctamente';
        }
    } catch (Exception $e) {
        $error = 'Error al actualizar el perfil: ' . $e->getMessage();
    }
}

// Obtener información actual del usuario
try {
    $stmt = $conn->prepare("SELECT Nombre, Contraseña, `Foto de Perfil` FROM usuarios WHERE Usuario_ID = ?");
    $stmt->bind_param("i", $_SESSION['Usuario_ID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
} catch (Exception $e) {
    $error = 'Error al obtener información del usuario: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - PRONCONS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href='configuracion.css'>
    
    
    <link rel="stylesheet" href='../Dashboard/sidebar.css'>
    <script src="../Dashboard/barra-nav-copy.js" defer></script>
</head>
<body>
        <!--Header importado-->
    <?php include('../Dashboard/sidebar.php'); ?>


    <div class="config-container">
        <h1>Configuración de Perfil</h1>
    </div>

    <div class="main-content">
        <div class="secciones">
            <div class="section-title">Perfil</div>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-success"><?php echo $mensaje; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="Nombre">Nombre:</label>
                    <input type="text" id="Nombre" name="Nombre" value="<?php echo htmlspecialchars($usuario['Nombre'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="Contraseña">Contraseña:</label>
                    <div class="password-container">
                        <input type="password" id="Contraseña" name="Contraseña" value="<?php echo htmlspecialchars($usuario['Contraseña'] ?? ''); ?>" required>
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="Foto de Perfil">Foto de Perfil:</label>
                    <input type="file" id="Foto de Perfil" name="Foto de Perfil" accept="image/*">
                    <?php if (!empty($usuario['Foto de Perfil'])): ?>
                        <img src="<?php echo htmlspecialchars($usuario['Foto de Perfil']); ?>" alt="Foto de perfil actual" style="max-width: 200px; margin-top: 10px;">
                    <?php endif; ?>
                </div>
                
                <div class="button-container">
                    <button type="button" onclick="window.location.href='../cambiar contraseña/cambiar.php'" class="btn btn-warning" aria-label="Cambiar Contraseña">
                        Cambiar Contraseña
                    </button>    
                    <button type="submit" name="actualizar_perfil" class="btn btn-primary" aria-label="Actualizar Perfil">
                        Actualizar Perfil
                    </button>
                </div>
            </form>
        </div>  
        </form>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('Contraseña');
        const toggleIcon = document.querySelector('.toggle-password i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
</body>
</html> 