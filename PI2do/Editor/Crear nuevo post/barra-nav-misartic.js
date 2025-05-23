document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const searchToggle = document.querySelector('.search-toggle-btn');
    const searchBar = document.querySelector('.search-bar');
    const searchClose = document.querySelector('.search-close-btn');
    const adminBtn = document.querySelector('.admin-btn');
    const sidebar = document.querySelector('.admin-sidebar');
    const closeSidebar = document.querySelector('.close-sidebar');

    // Toggle barra de búsqueda
    searchToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        searchBar.classList.toggle('hidden');
    });

    // Cerrar barra de búsqueda
    searchClose.addEventListener('click', function() {
        searchBar.classList.add('hidden');
    });

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

    // Cerrar al hacer clic fuera (versión mejorada)
    document.addEventListener('click', function(e) {
        // Para la barra de búsqueda
        if (!searchBar.contains(e.target) && e.target !== searchToggle && !searchToggle.contains(e.target)) {
            searchBar.classList.add('hidden');
        }
        
        // Para el sidebar
        if (!sidebar.contains(e.target) && !adminBtn.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    });
});

