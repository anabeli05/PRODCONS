<<<<<<< HEAD
<?php
session_start();
include '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /login.php');
    exit();
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'ID de post no proporcionado o no válido';
    header('Location: mis-articulos.php');
    exit();
}

$post_id = intval($_GET['id']);

try {
    // Iniciar transacción
    $conn->beginTransaction();
    
    // Verificar que el post pertenece al usuario
    $stmt = $conn->prepare("SELECT usuario_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post || $post['usuario_id'] != $_SESSION['usuario_id']) {
        throw new Exception('No tienes permiso para eliminar este post');
    }
    
    // Obtener las rutas de las imágenes
    $stmt = $conn->prepare("SELECT ruta_imagen FROM imagenes_posts WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $imagenes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Eliminar las imágenes del servidor
    $directorio = dirname(__FILE__) . '/../../uploads/posts/' . $post_id . '/';
    foreach ($imagenes as $imagen) {
        $ruta_completa = $directorio . $imagen;
        if (file_exists($ruta_completa)) {
            unlink($ruta_completa);
        }
    }
    
    // Eliminar las referencias de las imágenes en la base de datos
    $stmt = $conn->prepare("DELETE FROM imagenes_posts WHERE post_id = ?");
    $stmt->execute([$post_id]);
    
    // Eliminar el post
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    
    // Confirmar transacción
    $conn->commit();
    
    $_SESSION['mensaje'] = 'Post eliminado exitosamente';
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollBack();
    $_SESSION['error'] = 'Error al eliminar el post: ' . $e->getMessage();
}

header('Location: mis-articulos.php');
exit(); 
=======
<?php
session_start();
include '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /login.php');
    exit();
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'ID de post no proporcionado o no válido';
    header('Location: mis-articulos.php');
    exit();
}

$post_id = intval($_GET['id']);

try {
    // Iniciar transacción
    $conn->beginTransaction();
    
    // Verificar que el post pertenece al usuario
    $stmt = $conn->prepare("SELECT usuario_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post || $post['usuario_id'] != $_SESSION['usuario_id']) {
        throw new Exception('No tienes permiso para eliminar este post');
    }
    
    // Obtener las rutas de las imágenes
    $stmt = $conn->prepare("SELECT ruta_imagen FROM imagenes_posts WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $imagenes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Eliminar las imágenes del servidor
    $directorio = dirname(__FILE__) . '/../../uploads/posts/' . $post_id . '/';
    foreach ($imagenes as $imagen) {
        $ruta_completa = $directorio . $imagen;
        if (file_exists($ruta_completa)) {
            unlink($ruta_completa);
        }
    }
    
    // Eliminar las referencias de las imágenes en la base de datos
    $stmt = $conn->prepare("DELETE FROM imagenes_posts WHERE post_id = ?");
    $stmt->execute([$post_id]);
    
    // Eliminar el post
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    
    // Confirmar transacción
    $conn->commit();
    
    $_SESSION['mensaje'] = 'Post eliminado exitosamente';
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollBack();
    $_SESSION['error'] = 'Error al eliminar el post: ' . $e->getMessage();
}

header('Location: mis-articulos.php');
exit(); 
>>>>>>> main
?> 