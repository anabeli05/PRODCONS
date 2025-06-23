<<<<<<< HEAD
document.addEventListener('DOMContentLoaded', function() {
    const cookieBanner = document.getElementById('cookieBanner');
    const acceptBtn = document.getElementById('acceptCookies');
    const rejectBtn = document.getElementById('rejectCookies');
    
    // Mostrar el banner en cada visita (eliminamos la verificación de cookies)
    cookieBanner.style.display = 'block';
    
    // Manejar clic en Aceptar
    acceptBtn.addEventListener('click', function() {
        cookieBanner.style.display = 'none';
        // Aquí puedes cargar servicios que requieran cookies
        console.log("Cookies aceptadas");
    });
    
    // Manejar clic en Rechazar
    rejectBtn.addEventListener('click', function() {
        cookieBanner.style.display = 'none';
        console.log("Cookies rechazadas");
    });
});

    // Funciones auxiliares para manejar cookies
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }
    
    function getCookie(name) {
        const cookieName = name + "=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const cookieArray = decodedCookie.split(';');
        
        for(let i = 0; i < cookieArray.length; i++) {
            let cookie = cookieArray[i];
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(cookieName) === 0) {
                return cookie.substring(cookieName.length, cookie.length);
            }
        }
        return "";
    }
=======
document.addEventListener('DOMContentLoaded', function() {
    const cookieBanner = document.getElementById('cookieBanner');
    const acceptBtn = document.getElementById('acceptCookies');
    const rejectBtn = document.getElementById('rejectCookies');
    
    // Mostrar el banner en cada visita (eliminamos la verificación de cookies)
    cookieBanner.style.display = 'block';
    
    // Manejar clic en Aceptar
    acceptBtn.addEventListener('click', function() {
        cookieBanner.style.display = 'none';
        // Aquí puedes cargar servicios que requieran cookies
        console.log("Cookies aceptadas");
    });
    
    // Manejar clic en Rechazar
    rejectBtn.addEventListener('click', function() {
        cookieBanner.style.display = 'none';
        console.log("Cookies rechazadas");
    });
});

    // Funciones auxiliares para manejar cookies
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }
    
    function getCookie(name) {
        const cookieName = name + "=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const cookieArray = decodedCookie.split(';');
        
        for(let i = 0; i < cookieArray.length; i++) {
            let cookie = cookieArray[i];
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(cookieName) === 0) {
                return cookie.substring(cookieName.length, cookie.length);
            }
        }
        return "";
    }
>>>>>>> main
;