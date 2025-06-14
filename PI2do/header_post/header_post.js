// Código JavaScript para el header_post.js

// Función para alternar el menú de idiomas
function alternarIdioma() {
    const opciones = document.getElementById('idiomasOpciones');
    if (opciones) {
        opciones.style.display = opciones.style.display === 'none' ? 'block' : 'none';
    }
}

// Función para cambiar el idioma
function cambiarIdioma(idioma) {
    // 1. Actualizar la bandera principal y las banderas del menú
    const banderaPrincipal = document.getElementById('banderaIdioma');
    const banderaIngles = document.querySelector('.ingles');
    const banderaEspana = document.querySelector('.españa');
    
    if (banderaPrincipal) {
        banderaPrincipal.src = idioma === 'ingles' 
            ? "/PRODCONS/PI2do/imagenes/logos/ingles.png" 
            : "/PRODCONS/PI2do/imagenes/logos/espanol.png";
    }
    
    if (banderaIngles && banderaEspana) {
        banderaIngles.style.display = idioma === 'espanol' ? 'none' : 'block';
        banderaEspana.style.display = idioma === 'espanol' ? 'block' : 'none';
    }
    
    // 2. Actualizar el idioma actual
    const currentLanguage = idioma === 'ingles' ? 'en' : 'es';
    
    // 3. Guardar el idioma preferido
    localStorage.setItem('preferredLanguage', currentLanguage);
    
    // 4. Llamar a la función de traducción
    if (typeof translateContent === 'function') {
        translateContent(currentLanguage);
    }
    
    // 5. Cerrar el menú de opciones
    const opciones = document.getElementById('idiomasOpciones');
    if (opciones) {
        opciones.style.display = 'none';
    }
}

// Inicialización cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el estado del idioma
    const currentLanguage = localStorage.getItem('preferredLanguage') || 'es';
    const banderaIdioma = document.getElementById('banderaIdioma');
    
    if (banderaIdioma) {
        banderaIdioma.src = currentLanguage === 'en' 
            ? "/PRODCONS/PI2do/imagenes/logos/ingles.png" 
            : "/PRODCONS/PI2do/imagenes/logos/espanol.png";
    }

    // Event listener para el botón de idioma
    const idiomaToggle = document.getElementById('idiomaToggle');
    if (idiomaToggle) {
        idiomaToggle.addEventListener('click', alternarIdioma);
    }

    // Event listener para el botón de regresar
    const flechaLeft = document.querySelector('.flecha_left');
    if (flechaLeft) {
        flechaLeft.addEventListener('click', function() {
            window.history.back();
        });
    }

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