document.addEventListener("DOMContentLoaded", function(e){

    const parrafos = document.querySelectorAll('..descripcion')

    let alturas = [];
    let altruraMaxima = 0;

    const aplicarAlturas = (function aplicarAlturas(){

        parrafos.forEach(parrafo =>{
            if(alturaMaxima == 0){
                alturas.push(parrafo.clientHeight);
            }else{
                parrafo.style.height = altruraMaxima +"px";
            }

            });

            return aplicarAlturas;
    })();

    altruraMaxima = Math.max.apply(Math, alturas);

    aplicarAlturas();
});
<a href="articulos.html">Leer más...</a>
