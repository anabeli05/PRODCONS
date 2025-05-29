<?php
session_start();
include '../../Base de datos/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../inicio_sesion/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $contenido = $_POST['contenido'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $usuario_id = $_SESSION['Usuario_ID'];
    $errores = [];
    $ruta_imagen = null;

    // Validaciones básicas
    if (strlen($titulo) < 5) {
        $errores[] = 'El título debe tener al menos 5 caracteres.';
    }
    if (strlen($contenido) < 20) {
        $errores[] = 'El contenido debe tener al menos 20 caracteres.';
    }
    if (empty($fecha) || empty($hora)) {
        $errores[] = 'Debes seleccionar fecha y hora.';
    } else {
        // Validar que la fecha/hora no sea en el pasado
        $fecha_hora_programada = strtotime($fecha . ' ' . $hora . ':00');
        if ($fecha_hora_programada < time()) {
            $errores[] = 'No puedes programar un post en el pasado.';
        }
    }

    // Manejo de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['imagen']['tmp_name'];
        $nombre = basename($_FILES['imagen']['name']);
        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $permitidas)) {
            $errores[] = 'Solo se permiten imágenes JPG, PNG o GIF.';
        } else {
            $ruta_destino = '../imagenes/planeados/' . uniqid('img_') . '.' . $ext;
            if (!is_dir('../imagenes/planeados/')) {
                mkdir('../imagenes/planeados/', 0777, true);
            }
            if (move_uploaded_file($tmp_name, $ruta_destino)) {
                $ruta_imagen = $ruta_destino;
            } else {
                $errores[] = 'Error al subir la imagen.';
            }
        }
    }

    // Si no hay errores, guardar en la base de datos
    if (empty($errores)) {
        $fecha_hora = $fecha . ' ' . $hora . ':00';
        $stmt = $conn->prepare("INSERT INTO posts_planeados (Usuario_ID, Titulo, Contenido, Imagen, Fecha_Programada) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $titulo, $contenido, $ruta_imagen, $fecha_hora]);
        $_SESSION['mensaje'] = '¡Post planeado correctamente!';
        header('Location: formulario-nuevo-planeado.php');
        exit();
    } else {
        $_SESSION['errores'] = $errores;
        header('Location: formulario-nuevo-planeado.php');
        exit();
    }
} else {
    header('Location: post-planeados.php');
    exit();
} 
?>