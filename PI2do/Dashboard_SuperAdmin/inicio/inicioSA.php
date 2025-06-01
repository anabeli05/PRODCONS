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
    <link rel="stylesheet" href="inicioSA.css">
    <link rel="stylesheet" href="../Dashboard/sidebar.css">
    <script src='../Dashboard/barra-nav.js' defer></script>
</head>
<body>
    <header> 
        <div class="header-contenedor">
            <div class="principal"></div>
        </div>
    </header>

    <section class="logo"> 
        <div class="header_2">
            <img class="prodcons" src="../../imagenes/prodcon/logoSinfondo.png" alt="Logo">
            <div class="admin-controls">
                <button class="search-toggle-btn">
                    <i class="fas fa-search"></i>
                </button>
                <div class="search-bar hidden">
                    <input type="text" placeholder="Buscar...">
                    <button class="search-close-btn">&times;</button>
                </div>
                <button class="notif-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notif-badge">1</span>
                </button>
                <div class="admin-btn" id="sidebarToggle">
                    <span>Admin</span>
                    <i class="fas fa-chevron-down"></i>
                    <img src="../../imagenes/logos/perfil.png" alt="Admin" class="admin-avatar">
                </div>
            </div>
        </div>
    </section>

    <?php include('../Dashboard/sidebar.php'); ?>

    <section class="contenedor-principal">
        <div class="rec-admin">
            <div class="formato-txt">
                <h2>¡Hola Admin!</h2>
                <p>Un blog exitoso se construye post a post. ¡Sigue adelante!</p>
                <button class="new-post">ESCRIBE UN NUEVO POST</button>
            </div>
            <div class="admin-img">
                <img src="../../imagenes/chicaLaptop.png" alt="Admin Ilustración">
            </div>
        </div>

        <div class="contenedor-articulo">
            <div class="encabezado-articulos">
                <h2>ARTICULOS MAS VISTOS</h2>
                <div class="selector-meses">
                    <form method="get" style="margin:0;">
                        <select id="selectorMes" class="estilo-selector" name="mes" onchange="this.form.submit()">
                            <?php foreach($meses as $nombre_mes => $numero_mes): ?>
                                <option value="<?php echo $nombre_mes; ?>" <?php if($nombre_mes === $mes_seleccionado) echo 'selected'; ?>><?php echo ucfirst($nombre_mes); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="flecha-personalizada" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 11l8 3l8 -3" />
                        </svg>
                    </form>
                </div>
            </div>
            <div class="vista-articulos">
                <?php
                if ($hay_articulos) {
                    $contador = 1;
                    while($articulo = mysqli_fetch_assoc($resultado)) {
                        $fecha = date('d M Y', strtotime($articulo['fecha_publicacion']));
                        $numero = str_pad($contador, 2, '0', STR_PAD_LEFT);
                ?>
                <div class="articulo" data-mes="<?php echo strtolower(date('F', strtotime($articulo['fecha_publicacion']))); ?>">
                    <div class="numero"><?php echo $numero; ?></div>
                    <div class="contenido-articulo">
                        <div class="imagen-articulo">
                            <img src="<?php echo htmlspecialchars($articulo['imagen']); ?>" alt="<?php echo htmlspecialchars($articulo['titulo']); ?>">
                        </div>
                        <div class="texto-articulo">
                            <h3><?php echo htmlspecialchars($articulo['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($articulo['contenido']); ?></p>
                            <span class="fecha">Publicado el <?php echo $fecha; ?></span>
                        </div>
                    </div>
                </div>
                <?php
                        $contador++;
                    }
                } else {
                    echo '<div class="no-articulos">No hay artículos disponibles en este momento.</div>';
                }
                ?>
            </div>
        </div>
    </section>
</body>
<script>
// Mostrar/ocultar sidebar al hacer clic en la foto de perfil
const sidebar = document.querySelector('.sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
if (sidebar && sidebarToggle) {
    sidebar.style.transition = 'transform 0.3s';
    sidebar.style.transform = 'translateX(-100%)';
    let sidebarVisible = false;
    sidebarToggle.addEventListener('click', function() {
        sidebarVisible = !sidebarVisible;
        sidebar.style.transform = sidebarVisible ? 'translateX(0)' : 'translateX(-100%)';
    });
}
</script>
</html>
