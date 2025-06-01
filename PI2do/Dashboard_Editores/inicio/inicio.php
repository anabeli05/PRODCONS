<?php
// Incluir el archivo de verificación
include '../inicio_sesion/verificar_registro.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   <!-- <link rel="stylesheet" href="/PRODCONS/PI2do/Dashboard_Editores/inicio/styles.css"> -->
    <link href='/PRODCONS/PI2do/Dashboard_Editores/inicio/hola-adminstyles.css' rel="stylesheet">
    <!-- CSS DE HEADER-->
    <link href='/PRODCONS/PI2do/Dashboard_Editores/Dashboard/sidebar.css' rel="stylesheet">
    <script src='/PRODCONS/PI2do/Dashboard_Editores/Dashboard/barra-nav.js' defer></script>
</head>
<body>
    <header> 
        <div class="header-contenedor">
            <div class="principal"></div>
        </div>
    </header>

    <section class="logo"> 
        <div class="header_2">
            <img class="prodcons" src='/PRODCONS/PI2do/imagenes/prodcon/logoSinfondo.png' alt="Logo">

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
                <a href='/PRODCONS/PI2do/Dashboard_Editores/Notibox/noti-box.php' class="notif-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notif-badge">1</span>
                </a>

                <!-- Botón Admin con avatar -->
                <div class="admin-btn">
                    <span><?php echo htmlspecialchars($_SESSION['Nombre'] ?? 'Admin'); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 11l8 3l8 -3" />
                    </svg>
                    <img src='/PRODCONS/PI2do/imagenes/logos/perfil.png' alt="Admin" class="admin-avatar">
                </div>
            </div>

            <!-- Sidebar -->
            <div class="admin-sidebar" id="adminSidebar">
                <div class="sidebar-header">
                    <h3>ADMIN</h3>
                    <button class="close-sidebar">
                        <img src='../../imagenes/logos/perfil.png' alt="Admin" class="admin-avatar">
                    </button>
                </div>
                
                <nav class="sidebar-menu">

                <a href='../MisArticulos/mis-articulos.php'><!----cambiar la ruta a inicio---->
                        <span>Inicio</span>
                        <i class="fas fa-file-alt"></i>
                    </a>

                <a href='../MisArticulos/mis-articulos.php'>
                        <span>Mis Artículos</span>
                        <i class="fas fa-file-alt"></i>
                    </a>

                    <a href="../Crear nuevo post/formulario-new-post.php">
                        <span>Crear Post</span>
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href='../PostPlaneados/post-planeados.php'>
                        <span>Post Planeados</span>
                        <i class="fas fa-calendar"></i>
                    </a>
                                        
                    <a href='../Estadisticas/estadisticas-adm.php'>
                        <span>Estadísticas</span>
                        <i class="fas fa-chart-bar"></i>
                    </a>
                    
                    <a href='../Configuracion/config.php'>
                        <span>Configuración</span>
                        <i class="fas fa-cog"></i>
                    </a>
                
                <div class="sidebar-footer">
                    <a href='../../inicio_sesion/logout.php' class="logout-btn">
                        Cerrar Sesión
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!---------Saludo Admin--------->
    <section class="contenedor-principal">
        <div class="rec-admin">
            <div class="formato-txt">
              <h2>¡Hola <?php echo htmlspecialchars($_SESSION['Nombre'] ?? 'Admin'); ?>!</h2>
                <p>Un blog exitoso se construye post a post. ¡Sigue adelante!</p>
                <a href='../MisArticulos/formulario-new-post.php'>
                    <button class="new-post">ESCRIBE UN NUEVO POST</button>
                </a>
            </div>
            <div class="admin-img">
                <img src='/PRODCONS/PI2do/imagenes/chicaLaptop.png'>
            </div>
        </div>

        <div class="contenedor-articulo">
            <div class="encabezado-articulos">
                <h2>ARTICULOS MAS VISTOS</h2>
                <!-------selector de meses------->
                <div class="selector-meses">
                    <form method="GET" action="">
                        <select name="mes" id="selectorMes" class="estilo-selector" onchange="this.form.submit()">
                            <?php
                            $meses_nombres = [
                                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
                            ];
                            foreach ($meses_nombres as $mes) {
                                $selected = ($mes === $mes_seleccionado) ? 'selected' : '';
                                echo "<option value=\"$mes\" $selected>" . ucfirst($mes) . "</option>";
                            }
                            ?>
                        </select>
                    </form>
                    <svg class="flecha-personalizada" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 11l8 3l8 -3" />
                    </svg>
                </div>
            </div>

            <!-------ARTICULOS------>
            <div class="vista-articulos">
                <?php
                if (!empty($articulos)) {
                    $contador = 1;
                    foreach ($articulos as $articulo) {
                        $imagenes = explode(',', $articulo['imagenes']);
                        $imagen_principal = !empty($imagenes[0]) ? '/PRODCONS/PI2do/imagenes/articulos/' . $imagenes[0] : '/PRODCONS/PI2do/Dashboard_Editores/img-vista-admin/default-post.jpg';
                        ?>
                        <div class="articulo" data-mes="<?php echo $mes_seleccionado; ?>">
                            <div class="numero"><?php echo str_pad($contador, 2, '0', STR_PAD_LEFT); ?></div>
                            <div class="contenido-articulo">
                                <div class="imagen-articulo">
                                    <img src="<?php echo htmlspecialchars($imagen_principal); ?>" alt="<?php echo htmlspecialchars($articulo['Titulo']); ?>">
                                </div>
                                <div class="texto-articulo">
                                    <h3><?php echo htmlspecialchars($articulo['Titulo']); ?></h3>
                                    <p><?php echo htmlspecialchars(substr($articulo['Contenido'], 0, 100)) . '...'; ?></p>
                                    <span class="fecha">Publicado el <?php echo date('d \d\e F \d\e Y', strtotime($articulo['Fecha de Creacion'])); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php
                        $contador++;
                    }
                } else {
                    echo '<div class="no-articulos">No hay artículos disponibles para este mes.</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="error-message">
        <?php 
        echo htmlspecialchars($_SESSION['error']);
        unset($_SESSION['error']);
        ?>
    </div>
    <?php endif; ?>

    <script>
        // Script para manejar el selector de meses
        document.getElementById('selectorMes').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>