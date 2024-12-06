<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location: ../../index.php');
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location: ../view/admin/gestionarUsuarios.php');
        exit();
    }
    try{
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $username = trim($_POST['usuario']);
        $telefono = trim($_POST['telefono']);
        $dni = trim($_POST['dni']);
        $nacimiento = trim($_POST['nacimiento']);
        $email = trim($_POST['email']);
        $pwd = trim($_POST['pwd']);
        $hashedPwd = password_hash($pwd, PASSWORD_BCRYPT);
        $direccion = trim($_POST['direccion']);
        $rol = trim($_POST['rol']);
        require_once 'conexion.php';

        //Comprobar si existe un usuario igual que tiene username, email o dni iguales
        $sqlComprobar = "SELECT * FROM usuarios WHERE usuario = :usuario OR email = :email OR dni = :dni";
        $stmtComprobar = $conn->prepare($sqlComprobar);
        $stmtComprobar->bindParam(':usuario', $username, PDO::PARAM_STR);
        $stmtComprobar->bindParam(':email', $email, PDO::PARAM_STR);
        $stmtComprobar->bindParam(':dni', $dni, PDO::PARAM_STR);
        $stmtComprobar->execute();
        $usuarioComprobado = $stmtComprobar->fetch(PDO::FETCH_ASSOC);

        if($usuarioComprobado){
            if($usuarioComprobado['usuario'] == $username){
                $_SESSION['errorCrearUsuario'] = true;
            } else if($usuarioComprobado['email'] == $email){
                $_SESSION['errorCrearEmail'] = true;
            } else if($usuarioComprobado['dni'] == $dni){
                $_SESSION['errorCrearDni'] = true;
            }
            ?>
            <form method="POST" name="usuario" action="../view/admin/crearUsuario.php">
                <input type="hidden" name="nombre" value="<?= htmlspecialchars($nombre) ?>">
                <input type="hidden" name="apellido" value="<?= htmlspecialchars($apellido) ?>">
                <input type="hidden" name="usuario" value="<?= htmlspecialchars($username) ?>">
                <input type="hidden" name="telefono" value="<?= htmlspecialchars($telefono) ?>">
                <input type="hidden" name="dni" value="<?= htmlspecialchars($dni) ?>">
                <input type="hidden" name="nacimiento" value="<?= htmlspecialchars($nacimiento) ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <input type="hidden" name="direccion" value="<?= htmlspecialchars($direccion) ?>">
                <input type="hidden" name="rol" value="<?= htmlspecialchars($rol) ?>">
            </form>
            <script> document.usuario.submit(); </script>
            <?php
            exit();
        }
        

        // Insertar un usuario
        $sqlCrearUsuario = "INSERT INTO usuarios 
                            (nombre, usuario, apellido, telefono, dni, 
                            direccion, fecha_nacimiento, password, email, id_rol) 
                            VALUES 
                            (:nombre, :usuario, :apellido, :telefono, :dni, :direccion, 
                            :fecha_nacimiento, :password, :email, :id_rol)";
        $stmtCrearUsuario = $conn->prepare($sqlCrearUsuario);

        $stmtCrearUsuario->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmtCrearUsuario->bindParam(':usuario', $username, PDO::PARAM_STR);
        $stmtCrearUsuario->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmtCrearUsuario->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmtCrearUsuario->bindParam(':dni', $dni, PDO::PARAM_STR);
        $stmtCrearUsuario->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmtCrearUsuario->bindParam(':fecha_nacimiento', $nacimiento, PDO::PARAM_STR);
        $stmtCrearUsuario->bindParam(':password', $hashedPwd, PDO::PARAM_STR);
        $stmtCrearUsuario->bindParam(":email", $email, PDO::PARAM_STR);
        $stmtCrearUsuario->bindParam(':id_rol', $rol, PDO::PARAM_INT);

        $stmtCrearUsuario->execute();
        $_SESSION['crearUsuario'] = true;
        ?>
        <form method="GET" name="formulario" action="../view/admin/gestionarUsuarios.php"></form>
        <script> document.formulario.submit(); </script>
        <?php
    } catch(PDOException $e){
        echo 'Error: '.$e->getMessage();
        die();
    }