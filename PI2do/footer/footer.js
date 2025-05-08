document.addEventListener("DOMContentLoaded", function () {
    console.log("Intentando cargar footer..."); // para ver si entra
  
    fetch("/PI2do/footer/footer.html")
      .then(response => {
        if (!response.ok) throw new Error("No se pudo cargar el footer.");
        return response.text();
      })
      .then(data => {
        document.querySelector('.footer-container').innerHTML = data;
        console.log("Footer cargado correctamente");
      })
      .catch(error => console.error("Error al cargar el footer:", error));
  });
  