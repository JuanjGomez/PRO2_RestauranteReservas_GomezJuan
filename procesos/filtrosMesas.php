<?php
    try{
        if(isset($_GET['orden'])){
            $orden = $_GET['orden'];
            $sqlOrden = "SELECT * 
                        FROM mesa m
                        LEFT JOIN sala s ON m.id_sala = s.id_sala
                        LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala 
                        ORDER BY m.id_mesa $orden";
            $stmtResultado = $conn->prepare($sqlOrden);
        } else if (isset($_GET['tipoSala'])) {
            $id_tipoSala = $_GET['tipoSala'];        
            // Consulta para obtener las mesas del tipo de sala seleccionado
            $sqlMesaTipoSala = "SELECT * 
                                FROM mesa m
                                LEFT JOIN sala s ON m.id_sala = s.id_sala
                                LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala 
                                WHERE tp.id_tipoSala = :id_tipoSala";
            $stmtResultado = $conn->prepare($sqlMesaTipoSala);        
            $stmtResultado->bindParam(":id_tipoSala", $id_tipoSala, PDO::PARAM_INT);
            $_SESSION['salaTipo'] = $id_tipoSala;
        } else if(isset($_GET['sala'])){
            $salaID = trim($_GET['sala']);
            // Consulta para obtener todas las mesas de una sala seleccionada
            $sqlMesaXsala = "SELECT * 
                            FROM mesa m
                            LEFT JOIN sala s ON m.id_sala = s.id_sala
                            LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala 
                            WHERE s.id_sala = :salaID";
            $stmtResultado = $conn->prepare($sqlMesaXsala);
            $stmtResultado->bindParam(":salaID", $salaID, PDO::PARAM_INT);
        } else {
            $sqlMesas = "SELECT * 
                    FROM mesa m
                    LEFT JOIN sala s ON m.id_sala = s.id_sala
                    LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala";
            $stmtResultado = $conn->prepare($sqlMesas);
        }
        $stmtResultado->execute();
        $result = $stmtResultado->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: ". $e->getMessage();
        die();
    }