// Función para obtener el ID del usuario
window.getUserId = function() {
    try {
        const userIdElement = document.getElementById('userId');
        if (!userIdElement) {
            throw new Error('Elemento userId no encontrado');
        }
        return userIdElement.value;
    } catch (error) {
        console.error('Error al obtener el ID del usuario:', error);
        return '';
    }
};

// Función para obtener el ID del artículo
window.getArticleId = function() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const articleId = parseInt(urlParams.get('ID_Articulo'));
        if (!isNaN(articleId) && articleId > 0) {
            return articleId;
        }
        console.error('No se pudo obtener un ID del artículo válido');
        return null;
    } catch (error) {
        console.error('Error al obtener el ID del artículo:', error);
        return null;
    }
};

// Función para verificar el estado del like
window.checkLikeStatus = async function(articleId) {
    try {
        const response = await fetch('/PRODCONS/PI2do/postWeb/check-like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                ID_Articulo: articleId,
                Usuario_ID: window.getUserId()
            })
        });
        const data = await response.json();
        return {
            success: data.success,
            usuario_ha_liked: data.liked,
            total_likes: data.total_likes || 0
        };
    } catch (error) {
        console.error('Error al verificar el estado del like:', error);
        return { 
            success: false,
            usuario_ha_liked: false,
            total_likes: 0 
        };
    }
};

// Función para alternar el like
window.toggleLike = async function(articleId) {
    try {
        const userId = window.getUserId();
        if (!userId) {
            alert('Debes iniciar sesión para dar like');
            return;
        }

        const likeButton = document.getElementById('likeButton');
        if (!likeButton) {
            throw new Error('Botón de like no encontrado');
        }

        // Verificar si ya le ha dado like
        const hasLiked = likeButton.getAttribute('data-has-liked') === 'true';
        const currentCount = parseInt(likeButton.getAttribute('data-likes-count') || 0);
        const newLikedState = !hasLiked;

        try {
            // Guardar estado actual antes de la petición
            const currentState = hasLiked;
            
            const response = await fetch('/PRODCONS/PI2do/postWeb/likes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    ID_Articulo: articleId,
                    Usuario_ID: userId,
                    hasLiked: newLikedState
                })
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.statusText);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al actualizar el like');
            }

            // Solo actualizar UI después de confirmación del servidor
            likeButton.setAttribute('data-has-liked', newLikedState);
            likeButton.classList.toggle('liked', newLikedState);
            
            const svg = likeButton.querySelector('svg');
            const countSpan = likeButton.querySelector('span');
            
            if (svg) {
                svg.style.fill = newLikedState ? '#e74c3c' : '#666';
            }

            // Actualizar el contador con el valor real del servidor
            const newTotal = data.total_likes;
            if (countSpan) {
                countSpan.textContent = newTotal;
            }
            likeButton.setAttribute('data-likes-count', newTotal);

        } catch (error) {
            console.error('Error en la petición:', error);
            
            // Mantener el estado original en caso de error
            likeButton.setAttribute('data-has-liked', currentState);
            likeButton.classList.toggle('liked', currentState);
            
            const svg = likeButton.querySelector('svg');
            const countSpan = likeButton.querySelector('span');
            
            if (svg) {
                svg.style.fill = currentState ? '#e74c3c' : '#666';
            }
            if (countSpan) {
                countSpan.textContent = currentCount;
            }
            likeButton.setAttribute('data-likes-count', currentCount);
            
            console.error('Error al alternar like:', error);
            alert('No se pudo procesar la acción. Por favor, inténtalo de nuevo.');
        }

    } catch (error) {
        console.error('Error al alternar like:', error);
        alert('Error al alternar like: ' + error.message);
    }
};

