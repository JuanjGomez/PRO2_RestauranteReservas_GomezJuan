<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location:../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'GET'){
        header('Location:../view/admin/gestionarUsuarios.php');
        exit();
    }
    require_once 'conexion.php';
    $id = trim($_GET['id']);
    try{
        $conn->beginTransaction();

        // Eliminar todas las reservas hechas por el usuario
        $sqlEliminarReservasUsuario = "DELETE FROM reservas WHERE id_usuario = :id_usuario";
        $stmtEliminarReservasUsuario = $conn->prepare($sqlEliminarReservasUsuario);
        $stmtEliminarReservasUsuario->bindParam(':id_usuario', $id, PDO::PARAM_INT);
        $stmtEliminarReservasUsuario->execute();

        // Eliminar el historial del usuario
        $sqlEliminarHistorial = "DELETE FROM historial WHERE id_usuario = :id_usuario";
        $stmtEliminarHistorial = $conn->prepare($sqlEliminarHistorial);
        $stmtEliminarHistorial->bindParam(':id_usuario', $id, PDO::PARAM_INT);
        $stmtEliminarHistorial->execute();

        // Eliminar el usuario de la tabla usuarios
        $sqlEliminarUsuario = 'DELETE FROM usuarios WHERE id_usuario = :id_usuario';
        $stmtEliminarUsuario = $conn->prepare($sqlEliminarUsuario);
        $stmtEliminarUsuario->bindParam(':id_usuario', $id, PDO::PARAM_INT);
        $stmtEliminarUsuario->execute();

        $conn->commit();
        $_SESSION['eliminarUsuario'] = true;
        ?>
        <form method="POST" name="formulario" action="../view/admin/gestionarUsuarios.php"></form>
        <script> document.formulario.submit(); </script>
        <?php
        exit();
    } catch(PDOException $e){
        $conn->rollBack();
        echo 'Error: ' . $e->getMessage();
        die();
    }