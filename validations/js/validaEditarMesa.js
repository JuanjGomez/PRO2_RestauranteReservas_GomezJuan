document.getElementById("sala").onmouseleave = function validaSala() {
    let sala = this.value.trim()
    let errorSala = ""
    if(sala == ""){
        errorSala = "Debe seleccionar una sala."
    }
    document.getElementById("errorSala").innerHTML = errorSala
    validaForm()
}
document.getElementById("numSillas").onmouseleave = function validaNumeroSillas () {
    let numSillas = this.value.trim()
    let errorNumSillas = ""
    if(numSillas == ""){
        errorNumSillas = "Debes seleccionar un numero."
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