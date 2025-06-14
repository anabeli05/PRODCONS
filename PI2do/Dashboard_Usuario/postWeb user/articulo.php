<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol correcto
if (!isset($_SESSION['Usuario_ID']) || !isset($_SESSION['Rol']) || $_SESSION['Rol'] != 'Usuario') {
    // Redirigir al login con un mensaje de error
    $_SESSION['login_error'] = "Tu cuenta no está registrada, por favor regístrate";
    header("Location: /PRODCONS/PI2do/inicio_sesion/login.php");
    exit();
}

// Incluir el archivo de conexión
include '../../Base de datos/conexion.php';

// Crear instancia de Conexion y obtener la conexión
$conexion = new Conexion();
$conexion->abrir_conexion();
$conn = $conexion->conexion;

// Verificar si la conexión se estableció correctamente
if (!$conn) {
    die("Error de conexión a la base de datos.");
}

// Obtener el ID del artículo desde la URL
$articulo_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($articulo_id <= 0) {
    die("ID de artículo inválido.");
}

// Obtener el artículo desde la base de datos
$stmt = $conn->prepare("SELECT a.*, u.nombre as autor_nombre 
                       FROM articulos a 
                       JOIN usuarios u ON a.Usuario_ID = u.Usuario_ID 
                       WHERE a.ID_Articulo = ? AND a.Estado = 'publicado'");

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("i", $articulo_id);
$stmt->execute();
$result = $stmt->get_result();
$articulo = $result->fetch_assoc();
$stmt->close();

// Cerrar la conexión
$conexion->cerrar_conexion();

// Verificar si el artículo existe
if (!$articulo) {
    die("Artículo no encontrado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($articulo['Titulo']); ?></title>
    <link rel="stylesheet" href="/PRODCONS/PI2do/postWeb/code.css">
    
    <!-- Scripts de traducción -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="/PRODCONS/translate.js"></script>
    
    <!-- Estilos para el cuadro de idioma -->
    <style>
        .language-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            font-family: Arial, sans-serif;
        }
        
        .language-toggle p {
            margin: 0 0 8px 0;
            font-size: 14px;
            font-weight: bold;
        }
        
        .language-toggle .language-buttons {
            display: flex;
            gap: 10px;
        }
        
        .language-toggle button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            background-color: #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .language-toggle button:hover {
            background-color: #e0e0e0;
        }
        
        .language-toggle button.active {
            background-color: #4CAF50;
            color: white;
        }
        
        .close-button {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 8px;
            background-color: transparent;
            border: none;
            cursor: pointer;
            color: #bbb;
            padding: 6px 8px;
            line-height: 1;
            opacity: 0.7;
            z-index: 1001;
        }
        
        .close-button:hover {
            color: #999;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <!-- Barra para regresar -->
    <section class="barra_left">
        <i class="flecha_left">
            <a href="/PRODCONS/PI2do/Dashboard_Usuario/Inicio/usuario.php" title="Regresar a la página principal">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                </svg>
            </a>
        </i>
    </section>

    <!-- Cuadro de selección de idioma -->
    <div class="language-toggle" id="language-toggle">
        <button class="close-button" id="close-language-toggle" onclick="document.getElementById('language-toggle').style.display='none'">✕</button>
        <p id="toggle-text">¿Cambiar idioma?</p>
        <div class="language-buttons">
            <button id="btn-es" onclick="cambiarIdioma('espanol')" class="active">Español</button>
            <button id="btn-en" onclick="cambiarIdioma('ingles')">English</button>
        </div>
    </div>

    <!-- Header del artículo -->
    <section class="header-section">
        <h1><?php echo htmlspecialchars($articulo['Titulo']); ?></h1>
        <div class="contenedor-imagenes">
            <div class="texto">
                <p><?php echo nl2br(htmlspecialchars($articulo['Contenido'])); ?></p>
            </div>
            <div class="imagenes">
                <div class="imagen-placeholder" style="width: 300px; height: 200px; background-color: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #666;">
                    Imagen del artículo
                </div>
            </div>
        </div>
    </section>

    <main>
        <section>
            <div class="contenido-articulo">
                <?php echo nl2br(htmlspecialchars($articulo['Contenido'])); ?>
            </div>
        </section>
    </main>

    <!-- Footer del artículo -->
    <section class="footer-section">
        <div class="contenedor-imagenes">
            <div class="texto">
                <p><strong>Artículo publicado por:</strong> <?php echo htmlspecialchars($articulo['autor_nombre']); ?></p>
                <p><strong>Fecha de publicación:</strong> <?php echo htmlspecialchars($articulo['Fecha de Creacion']); ?></p>
            </div>
        </div>
    </section>

    <!-- Script para actualizar botones de idioma -->
    <script>
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

    <?php include '/xampp/htdocs/PRODCONS/PI2do/footer/Visitante/footer.php'; ?>

</body>
</html>
