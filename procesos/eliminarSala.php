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

        //
    } catch(PDOException $e){
        echo "Error: ". $e->getMessage();
        die();
    }