// Función para establecer el estado inicial del like y favorito
window.setInitialLikeState = async function() {
    try {
        const articleId = window.getArticleId();
        if (!articleId) {
            console.error('No se pudo obtener el ID del artículo');
            return;
        }

        // Verificar estado del like
        const likeStatus = await window.checkLikeStatus(articleId);
        if (likeStatus.success) {
            const likeButton = document.getElementById('likeButton');
            if (likeButton) {
                likeButton.setAttribute('data-has-liked', likeStatus.usuario_ha_liked);
                likeButton.classList.toggle('liked', likeStatus.usuario_ha_liked);
                
                const svg = likeButton.querySelector('svg');
                const countSpan = likeButton.querySelector('span');
                
                if (svg) {
                    svg.style.fill = likeStatus.usuario_ha_liked ? '#e74c3c' : '#666';
                }
                if (countSpan) {
                    countSpan.textContent = likeStatus.total_likes;
                }
            }
        }

        // Verificar estado del favorito
        const response = await fetch('/PRODCONS/PI2do/postWeb/check-favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                ID_Articulo: articleId,
                Usuario_ID: window.getUserId()
            })
        });
        const favoriteData = await response.json();

        if (favoriteData.success) {
            const favoriteButton = document.getElementById('favoriteButton');
            if (favoriteButton) {
                favoriteButton.setAttribute('data-is-favorited', favoriteData.favorited);
                favoriteButton.classList.toggle('favorited', favoriteData.favorited);
                
                const svg = favoriteButton.querySelector('svg');
                if (svg) {
                    svg.style.fill = favoriteData.favorited ? '#e74c3c' : '#666';
                }
            }
        }
    } catch (error) {
        console.error('Error al establecer el estado inicial:', error);
    }
};

// Función para alternar el estado de favorito
window.toggleFavorite = async function(articleId) {
    try {
        const userId = window.getUserId();
        if (!userId) {
            alert('Debes iniciar sesión para marcar como favorito');
            return;
        }

        const favoriteButton = document.getElementById('favoriteButton');
        if (!favoriteButton) {
            throw new Error('Botón de favorito no encontrado');
        }

        // Mostrar spinner
        const spinner = document.createElement('div');
        spinner.className = 'spinner-border';
        spinner.setAttribute('role', 'status');
        spinner.innerHTML = '<span class="visually-hidden">Procesando...</span>';
        
        // Mantener la estructura del botón
        const elements = favoriteButton.querySelectorAll('svg, span');
        if (elements.length === 2) {
            favoriteButton.innerHTML = '';
            elements.forEach(element => favoriteButton.appendChild(element));
            favoriteButton.appendChild(spinner);
        }

        // Verificar si ya está marcado como favorito
        const isFavorited = favoriteButton.getAttribute('data-is-favorited') === 'true';
        const newFavoritedState = !isFavorited;

        try {
            const response = await fetch('/PRODCONS/PI2do/postWeb/toggle-favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    articleId: articleId,
                    userId: userId,
                    isFavorited: newFavoritedState
                })
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.statusText);
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.error || 'Error al actualizar el favorito');
            }

        } catch (error) {
            console.error('Error al actualizar el favorito:', error);
            // Revertir todos los cambios en UI si falla la actualización
            favoriteButton.setAttribute('data-is-favorited', isFavorited);
            favoriteButton.classList.toggle('favorited', isFavorited);
            const svg = favoriteButton.querySelector('svg');
            if (svg) {
                svg.style.fill = isFavorited ? '#e74c3c' : '#666';
            }
            
            alert('Error al actualizar el estado de favorito: ' + error.message);
        }

        // Actualizar el estado del botón
        favoriteButton.setAttribute('data-is-favorited', newFavoritedState);
        favoriteButton.innerHTML = isFavorited ? '☆' : '★';
        
        // Cambiar el color del botón
        favoriteButton.style.color = isFavorited ? '#e74c3c' : '#3498db';
        
        alert(isFavorited ? '¡Has marcado como favorito!' : '¡Has removido de favoritos!');

    } catch (error) {
        console.error('Error al alternar favorito:', error);
        alert('Error al alternar favorito: ' + error.message);
        
        // Restaurar el estado del botón
        favoriteButton.disabled = false;
        favoriteButton.style.opacity = '1';
        favoriteButton.style.cursor = 'pointer';
    }
};

