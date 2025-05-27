<?php
session_start();
include '../../Base de datos/conexion.php';

// Unificar variable de sesión
if (!isset($_SESSION['usuario_id'])) {
    if (isset($_SESSION['Usuario_ID'])) {
        $_SESSION['usuario_id'] = $_SESSION['Usuario_ID'];
        unset($_SESSION['Usuario_ID']);
    } else {
        header('Location: ../../inicio_sesion/login.php');
        exit();
    }
}

// Función para cambiar idioma
function cambiarIdioma($idioma) {
    $_SESSION['idioma'] = $idioma;
    return true;
}

// Función para obtener la URL de la bandera
function obtenerUrlBandera($idioma) {
    $rutas = [
        'es' => '../imagenes/logos/espanol.png',
        'en' => '../imagenes/logos/ingles.png'
    ];
    return $rutas[$idioma] ?? '../imagenes/logos/espanol.png';
}

// Función para cambiar bandera
function cambiarBandera($idioma) {
    return obtenerUrlBandera($idioma);
}

// Función para cambiar contraseña
function cambiarContrasena($nuevaPassword, $confirmarPassword, $conn) {
    if ($nuevaPassword !== $confirmarPassword) {
        return ['success' => false, 'message' => 'Las contraseñas no coinciden'];
    }
    $stmt = $conn->prepare("UPDATE usuarios SET Contraseña = ? WHERE Usuario_ID = ?");
    $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
    $stmt->bind_param("si", $hash, $_SESSION['usuario_id']);
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
    } else {
        return ['success' => false, 'message' => 'Error al actualizar la contraseña'];
    }
}

// Función para cambiar foto de perfil
function cambiarFotoPerfil($archivo, $conn) {
    $directorio = "../imagenes/perfiles/";
    $nombreArchivo = $_SESSION['usuario_id'] . "_" . time() . "_" . basename($archivo['name']);
    $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $permitidas)) {
        return ['success' => false, 'message' => 'Solo se permiten imágenes JPG, PNG o GIF.'];
    }
    if ($archivo['size'] > 2 * 1024 * 1024) { // 2MB
        return ['success' => false, 'message' => 'La imagen no debe superar los 2MB.'];
    }
    if (move_uploaded_file($archivo['tmp_name'], $directorio . $nombreArchivo)) {
        // Actualizar en la base de datos
        $stmt = $conn->prepare("UPDATE usuarios SET Foto_Perfil = ? WHERE Usuario_ID = ?");
        $stmt->bind_param("si", $nombreArchivo, $_SESSION['usuario_id']);
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Foto de perfil actualizada', 'url' => $directorio . $nombreArchivo];
        }
    }
    return ['success' => false, 'message' => 'Error al subir la foto'];
}

// Función para cambiar nombre de usuario
function cambiarNombreUsuario($nuevoNombre, $conn) {
    if (empty($nuevoNombre)) {
        return ['success' => false, 'message' => 'El nombre no puede estar vacío'];
    }
    $stmt = $conn->prepare("UPDATE usuarios SET Nombre = ? WHERE Usuario_ID = ?");
    $stmt->bind_param("si", $nuevoNombre, $_SESSION['usuario_id']);
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Nombre actualizado correctamente'];
    } else {
        return ['success' => false, 'message' => 'Error al actualizar el nombre'];
    }
}

