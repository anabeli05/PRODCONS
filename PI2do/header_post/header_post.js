document.addEventListener("DOMContentLoaded", function () {
    fetch("/PRODCONS/PI2do/header_post/header_post.html")
        .then(response => {
            if (response.ok) {
                console.log("Header cargado exitosamente");
                return response.text();
            } else {
                console.log("Error al cargar el footer:", response.status);
            }
        })
        .then(data => {
            document.body.insertAdjacentHTML("beforeend", data);
        });
  });
  