<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location:../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location:../view/admin/gestionarUsuarios.php');
        exit();
    }
    require_once 'conexion.php';
    $id = trim($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']); 
    $direccion = trim($_POST['direccion']);
    $fechaDeNacimiento = trim($_POST['nacimiento']);
    $rol = trim($_POST['rol']); 
    try{
        // Consulta para editar el usuario con el id proporcionado
        $sqlEditarUsuario = "UPDATE usuarios
                            SET nombre = :nombre, apellido = :apellido, telefono = :telefono, 
                                direccion = :direccion, fecha_nacimiento = :nacimiento, id_rol = :rol";
        if(!empty($_POST['pwd'])){
            $pwd = trim($_POST['pwd']);
            $hashedPwd = password_hash($pwd, PASSWORD_BCRYPT);
            $sqlEditarUsuario .= ", password = :password";
        }
        $sqlEditarUsuario .= " WHERE id_usuario = :id_ususuario";

        $stmtEditarUsuario = $conn->prepare($sqlEditarUsuario);
        $stmtEditarUsuario->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmtEditarUsuario->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmtEditarUsuario->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmtEditarUsuario->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmtEditarUsuario->bindParam(':nacimiento', $fechaDeNacimiento, PDO::PARAM_STR);
        $stmtEditarUsuario->bindParam(':rol', $rol, PDO::PARAM_INT);
        if(!empty($_POST['pwd'])){
            $stmtEditarUsuario->bindParam(':password', $hashedPwd, PDO::PARAM_STR);
        }
        $stmtEditarUsuario->bindParam(':id_ususuario', $id, PDO::PARAM_INT);
        $stmtEditarUsuario->execute();

        $_SESSION['editarUsuario'] = true;
        ?>
        <form method="POST" name="formulario" action="../view/admin/gestionarUsuarios.php"></form>
        <script> document.formulario.submit(); </script>
        <?php
        exit();
    } catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        die();
    }