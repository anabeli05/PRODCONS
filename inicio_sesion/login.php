<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../Base de datos/conexion.php';

// Mostrar mensaje de error si existe
$error = isset($_GET['error']) ? "Credenciales incorrectas" : "";
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
            <a href="/">           
                 <img class="prodcons" src="../imagenes/prodcon/logoSinfondo.png" alt="Logo"> 
            </a>
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
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="login_var.php">
                    <!-- Campo para el correo electrónico -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="email" placeholder="Correo Electrónico" name="Correo" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <!-- Campo para la contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Contraseña" name="Contraseña" required>
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
                    <input type="submit" name="boton_ingresar" value="INGRESAR"> 
                    
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
                <form method="POST" action="registro.php">
                    <!-- Campo para el nombre completo -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="text" placeholder="Nombre Completo" name="Nombre" required>
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <!-- Campo para el correo electrónico -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="email" placeholder="Correo Electrónico" name="Correo" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <!-- Campo para la contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Contraseña" name="Contraseña" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Campo para confirmar la contraseña -->
                    <div class="buton">
                        <div class="input-area">
                            <input type="password" placeholder="Confirmar Contraseña" name="confirmar_password" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Checkbox de términos y condiciones -->
                    <div class="terminos">
                        <input class="C" type="checkbox" required>
                        <label>Acepto los <a href="/PRODCONS/footer/parafooter/term-condi/term-condi.html">términos y condiciones</a></label>
                    </div>
        
                    <!-- Botón de envío -->
                    <input type="submit" name="registro" value="REGISTRARSE"> 
                    
                    <!-- Enlace para alternar al formulario de login -->
                    <div class="alternar-form">
                        <p>¿Ya tienes una cuenta? <a href="#" id="mostrar-login">Inicia sesión aquí</a></p>
                    </div>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Referencias a los formularios
        const loginForm = document.getElementById('login-form');
        const registroForm = document.getElementById('registro-form');
        const recuperacionForm = document.getElementById('recuperacion-form');
        const nuevaContrasenaForm = document.getElementById('nueva-contrasena-form');

        // Referencias a los enlaces
        const mostrarRegistro = document.getElementById('mostrar-registro');
        const mostrarLogin = document.getElementById('mostrar-login');
        const mostrarRecuperacion = document.getElementById('mostrar-recuperacion');
        const volverLogin = document.getElementById('volver-login');
        const volverLogin2 = document.getElementById('volver-login-2');

        // Función para ocultar todos los formularios
        function ocultarTodosLosFormularios() {
            loginForm.style.display = 'none';
            registroForm.style.display = 'none';
            recuperacionForm.style.display = 'none';
            nuevaContrasenaForm.style.display = 'none';
        }

        // Función para mostrar un formulario específico
        function mostrarFormulario(formulario) {
            ocultarTodosLosFormularios();
            formulario.style.display = 'block';
        }

        // Event Listeners para los enlaces
        mostrarRegistro.addEventListener('click', function(e) {
            e.preventDefault();
            mostrarFormulario(registroForm);
        });

        mostrarLogin.addEventListener('click', function(e) {
            e.preventDefault();
            mostrarFormulario(loginForm);
        });

        mostrarRecuperacion.addEventListener('click', function(e) {
            e.preventDefault();
            mostrarFormulario(recuperacionForm);
        });

        volverLogin.addEventListener('click', function(e) {
            e.preventDefault();
            mostrarFormulario(loginForm);
        });

        volverLogin2.addEventListener('click', function(e) {
            e.preventDefault();
            mostrarFormulario(loginForm);
        });

        // Validación de contraseñas en el formulario de registro
        const registroPassword = registroForm.querySelector('input[placeholder="Contraseña"]');
        const registroConfirmPassword = registroForm.querySelector('input[placeholder="Confirmar Contraseña"]');

        registroConfirmPassword.addEventListener('input', function() {
            if (this.value !== registroPassword.value) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });

        // Validación de contraseñas en el formulario de nueva contraseña
        const nuevaPassword = nuevaContrasenaForm.querySelector('input[placeholder="Nueva Contraseña"]');
        const nuevaConfirmPassword = nuevaContrasenaForm.querySelector('input[placeholder="Confirmar Nueva Contraseña"]');

        nuevaConfirmPassword.addEventListener('input', function() {
            if (this.value !== nuevaPassword.value) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });

        // Manejo del formulario de login
        loginForm.querySelector('form').addEventListener('submit', function(e) {
            // Removemos el preventDefault para permitir que el formulario se envíe
            // e.preventDefault();
            // Aquí iría la lógica de autenticación
            console.log('Intentando iniciar sesión...');
        });

        // Manejo del formulario de recuperación
        recuperacionForm.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica de recuperación
            console.log('Enviando código de recuperación...');
            // Simular envío exitoso y mostrar formulario de nueva contraseña
            mostrarFormulario(nuevaContrasenaForm);
        });

        // Manejo del formulario de nueva contraseña
        nuevaContrasenaForm.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica de cambio de contraseña
            console.log('Cambiando contraseña...');
            // Simular cambio exitoso y volver al login
            mostrarFormulario(loginForm);
        });
    });
    </script>

<?php
    include 'login_var.php';
?>
</body>
</html>
