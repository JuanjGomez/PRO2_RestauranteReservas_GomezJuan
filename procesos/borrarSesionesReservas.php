<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente', 'Camarero'])){
        header('Location:../index.php');
        exit();
    }
    if(isset($_SESSION['encargado']) && $_SESSION['encargado']){
        unset($_SESSION['encargado']);
    }
    if(isset($_SESSION['tipoSala']) && $_SESSION['tipoSala']){
        unset($_SESSION['tipoSala']);
    }
    if(isset($_SESSION['sala']) && $_SESSION['sala']){
        unset($_SESSION['sala']);
    }
    if(isset($_SESSION['query'])){
        unset($_SESSION['query']);
    }
    if(isset($_GET['borrar'])){
        header('Location:../view/verReservas.php');
        exit();
    }
    if(isset($_GET['salir'])){
        header('Location:../view/index.php');
        exit();
    }