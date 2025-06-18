document.addEventListener('DOMContentLoaded', function() {
    // Cargar barra principal
    fetch('/PRODCONS/Header-visitantes/barra_principal.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('barraPrincipal').innerHTML = data;
        })
        .catch(error => {
            console.error('Error al cargar la barra principal:', error);
        });

    // Cargar barra secundaria
    fetch('/PRODCONS/Header-visitantes/barra_secundaria.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('barraSecundaria').innerHTML = data;
        })
        .catch(error => {
            console.error('Error al cargar la barra secundaria:', error);
        });
});
