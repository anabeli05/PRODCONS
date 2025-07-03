<?php
session_start();
include '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../inicio_sesion/login.php');
    exit();
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Error de seguridad: Token CSRF inválido');
}

// Función para validar el formulario
function validarFormulario($titulo, $contenido, $imagenes) {
    $errores = [];
    
    if (strlen($titulo) < 5 || strlen($titulo) > 100) {
        $errores[] = 'El título debe tener entre 5 y 100 caracteres';
    }
    
    if (strlen($contenido) < 50) {
        $errores[] = 'El contenido debe tener al menos 50 caracteres';
    }
    
    // Validar imágenes
    if (!empty($imagenes['name'][0])) {
        foreach ($imagenes['name'] as $key => $value) {
            $tipo = $imagenes['type'][$key];
            $tamano = $imagenes['size'][$key];
            
            if (!in_array($tipo, ['image/jpeg', 'image/png', 'image/gif'])) {
                $errores[] = 'Solo se permiten imágenes JPG, PNG y GIF';
            }
            
            if ($tamano > 5242880) { // 5MB
                $errores[] = 'Las imágenes no deben superar 5MB';
            }
        }
    }
    
    return $errores;
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $contenido = $_POST['contenido'] ?? '';
    $etiquetas = $_POST['etiquetas'] ?? '';
    $comentario_autor = $_POST['comentario_autor'] ?? '';
    $imagenes = $_FILES['imagenes'] ?? [];
    
    // Validar el formulario
    $errores = validarFormulario($titulo, $contenido, $imagenes);
    
    if (empty($errores)) {
        try {
            // Iniciar transacción
            $conn->beginTransaction();
            
            // Insertar el post en la base de datos
            $stmt = $conn->prepare("INSERT INTO posts (titulo, contenido, etiquetas, comentario_autor, usuario_id, fecha_creacion) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$titulo, $contenido, $etiquetas, $comentario_autor, $_SESSION['usuario_id']]);
            
            $post_id = $conn->lastInsertId();
            
            // Procesar las imágenes
            if (!empty($imagenes['name'][0])) {
                $upload_dir = '../../imagenes/posts/';
                
                // Crear directorio si no existe
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                foreach ($imagenes['name'] as $key => $value) {
                    $file_name = uniqid() . '_' . basename($imagenes['name'][$key]);
                    $file_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($imagenes['tmp_name'][$key], $file_path)) {
                        // Guardar la ruta de la imagen en la base de datos
                        $stmt = $conn->prepare("INSERT INTO imagenes_posts (post_id, ruta_imagen) VALUES (?, ?)");
                        $stmt->execute([$post_id, $file_path]);
                    }
                }
            }
            
            // Confirmar transacción
            $conn->commit();
            
            // Redirigir a la página de éxito
            $_SESSION['mensaje'] = 'Post creado exitosamente';
            header('Location: mis-articulos.php');
            exit();
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conn->rollBack();
            $errores[] = 'Error al crear el post: ' . $e->getMessage();
        }
    }
    
    // Si hay errores, guardarlos en la sesión y redirigir de vuelta al formulario
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_formulario'] = $_POST;
        header('Location: formulario-new-post.php');
        exit();
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!empty($errores)) {
    foreach ($errores as $error) {
        echo "<div class='error'>$error</div>";
    }
}
if (isset($_SESSION['exito'])) {
    echo "<div class='exito'>{$_SESSION['exito']}</div>";
    unset($_SESSION['exito']);
}
?> 

<style>
.error { color: red; }
.exito { color: green; }
</style> 

<button onclick="confirmarEliminar()">Eliminar</button>
<script>
function confirmarEliminar() {
    if (confirm('¿Estás seguro de que deseas eliminar este post?')) {
        window.location.href = 'eliminar-post.php?id=1';
    }
}
</script> 