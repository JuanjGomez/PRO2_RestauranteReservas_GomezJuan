document.getElementById("nombreReserva").oninput = function vaidaNombreReserva () {
    let nombreReserva = this.value.trim()
    let errorNombreReserva = ""
    if (nombreReserva.length == 0 || nombreReserva == null || /^\s+$/.test(nombreReserva)) {
        errorNombreReserva = "El campo no puede estar vacío."
    } else if (nombreReserva.length < 2) {
        errorNombreReserva = "El nombre debe tener mas de 2 caracteres."
    } else if(!letrasNumeros(nombreReserva)){
        errorNombreReserva = "El nombre solo puede tener letras y numeros."
    } else if (nombreReserva.length > 30) {
        errorNombreReserva = "El nombre de reserva no debe tener más de 30 caracteres."
    }
    function letrasNumeros(nombreReserva) {
        return /^[a-zA-Z0-9]+$/.test(nombreReserva);
    }
    document.getElementById("errorNombreReserva").innerHTML = errorNombreReserva
    validaForm()
}
document.getElementById('fechaReserva').onmouseleave = validaFechaHora()
document.getElementById('horaReserva').onmouseleave = validaFechaHora()

function validaFechaHora() {
    const fechaReserva = document.getElementById('fechaReserva').value.trim()
    const horaReserva = document.getElementById('horaReserva').value.trim()
    let errorFechaReserva = ""
    let errorHoraReserva = ""

    if(!fechaReserva) {
        errorFechaReserva = "Debe seleccionar una fecha."
    } else if(!horaReserva) {
        errorHoraReserva = "Debe seleccionar una hora."
    } else {
        const fechaCompletaReserva = new Date(`${fechaReserva}T${horaReserva}`)
        const fechaActual = new Date()

        if(fechaCompletaReserva < fechaActual){
            errorFechaReserva = "La fecha y hora debes ser posteriores al momento actual"
        }
    }

    //Actualizar los mensajes de error en el DOM
    document.getElementById("errorFechaReserva").innerHTML = errorFechaReserva
    document.getElementById("errorHoraReserva").innerHTML = errorHoraReserva
    validaForm()
}
function validaForm() {

    const errorFechaReserva = document.getElementById("errorFechaReserva").innerHTML;
    const errorHoraReserva = document.getElementById("errorHoraReserva").innerHTML;

    const boton = document.getElementById("boton");
    if (!errorFechaReserva && !errorHoraReserva) {
        boton.disabled = false;
    } else {
        boton.disabled = true;
    }
}