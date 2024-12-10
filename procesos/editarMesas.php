<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location:../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location:../view/admin/gestionarReservas.php');
        exit();
    }
    require_once 'conexion.php';
    $idMesa = trim($_POST['idMesa']);
    $idSala = trim($_POST['sala']);
    $numerosSillas = trim($_POST['numSillas']);

    try{
        $sqlEditarMesa = "UPDATE mesa 
                        SET id_sala = :id_sala, num_sillas = :num_sillas
                        WHERE id_mesa = :id_mesa";
        $stmtEditarMesa = $conn->prepare($sqlEditarMesa);
        $stmtEditarMesa->bindParam(':id_sala', $idSala, PDO::PARAM_INT);
        $stmtEditarMesa->bindParam(':num_sillas', $numerosSillas, PDO::PARAM_INT);
        $stmtEditarMesa->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtEditarMesa->execute();

        $_SESSION['editarMesaExitosa'] = true;
        ?>
        <form method="GET" name="formulario" action="../view/admin/gestionarMesas.php"></form>
        <script> document.formulario.submit(); </script>
        <?php
        exit();
    } catch(PDOException $e){
        echo "Error: ". $e->getMessage();
        die();
    }