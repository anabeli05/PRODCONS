// API key para Google Cloud Translation
const API_KEY = 'AIzaSyBjze7ZlB-8YXWrH8vHR6wdEU-7Zm1iDNM';
let currentLanguage = 'es';

// Elementos a traducir
const translatableElements = [
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'a', 'span', 'li'
];

// Función para obtener todos los textos traducibles
function getTranslatableContent() {
    const elements = {};
    translatableElements.forEach(tag => {
        document.querySelectorAll(tag).forEach((el, i) => {
            // Ignorar elementos sin contenido o con solo espacios
            if (el.innerText && el.innerText.trim() && !el.hasAttribute('data-no-translate')) {
                // Usar un ID único para cada elemento
                const id = `${tag}-${i}`;
                elements[id] = {
                    element: el,
                    text: el.innerText
                };
            }
        });
    });
    return elements;
}

// Función para traducir el contenido usando Google Cloud Translation API
async function translateContent(targetLanguage) {
    if (currentLanguage === targetLanguage) return;
    
    // Mostrar indicador de carga
    const loadingIndicator = document.createElement('div');
    loadingIndicator.id = 'translation-loading';
    loadingIndicator.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255,255,255,0.9); padding: 20px; border-radius: 10px; z-index: 9999; text-align: center; box-shadow: 0 0 10px rgba(0,0,0,0.2);';
    loadingIndicator.innerHTML = '<p style="margin: 0;">Traduciendo contenido, por favor espere...</p>';
    document.body.appendChild(loadingIndicator);
    
    const elements = getTranslatableContent();
    const textsToTranslate = Object.values(elements).map(item => item.text);
    
    try {
        // URL para la API de Google Cloud Translation
        const url = `https://translation.googleapis.com/language/translate/v2?key=${API_KEY}`;
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                q: textsToTranslate,
                target: targetLanguage,
                source: currentLanguage
            })
        });
        
        const data = await response.json();
        
        if (data.data && data.data.translations) {
            Object.keys(elements).forEach((id, index) => {
                elements[id].element.innerText = data.data.translations[index].translatedText;
            });
            
            currentLanguage = targetLanguage;
            // Actualizar el atributo lang de HTML
            document.documentElement.lang = targetLanguage;
            
            // Guardar la preferencia de idioma en localStorage
            localStorage.setItem('preferredLanguage', targetLanguage);
        }
    } catch (error) {
        console.error('Error al traducir:', error);
        alert('Error al traducir el contenido. Por favor, inténtalo de nuevo más tarde.');
    } finally {
        // Eliminar indicador de carga
        const loadingElement = document.getElementById('translation-loading');
        if (loadingElement) {
            loadingElement.remove();
        }
    }
}

// Cargar el idioma preferido del usuario al iniciar la página
document.addEventListener('DOMContentLoaded', () => {
    const savedLanguage = localStorage.getItem('preferredLanguage');
    if (savedLanguage) {
        translateContent(savedLanguage);
    }
}); 