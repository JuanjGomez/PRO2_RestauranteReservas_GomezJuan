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
document.getElementById("numeroSillas").onmouseleave = function validaNumerodeSillas () {
    let numeroSillas = this.value.trim()
    let errorNumeroSillas = ""
    if (numeroSillas == 0 || numeroSillas == null) {
        errorNumeroSillas = "El campo no puede estar vacío."
    } else if (numeroSillas < 1 || numeroSillas > 10) {
        errorNumeroSillas = "El número de sillas debe estar entre 1 y 10."
    }
    document.getElementById("errorNumeroSillas").innerHTML = errorNumeroSillas
    validaForm()
}
document.getElementById("fechaReserva").onmouseleave = function validarFecha() {
    let fechaReserva = new Date(this.value)
    let errorFechaReserva = ""

    // Obtener la fecha de hoy y la fecha limite de 3 meses en el futuro
    let hoy = new Date()
    hoy.setHours(0, 0, 0, 0) // Elmina la parte de horas para comparacion precisa
    let tresMesesDespues = new Date(hoy)
    tresMesesDespues.setMonth(tresMesesDespues.getMonth() + 3)

    //Validar que la fecha este dentro del rango permitido
    if (isNaN(fechaReserva.getTime())){
        errorFechaReserva = "Debe seleccionar una fecha válida."
    } else if (fechaReserva < hoy){
        errorFechaReserva = "La fecha no puede ser anterior a hoy."
    } else if(fechaReserva > tresMesesDespues) { 
        errorFechaReserva = "La fecha no puede ser mas de 3 mese a partir de hoy."
    }
    document.getElementById("errorFechaReserva").innerHTML = errorFechaReserva
    validaForm()
}
document.getElementById("horaReserva").onfocus = function actualizarHoras() {
    const selectHora = document.getElementById("horaReserva");
    const fechaReserva = document.getElementById("fechaReserva").value.trim();
    let errorHoraReserva = "";

    // Limpiar las opciones previas (excepto la primera opción)
    while (selectHora.options.length > 1) {
        selectHora.remove(1);
    }

    // Obtener la fecha de hoy
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    // Verificar si la fecha de reserva es hoy
    const fechaSeleccionada = new Date(fechaReserva);
    fechaSeleccionada.setHours(0, 0, 0, 0);

    // Obtener la hora actual solo si la fecha es hoy
    const horaActual = hoy.getTime() === fechaSeleccionada.getTime() ? new Date().getHours() : -1;

    // Generar las horas disponibles (16:00 a 22:00)
    const horaInicio = 16;
    const horaFin = 22;

    for (let hora = horaInicio; hora <= horaFin; hora++) {
        if (hora > horaActual) { // Mostrar solo horas futuras
            let formattedHora = `${hora.toString().padStart(2, '0')}:00`;
            let option = document.createElement("option");
            option.value = formattedHora;
            option.textContent = formattedHora;
            selectHora.appendChild(option);
        }
    }

    // Validar si no hay opciones disponibles
    if (selectHora.options.length <= 1) {
        errorHoraReserva = "No hay horarios disponibles para la fecha seleccionada.";
    }

    // Mostrar el error si corresponde
    document.getElementById("errorHoraReserva").innerHTML = errorHoraReserva;

    // Actualizar el estado del formulario
    validaForm();
};

function validaForm() {
    const errores = [
        document.getElementById("errorNombreReserva").innerHTML,
        document.getElementById("errorNumeroSillas").innerHTML,
        document.getElementById("errorFechaReserva").innerHTML,
        document.getElementById("errorHoraReserva").innerHTML
    ]
    const campos = [
        document.getElementById("nombreReserva").value.trim(),
        document.getElementById("numeroSillas").value.trim(),
        document.getElementById("fechaReserva").value.trim(),
        document.getElementById("horaReserva").value.trim()
    ]
    const hayErrores = errores.some(error => error!== "")
    const camposVacios = campos.some(campo => campo == "")

    document.getElementById("boton").disabled = hayErrores || camposVacios;
}