// Función para manejar comentarios
window.handleComments = function(e) {
    try {
        const button = e ? e.currentTarget : null;
        const articleId = window.getArticleId();
        const userId = window.getUserId();
        
        if (!articleId) throw new Error('No se pudo obtener el ID del artículo');
        if (!userId) {
            alert('Debes estar logueado para comentar');
            return;
        }

        const commentsSection = document.getElementById('commentsSection');
        if (!commentsSection) throw new Error('No se encontró la sección de comentarios');

        const isCurrentlyHidden = commentsSection.style.display === 'none' || !commentsSection.style.display;
        
        if (isCurrentlyHidden) {
            // Mostrar sección de comentarios
            commentsSection.style.display = 'block';
            commentsSection.classList.add('visible');
            
            // Cargar comentarios
            window.loadComments(articleId);
            
            // Actualizar texto del botón
            if (button) {
                const svg = button.querySelector('svg');
                const span = button.querySelector('span');
                button.innerHTML = '';
                if (svg) button.appendChild(svg);
                if (span) button.appendChild(span);
                button.appendChild(document.createTextNode(' Ocultar comentarios'));
            }

            // Esperar un momento para que el DOM se actualice
            setTimeout(() => {
                // Desplazamiento suave al formulario de comentarios
                const commentForm = document.getElementById('commentForm');
                if (commentForm) {
                    commentForm.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center'
                    });
                    
                    // Enfocar el área de texto
                    const textarea = document.getElementById('commentText');
                    if (textarea) {
                        textarea.focus();
                    }
                }
            }, 300); // Pequeño retraso para asegurar que todo esté renderizado
        } else {
            commentsSection.style.display = 'none';
            commentsSection.classList.remove('visible');
            
            // Actualizar texto del botón
            if (button) {
                const svg = button.querySelector('svg');
                const span = button.querySelector('span');
                button.innerHTML = '';
                if (svg) button.appendChild(svg);
                if (span) button.appendChild(span);
                button.appendChild(document.createTextNode(' Ver comentarios'));
            }
        }

    } catch (error) {
        console.error('Error en handleComments:', error);
        alert('Error al manejar los comentarios: ' + error.message);
    }
};

