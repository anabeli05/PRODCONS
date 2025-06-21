<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Verificar si el usuario está logueado y es Super Admin
if (!isset($_SESSION['Usuario_ID']) || $_SESSION['Rol'] !== 'Super Admin') {
    error_log("DEBUG: Acceso denegado - Usuario no es Super Admin");
    header("Location: ../../inicio_sesion/login.php");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = null;
$success = null;

// Verificar mensajes de sesión
if (isset($_SESSION['registro_error'])) {
    $error = $_SESSION['registro_error'];
    unset($_SESSION['registro_error']);
} elseif (isset($_SESSION['registro_success'])) {
    $success = $_SESSION['registro_success'];
    unset($_SESSION['registro_success']);
}

// Usar error_log directamente en lugar de writeLog
error_log("DEBUG PHP: Script registro.php started. Error: " . (isset($error) ? $error : "None"));

// Verificar que la clase Conexion existe
if (!class_exists('Conexion')) {
    include_once __DIR__ . '/../../Base de datos/conexion.php';
} else {
    error_log("DEBUG: Clase Conexion ya existe");
}   
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registro'])) {
    try {
        error_log("DEBUG: Iniciando proceso de registro");
        error_log("DEBUG: Datos recibidos: " . print_r($_POST, true));
        
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            error_log("DEBUG: Token CSRF inválido");
            throw new Exception("Error: Solicitud no válida");
        }

        $nombre = filter_var(trim($_POST['Nombre']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        error_log("DEBUG: Nombre procesado: " . $nombre);
        if (empty($nombre)) {
            throw new Exception("Error: El nombre es requerido");
        }

        $correo = filter_var(trim($_POST['Correo']), FILTER_SANITIZE_EMAIL);
        error_log("DEBUG: Correo procesado: " . $correo);
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Error: El correo electrónico no es válido");
        }

        $password = $_POST['Contraseña'];
        $confirmar_password = $_POST['confirm_password'];
        error_log("DEBUG: Contraseñas recibidas: " . $password . " - " . $confirmar_password);

        if (empty($password)) {
            throw new Exception("Error: La contraseña es requerida");
        }
        if (strlen($password) < 8) {
            throw new Exception("Error: La contraseña debe tener al menos 8 caracteres");
        }

        if ($password !== $confirmar_password) {
            throw new Exception("Error: Las contraseñas no coinciden");
        }

        error_log("DEBUG: Verificando duplicado de correo: " . $correo);
        error_log("DEBUG: Consulta SQL: SELECT * FROM usuarios WHERE Correo = '" . $correo . "'");
        
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        error_log("DEBUG: Resultado de verificación: " . $resultado->num_rows . " filas encontradas");
        error_log("DEBUG: Datos de la consulta: " . print_r($resultado->fetch_all(), true));

        if ($resultado->num_rows > 0) {
            error_log("DEBUG: Correo duplicado encontrado: " . $correo);
            throw new Exception("Error: El correo ya está registrado");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $rol = "Editor";
        $estado = "Activo";
        error_log("DEBUG: Preparando inserción");
        
        $stmt = $conn->prepare("INSERT INTO usuarios (Nombre, Correo, Contraseña, Rol, Estado) VALUES (?, ?, ?, ?, ?)");
        error_log("DEBUG: Consulta preparada: " . $stmt->error);
        $stmt->bind_param("sssss", $nombre, $correo, $hash, $rol, $estado);
        error_log("DEBUG: Parámetros vinculados");

        if ($stmt->execute()) { 
            error_log("DEBUG: Registro exitoso");
            // No modificar la sesión del Super Admin
            $_SESSION['registro_success'] = "¡Registro exitoso! El editor ha sido dado de alta correctamente.";
            // No redirigir, solo limpiar el formulario
            $_POST = array();
            // No regenerar ID de sesión ya que estamos como Super Admin
            // No modificar la sesión del Super Admin
            $success = $_SESSION['registro_success'];
            unset($_SESSION['registro_success']);
            error_log("DEBUG: Mostrando mensaje de éxito en la misma página");
        } else {
            error_log("DEBUG: Error en la ejecución: " . $stmt->error);
            throw new Exception("Error: No se pudo registrar el usuario: " . $stmt->error);
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        error_log("ERROR: Exception caught: " . $errorMessage);
        $_SESSION['registro_error'] = "Error al registrar el editor: " . $errorMessage;
        // No redirigir, solo mostrar el error
        $error = $_SESSION['registro_error'];
        unset($_SESSION['registro_error']);
        error_log("DEBUG: Mostrando mensaje de error en la misma página");
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Editor - PRODCONS</title>
    <link rel="stylesheet" href="registro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../Dashboard/sidebar.css">
</head>
<body>
    <?php include '../Dashboard/sidebar.php'; ?>

    <section>
    <div class="main-content">
        <div class="header">
            <h1>Registro de Nuevo Editor</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="registration-form">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="registro" value="1">
            
            <div class="form-group">
                <label for="Nombre">Nombre Completo:</label>
                <input type="text" id="Nombre" name="Nombre" required
                       value="<?php echo htmlspecialchars($_POST['Nombre'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="Correo">Email:</label>
                <input type="email" id="Correo" name="Correo" required
                       value="<?php echo htmlspecialchars($_POST['Correo'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="Contraseña">Contraseña:</label>
                <input type="password" id="Contraseña" name="Contraseña" required
                       minlength="8">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       minlength="8">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Registrar Editor
                </button>
                <a href="../Editores/editores.php" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</section>
    <script>
    // Validación de contraseñas en tiempo real
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('Contraseña').value;
        const confirmPassword = this.value;
        
        if (password !== confirmPassword) {
            this.setCustomValidity('Las contraseñas no coinciden');
        } else {
            this.setCustomValidity('');
        }
    });
    </script>
</body>
</html>