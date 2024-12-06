document.getElementById("nombre").oninput = function validaNombre() {
    let nombre = this.value.trim()
    let errorNombre = ""
    if (nombre.length == 0 || nombre == null || /^\s+$/.test(nombre)) {
        errorNombre = "El campo nombre no puede estar vacío."
    } else if (nombre.length < 2){
        errorNombre = "El nombre no tener menos 2 caracteres."
    } else if (!letras(nombre)){
        errorNombre = "El nombre solo puede contener letras."
    } else if (nombre.length > 30){
        errorNombre = "El nombre no puede tener más de 30 caracteres."
    }
    function letras(nombre) {
        return /^[a-zA-Z]+$/.test(nombre);
    }
    document.getElementById("errorNombre").innerHTML = errorNombre
    validaForm()
}
document.getElementById("apellido").oninput = function validaApellido() {
    let apellido = this.value.trim()
    let errorApellido = ""
    if (apellido.length == 0 || apellido == null || /^\s+$/.test(apellido)) {
        errorApellido = "El campo nombre no puede estar vacío"
    } else if(apellido.length < 2){
        errorApellido = "El apellido no puede tener menos 2 caracteres."
    } else if(!letras(apellido)){
        errorApellido = "El apellido solo puede contener letras."
    } else if(apellido.length > 50){
        errorApellido = "El apellido no puede tener más de 50 caracteres."
    }
    function letras(apellido) {
        return /^[a-zA-Z]+$/.test(apellido);
    }
    document.getElementById("errorApellido").innerHTML = errorApellido
    validaForm()
}
document.getElementById("usuario").oninput = function validaUsuario() {
    let usuario = this.value.trim()
    let errorUser = ""
    if (usuario.length == 0 || usuario == null || /^\s+$/.test(usuario)) {
        errorUser = "El campo nombre no puede estar vacío"
    } else if(usuario.length < 2){
        errorUser = "El usuario debe tener mas 2 caracteres."
    } else if(!letrasYnumeros(usuario)){
        errorUser = "El usuario solo puede contener letras y números."
    } else if(usuario.length > 30){
        errorUser = "El usuario no puede tener más de 30 caracteres."
    }
    function letrasYnumeros(usuario) {
        return /^[a-zA-Z0-9]+$/.test(usuario);
    }
    document.getElementById("errorUser").innerHTML = errorUser
    validaForm()
}
document.getElementById("telefono").oninput = function validaTelefono(){
    let telefono = this.value.trim()
    let errorTelefono = ""
    if (telefono.length == 0 || telefono == null || /^\s+$/.test(telefono)) {
        errorTelefono = "El campo nombre no puede estar vacío"
    } else if(!telefonoValido(telefono)){
        errorTelefono = "Solo formato XXXXXXXXX o +XX XXXXXXXXX"
    }
    function telefonoValido(telefono) {
        return /^(?:(?:\+?[0-9]{2,4})?[ ]?[6789][0-9 ]{8,13})$/.test(telefono);
    }
    document.getElementById("errorTelefono").innerHTML = errorTelefono
    validaForm()
}
document.getElementById("dni").oninput = function validaDNI() {
    let dni = this.value.trim()
    let errorDni = ""
    if (dni.length == 0 || dni == null || /^\s+$/.test(dni)) {
        errorDni = "El campo nombre no puede estar vacío."
    } else if (!calculoDNI(dni)) {
        errorDni = "El DNI no es válido."
    } else if (!letraDni(dni)){
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
    document.getElementById('errorDni').innerHTML = errorDni
    validaForm()
}
document.getElementById("nacimiento").onmouseleave = function validaNacimiento() {
    let nacimiento = this.value.trim()
    let errorNacimiento = ""
    if(nacimiento.length == 0 || nacimiento == null || /^\s+$/.test(nacimiento)) {
        errorNacimiento = "El campo fecha no puede estar vacío"
    } else if(!fechaValida(nacimiento)) {
        errorNacimiento = "Debe tener al menos 18 años"
    }
    function fechaValida(nacimiento) {
        let fechaNueva = new Date(nacimiento);
        let fechaActual = new Date();
        let diferenciaAnios = fechaActual.getFullYear() - fechaNueva.getFullYear();
        if (fechaActual.getMonth() < fechaNueva.getMonth() || 
            (fechaActual.getMonth() === fechaNueva.getMonth() && fechaActual.getDate() < fechaNueva.getDate())) {
            diferenciaAnios--;
        }
        return diferenciaAnios >= 18;
    }
    document.getElementById("errorNacimiento").innerHTML = errorNacimiento
    validaForm()
}
document.getElementById("email").oninput = function validaEmail() {
    let email = this.value.trim()
    let errorEmail = ""
    if (email.length == 0 || email == null || /^\s+$/.test(email)) {
        errorEmail = "El campo nombre no puede estar vacío"
    } else if(!emailValido(email)){
        errorEmail = "El email no es válido."
    }
    function emailValido(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    document.getElementById("errorEmail").innerHTML = errorEmail
    validaForm()
}
document.getElementById("pwd").oninput = function validaPwd() {
    let pwd = this.value.trim()
    let errorPwd = ""
    if (pwd.length == 0 || pwd == null || /^\s+$/.test(pwd)) {
        errorPwd = "El campo nombre no puede estar vacío"
    } else if (!patron(pwd)){
        errorPwd = "La contrasena debe tener 6 carac. en mayus, minus y numero."
    }
    function patron(pwd){
        let patron = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}/
        return patron.test(pwd)
    }
    document.getElementById("errorPwd").innerHTML = errorPwd
    validaForm()
}
document.getElementById("rPwd").oninput = function validaRepetirPwd() {
    let pwd = document.getElementById("pwd").value.trim()
    let rPwd = this.value.trim()
    let errorRpwd = ""
    if (rPwd.length == 0 || rPwd == null || /^\s+$/.test(rPwd)) {
        errorRpwd = "El campo nombre no puede estar vacío"
    } else if (rPwd !== pwd) {
        errorRpwd = "Las contraseñas no coinciden"
    }
    document.getElementById("errorRpwd").innerHTML = errorRpwd
    validaForm()
}
document.getElementById("direccion").oninput = function validaDireccion() {
    let direccion = this.value.trim()
    let errorDireccion = ""
    if (direccion.length == 0 || direccion == null || /^\s+$/.test(direccion)) {
        errorDireccion = "El campo nombre no puede estar vacío"
    } else if (direccion.length < 5) {
        errorDireccion = "La dirección debe tener al menos 5 caracteres"
    } else if(direccion.length > 254){
        errorDireccion = "La dirección no puede tener más de 254 caracteres"
    }
    document.getElementById("errorDireccion").innerHTML = errorDireccion
    validaForm()
}
document.getElementById("rol").onmouseleave = function validaRol() {
    let rol = this.value.trim()
    let errorRol = ""
    if(rol == ""){
        errorRol = "Debes escoger una opcion."
    }
    document.getElementById("errorRol").innerHTML = errorRol
    validaForm()
}
function validaForm() {
    const errores = [
        document.getElementById("errorNombre").innerHTML,
        document.getElementById('errorApellido').innerHTML,
        document.getElementById('errorUser').innerHTML,
        document.getElementById('errorTelefono').innerHTML,
        document.getElementById('errorDni').innerHTML,
        document.getElementById('errorNacimiento').innerHTML,
        document.getElementById('errorEmail').innerHTML,
        document.getElementById('errorPwd').innerHTML,
        document.getElementById('errorRpwd').innerHTML,
        document.getElementById('errorDireccion').innerHTML,
        document.getElementById('errorRol').innerHTML
    ]
    const campos = [
        document.getElementById("nombre").value.trim(),
        document.getElementById("apellido").value.trim(),
        document.getElementById("usuario").value.trim(),
        document.getElementById("telefono").value.trim(),
        document.getElementById("dni").value.trim(),
        document.getElementById("nacimiento").value.trim(),
        document.getElementById("email").value.trim(),
        document.getElementById("pwd").value.trim(),
        document.getElementById("rPwd").value.trim(),
        document.getElementById("direccion").value.trim(),
        document.getElementById("rol").value.trim()
    ]
    const camposVacios = campos.some(campo => campo == "")
    const hayErrores = errores.some(error => error !== "")
    document.getElementById("boton").disabled = camposVacios || hayErrores
}