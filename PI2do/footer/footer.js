document.addEventListener("DOMContentLoaded", function() {
    const footerContainer = document.querySelector('.footer-container');
    
    if (footerContainer && !footerContainer.hasChildNodes()) {
        console.log("Cargando footer dinámicamente...");
        
        fetch("/PI2do/footer/footer.html")
            .then(response => {
                if (!response.ok) throw new Error(`Error HTTP! estado: ${response.status}`);
                return response.text();
            })
            .then(html => {
                // Insertar el HTML y luego cargar el CSS dinámicamente
                footerContainer.innerHTML = html;
                loadFooterCSS();
                console.log("Footer y CSS cargados exitosamente");

                // Añadir eventos a los botones (opcional)
                setupFooterButtons();
            })
            .catch(error => {
                console.error("Error al cargar el footer:", error);
                footerContainer.innerHTML = `
                    <div class="footer-error">
                        <p>© ${new Date().getFullYear()} PRODCONS. Todos los derechos reservados.</p>
                    </div>
                `;
            });
    }

    function loadFooterCSS() {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = '/PI2do/footer/footer.css';
        document.head.appendChild(link);
    }

    function setupFooterButtons() {
        // Evento para el botón de registro
        document.querySelector('.btn-registro')?.addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = "/PI2do/registro/registro.html";
        });

        // Evento para el botón de inicio de sesión
        document.querySelector('.btn-login')?.addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = "/PI2do/inicio_sesion/login.html";
        });
    }
});