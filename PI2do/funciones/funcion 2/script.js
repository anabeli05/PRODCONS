<<<<<<< HEAD
<<<<<<< HEAD
async function fetchPosts() {
    const url = "posts.json"; // Asegúrate de que esta ruta sea correcta
    const container = document.getElementById("post-container");
  
    if (!container) {
      console.error("Container element not found");
      return;
    }
  
    try {
      // Mostrar un indicador de carga
      container.innerHTML = "<p>Cargando posts...</p>";
  
      const response = await fetch(url);
  
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
  
      const posts = await response.json();
  
      // Limpiar el contenedor
      container.innerHTML = "";
  
      // Crear y añadir elementos para cada post
      posts.forEach(post => {
        const postElement = document.createElement("article");
        postElement.className = "post";
        postElement.innerHTML = `
          <h2>${post.title}</h2>
          <p>${post.body}</p>
          <footer>
            <span>ID del Post: ${post.id}</span>
            <span>ID del Usuario: ${post.userId}</span>
          </footer>
        `;
        container.appendChild(postElement);
      });
  
    } catch (error) {
      console.error("Error fetching posts:", error); // Muestra el error en la consola
      container.innerHTML = "<p>Error al cargar los posts. Por favor, intente más tarde.</p>";
    }
  }
  
  // Llamar a la función cuando se carga la página
=======
=======
>>>>>>> 499d8689da2df5b15ff270fec87942ec846e9b42
async function fetchPosts() {
    const url = "posts.json"; // Asegúrate de que esta ruta sea correcta
    const container = document.getElementById("post-container");
  
    if (!container) {
      console.error("Container element not found");
      return;
    }
  
    try {
      // Mostrar un indicador de carga
      container.innerHTML = "<p>Cargando posts...</p>";
  
      const response = await fetch(url);
  
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
  
      const posts = await response.json();
  
      // Limpiar el contenedor
      container.innerHTML = "";
  
      // Crear y añadir elementos para cada post
      posts.forEach(post => {
        const postElement = document.createElement("article");
        postElement.className = "post";
        postElement.innerHTML = `
          <h2>${post.title}</h2>
          <p>${post.body}</p>
          <footer>
            <span>ID del Post: ${post.id}</span>
            <span>ID del Usuario: ${post.userId}</span>
          </footer>
        `;
        container.appendChild(postElement);
      });
  
    } catch (error) {
      console.error("Error fetching posts:", error); // Muestra el error en la consola
      container.innerHTML = "<p>Error al cargar los posts. Por favor, intente más tarde.</p>";
    }
  }
  
  // Llamar a la función cuando se carga la página
<<<<<<< HEAD
>>>>>>> cc3b0c4 (codigo corregido)
=======
>>>>>>> 499d8689da2df5b15ff270fec87942ec846e9b42
  document.addEventListener('DOMContentLoaded', fetchPosts);