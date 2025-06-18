document.addEventListener("DOMContentLoaded", function () {
    fetch("/PRODCONS/BarrasNav/barra_normal.html")
        .then(response => {
            if (response.ok) {
                console.log("Barra cargado exitosamente");
                return response.text();
            } else {
                console.log("Error al cargar el footer:", response.status);
            }
        })
        .then(data => {
            document.body.insertAdjacentHTML("beforeend", data);
        });
  });
  