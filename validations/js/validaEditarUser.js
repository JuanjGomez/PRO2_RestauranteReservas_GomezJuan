document.getElementById("username").onblur = function validaUsuario(){
    let usuario = this.value.trim();
    let errorUser = ""
    if(usuario.length == 0 || usuario == null || /^\s+$/.test(usuario)){
        errorUser = "El campo no puede estar vacio."
    } else if(usuario.length > 2){
        errorUser = "El usuario debe tener mas 2 caracteres."
    } else if(usuario.length < 45){
        errorUser = "El usuario no puede tener mas 45 caracteres."
    }
    document.getElementById("errorUser").innerHTML = errorUser
    validaForm()
}
document.getElementById("nombre").onblur = function validaNombre(){
    let nombre = this.value.trim()
    let errorNombre = ""
    if(nombre.length == 0 || nombre == null || /^\s+$/.test(nombre)){
        errorNombre = "El campo no puede estar vacio."
    } else if(nombre.length > 2){
        errorNombre = "El nombre debe tener mas 2 caracteres."
    } else if(nombre.length < 45){
        errorNombre = "El nombre no debe tener mas de 45 caracteres."
    }
    document.getElementById("errorNombre").innerHTML = errorNombre
    validaForm()
}
document.getElementById("apellido").onblur = function validaApellido(){
    let apellido = this.value.trim()
    let errorApellido = ""
    if(apellido.length == 0 || apellido == null || /^\s+$/.test(apellido)){
        errorApellido = "El campo no puede estar vacio."
    } else if(apellido.length > 2){
        errorApellido = "El apellido debe tener mas 2 caracteres."
    } else if(apellido.length < 50){
        errorApellido = "El apellido no debe tener mas 50 caracteres."
    }
    document.getElementById("errorApellido").innerHTML = errorApellido
    validaForm();
}
document.getElementById("dni").onblur = function validaDNI(){
    let dni = this.value.trim()
    let errorDni = ""
    if(dni.length == 0 || dni == null || /^\s+$/.test(dni)){
        errorDni = "El campo no puede estar vacio."
    } else if(!calculoDNI(dni)){
        errorDni = "El DNI no es valido."
    } else if(!letraDni(dni)){
        errorDni = "La letra del DNI no coincide con el numero."
    }
    function calculoDNI(dni){
        let formatoDni = /^\d{8}[A-Za-z]$/
        return formatoDni.test(dni)
    }
    function letraDni(dni){
        let letras = ['T', 'R', 'W', 'A', 'G', 'M', 'Y', 'F', 'P', 'D', 'X', 'B', 'N', 'J', 'Z', 'S', 'Q', 'V', 'H', 'L', 'C', 'K', 'E', 'T']
        let numeroDNI = dni.substring(0,8)
        let letraDNI = dni.charAt(8).toUpperCase()
        let letraExtraida = letras[numeroDNI % 23]
        return letraDNI == letraExtraida
    }
    document.getElementById("errorDni").innerHTML = errorDni
    validaForm()
}
document.getElementById("email").onblur = function validaEmail(){
    let email = this.value.trim()
    let errorEmail = ""
    if(email.length == 0 || email == null || /^\s+$/.test(email)) {
        errorEmail = "El campo email no puede estar vacío."
    } else if(!emailValido(email)) {
        errorEmail = "El email no es válido."
    }
    function emailValido(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    document.getElementById("errorEmail").innerHTML = errorEmail
    validaForm()
}
document.getElementById("telefono").onblur = function validaTelefono(){
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
document.getElementById("direccion").onblur  = function validaDireccion() {
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
document.getElementById("nacimiento").onblur = function validaDireccion() {
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
document.getElementById("pwd").onblur = function validaPwd(){
    let pwd = this.value.trim()
    let errorPwd = ""
    if(pwd.length == 0 || pwd == null || /^\s+$/.test(pwd)){
        errorPwd = "El campo no puede estar vacio."
    } else if(!patron(pwd)){
        errorPwd = "El campo necesita, 6 letras con mayúscula, minúscula y número."
    }
    function patron(pwd){
        let patron = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}/
        return patron.test(pwd)
    }
    document.getElementById("errorPwd").innerHTML = errorPwd
    validaForm()
}
document.getElementById("rPwd").onblur = function validaRepetirPwd() {
    let pwd = document.getElementById("pwd").value.trim()
    let rPwd = this.value.trim()
    let errorRPwd = ""
    if(pwd !== rPwd){
        errorRPwd = "Las contraseñas no coinciden."
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