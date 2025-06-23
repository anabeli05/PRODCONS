<<<<<<< HEAD
<?php
session_start();
include '../../Base de datos/conexion.php'; 

if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../inicio_sesion/login.php');
    exit();
}
// Obtener mensajes de sesión
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : [];
unset($_SESSION['errores']);
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : null;
unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programar Nuevo Post Planeado</title>
    <link href="post-planeados.css" rel="stylesheet">
    <link href="../Dashboard/sidebar.css" rel="stylesheet">
    <script src="../Dashboard/barra-nav.js" defer></script>
    <style>
        .form-planeado {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .form-planeado h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-planeado label {
            display: block;
            margin-top: 1rem;
            font-weight: bold;
        }
        .form-planeado input, .form-planeado textarea {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .form-planeado button {
            margin-top: 2rem;
            width: 100%;
            padding: 0.75rem;
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            cursor: pointer;
        }
        .form-planeado button:hover {
            background: #388e3c;
        }
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
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
        </div>
    </section>
    <main>
        <?php if ($mensaje): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <?php if (!empty($errores)): ?>
            <div class="mensaje-error">
                <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form class="form-planeado" action="procesar-planeado.php" method="POST" enctype="multipart/form-data">
            <h2>Programar Nuevo Post</h2>
            <label for="titulo">Título del post</label>
            <input type="text" id="titulo" name="titulo" required maxlength="100">

            <label for="contenido">Contenido</label>
            <textarea id="contenido" name="contenido" rows="7" required></textarea>

            <label for="imagen">Imagen (opcional)</label>
            <input type="file" id="imagen" name="imagen" accept="image/*">

            <label for="fecha">Fecha de publicación</label>
            <input type="date" id="fecha" name="fecha" required>

            <label for="hora">Hora de publicación</label>
            <input type="time" id="hora" name="hora" required>

            <button type="submit">Programar Post</button>
        </form>
    </main>
</body>
</html>
=======
<?php
session_start();
include '../../Base de datos/conexion.php'; 

if (!isset($_SESSION['Usuario_ID'])) {
    header('Location: ../inicio_sesion/login.php');
    exit();
}
// Obtener mensajes de sesión
$errores = isset($_SESSION['errores']) ? $_SESSION['errores'] : [];
unset($_SESSION['errores']);
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : null;
unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programar Nuevo Post Planeado</title>
    <link href="post-planeados.css" rel="stylesheet">
    <link href="../Dashboard/sidebar.css" rel="stylesheet">
    <script src="../Dashboard/barra-nav.js" defer></script>
    <style>
        .form-planeado {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .form-planeado h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-planeado label {
            display: block;
            margin-top: 1rem;
            font-weight: bold;
        }
        .form-planeado input, .form-planeado textarea {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .form-planeado button {
            margin-top: 2rem;
            width: 100%;
            padding: 0.75rem;
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            cursor: pointer;
        }
        .form-planeado button:hover {
            background: #388e3c;
        }
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
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
        </div>
    </section>
    <main>
        <?php if ($mensaje): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <?php if (!empty($errores)): ?>
            <div class="mensaje-error">
                <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form class="form-planeado" action="procesar-planeado.php" method="POST" enctype="multipart/form-data">
            <h2>Programar Nuevo Post</h2>
            <label for="titulo">Título del post</label>
            <input type="text" id="titulo" name="titulo" required maxlength="100">

            <label for="contenido">Contenido</label>
            <textarea id="contenido" name="contenido" rows="7" required></textarea>

            <label for="imagen">Imagen (opcional)</label>
            <input type="file" id="imagen" name="imagen" accept="image/*">

            <label for="fecha">Fecha de publicación</label>
            <input type="date" id="fecha" name="fecha" required>

            <label for="hora">Hora de publicación</label>
            <input type="time" id="hora" name="hora" required>

            <button type="submit">Programar Post</button>
        </form>
    </main>
</body>
</html>
>>>>>>> main
