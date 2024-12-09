<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location:../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'GET'){
        header('Location:../view/admin/gestionarSalas.php');
        exit();
    }
    require_once 'conexion.php';
    $idSala = htmlspecialchars(trim($_GET['id']));
    try{
        $conn->beginTransaction();

        // Obtener el tipo de sala a eliminar 
        $sqlTipoSala = "SELECT id_tipoSala FROM sala WHERE id_sala = :id_sala";
        $stmtTipoSala = $conn->prepare($sqlTipoSala);
        $stmtTipoSala->bindParam(':id_sala', $idSala);
        $stmtTipoSala->execute();
        $tipoSala = $stmtTipoSala->fetchColumn();

        // Obtener todas las mesas de la sala que se va a elimnar
        $sqlTodasMesas = "SELECT id_sala FROM sala 
                        WHERE id_sala != :id_sala AND id_tipoSala = :tipoSala 
                        ORDER BY (SELECT COUNT(*) FROM mesa WHERE mesa.id_sala = sala.id_sala) ASC LIMIT 1 ";
        $stmtTodasMesas = $conn->prepare($sqlTodasMesas);
        $stmtTodasMesas->bindParam(':id_sala', $idSala);
        $stmtTodasMesas->bindParam(':tipoSala', $tipoSala);
        $stmtTodasMesas->execute();
        $salaDestino = $stmtTodasMesas->fetch(PDO::FETCH_ASSOC);

        if($salaDestino){
            // Reasignar las mesas a la sala con menos mesas del mismo tipo
            $sqlActualizaMesas = "UPDATE mesa 
                                SET id_sala = :idSalaDestino 
                                WHERE id_sala = :id_sala";
            $stmtActualizaMesas = $conn->prepare($sqlActualizaMesas);
            $stmtActualizaMesas->bindParam(':idSalaDestino', $salaDestino['id_sala']);
            $stmtActualizaMesas->bindParam(':id_sala', $idSala);
            $stmtActualizaMesas->execute();
        }

        // Eliminar la sala
        $sqlEliminarSala = "DELETE FROM sala WHERE id_sala = :id_sala";
        $stmtEliminarSala = $conn->prepare($sqlEliminarSala);
        $stmtEliminarSala->bindParam(':id_sala', $idSala);
        $stmtEliminarSala->execute();

        // Confirmar la transaccion
        $conn->commit();
        $_SESSION['successBorrarSala'] = true;
        header('Location:../view/admin/gestionarSalas.php');
        exit();

    } catch(PDOException $e){
        // Rollback en caso de error
        $conn->rollBack();
        $_SESSION['errorBorrarSala'] = true;
        header('Location:../view/admin/gestionarSalas.php');
        die();
    }