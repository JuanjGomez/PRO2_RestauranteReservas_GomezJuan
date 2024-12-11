<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente', 'Camarero'])){
        header('Location:../../index.php');
        exit();
    }
    if(isset($_SESSION['salaTipo']) && $_SESSION['salaTipo']){
        unset($_SESSION['salaTipo']);
    }
    if(isset($_SESSION['salaID']) && $_SESSION['salaID']){
        unset($_SESSION['salaID']);
    }
    if(isset($_GET['salir']) && $_GET['salir']){
        header('Location: viewGerente.php');
        exit();
    }
    if(isset($_GET['borrar']) && $_GET['borrar']){
        header('Location: gestionarMesas.php');
        exit();
    }