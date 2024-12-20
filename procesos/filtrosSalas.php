<?php
    try{
        if(isset($_GET['orden'])){
            $orden = htmlspecialchars($_GET['orden']);
            // Ordenar alfabeticamente
            $sqlOrden = "SELECT s.id_sala, s.nombre_sala, tp.tipo_sala, imagen_sala
                        FROM sala s
                        LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala
                        ORDER BY s.nombre_sala $orden";
            $stmtResultado = $conn->prepare($sqlOrden);
        } else if(isset($_GET['popularidad'])){
            $popularidad = htmlspecialchars($_GET['popularidad']);
            // Consulta para ver el numeros de reservas y historial
            $sqlPopularidad = "SELECT s.id_sala, s.nombre_sala, tp.tipo_sala, imagen_sala, COUNT(h.id_historial) AS total_historial
                            FROM sala s
                            LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala
                            LEFT JOIN mesa m ON s.id_sala = m.id_sala
                            LEFT JOIN historial h ON m.id_mesa = h.id_mesa";
            // Si hay algun tipo de sala especifica a buscar se agrega en la consulta
            if(isset($_SESSION['tipoSala'])){
                $tipoSalaSumativo = $_SESSION['tipoSala'];
                $sqlPopularidad .= " WHERE s.id_tipoSala = :tipoSalaSumativo";
            }
            $sqlPopularidad .= " GROUP BY s.id_sala 
                            ORDER BY total_historial $popularidad";
            $stmtResultado = $conn->prepare($sqlPopularidad);
            if(isset($_SESSION['tipoSala'])){
                $stmtResultado->bindParam(":tipoSalaSumativo", $tipoSalaSumativo, PDO::PARAM_INT);
            }
        } else if(isset($_GET['tipoSala'])){
            $tipoSala = htmlspecialchars($_GET['tipoSala']);
            // Consulta para ver las salas de un tipo específico
            $sqlTiposSlas = "SELECT s.id_sala, s.nombre_sala, tp.tipo_sala, imagen_sala
                            FROM sala s
                            LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala
                            WHERE s.id_tipoSala = :tipoSala";
            $stmtResultado = $conn->prepare($sqlTiposSlas);
            $stmtResultado->bindParam(":tipoSala", $tipoSala, PDO::PARAM_INT);
            $_SESSION['tipoSala'] = $tipoSala;
        } else if(isset($_GET['disponibles'])){
            $disponibles = 0;
            // Consulta para ver las mesas disponibles
            $sqlDisponibles = "SELECT s.id_sala, s.nombre_sala, tp.tipo_sala, imagen_sala, COUNT(m.id_mesa) AS total_mesas 
                                FROM sala s 
                                LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala 
                                LEFT JOIN mesa m ON s.id_sala = m.id_sala 
                                WHERE m.libre = :libre";
            if(isset($_SESSION['tipoSala'])){
                $tipoSalaDisponibles = $_SESSION['tipoSala'];
                $sqlDisponibles .= " AND s.id_tipoSala = :tipoSalaDisponibles";
            }
            $sqlDisponibles .= " GROUP BY s.id_sala";
            $stmtResultado = $conn->prepare($sqlDisponibles);
            $stmtResultado->bindParam(':libre',$disponibles, PDO::PARAM_INT);
            if(isset($_SESSION['tipoSala'])){
                $stmtResultado->bindParam(":tipoSalaDisponibles", $tipoSalaDisponibles, PDO::PARAM_INT);
            }
        } else if(isset($_GET['query'])){
            $query = trim($_GET['query']);
            $queryConcadenado = "%$query%";
            // Consulta para buscar algun dato que esta en la tabla
            $sqlBusquedaSalas = "SELECT s.id_sala, s.nombre_sala, tp.tipo_sala, imagen_sala
                                FROM sala s
                                LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala
                                WHERE s.nombre_sala LIKE :busqueda1 OR tp.tipo_sala LIKE :busqueda2";
            $stmtResultado = $conn->prepare($sqlBusquedaSalas);
            $stmtResultado->bindParam(":busqueda1", $queryConcadenado);
            $stmtResultado->bindParam(":busqueda2", $queryConcadenado);
            $_SESSION['querySalas'] = $query;
        } else {
            // Consulta general para buscar un dato en la tabla
            $sqlSalas = "SELECT s.id_sala, s.nombre_sala, tp.tipo_sala, imagen_sala
                        FROM sala s
                        LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala";
            $stmtResultado = $conn->prepare($sqlSalas);
        }
        $stmtResultado->execute();
        $resultadosSalas = $stmtResultado->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: ". $e->getMessage();
        die();
    }