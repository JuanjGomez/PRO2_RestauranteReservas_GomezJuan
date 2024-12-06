<?php
    try{
        require_once '../../procesos/conexion.php';
        if (isset($_GET['orden']) && ($_GET['orden'] == "asc" || $_GET['orden'] == "desc")) {
            $orden = trim($_GET['orden']);
            // Consulta para ver los usuarios ordenados alfabéticamente
            $sqlOrden = "SELECT * FROM usuarios u 
                        INNER JOIN roles r ON u.id_rol = r.id_rol 
                        ORDER BY u.usuario $orden";
            $stmtResultado = $conn->prepare($sqlOrden);
        } else if (isset($_GET['rol'])) {
            $rol = trim($_GET['rol']);
            // Consulta para ver los usuarios que tienen un rol determinado
            $sqlRol = "SELECT * FROM usuarios u  
                        INNER JOIN roles r ON u.id_rol = r.id_rol 
                        WHERE u.id_rol = :id_rol";
            $stmtResultado = $conn->prepare($sqlRol);
            $stmtResultado->bindParam(':id_rol', $rol, PDO::PARAM_INT);
            $_SESSION['empleado'] = htmlspecialchars(trim($rol));
        } else if(isset($_GET['fechaContrato'])) {
            // Consulta para encontrar usuarios segun la fecha de contrato y sumativo
            $fechaContrato = htmlspecialchars(trim($_GET['fechaContrato']));
            $sqlFechaContrato = "SELECT * FROM usuarios u 
                                INNER JOIN roles r ON u.id_rol = r.id_rol 
                                WHERE DATE(u.fecha_creacion) = :fecha_contrato";
            if(isset($_SESSION['empleado'])){
                $sqlFechaContrato .= " AND u.id_rol = :id_rol";
            }
            $stmtResultado = $conn->prepare($sqlFechaContrato);
            $stmtResultado->bindParam(':fecha_contrato', $fechaContrato, PDO::PARAM_STR);
            if(isset($_SESSION['empleado'])){
                $stmtResultado->bindParam(':id_rol', $_SESSION['empleado'], PDO::PARAM_INT);
            }
        } else if(isset($_GET['fechaNacimiento'])){
            // Consulta por buscar fecha de nacimiento general y sumativo
            $fechaNacimiento = htmlspecialchars(trim($_GET['fechaNacimiento']));
            $sqlFechaDeNacimiento = "SELECT * FROM usuarios u
                                    INNER JOIN roles r ON u.id_rol = r.id_rol 
                                    WHERE fecha_nacimiento = :fecha_nacimiento";
            if(isset($_SESSION['empleado'])){
                $sqlFechaDeNacimiento .= " AND r.id_rol = :id_rol";
            }
            $stmtResultado = $conn->prepare($sqlFechaDeNacimiento);
            $stmtResultado->bindParam(':fecha_nacimiento', $fechaNacimiento, PDO::PARAM_STR);
            if(isset($_SESSION['empleado'])){
                $stmtResultado->bindParam(':id_rol', $_SESSION['empleado'], PDO::PARAM_INT);
            }
        } else if(isset($_GET['nombre']) || isset($_GET['apellido'])){
            // Consulta para encontrar usuarios segun el nombre y apellido del empleado
            $nombre = htmlspecialchars(trim($_GET['nombre']));
            $apellido = htmlspecialchars(trim($_GET['apellido']));
            $nombreConcatenado = "%$nombre%";
            $apellidoConcatenado = "%$apellido%";
            $sqlEmpleado = "SELECT * FROM usuarios u 
                            INNER JOIN roles r ON u.id_rol = r.id_rol 
                            WHERE u.nombre LIKE :nombre AND u.apellido LIKE :apellido";
            if(isset($_SESSION['empleado'])){
                $sqlEmpleado.= " AND r.id_rol = :id_rol";
            }
            $stmtResultado = $conn->prepare($sqlEmpleado);
            $stmtResultado->bindParam(':nombre',$nombreConcatenado, PDO::PARAM_STR);
            $stmtResultado->bindParam(':apellido',$apellidoConcatenado, PDO::PARAM_STR);
            if(isset($_SESSION['empleado'])){
                $stmtResultado->bindParam(':id_rol', $_SESSION['empleado'], PDO::PARAM_INT);
            }
        } else if(isset($_GET['query'])) { 
            $query = trim($_GET['query']);
            // Consulta de datos por la barra de busqueda
            $sqlQuery = 'SELECT * FROM usuarios u 
                        INNER JOIN roles r ON u.id_rol = r.id_rol 
                        WHERE u.usuario LIKE :query1 
                        OR u.nombre LIKE :query2 
                        OR u.apellido LIKE :query3 
                        OR u.email LIKE :query4 
                        OR r.nombre_rol LIKE :query5 
                        OR u.direccion LIKE :query6 
                        OR u.telefono LIKE :query7';
            $stmtResultado = $conn->prepare($sqlQuery);
            $queryConcatenado = "%$query%";
            $stmtResultado->bindParam(':query1', $queryConcatenado, PDO::PARAM_STR);
            $stmtResultado->bindParam(':query2', $queryConcatenado, PDO::PARAM_STR);
            $stmtResultado->bindParam(':query3', $queryConcatenado, PDO::PARAM_STR);
            $stmtResultado->bindParam(':query4', $queryConcatenado, PDO::PARAM_STR);
            $stmtResultado->bindParam(':query5', $queryConcatenado, PDO::PARAM_STR);
            $stmtResultado->bindParam(':query6', $queryConcatenado, PDO::PARAM_STR);
            $stmtResultado->bindParam(':query7', $queryConcatenado, PDO::PARAM_STR);
            $_SESSION['query'] = htmlspecialchars(trim($query));
        } else {
            $sqlUsarios = "SELECT * FROM usuarios u 
                            INNER JOIN roles r ON u.id_rol = r.id_rol";
            $stmtResultado = $conn->prepare($sqlUsarios);
        }
        $stmtResultado->execute();
        $resultados = $stmtResultado->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e){
        echo 'ERROR: ' . $e->getMessage();
        die();
    }