// Función para enviar un comentario
window.submitComment = function(e) {
    try {
        e.preventDefault();
        
        const articleId = window.getArticleId();
        const userId = window.getUserId();
        
        if (!articleId) {
            console.error('No se pudo obtener el ID del artículo');
            return;
        }

        if (!userId) {
            alert('Debes estar logueado para comentar');
            return;
        }

        const commentText = document.getElementById('commentText').value.trim();
        if (!commentText) {
            alert('Por favor, ingresa un comentario');
            return;
        }

        // Deshabilitar el formulario mientras se envía
        const form = document.getElementById('commentForm');
        const submitButton = form.querySelector('button[type="submit"]');
        const textarea = document.getElementById('commentText');
        
        submitButton.disabled = true;
        textarea.disabled = true;
        
        // Mostrar spinner en el botón
        const originalButtonText = submitButton.innerHTML;
        submitButton.innerHTML = `
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Enviando...</span>
            </div>
            Enviando...
        `;

        fetch('/PRODCONS/PI2do/postWeb/comentarios.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                ID_Articulo: articleId,
                Usuario_ID: userId,
                Comentario: commentText
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    // Limpiar el formulario
                    textarea.value = '';
                    
                    // Mostrar mensaje de éxito temporalmente
                    submitButton.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                        </svg>
                        ¡Comentario publicado!
                    `;
                    
                    // Recargar comentarios
                    window.loadComments(articleId);
                    
                    // Restaurar el botón después de 2 segundos
                    setTimeout(() => {
                        submitButton.innerHTML = originalButtonText;
                        submitButton.disabled = false;
                        textarea.disabled = false;
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Error al publicar el comentario');
                }
            } catch (parseError) {
                console.error('Error al parsear JSON:', parseError);
                throw new Error('Error al procesar la respuesta del servidor');
            }
        })
        .catch(error => {
            console.error('Error al enviar el comentario:', error);
            alert('Error al enviar el comentario: ' + error.message);
            
            // Restaurar el formulario
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
            textarea.disabled = false;
        });
    } catch (error) {
        console.error('Error en submitComment:', error);
        alert('Error al enviar el comentario: ' + error.message);
    }
};

// Función para cargar comentarios
window.loadComments = function(articleId) {
    try {
        const commentsList = document.getElementById('commentsList');
        if (commentsList) {
            commentsList.innerHTML = '<div class="loading-comments">Cargando comentarios...</div>';
        }

        fetch('/PRODCONS/PI2do/postWeb/get-comments.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ ID_Articulo: articleId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            console.log('Respuesta del servidor:', text); // Debug
            try {
                const data = JSON.parse(text);
                
                if (data.success) {
                    const commentsList = document.getElementById('commentsList');
                    const noComments = document.getElementById('noComments');
                    
                    if (commentsList) {
                        commentsList.innerHTML = '';
                        
                        if (data.comments && data.comments.length > 0) {
                            data.comments.forEach(comment => {
                                const commentElement = document.createElement('div');
                                commentElement.className = 'comment';
                                commentElement.innerHTML = `
                                    <div class="comment-header">
                                        <div class="comment-author">${comment.Usuario_Nombre || 'Usuario Anónimo'}</div>
                                        <div class="comment-date">${comment.Fecha || ''}</div>
                                    </div>
                                    <div class="comment-text">${comment.Comentario || ''}</div>
                                `;
                                commentsList.appendChild(commentElement);
                            });
                            
                            if (noComments) {
                                noComments.style.display = 'none';
                            }
                        } else {
                            if (noComments) {
                                noComments.style.display = 'block';
                            }
                        }
                        
                        // Update comment counter
                        const commentsCountDisplay = document.getElementById('commentsCountDisplay');
                        const commentCount = document.getElementById('commentCount');
                        const totalComments = data.comments ? data.comments.length : 0;
                        
                        if (commentsCountDisplay) {
                            commentsCountDisplay.textContent = `${totalComments} comentario${totalComments !== 1 ? 's' : ''}`;
                        }
                        
                        if (commentCount) {
                            commentCount.textContent = totalComments;
                        }
                    }
                } else {
                    console.error('Error al cargar los comentarios:', data.message);
                    if (commentsList) {
                        commentsList.innerHTML = '<div class="error-message">Error al cargar los comentarios</div>';
                    }
                }
            } catch (parseError) {
                console.error('Error al parsear JSON:', parseError);
                console.error('Respuesta recibida:', text);
                if (commentsList) {
                    commentsList.innerHTML = '<div class="error-message">Error al procesar la respuesta del servidor</div>';
                }
            }
        })
        .catch(error => {
            console.error('Error al cargar los comentarios:', error);
            const commentsList = document.getElementById('commentsList');
            if (commentsList) {
                commentsList.innerHTML = '<div class="error-message">Error de conexión</div>';
            }
        });
    } catch (error) {
        console.error('Error en loadComments:', error);
    }
};

// Función para actualizar los botones de idiomas
window.updateLanguageButtons = function() {
    try {
        const btnEn = document.getElementById('btnEn');
        const btnEs = document.getElementById('btnEs');
        
        if (!btnEn || !btnEs) {
            console.error('Botones de idiomas no encontrados');
            return;
        }

        // Establecer eventos para los botones de idiomas
        btnEn.addEventListener('click', function() {
            window.location.href = window.location.pathname + '?lang=en';
        });

        btnEs.addEventListener('click', function() {
            window.location.href = window.location.pathname + '?lang=es';
        });

        // Establecer el botón activo según el idioma actual
        const urlParams = new URLSearchParams(window.location.search);
        const lang = urlParams.get('lang') || 'es';
        
        if (lang === 'en') {
            btnEn.classList.add('active');
            btnEs.classList.remove('active');
        } else {
            btnEn.classList.remove('active');
            btnEs.classList.add('active');
        }

    } catch (error) {
        console.error('Error al actualizar los botones de idiomas:', error);
    }
};

// Función principal de inicialización
window.initInteractions = async function() {
    try {
        // Esperar un momento para asegurar que todos los elementos estén listos
        await new Promise(resolve => setTimeout(resolve, 100));
        
        const articleId = window.getArticleId();
        if (!articleId) {
            console.error('No se pudo obtener el ID del artículo');
            return;
        }

        // Inicializar botones de comentarios
        const commentButtons = document.querySelectorAll('.comment-toggle');
        commentButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                window.handleComments(e);
            });
        });

        // Inicializar formulario de comentarios
        const commentForm = document.getElementById('commentForm');
        if (commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                window.submitComment(e);
            });
        }

        // Inicializar botón de like
        const likeButton = document.getElementById('likeButton');
        if (likeButton) {
            likeButton.addEventListener('click', async function(e) {
                e.preventDefault();
                await window.toggleLike(articleId);
            });
        }

        // Inicializar botón de favorito
        const favoriteButton = document.getElementById('favoriteButton');
        if (favoriteButton) {
            favoriteButton.addEventListener('click', async function(e) {
                e.preventDefault();
                await window.toggleFavorite(articleId);
            });
        }

        // Establecer estado inicial del like
        await window.setInitialLikeState();
        
        // Cargar comentarios iniciales
        window.loadComments(articleId);

        console.log('Interacciones inicializadas correctamente');

    } catch (error) {
        console.error('Error al inicializar:', error);
        throw error;
    }
};