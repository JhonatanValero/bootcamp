//hideFormRegistro();
//hideMenu();
function hideMenu() {
    var list = document.getElementById("menu-lista");
    var icono = document.getElementById("boton-menu");
    var iconoBack = document.getElementById("botonatras-menu");
    var containerForm = document.getElementById("container-form");
    var pageWrapper = document.getElementsByClassName("page-wrapper");
    
    if(list.style.display === "none"){
        list.style.display = "block";
    } else {
        list.style.display = "none";
        icono.style.display = "flex";
        iconoBack.style.display = "none";
        containerForm.style.display = "flex";
    }

    if(list.style.display === "block") {
        icono.style.display = "none";
        iconoBack.style.display = "block";
        containerForm.style.display = "none";
    } else {
        iconoBack.style.display = "none";
    }
}

function hideFormRegistro() {
    var formRegistro = document.getElementById("formulario-registro");
    var checkRegistro = document.getElementById("check-registro");
    var formIngreso = document.getElementById("formulario-ingreso");
    var botonRegistro = document.getElementById("registro-cabecera");
    var botonIngreso = document.getElementById("ingreso-cabecera");
    
    formRegistro.style.display = "none";
    formIngreso.style.display = "flex";
    botonIngreso.style.opacity = "0.9";
    botonRegistro.style.opacity = "0.35";
    checkRegistro.style.display = "none";
}

function hideFormIngreso() {
    var formRegistro = document.getElementById("formulario-registro");
    var formIngreso = document.getElementById("formulario-ingreso");
    var botonRegistro = document.getElementById("registro-cabecera");
    var botonIngreso = document.getElementById("ingreso-cabecera");

    formRegistro.style.display = "flex";
    formIngreso.style.display = "none";
    botonIngreso.style.opacity = "0.35";
    botonRegistro.style.opacity = "0.9";
}

function hideCheckRegistro() {
    var formRegistro = document.getElementById("formulario-registro");
    var checkRegistro = document.getElementById("check-registro");

    formRegistro.style.display = "none";
    checkRegistro.style.display = "flex";

    setTimeout( function() {
        hideFormRegistro();
    }, 5000);
}
