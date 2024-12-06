<?php
    try{
        if(isset($_POST['orden'])){

        } else {
            $sqlSalas = "SELECT s.id_sala, s.nombre_sala, tp.tipo_sala, imagen_sala, COUNT(m.id_mesa) AS total_mesas, COUNT(h.id_historial) AS total_historial
                        FROM sala s
                        LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala
                        LEFT JOIN mesa m ON s.id_sala = m.id_sala
                        LEFT JOIN historial h ON m.id_mesa = h.id_mesa
                        GROUP BY s.id_sala;";
            $stmtResultado = $conn->prepare($sqlSalas);
        }
        $stmtResultado->execute();
        $resultadosSalas = $stmtResultado->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: ". $e->getMessage();
        die();
    }