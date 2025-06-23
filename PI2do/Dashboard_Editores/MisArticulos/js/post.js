<<<<<<< HEAD
// Validación del formulario en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const tituloInput = document.querySelector('input[name="titulo"]');
    const contenidoInput = document.querySelector('textarea[name="contenido"]');
    const imagenInput = document.querySelector('input[name="imagenes[]"]');
    const previewContainer = document.getElementById('preview-container');

    // Validación del título
    tituloInput.addEventListener('input', function() {
        if (this.value.length < 5) {
            this.setCustomValidity('El título debe tener al menos 5 caracteres');
        } else if (this.value.length > 100) {
            this.setCustomValidity('El título no debe exceder los 100 caracteres');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validación del contenido
    contenidoInput.addEventListener('input', function() {
        if (this.value.length < 50) {
            this.setCustomValidity('El contenido debe tener al menos 50 caracteres');
        } else {
            this.setCustomValidity('');
        }
    });

    // Previsualización de imágenes
    imagenInput.addEventListener('change', function() {
        previewContainer.innerHTML = ''; // Limpiar previsualizaciones anteriores
        
        if (this.files) {
            Array.from(this.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    const preview = document.createElement('div');
                    preview.className = 'preview-item';
                    
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <img src="${e.target.result}" alt="Vista previa">
                            <p>${file.name}</p>
                            <p>${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        `;
                    };
                    
                    reader.readAsDataURL(file);
                    previewContainer.appendChild(preview);
                }
            });
        }
    });

    // Confirmación de eliminación
    window.confirmarEliminar = function(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este post?')) {
            window.location.href = `eliminar-post.php?id=${id}`;
        }
    };

    // Validación del formulario antes de enviar
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validar título
        if (tituloInput.value.length < 5 || tituloInput.value.length > 100) {
            isValid = false;
            alert('El título debe tener entre 5 y 100 caracteres');
        }
        
        // Validar contenido
        if (contenidoInput.value.length < 50) {
            isValid = false;
            alert('El contenido debe tener al menos 50 caracteres');
        }
        
        // Validar imágenes
        if (imagenInput.files.length > 0) {
            Array.from(imagenInput.files).forEach(file => {
                if (!file.type.startsWith('image/')) {
                    isValid = false;
                    alert('Solo se permiten archivos de imagen');
                }
                if (file.size > 5 * 1024 * 1024) { // 5MB
                    isValid = false;
                    alert('Las imágenes no deben superar 5MB');
                }
            });
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
=======
// Validación del formulario en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const tituloInput = document.querySelector('input[name="titulo"]');
    const contenidoInput = document.querySelector('textarea[name="contenido"]');
    const imagenInput = document.querySelector('input[name="imagenes[]"]');
    const previewContainer = document.getElementById('preview-container');

    // Validación del título
    tituloInput.addEventListener('input', function() {
        if (this.value.length < 5) {
            this.setCustomValidity('El título debe tener al menos 5 caracteres');
        } else if (this.value.length > 100) {
            this.setCustomValidity('El título no debe exceder los 100 caracteres');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validación del contenido
    contenidoInput.addEventListener('input', function() {
        if (this.value.length < 50) {
            this.setCustomValidity('El contenido debe tener al menos 50 caracteres');
        } else {
            this.setCustomValidity('');
        }
    });

    // Previsualización de imágenes
    imagenInput.addEventListener('change', function() {
        previewContainer.innerHTML = ''; // Limpiar previsualizaciones anteriores
        
        if (this.files) {
            Array.from(this.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    const preview = document.createElement('div');
                    preview.className = 'preview-item';
                    
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <img src="${e.target.result}" alt="Vista previa">
                            <p>${file.name}</p>
                            <p>${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        `;
                    };
                    
                    reader.readAsDataURL(file);
                    previewContainer.appendChild(preview);
                }
            });
        }
    });

    // Confirmación de eliminación
    window.confirmarEliminar = function(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este post?')) {
            window.location.href = `eliminar-post.php?id=${id}`;
        }
    };

    // Validación del formulario antes de enviar
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validar título
        if (tituloInput.value.length < 5 || tituloInput.value.length > 100) {
            isValid = false;
            alert('El título debe tener entre 5 y 100 caracteres');
        }
        
        // Validar contenido
        if (contenidoInput.value.length < 50) {
            isValid = false;
            alert('El contenido debe tener al menos 50 caracteres');
        }
        
        // Validar imágenes
        if (imagenInput.files.length > 0) {
            Array.from(imagenInput.files).forEach(file => {
                if (!file.type.startsWith('image/')) {
                    isValid = false;
                    alert('Solo se permiten archivos de imagen');
                }
                if (file.size > 5 * 1024 * 1024) { // 5MB
                    isValid = false;
                    alert('Las imágenes no deben superar 5MB');
                }
            });
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
>>>>>>> main
}); 