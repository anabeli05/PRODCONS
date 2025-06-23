<<<<<<< HEAD
// Almacenamiento local para likes y comentarios
const STORAGE_KEY = 'blog_interactions';

// Cargar datos guardados o inicializar si no existen
let interactions = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {
    likes: {},
    comments: {}
};

// Guardar datos en localStorage
function saveInteractions() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(interactions));
}

// Función para dar like a un post
function likePost(postId) {
    const likesCount = document.getElementById(`likes-count-${postId}`);
    const likeButton = document.querySelector(`[onclick="likePost(${postId})"]`);
    
    if (!interactions.likes[postId]) {
        interactions.likes[postId] = 0;
    }
    
    if (likeButton.classList.contains('liked')) {
        interactions.likes[postId]--;
        likeButton.classList.remove('liked');
    } else {
        interactions.likes[postId]++;
        likeButton.classList.add('liked');
    }
    
    likesCount.textContent = interactions.likes[postId];
    saveInteractions();
}

// Función para mostrar/ocultar sección de comentarios
function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-section-${postId}`);
    const isHidden = commentsSection.style.display === 'none';
    
    commentsSection.style.display = isHidden ? 'block' : 'none';
    
    if (isHidden) {
        displayComments(postId);
    }
}

// Función para mostrar comentarios existentes
function displayComments(postId) {
    const commentsContainer = document.getElementById(`existing-comments-${postId}`);
    const comments = interactions.comments[postId] || [];
    
    const commentsHtml = comments.map(comment => `
        <div class="comment">
            <div class="comment-author">Usuario Anónimo</div>
            <div class="comment-text">${escapeHtml(comment.text)}</div>
            <div class="comment-date">${new Date(comment.date).toLocaleString()}</div>
        </div>
    `).join('');
    
    commentsContainer.innerHTML = commentsHtml;
    updateCommentsCount(postId);
}

// Función para agregar un nuevo comentario
function addComment(postId) {
    const commentInput = document.getElementById(`comment-input-${postId}`);
    const commentText = commentInput.value.trim();
    
    if (!commentText) return;
    
    if (!interactions.comments[postId]) {
        interactions.comments[postId] = [];
    }
    
    interactions.comments[postId].push({
        text: commentText,
        date: new Date().toISOString()
    });
    
    saveInteractions();
    displayComments(postId);
    commentInput.value = '';
}

// Función para actualizar el contador de comentarios
function updateCommentsCount(postId) {
    const commentsCount = document.getElementById(`comments-count-${postId}`);
    const count = (interactions.comments[postId] || []).length;
    commentsCount.textContent = count;
}

// Función para escapar HTML y prevenir XSS
function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return String(unsafe)
        .replace(/&/g, '&amp;')
        .replace(/</g, '<')
        .replace(/>/g, '>')
        .replace(/"/g, '"')
        .replace(/'/g, '&#039;');
}

// Inicializar contadores al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar likes
    for (let i = 1; i <= 6; i++) {
        const likesCount = document.getElementById(`likes-count-${i}`);
        const likeButton = document.querySelector(`[onclick="likePost(${i})"]`);
        
        if (interactions.likes[i]) {
            likesCount.textContent = interactions.likes[i];
            if (interactions.likes[i] > 0) {
                likeButton.classList.add('liked');
            }
        }
        
        // Inicializar contadores de comentarios
        updateCommentsCount(i);
    }
});
=======
// Almacenamiento local para likes y comentarios
const STORAGE_KEY = 'blog_interactions';

// Cargar datos guardados o inicializar si no existen
let interactions = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {
    likes: {},
    comments: {}
};

// Guardar datos en localStorage
function saveInteractions() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(interactions));
}

// Función para dar like a un post
function likePost(postId) {
    const likesCount = document.getElementById(`likes-count-${postId}`);
    const likeButton = document.querySelector(`[onclick="likePost(${postId})"]`);
    
    if (!interactions.likes[postId]) {
        interactions.likes[postId] = 0;
    }
    
    if (likeButton.classList.contains('liked')) {
        interactions.likes[postId]--;
        likeButton.classList.remove('liked');
    } else {
        interactions.likes[postId]++;
        likeButton.classList.add('liked');
    }
    
    likesCount.textContent = interactions.likes[postId];
    saveInteractions();
}

// Función para mostrar/ocultar sección de comentarios
function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-section-${postId}`);
    const isHidden = commentsSection.style.display === 'none';
    
    commentsSection.style.display = isHidden ? 'block' : 'none';
    
    if (isHidden) {
        displayComments(postId);
    }
}

// Función para mostrar comentarios existentes
function displayComments(postId) {
    const commentsContainer = document.getElementById(`existing-comments-${postId}`);
    const comments = interactions.comments[postId] || [];
    
    const commentsHtml = comments.map(comment => `
        <div class="comment">
            <div class="comment-author">Usuario Anónimo</div>
            <div class="comment-text">${escapeHtml(comment.text)}</div>
            <div class="comment-date">${new Date(comment.date).toLocaleString()}</div>
        </div>
    `).join('');
    
    commentsContainer.innerHTML = commentsHtml;
    updateCommentsCount(postId);
}

// Función para agregar un nuevo comentario
function addComment(postId) {
    const commentInput = document.getElementById(`comment-input-${postId}`);
    const commentText = commentInput.value.trim();
    
    if (!commentText) return;
    
    if (!interactions.comments[postId]) {
        interactions.comments[postId] = [];
    }
    
    interactions.comments[postId].push({
        text: commentText,
        date: new Date().toISOString()
    });
    
    saveInteractions();
    displayComments(postId);
    commentInput.value = '';
}

// Función para actualizar el contador de comentarios
function updateCommentsCount(postId) {
    const commentsCount = document.getElementById(`comments-count-${postId}`);
    const count = (interactions.comments[postId] || []).length;
    commentsCount.textContent = count;
}

// Función para escapar HTML y prevenir XSS
function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return String(unsafe)
        .replace(/&/g, '&amp;')
        .replace(/</g, '<')
        .replace(/>/g, '>')
        .replace(/"/g, '"')
        .replace(/'/g, '&#039;');
}

// Inicializar contadores al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar likes
    for (let i = 1; i <= 6; i++) {
        const likesCount = document.getElementById(`likes-count-${i}`);
        const likeButton = document.querySelector(`[onclick="likePost(${i})"]`);
        
        if (interactions.likes[i]) {
            likesCount.textContent = interactions.likes[i];
            if (interactions.likes[i] > 0) {
                likeButton.classList.add('liked');
            }
        }
        
        // Inicializar contadores de comentarios
        updateCommentsCount(i);
    }
});
>>>>>>> main
