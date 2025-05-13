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

    // Toggle sidebar
    adminBtn.addEventListener('click', function() {
        sidebar.classList.toggle('active');
    });

    // Cerrar sidebar
    closeSidebar.addEventListener('click', function() {
        sidebar.classList.remove('active');
    });

    // Cerrar al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!searchBar.contains(e.target) && e.target !== searchToggle) {
            searchBar.classList.add('hidden');
        }
        if (!sidebar.contains(e.target) && e.target !== adminBtn) {
            sidebar.classList.remove('active');
        }
    });
});