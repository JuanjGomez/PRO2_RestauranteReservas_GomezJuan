<?php
    session_start();
    if(!isset($_SESSION['id']) && $_SESSION['rol'] !== 'Gerente'){
        header('Location: ../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location:../view/admin/gestionarUsuarios.php');
        exit();
    }
    require_once 'conexion.php';
    try{
        $conn->beginTransaction();

        // Eliminar datos del usuario pasado por id de la tabla historial
        $idUser = htmlspecialchars(trim($_POST['id']));
        $sql = "DELETE FROM historial WHERE id_usuario = :id_usuario";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUser, PDO::PARAM_INT);
        $stmt->execute();

        // Eliminar los datos del usuario pasado por id de la tabla usuario
        $sql = "DELETE FROM usuario WHERE id_usuario = :id_usuario";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUser, PDO::PARAM_INT);
        $stmt->execute();
        
        $conn->commit();
        $_SESSION['userEliminado'] = true;
        
        header('Location:../view/admin/gestionarUsuarios.php');
        exit();
    } catch(PDOException $e){
        echo "Error: ". $e->getMessage();
        $conn->rollBack();
        die();
    }