<?php
session_start();

// Verificar si el usuario está logueado y es Super Admin
if (!isset($_SESSION['Usuario_ID']) || $_SESSION['Rol'] !== 'Super Admin') {
    header('Location: ../../inicio_sesion/login.php');
    exit();
}

// Incluir conexión a la base de datos
require_once __DIR__ . '/../../Base de datos/conexion.php';

// Crear una instancia de la clase Conexion
$conexion = new Conexion();

// Inicializar variables
$editores = [];
$error = null;

try {
    // Abrir conexión
    $conexion->abrir_conexion();
    
    // Obtener lista de editores
    $sql = "SELECT u.Usuario_ID, u.Nombre, u.Correo, u.estado, 
            COUNT(DISTINCT a.ID_Articulo) as total_posts,
            COUNT(ev.Vista_ID) as total_visitas
            FROM usuarios u
            LEFT JOIN articulos a ON u.Usuario_ID = a.Usuario_ID
            LEFT JOIN estadisticas_vistas ev ON a.ID_Articulo = ev.Articulo_ID
            WHERE u.rol = 'Editor'
            GROUP BY u.Usuario_ID
            ORDER BY u.Nombre ASC";
            
    $stmt = $conexion->conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $editores = $result->fetch_all(MYSQLI_ASSOC);
    
} catch (Exception $e) {
    $error = "Error al cargar los editores: " . $e->getMessage();
} finally {
    $conexion->cerrar_conexion();
}

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Editores - PRODCONS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Editores.css">
    <link rel="stylesheet" href="../Dashboard/sidebar.css">
</head>
<body>
    

    

    <?php include '../Dashboard/sidebar.php'; ?>

    <div class="contenedor-principal">
        <div class="header_1">
            <h1>Gestión de Editores</h1>
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="editors-table">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Posts</th>
                        <!--<th>Visitas</th>-->
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($editores)): ?>
                        <tr>
                            <td colspan="6" class="no-data">No hay editores registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($editores as $editor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($editor['Nombre']); ?></td>
                                <td><?php echo htmlspecialchars($editor['Correo']); ?></td>
                                <td><?php echo number_format($editor['total_posts']); ?></td>
                                <!--<td>/?php echo number_format($editor['total_visitas'] ?? 0); ?></td>-->
                                <td>
                                    <span class="status-badge <?php echo $editor['estado']; ?>">
                                        <?php echo ucfirst($editor['estado']); ?>
                                    </span>
                                </td>
                                <td class="actions"><!--Boton de editar
                                    <a href="editar-editor.php?id=<//?php echo $editor['Usuario_ID']; ?>" 
                                       class="btn-edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>-->
                                    <button onclick="confirmarEliminacion(<?php echo $editor['Usuario_ID']; ?>)" 
                                            class="btn-delete" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function confirmarEliminacion(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este editor?')) {
            window.location.href = `eliminar-editor.php?id=${id}&csrf_token=<?php echo $_SESSION['csrf_token']; ?>`;
        }
    }

    // Mostrar/ocultar sidebar al hacer clic en el logo de perfil
    const adminSidebar = document.querySelector('.admin-sidebar'); // Selecciona el sidebar admin
    const sidebarToggle = document.getElementById('sidebarToggle');
    const overlay = document.createElement('div');
    overlay.classList.add('sidebar-overlay');
    document.body.appendChild(overlay);

    console.log('Sidebar element:', adminSidebar);
    console.log('Toggle button:', sidebarToggle);
    console.log('Overlay element:', overlay);

    if (adminSidebar && sidebarToggle) {
        console.log('Elements found, adding event listeners.');
        sidebarToggle.addEventListener('click', function(event) {
            console.log('Toggle button clicked.');
            event.stopPropagation(); // Evita que el clic llegue al body/overlay
            adminSidebar.classList.toggle('active'); // Alterna la clase 'active' en el sidebar admin
            overlay.classList.toggle('active');
        });

        // Ocultar sidebar al hacer clic en el overlay
        overlay.addEventListener('click', function() {
            console.log('Overlay clicked.');
            adminSidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    } else {
        console.error('Sidebar or toggle button not found!');
    }
    </script>
</body>
</html>
