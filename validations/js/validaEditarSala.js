document.getElementById("nombreS").oninput = function validaNombre() {
    let nombre = this.value.trim()
    let errorNombreS = ""
    if(nombre.length == 0 || nombre == null || /^\s+$/.test(nombre)){
        errorNombreS = "El campo no puede estar vacio."
    } else if(!letrasNumerosEspacios(nombre)){
        errorNombreS = "El campo solo puede contener letras y espacios."
    } else if(nombre.length < 3){
        errorNombreS = "El nombre debe tener al menos 3 caracteres."
    } else if(nombre.length > 40){
        errorNombreS = "El nombre no debe tener más de 40 caracteres."
    }
    function letrasNumerosEspacios(nombreS) {
        let textoLimpio = nombreS.trim()
        return textoLimpio === "" || /^[a-zA-Z0-9\s]+$/.test(textoLimpio);
    }
    document.getElementById("errorNombreS").innerHTML = errorNombreS
    validaForm()
}
document.getElementById("tipoSala").onmouseleave = function validaTipoSala() {
    let tipoSala = this.value.trim()
    let errorTipoSala = ""
    if(tipoSala == ""){
        errorTipoSala = "Debe seleccionar un tipo de sala."
    }
    document.getElementById("errorTipoSala").innerHTML = errorTipoSala
    validaForm()
}
document.getElementById("imagen").onchange = function validaImagen() {
    let imagen = this.value.trim()
    let errorImagen = ""
    if(imagen !== ""){
        if(!imagen.match(/\.(jpg|jpeg|png)$/i)){
            errorImagen = "El archivo debe ser una imagen (JPG, JPEG, PNG)."
        }
    }
    document.getElementById("errorImagen").innerHTML = errorImagen
    validaForm()
}
function validaForm() {
    const errores = [
        document.getElementById("errorNombreS").innerHTML,
        document.getElementById("errorTipoSala").innerHTML,
        document.getElementById("errorImagen").innerHTML
    ]
    const campos = [
        document.getElementById("nombreS").value.trim(),
        document.getElementById("tipoSala").value.trim(),
    ]
    const hayErrores = errores.some(error => error !== "")
    const camposVacios = campos.some(campo => campo == "")
    document.getElementById("boton").disabled = hayErrores || camposVacios;
}