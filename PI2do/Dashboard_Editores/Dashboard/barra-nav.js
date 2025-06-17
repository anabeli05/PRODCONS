// Elimina la carga dinámica del sidebar, solo maneja los eventos del sidebar

document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const adminBtn = document.querySelector('.admin-btn');
    const sidebar = document.querySelector('.admin-sidebar');
    const closeSidebar = document.querySelector('.close-sidebar');

    // Verifica que todos los elementos existen antes de agregar listeners
    if (adminBtn && sidebar && closeSidebar) {
        // Toggle sidebar - Área clickeable mejorada
        adminBtn.addEventListener('click', function(e) {
            // Verifica si el clic fue en un elemento interactivo interno
            if (!e.target.closest('a, button, input, select')) {
                sidebar.classList.toggle('active');
            }
        });

        // Cerrar sidebar
        closeSidebar.addEventListener('click', function() {
            sidebar.classList.remove('active');
        });

        // Cerrar al hacer clic fuera
        document.addEventListener('click', function(e) {
            // Para el sidebar
            if (!sidebar.contains(e.target) && !adminBtn.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    }
});
