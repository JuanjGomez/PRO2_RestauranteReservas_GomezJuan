<?php
    session_start();
    if(!isset($_SESSION['id']) && !in_array($_SESSION['rol'] , ['Gerente'])){
        header('Location:../../index.php');
        exit();
    }
    if(isset($_SESSION['empleado']) && $_SESSION['empleado']){
        unset($_SESSION['empleado']);
    }
    if(isset($_SESSION['query']) && $_SESSION['query']){
        unset($_SESSION['query']);
    }
    if(isset($_GET['salir']) && $_GET['salir']){
        header('Location: viewGerente.php');
        exit();
    }
    if(isset($_GET['borrar']) && $_GET['borrar']){
        header('Location: gestionarUsuarios.php');
        exit();
    }