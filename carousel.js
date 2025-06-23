// Función para inicializar el carrusel
function initializeCarousel() {
    const carousel = document.querySelector('.carousel');
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');
    const items = document.querySelectorAll('.carousel-item');
    let currentIndex = 0;

    if (!carousel || !items.length) return;

<<<<<<< HEAD
    // Función para mostrar el item actual
    function showItem(index) {
        if (!carousel) return;
        const offset = -index * 100;
        carousel.style.transform = `translateX(${offset}%)`;
=======
    // Ajustar el ancho del carrusel para mostrar tres posts
    carousel.style.width = '300%'; // 3 posts * 100%

    // Función para mostrar el grupo actual
    function showGroup(index) {
        if (!carousel) return;
        const offset = -index * 100;
        carousel.style.transform = `translateX(${offset}%)`;
        
        // Actualizar la visibilidad de los botones
        updateButtonVisibility();
    }

    // Función para actualizar la visibilidad de los botones
    function updateButtonVisibility() {
        if (!prevButton || !nextButton) return;
        
        const maxIndex = Math.ceil(items.length / 3) - 1;
        prevButton.style.display = currentIndex <= 0 ? 'none' : 'block';
        nextButton.style.display = currentIndex >= maxIndex ? 'none' : 'block';
>>>>>>> main
    }

    // Manejar clic en botón previo
    prevButton?.addEventListener('click', () => {
<<<<<<< HEAD
        currentIndex = (currentIndex - 1 + items.length) % items.length;
        showItem(currentIndex);
=======
        currentIndex = Math.max(0, currentIndex - 1);
        showGroup(currentIndex);
>>>>>>> main
    });

    // Manejar clic en botón siguiente
    nextButton?.addEventListener('click', () => {
<<<<<<< HEAD
        currentIndex = (currentIndex + 1) % items.length;
        showItem(currentIndex);
    });

    // Mostrar el primer item al cargar
    showItem(currentIndex);
=======
        const maxIndex = Math.ceil(items.length / 3) - 1;
        currentIndex = Math.min(maxIndex, currentIndex + 1);
        showGroup(currentIndex);
    });

    // Mostrar el primer grupo al cargar
    showGroup(currentIndex);

    // Asegurar que el carrusel contenedor tenga el ancho correcto
    const container = document.querySelector('.carousel-container');
    if (container) {
        container.style.width = '100%';
        container.style.maxWidth = '1200px';
    }

    // Asegurar que las flechas sean visibles
    if (prevButton) prevButton.style.display = 'block';
    if (nextButton) nextButton.style.display = 'block';
>>>>>>> main
}

// Inicializar el carrusel cuando se cargue la página
document.addEventListener('DOMContentLoaded', initializeCarousel);
