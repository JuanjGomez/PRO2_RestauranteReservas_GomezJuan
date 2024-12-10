<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location:../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'GET'){
        header('Location:../view/admin/gestionarMesas.php');
        exit();
    }
    require_once 'conexion.php';
    $idMesa = htmlspecialchars(trim($_GET['id']));

    // Consulta para eliminar el historial y reservas, para poder eliminar la mesa
    try{
        $conn->beginTransaction();

        // Consulta para eliminar las reservas que se hicieron con la mesa que se viene por ID
        $sqlEliminarHistoriaMesa = "DELETE FROM historial 
                                    WHERE id_mesa = :id_mesa";
        $stmtEliminarHistorialMesa = $conn->prepare($sqlEliminarHistoriaMesa);
        $stmtEliminarHistorialMesa->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtEliminarHistorialMesa->execute();

        // Consulta para eliminar todas las reservas que se hicieron con la mesa que viene por ID
        $sqlEliminarReservasMesa = "DELETE FROM reservas 
                                    WHERE id_mesa = :id_mesa";
        $stmtEliminarReservasMesa = $conn->prepare($sqlEliminarReservasMesa);
        $stmtEliminarReservasMesa->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtEliminarReservasMesa->execute();

        // Consulta para eliminar la mesa que se viene por ID
        $sqlEliminarMesa = "DELETE FROM mesa 
                            WHERE id_mesa = :id_mesa";
        $stmtEliminarMesa = $conn->prepare($sqlEliminarMesa);
        $stmtEliminarMesa->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtEliminarMesa->execute();
        
        $conn->commit();

        $_SESSION['successEliminarMesa'] = true;
        header('Location:../view/admin/gestionarMesas.php');
        exit();
    } catch(PDOException $e){
        $conn->rollBack();
        echo "Error al eliminar la mesa: ". $e->getMessage();
        die();
    }