<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../Base de datos/conexion.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['Usuario_ID'])) {
    header("Location: ../dashboard.php");
    exit();
}

// --- REGISTRO ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registro'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['email'];
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar_password'];

    if ($password !== $confirmar_password) {
        $error = "Las contraseñas no coinciden";
    } else {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $error = "El correo ya está registrado";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $rol = "Usuario";
            $stmt = $conn->prepare("INSERT INTO usuarios (Nombre, Correo, Contraseña, Rol) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $correo, $hash, $rol);
            if ($stmt->execute()) {
                $_SESSION['Usuario_ID'] = $stmt->insert_id;
                $_SESSION['Nombre'] = $nombre;
                $_SESSION['Rol'] = $rol;
                header("Location: /PI2do/usuario/usuario.php");
                exit();
            } else {
                $error = "Error al registrar el usuario";
            }
        }
        $stmt->close();
    }
    $conn->close();
    // IMPORTANTE: Detener la ejecución aquí para que no siga al login
    exit();
}

// --- LOGIN ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['registro'])) {
    $correo = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($password, $usuario['Contraseña'])) {
            $_SESSION['Usuario_ID'] = $usuario['Usuario_ID'];
            $_SESSION['Nombre'] = $usuario['Nombre'];
            $_SESSION['Rol'] = $usuario['Rol'];

            if ($usuario['Rol'] == 'Super Admin') {
                header("Location: /PI2do/SuperAdmin/inicioSA.html");
            } elseif ($usuario['Rol'] == 'Editor') {
                header("Location: /PI2do/Editor/hola-admin.html");
            } else {
                header("Location: /PI2do/usuario/usuario.php");
            }
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Usuario no encontrado";
    }
    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Configuración básica del documento -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS</title>
    
    <!-- Hojas de estilo -->
    <link rel="stylesheet" href="css/styles.css">
    <link href="login.css" rel="stylesheet">
    <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!-- ============================================= -->
    <!-- SECCIÓN DE ANIMACIÓN DE FONDO -->
    <!-- ============================================= -->
    <div class="background-animation">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
    </div>
    
    <!-- ============================================= -->
    <!-- CABECERA PRINCIPAL -->
    <!-- ============================================= -->
    <header> 
        <div class="header-contenedor">
            <div class="principal"></div>
        </div>
    </header>

    <!-- ============================================= -->
    <!-- SECCIÓN DEL LOGO -->
    <!-- ============================================= -->
    <section class="logo"> 
        <div class="header_2">
            <img class="prodcons" src="../imagenes/prodcon/logoSinfondo.png" alt="Logo"> 
        </div>
    </section>

    <!-- ============================================= -->
    <!-- CONTENIDO PRINCIPAL -->
    <!-- ============================================= -->
    <section class="contenedor-main">
        <section class="wrapper">
            <!-- ============================================= -->
            <!-- FORMULARIO DE LOGIN (visible por defecto) -->
            <!-- ============================================= -->
            <div class="form" id="login-form">
                <h1>INGRESAR USUARIO</h1>
                <?php
                if (isset($error)) {
                    echo '<p style="color:red; text-align:center;">'.$error.'</p>';
                }
                if (isset($_SESSION['error'])) {
                    echo '<p style="color:red; text-align:center;">'.$_SESSION['error'].'</p>';
                    unset($_SESSION['error']);
                }
                ?>
                <form action="login.php" method="POST">
                    <!-- Campo para el correo electrónico -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="email" name="email" placeholder="Correo Electrónico" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <!-- Campo para la contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" name="password" placeholder="Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Checkbox "Recuérdame" y enlace para recuperar contraseña -->
                    <div class="recuerdame-contenedor">
                        <div class="recuerdame">
                            <input class="C" type="checkbox">
                            <label>Recuérdame</label>
                        </div>
                        <div class="olvido-contrasena">
                            <a href="#" id="mostrar-recuperacion">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>
        
                    <!-- Botón de envío -->
                    <input type="submit" value="INGRESAR"> 
                    
                    <!-- Enlace para alternar al formulario de registro -->
                    <div class="alternar-form">
                        <p>¿No tienes una cuenta? <a href="#" id="mostrar-registro">Regístrate aquí</a></p>
                    </div>
                </form>
            </div>
            
            <!-- ============================================= -->
            <!-- FORMULARIO DE REGISTRO (oculto inicialmente) -->
            <!-- ============================================= -->
            <div class="form" id="registro-form" style="display: none;">
                <h1>REGISTRAR USUARIO</h1>
                <form action="login.php" method="POST">
                    <input type="hidden" name="registro" value="1">
                    <div class="buton">
                        <div class="input-area">
                            <input type="text" name="nombre" placeholder="Nombre Completo" required>
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="buton">
                        <div class="input-area">
                            <input type="email" name="email" placeholder="Correo Electrónico" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" name="password" placeholder="Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" name="confirmar_password" placeholder="Confirmar Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <div class="terminos">
                        <input class="C" type="checkbox" required>
                        <label>Acepto los <a href="/PI2do/footer/parafooter/term-condi/term-condi.html">términos y condiciones</a></label>
                    </div>
                    <input type="submit" value="REGISTRARSE">
                </form>
            </div>
            
            <!-- ============================================= -->
            <!-- FORMULARIO DE RECUPERACIÓN (oculto inicialmente) -->
            <!-- ============================================= -->
            <div class="form" id="recuperacion-form" style="display: none;">
                <h1>RECUPERAR CONTRASEÑA</h1>
                <form action="">
                    <!-- Instrucciones -->
                    <div class="instrucciones">
                        <p>Ingresa tu correo electrónico y te enviaremos un código para restablecer tu contraseña.</p>
                    </div>

                    <!-- Campo para el correo electrónico -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="email" placeholder="Correo Electrónico" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <!-- Botón de envío -->
                    <input type="submit" value="ENVIAR CÓDIGO"> 
                    
                    <!-- Enlace para volver al login -->
                    <div class="alternar-form">
                        <p>¿Recordaste tu contraseña? <a href="#" id="volver-login">Inicia sesión aquí</a></p>
                    </div>
                </form>
            </div>
            
            <!-- ============================================= -->
            <!-- FORMULARIO DE NUEVA CONTRASEÑA (oculto inicialmente) -->
            <!-- ============================================= -->
            <div class="form" id="nueva-contrasena-form" style="display: none;">
                <h1>RESTABLECER CONTRASEÑA</h1>
                <form action="">
                    <!-- Instrucciones -->
                    <div class="instrucciones">
                        <p>Ingresa el código que recibiste y tu nueva contraseña.</p>
                    </div>

                    <!-- Campo para el código -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="text" placeholder="Código de verificación" required>
                            <i class="fas fa-key"></i>
                        </div>
                    </div>

                    <!-- Campo para la nueva contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Nueva Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Campo para confirmar la nueva contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Confirmar Nueva Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Botón de envío -->
                    <input type="submit" value="CAMBIAR CONTRASEÑA"> 
                    
                    <!-- Enlace para volver al login -->
                    <div class="alternar-form">
                        <p>¿Recordaste tu contraseña? <a href="#" id="volver-login-2">Inicia sesión aquí</a></p>
                    </div>
                </form>
            </div>
            
            <!-- ============================================= -->
            <!-- CONTENEDOR DEL LOGO (lado derecho) -->
            <!-- ============================================= -->
            <div class="contenedor-logo">
                <img src="../imagenes/login.png" alt="Imagen de fondo" class="bg-image">
                <figure>
                    <img src="../imagenes/prodcon/logoSinfondo.png" alt="Logo transparente" class="logo-portada">
                </figure>
            </div>
        </section>
    </section>
    
    <!-- ============================================= -->
    <!-- SCRIPTS JAVASCRIPT -->
    <!-- ============================================= -->
    <script src="/PRODCONS/PI2do/Editor/1SideBar/barra-nav.js"></script>
    <script>
        // Función para alternar entre formularios
        function mostrarFormulario(idFormulario) {
            // Ocultar todos los formularios primero
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('registro-form').style.display = 'none';
            document.getElementById('recuperacion-form').style.display = 'none';
            document.getElementById('nueva-contrasena-form').style.display = 'none';
            
            // Mostrar el formulario solicitado
            document.getElementById(idFormulario).style.display = 'block';
        }
        
        // Event listeners para los enlaces
        document.getElementById('mostrar-registro').addEventListener('click', function(e) {
            e.preventDefault();
            mostrarFormulario('registro-form');
        });
        
        document.getElementById('mostrar-login').addEventListener('click', function(e) {
            e.preventDefault();
            mostrarFormulario('login-form');
        });
        
        document.getElementById('mostrar-recuperacion').addEventListener('click', function(e) {
            e.preventDefault();
            mostrarFormulario('recuperacion-form');
        });
        
        document.getElementById('volver-login').addEventListener('click', function(e) {
            e.preventDefault();
            mostrarFormulario('login-form');
        });

        document.getElementById('volver-login-2').addEventListener('click', function(e) {
            e.preventDefault();
            mostrarFormulario('login-form');
        });

        // Simular envío de código y mostrar formulario de nueva contraseña
        document.querySelector('#recuperacion-form form').addEventListener('submit', function(e) {
            e.preventDefault();
            mostrarFormulario('nueva-contrasena-form');
        });

        // Efectos de hover para los botones de envío
        const submitButtons = document.querySelectorAll('input[type="submit"]');
        submitButtons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
                this.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.2)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });
    </script>
</body>
</html>
