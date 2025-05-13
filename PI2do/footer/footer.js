document.addEventListener("DOMContentLoaded", function() {
    // Verificar si el footer-container existe y está vacío
    const footerContainer = document.querySelector('.footer-container');
    
    if (footerContainer && !footerContainer.hasChildNodes()) {
        console.log("Cargando footer dinámicamente...");
        
        fetch("/PI2do/footer/footer.html")
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                footerContainer.innerHTML = html;
                console.log("Footer cargado exitosamente");
                
                // Añadir eventos a los botones después de cargar
                document.querySelector('.suscribirme')?.addEventListener('click', () => {
                    // Lógica para suscripción
                });
                
                document.querySelector('.ini_secion')?.addEventListener('click', () => {
                    window.location.href = "/PI2do/inicio sesion/login.html";
                });
            })
            .catch(error => {
                console.error("Error al cargar el footer:", error);
                // Cargar versión de respaldo si falla
                footerContainer.innerHTML = `
                    <div class="footer-error">
                        <p>© 2025 PRODCONS. Todos los derechos reservados.</p>
                    </div>
                `;
            });
    }
});