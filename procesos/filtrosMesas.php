<?php
    try{
        $iDSala = htmlspecialchars($_SESSION['verMesas']);
        if(isset($_GET['orden'])){
            $orden = $_GET['orden'];
        } else {
            $sql = "SELECT * FROM mesa 
                    WHERE id_sala = :id_sala";
            $stmtResultado = $conn->prepare($sql);
            $stmtResultado->bindParam(":id_sala", $iDSala, PDO::PARAM_INT);
        }
        $stmtResultado->execute();
        $result = $stmtResultado->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: ". $e->getMessage();
        die();
    }