document.getElementById("sala").onmouseleave = function validaSala() {
    let sala = this.value.trim()
    let errorSala = ""
    if(sala == ""){
        errorSala = "Debe seleccionar una sala."
    }
    document.getElementById("errorSala").innerHTML = errorSala
    validaForm()
}
document.getElementById("numSillas").oninput = function validaNumeroSillas () {
    let numSillas = this.value.trim()
    let errorNumSillas = ""
    if(numSillas.length == 0 || numSillas == null || /^\s+$/.test(numSillas)){
        errorNumSillas = "El campo no puede estar vacio."
    } else if(isNaN(numSillas)){
        errorNumSillas = "El valor debe ser numerico."
    } else if(numSillas < 2 || numSillas > 10){
        errorNumSillas = "El numero de sillas debe ser entre 2 y 10."
    }
    document.getElementById("errorNumSillas").innerHTML = errorNumSillas
    validaForm()
}
function validaForm() {
    const errores = [
        document.getElementById("errorSala").innerHTML,
        document.getElementById("errorNumSillas").innerHTML
    ]
    const campos = [
        document.getElementById("sala").value.trim(),
        document.getElementById("numSillas").value.trim()
    ]
    const hayErrores = errores.some(error => error !== "")
    const camposVacios = campos.some(campo => campo == "")
    document.getElementById("boton").disabled = hayErrores || camposVacios;
}