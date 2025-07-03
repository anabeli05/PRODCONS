// Función para inicializar el carrusel
function initializeCarousel() {
    const carousel = document.querySelector('.carousel');
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');
    const items = document.querySelectorAll('.carousel-item');
    let currentIndex = 0;

    if (!carousel || !items.length) return;

    // Función para mostrar el item actual
    function showItem(index) {
        if (!carousel) return;
        const offset = -index * 100;
        carousel.style.transform = `translateX(${offset}%)`;
    }

    // Manejar clic en botón previo
    prevButton?.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + items.length) % items.length;
        showItem(currentIndex);
    });

    // Manejar clic en botón siguiente
    nextButton?.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % items.length;
        showItem(currentIndex);
    });

    // Mostrar el primer item al cargar
    showItem(currentIndex);
}

// Inicializar el carrusel cuando se cargue la página
document.addEventListener('DOMContentLoaded', initializeCarousel);
