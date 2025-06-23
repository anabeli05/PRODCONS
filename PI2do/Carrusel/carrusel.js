<<<<<<< HEAD
// carrusel.js
document.addEventListener('DOMContentLoaded', () => {
  const carousel = document.querySelector('.carousel');
  const prevBtn = document.querySelector('button.prev');
  const nextBtn = document.querySelector('button.next');
  
  const itemWidth = document.querySelector('.carousel-item').offsetWidth + 20; // ancho + margen lateral
  const visibleItemsCount = Math.floor(carousel.parentElement.offsetWidth / itemWidth);
  const totalItems = carousel.children.length;
  
  let currentIndex = 0;

  function updateCarousel() {
    // Limitar índice para no pasar el final ni antes del inicio
    if (currentIndex < 0) currentIndex = 0;
    if (currentIndex > totalItems - visibleItemsCount) currentIndex = totalItems - visibleItemsCount;
    // Mover carousel con translateX negativo
    carousel.style.transform = `translateX(${-currentIndex * itemWidth}px)`;
  }

  prevBtn.addEventListener('click', () => {
    currentIndex--;
    updateCarousel();
  });

  nextBtn.addEventListener('click', () => {
    currentIndex++;
    updateCarousel();
  });

  // Opcional: deshabilitar botones al inicio o final
  function updateButtons() {
    prevBtn.disabled = currentIndex <= 0;
    nextBtn.disabled = currentIndex >= totalItems - visibleItemsCount;
  }

  // Actualizar botones tras cada movimiento
  prevBtn.addEventListener('click', updateButtons);
  nextBtn.addEventListener('click', updateButtons);
  updateButtons();

  // Inicial
  updateCarousel();
});
=======
// carrusel.js
document.addEventListener('DOMContentLoaded', () => {
  const carousel = document.querySelector('.carousel');
  const prevBtn = document.querySelector('button.prev');
  const nextBtn = document.querySelector('button.next');
  
  const itemWidth = document.querySelector('.carousel-item').offsetWidth + 20; // ancho + margen lateral
  const visibleItemsCount = Math.floor(carousel.parentElement.offsetWidth / itemWidth);
  const totalItems = carousel.children.length;
  
  let currentIndex = 0;

  function updateCarousel() {
    // Limitar índice para no pasar el final ni antes del inicio
    if (currentIndex < 0) currentIndex = 0;
    if (currentIndex > totalItems - visibleItemsCount) currentIndex = totalItems - visibleItemsCount;
    // Mover carousel con translateX negativo
    carousel.style.transform = `translateX(${-currentIndex * itemWidth}px)`;
  }

  prevBtn.addEventListener('click', () => {
    currentIndex--;
    updateCarousel();
  });

  nextBtn.addEventListener('click', () => {
    currentIndex++;
    updateCarousel();
  });

  // Opcional: deshabilitar botones al inicio o final
  function updateButtons() {
    prevBtn.disabled = currentIndex <= 0;
    nextBtn.disabled = currentIndex >= totalItems - visibleItemsCount;
  }

  // Actualizar botones tras cada movimiento
  prevBtn.addEventListener('click', updateButtons);
  nextBtn.addEventListener('click', updateButtons);
  updateButtons();

  // Inicial
  updateCarousel();
});
>>>>>>> main
