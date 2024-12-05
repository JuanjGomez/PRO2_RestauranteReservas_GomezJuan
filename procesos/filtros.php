<?php
    try{
        // session_start();
        require_once "../procesos/conexion.php";
        if (isset($_GET['orden']) && ($_GET['orden'] == "asc" || $_GET['orden'] == "desc")) {
            // Recoge si viene orden por method GET
            $orden = htmlspecialchars(trim($_GET['orden']));
            if(isset($_SESSION['camarero'])){
                $camarero = htmlspecialchars(trim($_SESSION['camarero']));
                // Consulta para ver lo sumativo de lo que ha hecho camarero
                $sqlOrden = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                            FROM historial h 
                            INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                            INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                            INNER JOIN sala s ON s.id_sala = m.id_sala 
                            INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                            WHERE c.id_usuario = :id_usuario 
                            ORDER BY h.hora_inicio $orden";
                $stmtResultado = $conn->prepare($sqlOrden);
                $stmtResultado->bindParam(":id_camarero", $camarero, PDO::PARAM_INT);
            } else {
                // Consulta para ordenar por orden de hora_inicio la tabla
                $sqlOrden = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                                FROM historial h 
                                INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                                INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                                INNER JOIN sala s ON s.id_sala = m.id_sala 
                                INNER JOIN tipo_sala tp ON tp.id_tipoSala = s.id_tipoSala 
                                ORDER BY h.hora_inicio $orden";
                $stmtResultado = $conn->prepare($sqlOrden);
            }
        } else if (isset($_GET['camarero'])) {
            //Recoge lo que viene por GET el id de camarero
            $camarero = htmlspecialchars(trim($_GET['camarero']));
            // Consulta para ver todos las ocupaciones que ha hecho un camarero
            $sqlCamarero = "SELECT c.id_usuario, c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                            FROM historial h 
                            INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                            INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                            INNER JOIN sala s ON s.id_sala = m.id_sala 
                            INNER JOIN tipo_sala tp On tp.id_tipoSala = s.id_tipoSala 
                            WHERE c.id_usuario = :id_camarero";
            $stmtResultado = $conn->prepare($sqlCamarero);
            $stmtResultado->bindParam(":id_camarero", $camarero, PDO::PARAM_INT);
            $_SESSION['camarero'] = htmlspecialchars_decode($_GET['camarero']); // Se guarda para que sea sumativo
        } else if (isset($_GET['tipoSala'])) {
            // Recoge el valor tipoSala por por method GET
            $tipoSala = htmlspecialchars(trim($_GET['tipoSala']));
            $_SESSION['tipoSala'] = $tipoSala;
            // Consulta para filtrar por tipo de sala la tabla
            if (!isset($_SESSION['camarero'])) {
                // Consulta para ver lo sumativo de lo que ha hecho camarero
                $sqlTipoSala = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin, tp.id_tipoSala 
                                FROM historial h 
                                INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                                INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                                INNER JOIN sala s ON s.id_sala = m.id_sala 
                                INNER JOIN tipo_sala tp On tp.id_tipoSala = s.id_tipoSala 
                                WHERE tp.id_tipoSala = :id_tipoSala";
                $stmtResultado = $conn->prepare($sqlTipoSala);
                $stmtResultado->bindParam(":id_tipoSala", $tipoSala, PDO::PARAM_INT);
            } else {
                // Consulta para ver todas las los datos de del camareror seleccionado anteriormente, con el fin de las ocupaciones y desocupa que realizo solo el camarero seleccionado
                $camarero = htmlspecialchars(trim($_SESSION['camarero']));
                $sqlTipoSala = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin, tp.id_tipoSala 
                                FROM historial h 
                                INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                                INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                                INNER JOIN sala s ON s.id_sala = m.id_sala 
                                INNER JOIN tipo_sala tp On tp.id_tipoSala = s.id_tipoSala 
                                WHERE tp.id_tipoSala = :id_tipoSala AND c.id_usuario = :id_camarero";
                $stmtResultado = $conn->prepare($sqlTipoSala);
                $stmtResultado->bindParam(":id_tipoSala", $tipoSala, PDO::PARAM_INT);
                $stmtResultado->bindParam(":id_camarero", $camarero, PDO::PARAM_INT);
            }
            if (isset($_SESSION['sala']) && $_SESSION['sala']) {
                unset($_SESSION['sala']);
            }
        } else if (isset($_GET['sala'])) {
            // Recoge el valor sala por method GET
            $id_sala = htmlspecialchars(trim($_GET['sala']));
            $_SESSION['sala'] = $id_sala;
            $salaID = htmlspecialchars(trim($_SESSION['tipoSala']));
            // Consulta para filtrar por tipo de sala y sala para mostrar en la tabla
            if (!isset($_SESSION['camarero'])) {
                // Recoge todos los datos de las ocupas y desocupas de cada sala
                $sqlSala = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin, s.id_sala 
                            FROM historial h 
                            INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                            INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                            INNER JOIN sala s ON s.id_sala = m.id_sala 
                            INNER JOIN tipo_sala tp On tp.id_tipoSala = s.id_tipoSala 
                            WHERE tp.id_tipoSala = :id_tipoSala AND s.id_sala = :id_sala";
                $stmtResultado = $conn->prepare($sqlSala);
                $stmtResultado->bindParam(":id_tipoSala", $salaID, PDO::PARAM_INT);
                $stmtResultado->bindParam(":id_sala", $id_sala, PDO::PARAM_INT);
            } else {
                // Recoge los datos de un solo camarero elegido previamente para ver sus ocupas y desocupas
                $camarero = htmlspecialchars(trim($_SESSION['camarero']));
                $sqlSala = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin, s.id_sala 
                            FROM historial h 
                            INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                            INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                            INNER JOIN sala s ON s.id_sala = m.id_sala 
                            INNER JOIN tipo_sala tp On tp.id_tipoSala = s.id_tipoSala 
                            WHERE tp.id_tipoSala = :id_tipoSala AND s.id_sala = :id_sala AND c.id_usuario = :id_camarero";
                $stmtResultado = $conn->prepare($sqlSala);
                $stmtResultado->bindParam(":id_tipoSala", $salaID, PDO::PARAM_INT);
                $stmtResultado->bindParam(":id_sala", $id_sala, PDO::PARAM_INT);
                $stmtResultado->bindParam(":id_camarero", $camarero, PDO::PARAM_INT);
            }
        } else if (isset($_GET['mesa'])) {
            if (!isset($_SESSION['sala'])) {
                // Recoge los datos que vienen por method GET
                $idMesa = htmlspecialchars(trim($_GET['mesa']));
                // Consulta para ver todas las mesas
                $sqlMesa = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                            FROM historial h 
                            INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                            INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                            INNER JOIN sala s ON s.id_sala = m.id_sala 
                            INNER JOIN tipo_sala tp On tp.id_tipoSala = s.id_tipoSala 
                            WHERE m.id_mesa = :id_mesa";
                $stmtResultado = $conn->prepare($sqlMesa);
                $stmtResultado->bindParam(":id_mesa", $idMesa, PDO::PARAM_INT);
            } else {
                // Recoge los datos que vienen por method GET y sesiones
                $idMesa = htmlspecialchars(trim($_GET['mesa']));
                $sala = htmlspecialchars(trim($_SESSION['sala']));
                // Consulta para ver todas las mesas
                $sqlMesaS = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin, s.id_sala 
                            FROM historial h 
                            INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                            INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                            INNER JOIN sala s ON s.id_sala = m.id_sala 
                            INNER JOIN tipo_sala tp On tp.id_tipoSala = s.id_tipoSala 
                            WHERE m.id_mesa = :id_mesa AND s.id_sala = :id_sala";
                $stmtResultado = $conn->prepare($sqlMesaS);
                $stmtResultado->bindParam(":id_mesa", $idMesa, PDO::PARAM_INT);
                $stmtResultado->bindParam(":id_sala", $sala, PDO::PARAM_INT);
            }
        } else if (isset($_GET['tiempo'])) {
            $tiempo = htmlspecialchars(trim($_GET['tiempo']));
            $_SESSION['tiempo'] = $tiempo;
            if(!isset($_SESSION['camarero'])){
                // Consulta para obtener los datos según el tiempo
                $sqlTiempo = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                                FROM historial h 
                                INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                                INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                                INNER JOIN sala s ON s.id_sala = m.id_sala 
                                INNER JOIN tipo_sala tp On tp.id_tipoSala = s.id_tipoSala 
                                WHERE (h.hora_inicio 
                                BETWEEN DATE_SUB(:tiempo1, INTERVAL 24 HOUR) AND DATE_ADD(:tiempo2, INTERVAL 24 HOUR)) 
                                OR (h.hora_fin BETWEEN DATE_SUB(:tiempo3, INTERVAL 24 HOUR) AND DATE_ADD(:tiempo4, INTERVAL 24 HOUR))";
                $stmtResultado = $conn->prepare($sqlTiempo);
                $stmtResultado->bindParam(":tiempo1", $tiempo);
                $stmtResultado->bindParam(":tiempo2", $tiempo);
                $stmtResultado->bindParam(":tiempo3", $tiempo);
                $stmtResultado->bindParam(":tiempo4", $tiempo);
            } else {
                $camarero = htmlspecialchars(trim($_SESSION['camarero']));
                $sqlTiempo = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                                FROM historial h 
                                INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                                INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                                INNER JOIN sala s ON s.id_sala = m.id_sala 
                                INNER JOIN tipo_sala tp On tp.id_tipoSala = s.id_tipoSala 
                                WHERE ((h.hora_inicio 
                                BETWEEN DATE_SUB(:tiempo1, INTERVAL 24 HOUR) AND DATE_ADD(:tiempo2, INTERVAL 24 HOUR)) 
                                OR (h.hora_fin BETWEEN DATE_SUB(:tiempo3, INTERVAL 24 HOUR) AND DATE_ADD(:tiempo4, INTERVAL 24 HOUR))) 
                                AND (c.id_usuario = :id_camarero)";
                $stmtResultado = $conn->prepare($sqlTiempo);
                $stmtResultado->bindParam(":tiempo1", $tiempo);
                $stmtResultado->bindParam(":tiempo2", $tiempo);
                $stmtResultado->bindParam(":tiempo3", $tiempo);
                $stmtResultado->bindParam(":tiempo4", $tiempo);
                $stmtResultado->bindParam(":id_camarero", $camarero, PDO::PARAM_INT);
            }
        } else if (isset($_GET['query'])) {
            $busqueda = htmlspecialchars(trim($_GET['query']));
            $sqlBusqueda = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                            FROM historial h 
                            INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                            INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                            INNER JOIN sala s ON s.id_sala = m.id_sala 
                            INNER JOIN tipo_sala tp On tp.id_tipoSala = s.id_tipoSala 
                            WHERE c.nombre = :busqueda1 OR tp.tipo_sala = :busqueda2 OR s.nombre_sala = :busqueda3 OR m.id_mesa = :busqueda4";
            $stmtResultado = $conn->prepare($sqlBusqueda);
            $stmtResultado->bindParam(":busqueda1", $busqueda);
            $stmtResultado->bindParam(":busqueda2", $busqueda);
            $stmtResultado->bindParam(":busqueda3", $busqueda);
            $stmtResultado->bindParam(":busqueda4", $busqueda);
            $_SESSION['busqueda'] = $busqueda;
        } else {
            // Consulta para obtener todos los datos de la base de datos
            $sql = "SELECT c.nombre, tp.tipo_sala, s.nombre_sala, m.id_mesa, h.hora_inicio, h.hora_fin 
                    FROM historial h 
                    INNER JOIN usuarios c ON h.id_usuario = c.id_usuario 
                    INNER JOIN mesa m ON m.id_mesa = h.id_mesa 
                    INNER JOIN sala s ON s.id_sala = m.id_sala 
                    INNER JOIN tipo_sala tp On tp.id_tipoSala = s.id_tipoSala";
            $stmtResultado = $conn->prepare($sql);
        }
        $stmtResultado->execute();
        $resultados = $stmtResultado->fetchAll();
    } catch (PDOException $e) {
        echo "Error: ". $e->getMessage();
        die();
    }