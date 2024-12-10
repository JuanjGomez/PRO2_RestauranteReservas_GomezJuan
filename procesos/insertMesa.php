<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location:../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location:../view/admin/gestionarMesas.php');
        exit();
    }
    require_once 'conexion.php';
    $iDSala = trim($_POST['sala']);
    $libre = 0; // Por defecto la mesa estara libre al crearse
    $numeroDeSillas = trim($_POST['numSillas']);

    try{
        // Insert de mesa en la base de datos
        $sqlCrearMesa = "INSERT INTO mesa (id_sala, libre ,num_sillas) 
                        VALUES (:id_sala, :libre, :num_sillas)";
        $stmtCrearMesa = $conn->prepare($sqlCrearMesa);
        $stmtCrearMesa->bindParam(':id_sala', $iDSala);
        $stmtCrearMesa->bindParam(':libre', $libre);
        $stmtCrearMesa->bindParam(':num_sillas', $numeroDeSillas);
        $stmtCrearMesa->execute();

        $_SESSION['successCrearMesa'] = true;
        header('Location:../view/admin/gestionarMesas.php');
        exit();
    } catch(PDOException $e){
        echo "Error al crear la mesa: " . $e->getMessage();
        die();
    }