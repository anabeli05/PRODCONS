<?php
session_start();
include '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Función para validar el formulario
function validarFormulario($currentPassword, $newPassword, $confirmPassword) {
    $errores = [];
    
    // Validar que las nuevas contraseñas coincidan
    if ($newPassword !== $confirmPassword) {
        $errores[] = 'Las nuevas contraseñas no coinciden';
    }
    
    // Validar que la nueva contraseña sea diferente a la actual
    if ($newPassword === $currentPassword) {
        $errores[] = 'La nueva contraseña debe ser diferente a la actual';
    }
    
    // Validar fortaleza de la contraseña
    if (strlen($newPassword) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres';
    }
    
    return $errores;
}

// Manejar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['nueva_password'] ?? '';
    $confirmPassword = $_POST['confirmar_password'] ?? '';
    
    // Validar el formulario
    $errores = validarFormulario($currentPassword, $newPassword, $confirmPassword);
    
    // Validar la contraseña actual contra la base de datos
    if (empty($errores)) {
        $stmt = $conn->prepare("SELECT Contraseña FROM usuarios WHERE Usuario_ID = ?");
        $stmt->bind_param("i", $_SESSION['usuario_id']);
        $stmt->execute();
        $stmt->bind_result($hash_actual);
        if ($stmt->fetch()) {
            if (!password_verify($currentPassword, $hash_actual)) {
                $errores[] = 'La contraseña actual es incorrecta';
            }
        } else {
            $errores[] = 'Usuario no encontrado';
        }
        $stmt->close();
    }
    
    // Si no hay errores, proceder con el cambio de contraseña
    if (empty($errores)) {
        $hash_nueva = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET Contraseña = ? WHERE Usuario_ID = ?");
        $stmt->bind_param("si", $hash_nueva, $_SESSION['usuario_id']);
        if ($stmt->execute()) {
            session_destroy();
            header('Location: ../../inicio_sesion/login.php?success=true');
            exit();
        } else {
            $errores[] = 'Error al actualizar la contraseña';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Cambiar Contraseña</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="cambiar.css">
    <style>
        .error-message {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .success-message {
            color: green;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form">
        <h1>CAMBIAR CONTRASEÑA</h1>
        
        <?php if (isset($errores) && !empty($errores)): ?>
            <?php foreach ($errores as $error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <form id="formCambioContrasena" action="" method="POST">
            <div class="instrucciones">
                <p>Ingresa tu contraseña actual y tu nueva contraseña.</p>
            </div>

            <div class="buton">
                <div class="input-area">
                    <input type="password" id="currentPassword" name="currentPassword" placeholder="Contraseña Actual" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <div class="buton">
                <div class="input-area">
                    <input type="password" id="newPassword" name="nueva_password" placeholder="Nueva Contraseña" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <div class="buton">
                <div class="input-area">
                    <input type="password" id="confirmPassword" name="confirmar_password" placeholder="Confirmar Nueva Contraseña" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <input type="submit" value="CAMBIAR CONTRASEÑA">
            
            <div class="alternar-form">
                <p><a href="../../inicio_sesion/login.php">Volver al inicio de sesión</a></p>
            </div>
        </form>
    </div>
</body>
</html>