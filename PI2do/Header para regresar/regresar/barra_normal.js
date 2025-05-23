document.addEventListener("DOMContentLoaded", function () {
    fetch("/PI2do/BarrasNav/barra_normal.html")
        .then(response => {
            if (response.ok) {
                console.log("Footer cargado exitosamente");
                return response.text();
            } else {
                console.log("Error al cargar el footer:", response.status);
            }
        })
        .then(data => {
            document.body.insertAdjacentHTML("beforeend", data);
        });
  });
  