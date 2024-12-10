<?php
    session_start();
    if(!isset($_SESSION['id']) && !in_array($_SESSION['rol'] , ['Gerente'])){
        header('Location:../../index.php');
        exit();
    }
    if(isset($_SESSION['tipoSala']) && $_SESSION['tipoSala']){
        unset($_SESSION['tipoSala']);
    }
    if(isset($_SESSION['querySalas']) && $_SESSION['querySalas']){
        unset($_SESSION['querySalas']);
    }
    if(isset($_GET['salir']) && $_GET['salir']){
        header('Location: viewGerente.php');
        exit();
    }
    if(isset($_GET['borrar']) && $_GET['borrar']){
        header('Location: gestionarSalas.php');
        exit();
    }