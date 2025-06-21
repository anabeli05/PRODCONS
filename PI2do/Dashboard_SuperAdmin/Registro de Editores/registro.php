<?php
session_start();

// Verificar si el usuario está logueado y es Super Admin
if (!isset($_SESSION['Usuario_ID']) || $_SESSION['Rol'] !== 'Super Admin') {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Base de datos/conexion.php';

// Crear una instancia de la clase Conexion
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

// Inicializar variables
$error = null;
$success = null;

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Error de seguridad: Token inválido";
    } else {
        try {
            // Validar datos
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Validaciones
            if (empty($nombre) || empty($email) || empty($password)) {
                throw new Exception("Todos los campos son obligatorios");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("El email no es válido");
            }

            if (strlen($password) < 8) {
                throw new Exception("La contraseña debe tener al menos 8 caracteres");
            }

            if ($password !== $confirm_password) {
                throw new Exception("Las contraseñas no coinciden");
            }

            // Abrir conexión
            $conexion->abrir_conexion();

            // Verificar si el email ya existe
            $sql = "SELECT id FROM usuarios WHERE email = ?";
            $stmt = $conexion->conexion->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("El email ya está registrado");
            }

            // Hash de la contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insertar nuevo editor
            $sql = "INSERT INTO usuarios (Nombre, Correo, Contraseña, Rol, Estado) 
                    VALUES (?, ?, ?, 'Editor', 'activo')";
            $stmt = $conexion->conexion->prepare($sql);
            $stmt->bind_param("sss", $nombre, $email, $password_hash);
            
            if ($stmt->execute()) {
                $success = "Editor registrado exitosamente";
                // Limpiar el formulario
                $_POST = array();
            } else {
                throw new Exception("Error al registrar el editor");
            }

        } catch (Exception $e) {
            $error = $e->getMessage();
        } finally {
            $conexion->cerrar_conexion();
        }
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
            
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required
                       value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
            </div>
            <!--
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required
                       value="<//?php echo htmlspecialchars($_POST['apellido'] ?? ''); ?>">
            </div>
        -->
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required
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
        const password = document.getElementById('password').value;
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