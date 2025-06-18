<?php
session_start();
include '../../Base de datos/conexion.php';

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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

// Función para validar token CSRF
function validarCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Función para cambiar idioma
function cambiarIdioma($idioma) {
    if (!in_array($idioma, ['es', 'en'])) {
        return false;
    }
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
if (isset($_POST['idioma']) && validarCSRF($_POST['csrf_token'] ?? '')) {
    cambiarIdioma($_POST['idioma']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Manejar acciones según el tipo de petición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    // Verificar token CSRF
    if (!validarCSRF($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Error de seguridad: Token inválido']);
        exit();
    }
    
    switch ($_POST['action']) {
        case 'cambiar_contraseña':
            // Validar longitud mínima de contraseña
            if (strlen($_POST['nueva_password']) < 8) {
                echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres']);
                exit();
            }
            echo json_encode(cambiarContrasena($_POST['nueva_password'], $_POST['confirmar_password'], $conn));
            break;
            
        case 'cambiar_foto':
            if (isset($_FILES['foto'])) {
                // Validar tipo MIME
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES['foto']['tmp_name']);
                finfo_close($finfo);
                
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($mime_type, $allowed_types)) {
                    echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
                    exit();
                }
                
                echo json_encode(cambiarFotoPerfil($_FILES['foto'], $conn));
            }
            break;
            
        case 'cambiar_nombre':
            // Sanitizar y validar nombre
            $nombre = filter_var(trim($_POST['nombre']), FILTER_SANITIZE_STRING);
            if (empty($nombre) || strlen($nombre) > 50) {
                echo json_encode(['success' => false, 'message' => 'Nombre inválido']);
                exit();
            }
            echo json_encode(cambiarNombreUsuario($nombre, $conn));
            break;
            
        case 'cancelar_suscripcion':
            if (confirmarCancelacion()) {
                $stmt = $conn->prepare("UPDATE usuarios SET activo = 0 WHERE Usuario_ID = ?");
                $stmt->bind_param("i", $_SESSION['usuario_id']);
                $success = $stmt->execute();
                session_unset();
                session_destroy();
                echo json_encode(['success' => $success, 'message' => $success ? 'Suscripción cancelada correctamente.' : 'Error al cancelar la suscripción']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Operación cancelada']);
            }
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
    <link href='config.css' rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include '../Dashboard/sidebar.php'; ?>

    <div class="contenedor-configuracion">
        <div class="config-titulo">
            <h1>Configuración</h1>
        </div>

        <div class="main-content">
            <div class="secciones">
                <div class="section-title">Perfil</div>
                <div class="profile-item">
                    <label for="avatarInput" class="avatar-selector">
                        <img src="../imagenes/logos/perfil.png" alt="Admin" class="admin-avatar" />
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
                <img src='../imagenes/chicosSaltando.png' alt="Personalización">
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
                    <a href="../Cambiar contraseña/cambiar.php" class="change-password">Cambiar Contraseña</a>
                </div>

                <button class="cancelar-suscripcion-btn">Cancelar Suscripción</button>
            </div>
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

    // Agregar token CSRF a todas las peticiones fetch
    function getCSRFToken() {
        return document.querySelector('input[name="csrf_token"]').value;
    }
    
    // Modificar las llamadas fetch existentes
    function realizarPeticion(url, formData) {
        formData.append('csrf_token', getCSRFToken());
        return fetch(url, { 
            method: 'POST', 
            body: formData 
        });
    }

    // Cambiar nombre de usuario
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        usernameInput.addEventListener('change', function() {
            const formData = new FormData();
            formData.append('action', 'cambiar_nombre');
            formData.append('nombre', this.value);
            realizarPeticion('config.php', formData)
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
            realizarPeticion('config.php', formData)
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
            realizarPeticion('config.php', formData)
                .then(res => res.json())
                .then(data => mostrarFeedback(data.message, data.success));
        });
    }

    // Cancelar suscripción
    const cancelarBtn = document.querySelector('.cancelar-suscripcion-btn');
    if (cancelarBtn) {
        cancelarBtn.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que deseas cancelar tu suscripción? Esta acción no se puede deshacer.')) {
                const formData = new FormData();
                formData.append('action', 'cancelar_suscripcion');
                realizarPeticion('config.php', formData)
                    .then(res => res.json())
                    .then(data => {
                        mostrarFeedback(data.message, data.success);
                        if (data.success) {
                            setTimeout(() => {
                                window.location.href = '../../inicio_sesion/login.php';
                            }, 2000);
                        }
                    });
            }
        });
    }

    // Funcionalidad de búsqueda
    document.addEventListener('DOMContentLoaded', function() {
        const searchToggleBtn = document.querySelector('.search-toggle-btn');
        const searchBar = document.querySelector('.search-bar');
        const searchCloseBtn = document.querySelector('.search-close-btn');
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.querySelector('.search-results');
        let searchTimeout;

        // Mostrar/ocultar barra de búsqueda
        searchToggleBtn.addEventListener('click', function() {
            searchBar.classList.toggle('hidden');
            if (!searchBar.classList.contains('hidden')) {
                searchInput.focus();
            }
        });

        // Cerrar barra de búsqueda
        searchCloseBtn.addEventListener('click', function() {
            searchBar.classList.add('hidden');
            searchInput.value = '';
            searchResults.classList.add('hidden');
        });

        // Realizar búsqueda
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;

            searchTimeout = setTimeout(() => {
                const formData = new FormData();
                formData.append('action', 'buscar');
                formData.append('query', query);
                formData.append('csrf_token', getCSRFToken());

                realizarPeticion('config.php', formData)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            mostrarResultados(data.results);
                        } else {
                            mostrarFeedback(data.message, false);
                        }
                    })
                    .catch(error => {
                        console.error('Error en la búsqueda:', error);
                        mostrarFeedback('Error al realizar la búsqueda', false);
                    });
            }, 300);
        });

        // Función para mostrar resultados
        function mostrarResultados(results) {
            searchResults.innerHTML = '';
            
            if (results.length === 0) {
                searchResults.innerHTML = '<div class="no-results">No se encontraron resultados</div>';
            } else {
                results.forEach(result => {
                    const resultItem = document.createElement('div');
                    resultItem.className = 'search-result-item';
                    resultItem.innerHTML = `
                        <a href="${result.url}">
                            <div class="result-title">${result.title}</div>
                            <div class="result-description">${result.description}</div>
                        </a>
                    `;
                    searchResults.appendChild(resultItem);
                });
            }
            
            searchResults.classList.remove('hidden');
        }

        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!searchBar.contains(e.target) && !searchToggleBtn.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    });
    </script>
</body>
</html>