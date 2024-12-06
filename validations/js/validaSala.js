document.getElementById("nombreS").oninput = function validaNombreSala() {
    let nombreS = this.value.trim()
    let errorNombreS = ""
    if(nombreS.length == 0 || nombreS == null || /^\s+$/.test(nombreS)){
        errorNombreS = "El campo no puede estar vacío."
    } else if(nombreS.length < 2){
        errorNombreS = "El nombre debe tener al menos 2 caracteres."
    } else if(!letrasNumerosEspacios(nombreS)){
        errorNombreS = "El nombre solo puede contener letras y numeros."
    } else if(nombreS.length > 40){
        errorNombreS = "El nombre no puede tener más de 40 caracteres."
    }
    function letrasNumerosEspacios(nombreS) {
        let textoLimpio = nombreS.trim();
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
document.getElementById("imagen").onmouseleave = function validaImagen() {
    let imagen = this.value.trim()
    let errorImagen = ""
    if(imagen == ""){
        errorImagen = "Debe seleccionar una imagen."
    } else if(!imagen.match(/\.(jpg|jpeg|png)$/i)){
        errorImagen = "El archivo debe ser una imagen (JPG, JPEG, PNG)."
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
        document.getElementById("imagen").value.trim()
    ]
    const hayErrores = errores.some(error => error !== "")
    const camposVacios = campos.some(campo => campo == "")
    document.getElementById("boton").disabled = hayErrores || camposVacios
}