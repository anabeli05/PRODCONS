<<<<<<< HEAD
const leavesContainer = document.querySelector('.leaves');

function createLeaf() {
  const leaf = document.createElement('div');
  leaf.classList.add('leaf');
  leaf.style.left = Math.random() * 100 + 'vw';
  leaf.style.animationDuration = (5 + Math.random() * 2) + 's'; // caída entre 5 y 7 segundos
  leaf.style.opacity = Math.random();
  leaf.style.width = Math.random() * 60 + 50 + 'px'; // hojas grandes

  // Hoja SVG contorno verde
  leaf.innerHTML = `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
         xmlns="http://www.w3.org/2000/svg">
      <path d="M12 2C12 2 15 5 15 8C15 11 12 13 12 13C12 13 9 11 9 8C9 5 12 2 12 2Z"
            stroke="green" fill="none" stroke-width="1.5"/>
    </svg>
  `;

  leavesContainer.appendChild(leaf);
  setTimeout(() => leaf.remove(), 7000); // eliminar después de 7 segundos
}

// Crear hojas cada 100 ms
const leafInterval = setInterval(createLeaf, 100);

// Ocultar bienvenida y mostrar blog después de 7 segundos
setTimeout(() => {
  clearInterval(leafInterval);

  const welcome = document.getElementById('welcome');
  welcome.style.transition = 'opacity 1s ease';
  welcome.style.opacity = '0';

  setTimeout(() => {
    welcome.style.display = 'none';
    document.getElementById('main-content').classList.add('show');
  }, 1000);
}, 7000);
=======
const leavesContainer = document.querySelector('.leaves');

function createLeaf() {
  const leaf = document.createElement('div');
  leaf.classList.add('leaf');
  leaf.style.left = Math.random() * 100 + 'vw';
  leaf.style.animationDuration = (5 + Math.random() * 2) + 's'; // caída entre 5 y 7 segundos
  leaf.style.opacity = Math.random();
  leaf.style.width = Math.random() * 60 + 50 + 'px'; // hojas grandes

  // Hoja SVG contorno verde
  leaf.innerHTML = `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
         xmlns="http://www.w3.org/2000/svg">
      <path d="M12 2C12 2 15 5 15 8C15 11 12 13 12 13C12 13 9 11 9 8C9 5 12 2 12 2Z"
            stroke="green" fill="none" stroke-width="1.5"/>
    </svg>
  `;

  leavesContainer.appendChild(leaf);
  setTimeout(() => leaf.remove(), 7000); // eliminar después de 7 segundos
}

// Crear hojas cada 100 ms
const leafInterval = setInterval(createLeaf, 100);

// Ocultar bienvenida y mostrar blog después de 7 segundos
setTimeout(() => {
  clearInterval(leafInterval);

  const welcome = document.getElementById('welcome');
  welcome.style.transition = 'opacity 1s ease';
  welcome.style.opacity = '0';

  setTimeout(() => {
    welcome.style.display = 'none';
    document.getElementById('main-content').classList.add('show');
  }, 1000);
}, 7000);
>>>>>>> main
