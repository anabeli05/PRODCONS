<?php
session_start();
var_dump($_SESSION); // <-- Esto mostrará el contenido de la sesión
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] != 'Usuario') {
    header("Location: /PRODCONS/inicio_sesion/login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODCONS</title>
    <link rel="stylesheet" href="/PRODCONS/styles.css">
    <link rel="stylesheet" href="/PRODCONS/footer/footer.css">
    <link rel="stylesheet" href="articulos.css">
    <link href="/PRODCONS/Header visitantes/barra_principal.css" rel="stylesheet">
    <!-- Google Cloud Translation API -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <!-- Script de traducción global -->
    <script src="/PRODCONS/traslate.js"></script>
    <style>
        /* Estilos para la bandera de idioma */
        #banderaIdioma {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid black;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        #banderaIdioma:hover {
            transform: scale(1.1);
        }
        
        /* Estilos para likes y comentarios */
        .post-footer {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        
        .interaction-buttons {
            display: flex;
            margin-left: 15px;
            align-items: center;
        }
        
        .like-button-small, .comment-toggle-small {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0;
            margin: 0 5px;
            color: #666;
            font-size: 12px;
        }
        
        .like-button-small svg, .comment-toggle-small svg {
            margin-right: 3px;
        }
        
        .like-button-small:hover, .like-button-small.liked {
            color: #e74c3c;
        }
        
        .comment-toggle-small:hover {
            color: #3498db;
        }
        
        .comments-section {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
        }
        
        .existing-comments {
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 10px;
        }
        
        .comment {
            background-color: white;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 8px;
        }
        
        .comment-author {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .comment-date {
            font-size: 0.8em;
            color: #777;
        }
        
        .add-comment textarea {
            width: 100%;
            min-height: 60px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
            resize: vertical;
        }
        
        .add-comment button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .add-comment button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <header>
        <div class="header-contenedor">
            <div class="principal">
                <a class="navlink" href="/PRODCONS/empresas_responsables/empresasr.html">EMPRESAS RESPONSABLES</a>

                <!-- Bandera para cambiar idioma (alterna con cada clic) -->
                <div id="idiomaToggle">
                    <img class="españa" id="banderaIdioma" src="/PRODCONS/imagenes/logos/espanol.png" alt="Idioma" onclick="alternarIdioma()">
                </div>
            </div>
        </div>
    </header>

    <section class="logo">
        <div class="header_2">
            <svg class="hamburguesa" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round" width="36" height="36" stroke-width="1.5" onclick="toggleMenu()">
                <path d="M4 6l16 0"></path>
                <path d="M4 12l16 0"></path>
                <path d="M4 18l16 0"></path>
            </svg>

            <img class="prodcons" src="/PRODCONS/imagenes/prodcon/logoSinfondo.png" alt="Logo">

            <div class="subtitulos">
                <li><a href="/PRODCONS/pr/produccionr.html">PRODUCCIÓN RESPONSABLE</a></li>
                <li><a href="/PRODCONS/cr/consumores.html">CONSUMO RESPONSABLE</a></li>

                <form class="search-form">
                    <button class="lupa">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="36" height="36"
                            stroke-width="1.5">
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                            <path d="M21 21l-6 -6"></path>
                        </svg>
                    </button>
                    <input type="search" placeholder="Buscar..." />
                </form>
            </div>
        </div>
    </section>


    <!-- Menú de hamburguesa -->
    <div class="menu" id="menu">
        <ul>
            <li><a href="/PRODCONS/pr/produccionr.html" onclick="setActive(this)">PRODUCCION RESPONSABLE</a></li>
            <li><a href="/PRODCONS/cr/consumores.html" onclick="setActive(this)">CONSUMO RESPONSABLE</a></li>
            <li><a href="#">Lupa de búsqueda</a></li>
        </ul>
    </div>

    <script>
        // Toggle para la visibilidad del menú de hamburguesa
        function toggleMenu() {
            const menu = document.getElementById("menu");
            menu.classList.toggle("active");
        }

        // Función para alternar entre idiomas con un solo clic
        function alternarIdioma() {
            const bandera = document.getElementById('banderaIdioma');
            const idiomaActual = bandera.src.includes('ingles.png') ? 'ingles' : 'espanol';
            const nuevoIdioma = idiomaActual === 'ingles' ? 'espanol' : 'ingles';
            
            // Cambiar la imagen de la bandera inmediatamente
            bandera.src = nuevoIdioma === 'ingles' 
                ? "/PRODCONS/imagenes/logos/ingles.png" 
                : "/PRODCONS/imagenes/logos/espanol.png";
            
            // Realizar la traducción
            translateContent(nuevoIdioma === 'ingles' ? 'en' : 'es');
            
            // Guardar la preferencia en localStorage
            localStorage.setItem('preferredLanguage', nuevoIdioma === 'ingles' ? 'en' : 'es');
        }
        
        // Cargar el idioma guardado al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            const savedLanguage = localStorage.getItem('preferredLanguage');
            if (savedLanguage) {
                const bandera = document.getElementById('banderaIdioma');
                bandera.src = savedLanguage === 'en' 
                    ? "/PRODCONS/imagenes/logos/ingles.png" 
                    : "/PRODCONS/imagenes/logos/espanol.png";
            }
        });
    </script>


    <div class="sobrecont">
        <div class="cuadro-info">
            <h2>BLOG</h2>
            <p>Somos una organización dedicada a cuidar del medio ambiente, aplicándolo en nuestra vida diaria y
                promoviendo a los demás a hacerlo para el bienestar de todos.</p>
        </div>
        <img class="imagen-principal" src="/PRODCONS/imagenes/tractor.png" alt="Imagen Principal">
    </div>


    <h3 class="apubli"> MIRA MAS DE NUESTRO CONTENIDO </h3>


    <section class="post-list">
        <div class="content">
            <article class="post">
                <div class="post-header">
                    <div class="post-img-1"></div> 
                </div>
                <div class="post-body">
                    <h2>Menos plásticos mas vida</h2>
                    <p class="descripcion">El plástico nos rodea: en casa, en tiendas y hasta en los océanos. Con pequeñas decisiones, podemos reducir su uso y hacer la diferencia. ¿Listo para cambiar hábitos y ayudar al planeta? </p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo1.html" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(1)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-1">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(1)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-1">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-1" style="display: none;">
                        <div class="existing-comments" id="existing-comments-1">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-1" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(1)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 14 de febrero del 2025 </span>
                    <span>| Juan Pablo Mancilla Rodriguez</span>
                </div>
            </article>

            <article class="post">
                <div class="post-header">
                    <div class="post-img-2"></div>
                </div>
                <div class="post-body">
                    <h2>Tu puedes hacer la diferencia</h2>
                    <p>Cada elección cuenta. Adoptar hábitos más sostenibles en el día a día no solo reduce nuestra huella ecológica, sino que inspira un cambio real en la sociedad. ¿Te animas a dar el primer paso?</p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo2.html" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(2)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-2">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(2)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-2">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-2" style="display: none;">
                        <div class="existing-comments" id="existing-comments-2">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-2" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(2)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 19 de Febrero del 2025 </span>
                    <span>| Yureni Elizabeth Sierra Aguilar </span>
                </div>
                <div>

                    
                </div>

                
            </article>

            <article class="post">
                <div class="post-header">
                    <div class="post-img-3"></div>
                </div>
                <div class="post-body">
                    <h2>La Revolución de la Moda Sostenible </h2>
                    <p class="descripcion">La industria de la moda es poderosa, pero también contaminante. Apostar por opciones sostenibles es clave para un futuro más limpio. ¿Sabes cómo tu ropa puede marcar la diferencia?</p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo3.html" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(3)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-3">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(3)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-3">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-3" style="display: none;">
                        <div class="existing-comments" id="existing-comments-3">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-3" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(3)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 19 de Febrero del 2025</span>
                    <span> | Daniel Sahid Barroso Alvarez </span>
                </div>
            </article>

            <article class="post">
                <div class="post-header">
                    <div class="post-img-4"></div>
                </div>
                <div class="post-body">

                    <h2>Crea tu propio huerto y sus ventajas</h2>
                    <p class="descripcion">Cultivar tus propios alimentos te da frescura, control y una alimentación más sana. Además, reduces residuos y cuidas el medioambiente. ¿Te animas a empezar tu propio huerto? </p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo4.html" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(4)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-4">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(4)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-4">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-4" style="display: none;">
                        <div class="existing-comments" id="existing-comments-4">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-4" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(4)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 20 de Febrero del 2025 </span>
                    <span>| Xiomara Anabeli Cobian Ramirez</span>
                </div>
            </article>

            <article class="post">
                <div class="post-header">
                    <div class="post-img-5"></div>
                </div>
                <div class="post-body">
                    <h2>Reduciendo residuos en el hogar</h2>
                    <p class="descripcion">Consumimos sin medida, sin considerar el impacto. Es momento de tomar decisiones responsables y reducir nuestra huella ecológica. Cada elección cuenta. ¿Qué harás hoy por un futuro más verde?</p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo5.html" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(5)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-5">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(5)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-5">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-5" style="display: none;">
                        <div class="existing-comments" id="existing-comments-5">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-5" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(5)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 21 de Febrero del 2025 </span>
                    <span>| Fernando Benitez Astudillo</span>
                </div>
            </article>

            <article class="post">
                <div class="post-header">
                    <div class="post-img-6"></div>
                </div>
                <div class="post-body">

                    <h2>Consumo Digital y Producción Responsable</h2>
                    <p class="descripcion">El consumo digital impacta el planeta más de lo que imaginas. Optar por prácticas responsables en tecnología puede hacer una gran diferencia. ¿Sabes cómo reducir tu impacto digital?</p>
                    <div class="post-footer">
                        <a href="/PRODCONS/postWeb/articulo6.html" class="post-link">Leer más...</a>
                        <div class="interaction-buttons">
                            <button class="like-button-small" onclick="likePost(6)" title="Me gusta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg>
                                <span id="likes-count-6">0</span>
                            </button>
                            <button class="comment-toggle-small" onclick="toggleComments(6)" title="Comentarios">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/>
                                </svg>
                                <span id="comments-count-6">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section" id="comments-section-6" style="display: none;">
                        <div class="existing-comments" id="existing-comments-6">
                            <!-- Los comentarios se mostrarán aquí -->
                        </div>
                        <div class="add-comment">
                            <textarea id="comment-input-6" placeholder="Escribe tu comentario..."></textarea>
                            <button onclick="addComment(6)">Publicar</button>
                        </div>
                    </div>
                    <span>Publicado el 21 de Febrero del 2025 </span>
                    <span>| Isabela Monserrat Vidrio Camarena</span>
                </div>
            </article>
        </div>
    </section>

    

    <!-- <div class="carousel-container">
        <div class="carousel">
            <?php foreach ($publicaciones as $pub): ?>
            <div class="carousel-item">
                <div class="post-header">
                    <img src="<?= $pub['imagen'] ?>" alt="<?= $pub['titulo'] ?>" class="post-img">
                </div>
                <div class="post-body">
                    <h2><?= $pub['titulo'] ?></h2>
                    <p class="descripcion"><?= $pub['descripcion'] ?></p>
                    <a href="<?= $pub['link'] ?>" class="post-link">Leer más...</a>
                    <span>Publicado el <?= $pub['fecha'] ?></span>
                    <span>| <?= $pub['autor'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="prev">‹</button>
        <button class="next">›</button>
    </div> -->
    
    <!--FOOTER-->
    <!-- <footer>
        <div class="footer-container"></div>
      </footer> -->
      
      <script src="/PRODCONS/Header visitantes/barra_principal.js"></script>
<!-- <script src="/PRODCONS/footer/footer.js"></script> -->

<script>
    // Funciones para manejar likes y comentarios
    // Simulación de almacenamiento local para likes y comentarios
    
    // Inicializar datos si no existen
    if (!localStorage.getItem('articleLikes')) {
        localStorage.setItem('articleLikes', JSON.stringify({1: 0, 2: 0, 3: 0, 4: 0, 5: 0, 6: 0}));
    }
    
    if (!localStorage.getItem('articleComments')) {
        localStorage.setItem('articleComments', JSON.stringify({
            1: [], 2: [], 3: [], 4: [], 5: [], 6: []
        }));
    }
    
    // Registro de likes de usuario
    if (!localStorage.getItem('userLikes')) {
        localStorage.setItem('userLikes', JSON.stringify({
            1: false, 2: false, 3: false, 4: false, 5: false, 6: false
        }));
    }
    
    // Cargar los datos existentes al iniciar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar likes
        const likes = JSON.parse(localStorage.getItem('articleLikes'));
        const userLikes = JSON.parse(localStorage.getItem('userLikes'));
        
        for (const [articleId, count] of Object.entries(likes)) {
            document.getElementById(`likes-count-${articleId}`).textContent = count;
            
            // Si el usuario ya dio like, cambiar el corazón a relleno
            if (userLikes[articleId]) {
                const likeButton = document.querySelector(`[onclick="likePost(${articleId})"]`);
                likeButton.classList.add('liked');
                likeButton.setAttribute('title', 'Ya has dado like');
                const heartIcon = likeButton.querySelector('svg');
                heartIcon.innerHTML = '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>';
            }
        }
        
        // Cargar comentarios
        const comments = JSON.parse(localStorage.getItem('articleComments'));
        for (const [articleId, articleComments] of Object.entries(comments)) {
            document.getElementById(`comments-count-${articleId}`).textContent = articleComments.length;
        }
    });
    
    // Función para mostrar mensaje de alerta personalizado
    function showAlert(message) {
        const alertBox = document.createElement('div');
        alertBox.style.position = 'fixed';
        alertBox.style.top = '20px';
        alertBox.style.left = '50%';
        alertBox.style.transform = 'translateX(-50%)';
        alertBox.style.backgroundColor = '#f8d7da';
        alertBox.style.color = '#721c24';
        alertBox.style.padding = '10px 20px';
        alertBox.style.borderRadius = '5px';
        alertBox.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        alertBox.style.zIndex = '1000';
        alertBox.style.textAlign = 'center';
        alertBox.innerText = message;
        
        document.body.appendChild(alertBox);
        
        // Eliminar la alerta después de 3 segundos
        setTimeout(() => {
            alertBox.style.opacity = '0';
            alertBox.style.transition = 'opacity 0.5s';
            setTimeout(() => document.body.removeChild(alertBox), 500);
        }, 3000);
    }
    
    // Función para dar "me gusta"
    function likePost(articleId) {
        const userLikes = JSON.parse(localStorage.getItem('userLikes'));
        
        // Verificar si el usuario ya ha dado like a este artículo
        if (userLikes[articleId]) {
            showAlert('Ya has dado like a este artículo');
            return;
        }
        
        // Actualizar conteo de likes
        const likes = JSON.parse(localStorage.getItem('articleLikes'));
        likes[articleId]++;
        localStorage.setItem('articleLikes', JSON.stringify(likes));
        
        // Registrar que este usuario ha dado like
        userLikes[articleId] = true;
        localStorage.setItem('userLikes', JSON.stringify(userLikes));
        
        // Actualizar la UI
        document.getElementById(`likes-count-${articleId}`).textContent = likes[articleId];
        
        // Efecto visual al dar like
        const likeButton = document.querySelector(`[onclick="likePost(${articleId})"]`);
        likeButton.classList.add('liked');
        likeButton.setAttribute('title', 'Ya has dado like');
        
        // Cambiar el corazón a relleno cuando se da like
        const heartIcon = likeButton.querySelector('svg');
        heartIcon.innerHTML = '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>';
    }
    
    // Función para mostrar/ocultar sección de comentarios
    function toggleComments(articleId) {
        const commentsSection = document.getElementById(`comments-section-${articleId}`);
        if (commentsSection.style.display === 'none') {
            commentsSection.style.display = 'block';
            loadComments(articleId);
        } else {
            commentsSection.style.display = 'none';
        }
    }
    
    // Función para cargar comentarios
    function loadComments(articleId) {
        const comments = JSON.parse(localStorage.getItem('articleComments'))[articleId];
        const commentsContainer = document.getElementById(`existing-comments-${articleId}`);
        commentsContainer.innerHTML = '';
        
        if (comments.length === 0) {
            commentsContainer.innerHTML = '<p>No hay comentarios aún. ¡Sé el primero en comentar!</p>';
            return;
        }
        
        for (const comment of comments) {
            const commentElement = document.createElement('div');
            commentElement.className = 'comment';
            commentElement.innerHTML = `
                <div class="comment-author">Usuario</div>
                <div class="comment-content">${comment.text}</div>
                <div class="comment-date">${comment.date}</div>
            `;
            commentsContainer.appendChild(commentElement);
        }
    }
    
    // Función para agregar un comentario
    function addComment(articleId) {
        const commentInput = document.getElementById(`comment-input-${articleId}`);
        const commentText = commentInput.value.trim();
        
        if (!commentText) return;
        
        const comments = JSON.parse(localStorage.getItem('articleComments'));
        const now = new Date();
        const formattedDate = now.toLocaleString('es-ES');
        
        comments[articleId].push({
            text: commentText,
            date: formattedDate
        });
        
        localStorage.setItem('articleComments', JSON.stringify(comments));
        
        // Actualizar la UI
        document.getElementById(`comments-count-${articleId}`).textContent = comments[articleId].length;
        commentInput.value = '';
        
        // Recargar los comentarios
        loadComments(articleId);
    }
</script>
</body>

</html>