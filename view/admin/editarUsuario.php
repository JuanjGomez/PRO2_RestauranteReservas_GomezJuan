<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location: ../../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location: gestionarUsuarios.php');
        exit();
    }
    $user = trim($_POST['id']);
    $sqlEdit = 'SELECT * FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id_rol 
                WHERE u.id_usuario = :id_usuario';
    $stmtEdit = $conn->prepare($sqlEdit);
    $stmtEdit->bindParam(':id_usuario', $user, PDO::PARAM_INT);
    $stmtEdit->execute();
    $fila = $stmtEdit->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    <div>
        <form method="POST" action="../../procesos/editarUser.php">
            <input type="hidden" name="id" value="<?php echo $user; ?>">
            <label for="usuario">Nombre usuario: 
                <input type="text" name="username" id="username" value="<?php echo $fila['nombre']; ?>">
            </label>
            <p id="errorUser" class="error"></p>
            <label for="nombre">Nombre: 
                <input type="text" name="nombre" id="nombre" value="<?php echo $fila['nombre']; ?>">
            </label>
            <p id="errorNombre" class="error"></p>
            <label for="apellido">Apellido: 
                <input type="text" name="apellido" id="apellido" value="<?php echo $fila['apellido'];?>">
            </label>
            <p id="errorApellido" class="error"></p>
            <label for="dni">DNI: 
                <input type="text" name="dni" id="dni" value="<?php echo $fila['dni']; ?>">
            </label>
            <p id="errorDni" class="error"></p></p>
            <label for="email">Email: 
                <input type="text" name="email" id="email" value="<?php echo $fila['email']; ?>">
            </label>
            <p id="errorEmail" class="error"></p>
            <label for="telefono">Telefono: 
                <input type="text" name="telefono" id="telefono" value="<?php echo $fila['telefono'];?>">
            </label>
            <p id="errorTelefono" class="error"></p>
            <label for="direccion">Direccion: 
                <input type="text" name="direccion" id="direccion" value="<?php echo $fila['direccion'];?>">
            </label>
            <p id="errorDireccion" class="error"></p>
            <label for="nacimiento">Fecha de Nacimiento: 
                <input type="date" name="nacimiento" id="nacimiento" value="<?php echo $fila['fecha_nacimiento']; ?>">
            </label>
            <p id="errorNacimiento" class="error"></p>
            <label for="pwd">Contrasena: 
                <input type="text" name="pwd" id="pwd">
            </label>
            <p id="errorPwd" class="error"></p>
            <label for="rPwd">Repetir Contrasena: 
                <input type="text" name="rPwd" id="rPwd">
            </label>
            <p id="errorRpwd" class="error"></p>
            <label for="rol">Rol: 
                <select name="rol" id="rol">
                    <option value="" disabled>Seleccione un rol</option>
                    <?php
                        $sqlRoles = 'SELECT * FROM roles';
                        $stmtRoles = $conn->prepare($sqlRoles);
                        $stmtRoles->execute();
                        while($rol = $stmtRoles->fetch(PDO::FETCH_ASSOC)){
                            echo '<option value="'.$rol['rol'].'"';
                            if($rol['rol'] === $fila['rol']){
                                echo'selected';
                            }
                            echo '>'.$rol['rol'].'</option>';
                        }
                    ?>
                </select>
            </label>
            <p id="errorRol" class="error"></p>
            <button type="submit" id="boton" disabled>Actualizar</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
    <script src="../../validations/js/validaEditarUser.js"></script>
</body>
</html>