// Manejar selección de idioma
if (isset($_POST['idioma'])) {
    cambiarIdioma($_POST['idioma']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Manejar acciones según el tipo de petición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    switch ($_POST['action']) {
        case 'cambiar_contraseña':
            echo json_encode(cambiarContrasena($_POST['nueva_password'], $_POST['confirmar_password'], $conn));
            break;
        case 'cambiar_foto':
            if (isset($_FILES['foto'])) {
                echo json_encode(cambiarFotoPerfil($_FILES['foto'], $conn));
            }
            break;
        case 'cambiar_nombre':
            echo json_encode(cambiarNombreUsuario($_POST['nombre'], $conn));
            break;
    }
    exit();
}

// Función para obtener información del usuario
function obtenerInfoUsuario($conn) {
    $stmt = $conn->prepare("SELECT Nombre, Foto_Perfil FROM usuarios WHERE Usuario_ID = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Obtener información del usuario para mostrar en la página
$infoUsuario = obtenerInfoUsuario($conn);
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Configuracion</title>
    <link href='../Configuracion/config.css' rel="stylesheet">
    <link href='../Dashboard/sidebar.css' rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!--Javascrip header y barra-->
    <script src='../Dashboard/barra-nav.js' defer></script>

    <!-- Js cambiar banderas-->
    <!-- <script src="../Configuracion/config-bandera.js" defer></script> -->

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
                <a href='../Notibox/noti-box.html' class="notif-btn">
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

    <div class="contenedor-configuracion">
        <div class="config-titulo">
            <h1>Configuración</h1>
        </div>

        <div class="main-content">
            <div class="secciones">
                <div class="section-title">Perfil</div>
                <div class="profile-item">
                  <label for="avatarInput" class="avatar-selector">
                    <img src='../imagenes/logos/perfil.png' alt="Admin" class="admin-avatar" />
                    <p>Foto de perfil</p>
                  </label>

                  <div class="nombre-container">
                    <label for="username" class="profile-label">Nombre:</label>
                    <input type="text" id="username" name="username" value="ADMIN" class="profile-name-input" />
                  </div>
                  <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" />
                  
                </div>
                
                <div class="section-title">Idioma</div>
                <div class="language-item">
                    <form method="POST" action="" id="form-idioma">
                        <div class="seletor-idiomas">
                            <img id="icono-bandera" src="<?php echo obtenerUrlBandera(isset($_POST['idioma']) ? $_POST['idioma'] : 'es'); ?>" alt="Bandera" class="icono-bandera" />
                            <select id="idiomas" name="idioma" onchange="document.getElementById('form-idioma').submit()">
                                <option value="es" <?php echo $_SESSION['idioma'] === 'es' ? 'selected' : ''; ?>>Español</option>
                                <option value="en" <?php echo $_SESSION['idioma'] === 'en' ? 'selected' : ''; ?>>Ingles</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mensaje-cuadro">
                <div class="personaliza-text">Personaliza tu perfil!</div>
                <img src='../imagenes/chicosSaltando.png' href="XD">
            </div>
            
        </div>
        
        <div class="seccion-sesion">
            <div class="seguridad-contenido">
              <div class="section-title">Seguridad</div>

             <div class="security-item">
                 <svg xmlns="http://www.w3.org/2000/svg" class="lock-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                 </svg>
                 <a href='../Cambiar contraseña/cambiar.php' class="change-password">Cambiar Contraseña</a>
              </div>
            </div>

            <button class="logout-button">Cerrar Sesión</button>
        </div>
    </div>

    <script>
    function mostrarFeedback(mensaje, exito = true) {
        let feedback = document.getElementById('feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.id = 'feedback';
            document.body.prepend(feedback);
        }
        feedback.textContent = mensaje;
        feedback.style.background = exito ? '#d4edda' : '#f8d7da';
        feedback.style.color = exito ? '#155724' : '#721c24';
        feedback.style.padding = '10px';
        feedback.style.margin = '10px';
        feedback.style.borderRadius = '4px';
        setTimeout(() => feedback.remove(), 4000);
    }

    // Cambiar nombre de usuario
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        usernameInput.addEventListener('change', function() {
            const formData = new FormData();
            formData.append('action', 'cambiar_nombre');
            formData.append('nombre', this.value);
            fetch('config.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => mostrarFeedback(data.message, data.success));
        });
    }

    // Cambiar foto de perfil
    const avatarInput = document.getElementById('avatarInput');
    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const formData = new FormData();
            formData.append('action', 'cambiar_foto');
            formData.append('foto', file);
            fetch('config.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    mostrarFeedback(data.message, data.success);
                    if (data.success && data.url) {
                        document.querySelector('.admin-avatar').src = data.url;
                    }
                });
        });
    }

    // Cambiar contraseña (ejemplo, deberías tener un formulario modal o similar)
    const changePasswordBtn = document.querySelector('.change-password');
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const nueva = prompt('Nueva contraseña:');
            if (!nueva) return;
            const confirmar = prompt('Confirmar contraseña:');
            if (!confirmar) return;
            const formData = new FormData();
            formData.append('action', 'cambiar_contraseña');
            formData.append('nueva_password', nueva);
            formData.append('confirmar_password', confirmar);
            fetch('config.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => mostrarFeedback(data.message, data.success));
        });
    }
    </script>

</body>
</html>