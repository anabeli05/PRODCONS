document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCambioContrasena');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        // Validar que las nuevas contraseñas coincidan
        if (newPassword !== confirmPassword) {
            mostrarError('Las nuevas contraseñas no coinciden');
            return;
        }
        
        // Validar que la nueva contraseña sea diferente a la actual
        if (newPassword === currentPassword) {
            mostrarError('La nueva contraseña debe ser diferente a la actual');
            return;
        }
        
        // Validar fortaleza de la contraseña (opcional)
        if (newPassword.length < 8) {
            mostrarError('La contraseña debe tener al menos 8 caracteres');
            return;
        }
        
        // Aquí iría la lógica para enviar los datos al servidor
        cambiarContrasena(currentPassword, newPassword);
    });
    
    function cambiarContrasena(currentPass, newPass) {
        // Simulación de envío al servidor
        console.log('Enviando datos al servidor...');
        console.log('Contraseña actual:', currentPass);
        console.log('Nueva contraseña:', newPass);
        
        // Simular respuesta del servidor después de 1 segundo
        setTimeout(function() {
            // En un caso real, aquí procesarías la respuesta del servidor
            mostrarExito('Contraseña cambiada exitosamente');
            
            // Redirigir al login después de 2 segundos
            setTimeout(function() {
                window.location.href = 'login.html';
            }, 2000);
        }, 1000);
    }
    
    function mostrarError(mensaje) {
        // Crear elemento de error si no existe
        let errorDiv = document.querySelector('.error-message');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            form.insertBefore(errorDiv, form.firstChild);
        }
        
        errorDiv.textContent = mensaje;
        errorDiv.style.color = 'red';
        errorDiv.style.marginBottom = '15px';
        errorDiv.style.textAlign = 'center';
    }
    
    function mostrarExito(mensaje) {
        // Crear elemento de éxito si no existe
        let exitoDiv = document.querySelector('.success-message');
        if (!exitoDiv) {
            exitoDiv = document.createElement('div');
            exitoDiv.className = 'success-message';
            form.insertBefore(exitoDiv, form.firstChild);
        }
        
        exitoDiv.textContent = mensaje;
        exitoDiv.style.color = 'green';
        exitoDiv.style.marginBottom = '15px';
        exitoDiv.style.textAlign = 'center';
    }
});