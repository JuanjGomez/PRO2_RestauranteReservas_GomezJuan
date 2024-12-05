document.getElementById("nombre").oninput = function validaNombre(){
    let nombre = this.value.trim()
    let errorNombre = ""
    if(nombre.length == 0 || nombre == null || /^\s+$/.test(nombre)){
        errorNombre = "El campo no puede estar vacio."
    } else if(nombre.length < 2){
        errorNombre = "El nombre debe tener mas 2 caracteres."
    } else if(nombre.length > 45){
        errorNombre = "El nombre no debe tener mas de 45 caracteres."
    }
    document.getElementById("errorNombre").innerHTML = errorNombre
    validaForm()
}
document.getElementById("apellido").oninput = function validaApellido(){
    let apellido = this.value.trim()
    let errorApellido = ""
    if(apellido.length == 0 || apellido == null || /^\s+$/.test(apellido)){
        errorApellido = "El campo no puede estar vacio."
    } else if(apellido.length < 2){
        errorApellido = "El apellido debe tener mas 2 caracteres."
    } else if(apellido.length > 50){
        errorApellido = "El apellido no debe tener mas 50 caracteres."
    }
    document.getElementById("errorApellido").innerHTML = errorApellido
    validaForm();
}
document.getElementById("telefono").oninput = function validaTelefono(){
    let telefono = this.value.trim()
    let errorTelefono = ""
    if(telefono.length == 0 || telefono == null || /^\s+$/.test(telefono)) {
        errorTelefono = "El campo teléfono no puede estar vacío."
    } else if(!telefonoValido(telefono)) {
        errorTelefono = "El teléfono no es válido."
    }
    function telefonoValido(telefono) {
        return /^(?:(?:\+?[0-9]{2,4})?[ ]?[6789][0-9 ]{8,13})$/.test(telefono);
    }
    document.getElementById("errorTelefono").innerHTML = errorTelefono
    validaForm()
}
document.getElementById("direccion").oninput = function validaDireccion() {
    let direccion = this.value.trim()
    let errorDireccion = ""
    if(direccion.length == 0 || direccion == null || /^\s+$/.test(direccion)) {
        errorDireccion = "El campo dirección no puede estar vacío."
    } else if(direccion.length < 10){
        errorDireccion = "La dirección debe tener al menos 10 caracteres."
    } else if(direccion.length > 254){
        errorDireccion = "La dirección no puede tener más de 254 caracteres."
    }
    document.getElementById("errorDireccion").innerHTML = errorDireccion
    validaForm()
}
document.getElementById("nacimiento").onmouseleave = function validaNacimiento() {
    let fecha = this.value.trim()
    let errorNacimiento = ""
    if(fecha.length == 0 || fecha == null || /^\s+$/.test(fecha)) {
        errorNacimiento = "El campo fecha no puede estar vacío."
    } else if(!fechaValida(fecha)) {
        errorNacimiento = "La persona debe tener al menos 18 años."
    }
    function fechaValida(fecha) {
        let fechaNueva = new Date(fecha);
        let fechaActual = new Date();
        let diferenciaAnios = fechaActual.getFullYear() - fechaNueva.getFullYear();
        if (fechaActual.getMonth() < fechaNueva.getMonth() || 
            (fechaActual.getMonth() === fechaNueva.getMonth() && fechaActual.getDate() < fechaNueva.getDate())) {
            diferenciaAnios--;
        }
        return diferenciaAnios >= 18;
    }    
    document.getElementById("errorNacimiento").innerHTML = errorFecha
    validaForm()
}
document.getElementById("pwd").oninput = function validaPwd(){
    let pwd = this.value.trim();
    let errorPwd = "";
    if (pwd !== "") {
        if (!patron(pwd)) {
            errorPwd = "El campo necesita 6 letras con mayúscula, minúscula y número."
        }
    }
    function patron(pwd) {
        let patron = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}/
        return patron.test(pwd)
    }
    document.getElementById("errorPwd").innerHTML = errorPwd
    validaForm()
}
document.getElementById("rPwd").oninput = function validaRepetirPwd() {
    let pwd = document.getElementById("pwd").value.trim()
    let rPwd = this.value.trim()
    let errorRPwd = ""
    if (pwd !== "" && pwd !== rPwd) {
        errorRPwd = "Las contraseñas no coinciden.";
    }
    document.getElementById("errorRpwd").innerHTML = errorRPwd
    validaForm()
}
document.getElementById("rol").onmouseleave = function validaRol() {
    let rol = this.value
    let errorRol = ""
    if(rol === "Seleccione un rol"){
        errorRol = "Debe seleccionar un rol."
    }
    document.getElementById("errorRol").innerHTML = errorRol
    validaForm()
}
function validaForm() {
    const errores = [
        document.getElementById("errorNombre").innerHTML,
        document.getElementById("errorApellido").innerHTML,
        document.getElementById("errorTelefono").innerHTML,
        document.getElementById("errorDireccion").innerHTML,
        document.getElementById("errorNacimiento").innerHTML,
        document.getElementById("errorPwd").innerHTML,
        document.getElementById("errorRpwd").innerHTML,
        document.getElementById("errorRol").innerHTML
    ]
    const campos = [
        document.getElementById("nombre").value.trim(),
        document.getElementById("apellido").value.trim(),
        document.getElementById("telefono").value.trim(),
        document.getElementById("direccion").value.trim(),
        document.getElementById("nacimiento").value.trim(),
        document.getElementById("rol").value
    ]
    const camposVacios = campos.some(campo => campo == "") || (pwd !== "" && rPwd === ""); //Si escribe en pwd, rPwd no puede estar vacio
    const hayErrores = errores.some(error => error !== "")
    document.getElementById("boton").disabled = camposVacios || hayErrores
}