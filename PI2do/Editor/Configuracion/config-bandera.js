const languageSelector = document.getElementById("idiomas");
const flagIcon = document.getElementById("icono-bandera");

languageSelector.addEventListener("change", function () {
  const selected = this.value;
  if (selected === "es") {
    flagIcon.src = "/PRODCONS-main/PI2do/Vista-Admin/UsuarioAdmin/img-vista-admin/espanol.png";
  } else if (selected === "en") {
    flagIcon.src = "/PRODCONS-main/PI2do/Vista-Admin/UsuarioAdmin/img-vista-admin/ingles.png";
  }
});