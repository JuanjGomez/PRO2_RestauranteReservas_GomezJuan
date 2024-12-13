<?php
    try{
        if(isset($_GET['orden'])){
            $orden = strtoupper($_GET['orden']); // Convertir a mayúsculas
        if (!in_array($orden, ['ASC', 'DESC'])) {
            $orden = 'ASC'; // Valor predeterminado si es inválido
        }
        $sqlVerReservasOrden = "SELECT r.id_reserva, r.nombre_reserva, r.hora_inicio_reserva, r.hora_final_reserva, 
                                u.usuario, m.id_mesa, s.nombre_sala, ts.tipo_sala, 
                                CASE 
                                    WHEN r.hora_final_reserva > NOW() THEN 1 
                                    ELSE 0 
                                END AS puede_eliminar
                        FROM reservas r 
                        INNER JOIN usuarios u ON r.id_usuario = u.id_usuario 
                        INNER JOIN mesa m ON r.id_mesa = m.id_mesa 
                        INNER JOIN sala s ON m.id_sala = s.id_sala 
                        INNER JOIN tipo_sala ts ON s.id_tipoSala = ts.id_tipoSala 
                        ORDER BY r.hora_inicio_reserva $orden";
        $stmtVerReservas = $conn->prepare($sqlVerReservasOrden);
        } else if(isset($_GET['camarero'])){
            $camarero = trim($_GET['camarero']); // Sanitizar entrada
            $sqlVerReservasEncargado = "SELECT r.id_reserva, r.nombre_reserva, r.hora_inicio_reserva, r.hora_final_reserva, 
                                        u.usuario, m.id_mesa, s.nombre_sala, ts.tipo_sala, 
                                        CASE 
                                            WHEN r.hora_final_reserva > NOW() THEN 1 
                                            ELSE 0 
                                        END AS puede_eliminar
                                        FROM reservas r 
                                        INNER JOIN usuarios u ON r.id_usuario = u.id_usuario 
                                        INNER JOIN mesa m ON r.id_mesa = m.id_mesa 
                                        INNER JOIN sala s ON m.id_sala = s.id_sala 
                                        INNER JOIN tipo_sala ts ON s.id_tipoSala = ts.id_tipoSala 
                                        WHERE u.id_usuario = :usuario";
            $stmtVerReservas = $conn->prepare($sqlVerReservasEncargado);
            $stmtVerReservas->bindParam(":usuario", $camarero, PDO::PARAM_INT);
            $_SESSION['encargado'] = $camarero;
        } else if(isset($_GET['tipoSala'])){
            $id_tipoSala = trim($_GET['tipoSala']); // Sanitizar entrada
            $sqlVerReservasTipoSala = "SELECT r.id_reserva, r.nombre_reserva, r.hora_inicio_reserva, r.hora_final_reserva, 
                                    u.usuario, m.id_mesa, s.nombre_sala, ts.tipo_sala, 
                                    CASE 
                                        WHEN r.hora_final_reserva > NOW() THEN 1 
                                        ELSE 0 
                                    END AS puede_eliminar
                                    FROM reservas r 
                                    INNER JOIN usuarios u ON r.id_usuario = u.id_usuario 
                                    INNER JOIN mesa m ON r.id_mesa = m.id_mesa 
                                    INNER JOIN sala s ON m.id_sala = s.id_sala 
                                    INNER JOIN tipo_sala ts ON s.id_tipoSala = ts.id_tipoSala 
                                    WHERE ts.id_tipoSala = :id_tipoSala";
            $stmtVerReservas = $conn->prepare($sqlVerReservasTipoSala);
            $stmtVerReservas->bindParam(":id_tipoSala", $id_tipoSala, PDO::PARAM_INT);
            $_SESSION['tipoSala'] = $id_tipoSala;
        } else if(isset($_GET['sala'])){
            $id_sala = trim($_GET['sala']); // Sanitizar entrada
            $sqlverReservasSalas = "SELECT r.id_reserva, r.nombre_reserva, r.hora_inicio_reserva, r.hora_final_reserva, 
                                    u.usuario, m.id_mesa, s.nombre_sala, ts.tipo_sala, 
                                    CASE 
                                        WHEN r.hora_final_reserva > NOW() THEN 1 
                                        ELSE 0 
                                    END AS puede_eliminar
                                FROM reservas r 
                                INNER JOIN usuarios u ON r.id_usuario = u.id_usuario 
                                INNER JOIN mesa m ON r.id_mesa = m.id_mesa 
                                INNER JOIN sala s ON m.id_sala = s.id_sala 
                                INNER JOIN tipo_sala ts ON s.id_tipoSala = ts.id_tipoSala 
                                WHERE s.id_sala = :id_sala";
            $stmtVerReservas = $conn->prepare($sqlverReservasSalas);
            $stmtVerReservas->bindParam(":id_sala", $id_sala, PDO::PARAM_INT);
            $_SESSION['sala'] = $id_sala;
        } else if(isset($_GET['mesa'])){
            $id_Mesa = htmlspecialchars(trim($_GET['mesa']));
            $sqlVerReservaMesa = "SELECT r.id_reserva, r.nombre_reserva, r.hora_inicio_reserva, r.hora_final_reserva, 
                                u.usuario, m.id_mesa, s.nombre_sala, ts.tipo_sala, 
                                CASE 
                                    WHEN r.hora_final_reserva > NOW() THEN 1 
                                    ELSE 0 
                                END AS puede_eliminar
                                FROM reservas r 
                                INNER JOIN usuarios u ON r.id_usuario = u.id_usuario 
                                INNER JOIN mesa m ON r.id_mesa = m.id_mesa 
                                INNER JOIN sala s ON m.id_sala = s.id_sala 
                                INNER JOIN tipo_sala ts ON s.id_tipoSala = ts.id_tipoSala";
            // Si hay una sala definida en la sesión, añadir el filtro correspondiente
            if (isset($_SESSION['sala'])) {
                $sqlVerReservaMesa .= " WHERE m.id_mesa = :id_mesa AND s.id_sala = :id_sala";
                $id_sala = htmlspecialchars(trim($_SESSION['sala'])); // Obtener la sala desde la sesión
            } else {
                $sqlVerReservaMesa .= " WHERE m.id_mesa = :id_mesa";
            }
            $stmtVerReservas = $conn->prepare($sqlVerReservaMesa);
            $stmtVerReservas->bindParam(":id_mesa", $id_Mesa, PDO::PARAM_INT);
            if (isset($id_sala)) {
                $stmtVerReservas->bindParam(":id_sala", $id_sala, PDO::PARAM_INT);
            }
        } else if(isset($_GET['tiempo'])){
            $tiempo = htmlspecialchars(trim($_GET['tiempo']));
            // Calcular los rangos de tiempo
            $timeRangeStart = date('Y-m-d H:i:s', strtotime("$tiempo - 24 hours"));
            $timeRangeEnd = date('Y-m-d H:i:s', strtotime("$tiempo + 24 hours"));
            $sqlTiempo = "SELECT r.id_reserva, r.nombre_reserva, r.hora_inicio_reserva, r.hora_final_reserva, 
                        u.usuario, m.id_mesa, s.nombre_sala, ts.tipo_sala, 
                        CASE 
                            WHEN r.hora_final_reserva > NOW() THEN 1 
                            ELSE 0 
                        END AS puede_eliminar
                    FROM reservas r 
                    INNER JOIN usuarios u ON r.id_usuario = u.id_usuario 
                    INNER JOIN mesa m ON r.id_mesa = m.id_mesa 
                    INNER JOIN sala s ON m.id_sala = s.id_sala 
                    INNER JOIN tipo_sala ts ON s.id_tipoSala = ts.id_tipoSala 
                    WHERE r.hora_inicio_reserva BETWEEN :timeStart AND :timeEnd";
                    // Verificar si hay un encargado en la sesión y ajustar la consulta
                    if (isset($_SESSION['encargado'])) {
                        $sqlTiempo .= " AND u.id_usuario = :id_usuario";
                        $id_usuario = htmlspecialchars(trim($_SESSION['encargado']));
                    }
            $stmtVerReservas = $conn->prepare($sqlTiempo);
            $stmtVerReservas->bindParam(":timeStart", $timeRangeStart, PDO::PARAM_STR);
            $stmtVerReservas->bindParam(":timeEnd", $timeRangeEnd, PDO::PARAM_STR);
            // Vincular parámetro del encargado si existe
            if (isset($id_usuario)) {
                $stmtVerReservas->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
            }
        } else if(isset($_GET['query'])){
            $query = "%" . htmlspecialchars(trim($_GET['query'] ?? '')) . "%";  // Manejar el caso de una consulta vacía de forma segura
            $sqlBusqueda = "SELECT r.id_reserva, r.nombre_reserva, r.hora_inicio_reserva, r.hora_final_reserva, 
                            u.usuario, m.id_mesa, s.nombre_sala, ts.tipo_sala, 
                            CASE 
                                WHEN r.hora_final_reserva > NOW() THEN 1 
                                ELSE 0 
                            END AS puede_eliminar
                            FROM reservas r 
                            INNER JOIN usuarios u ON r.id_usuario = u.id_usuario 
                            INNER JOIN mesa m ON r.id_mesa = m.id_mesa 
                            INNER JOIN sala s ON m.id_sala = s.id_sala 
                            INNER JOIN tipo_sala ts ON s.id_tipoSala = ts.id_tipoSala 
                            WHERE r.nombre_reserva LIKE :busqueda 
                            OR u.usuario LIKE :busqueda 
                            OR m.id_mesa LIKE :busqueda 
                            OR s.nombre_sala LIKE :busqueda";
            $stmtVerReservas = $conn->prepare($sqlBusqueda);
            $stmtVerReservas->bindParam(":busqueda", $query, PDO::PARAM_STR);
            $_SESSION['query'] = $_GET['query'] ?? '';
        } else {
            $sqlVerReservas = "SELECT r.id_reserva, r.nombre_reserva, r.hora_inicio_reserva, r.hora_final_reserva, 
                            u.usuario, m.id_mesa, s.nombre_sala, ts.tipo_sala, 
                            CASE 
                                WHEN r.hora_final_reserva > NOW() THEN 1 
                                ELSE 0 
                            END AS puede_eliminar
                            FROM reservas r 
                            INNER JOIN usuarios u ON r.id_usuario = u.id_usuario 
                            INNER JOIN mesa m ON r.id_mesa = m.id_mesa 
                            INNER JOIN sala s ON m.id_sala = s.id_sala 
                            INNER JOIN tipo_sala ts ON s.id_tipoSala = ts.id_tipoSala";

            $stmtVerReservas = $conn->prepare($sqlVerReservas);
        }
        $stmtVerReservas->execute();
        $reservas = $stmtVerReservas->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e){
        echo "Error: ". $e->getMessage();
        die();
    }