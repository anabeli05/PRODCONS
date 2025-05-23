<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Propios Posts</title>
  <style>
    /* Estilos CSS aquí (igual que en el ejemplo anterior) */
  </style>
</head>
<body>
  <h1>Lista de Mis Posts</h1>
  <div id="post-container"></div>

  <script>
    async function fetchPosts() {
      const url = "posts.json"; // Ruta al archivo JSON
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
        console.error("Error fetching posts:", error);
        container.innerHTML = "<p>Error al cargar los posts. Por favor, intente más tarde.</p>";
      }
    }

    document.addEventListener('DOMContentLoaded', fetchPosts);
  </script>
</body>
</html>