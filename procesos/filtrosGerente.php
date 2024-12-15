<?php
    $usuario = $_SESSION['id'];
    try{
        require_once '../../procesos/conexion.php';
        if (isset($_GET['orden']) && ($_GET['orden'] == "asc" || $_GET['orden'] == "desc")) {
            $orden = trim($_GET['orden']);
            // Consulta para ver los usuarios ordenados alfabéticamente
            $sqlOrden = "SELECT * FROM usuarios u 
                        INNER JOIN roles r ON u.id_rol = r.id_rol 
                        WHERE u.id_usuario != :id_usuario 
                        ORDER BY u.usuario $orden";
            $stmtResultado = $conn->prepare($sqlOrden);
        } else if (isset($_GET['rol'])) {
            $rol = trim($_GET['rol']);
            // Consulta para ver los usuarios que tienen un rol determinado
            $sqlRol = "SELECT * FROM usuarios u  
                        INNER JOIN roles r ON u.id_rol = r.id_rol 
                        WHERE u.id_rol = :id_rol AND u.id_usuario != :id_usuario";
            $stmtResultado = $conn->prepare($sqlRol);
            $stmtResultado->bindParam(':id_rol', $rol, PDO::PARAM_INT);
            $_SESSION['empleado'] = htmlspecialchars(trim($rol));
        } else if(isset($_GET['fechaContrato'])) {
            // Consulta para encontrar usuarios segun la fecha de contrato y sumativo
            $fechaContrato = htmlspecialchars(trim($_GET['fechaContrato']));
            $sqlFechaContrato = "SELECT * FROM usuarios u 
                                INNER JOIN roles r ON u.id_rol = r.id_rol 
                                WHERE DATE(u.fecha_creacion) = :fecha_contrato AND u.id_usuario != :id_usuario";
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
                                    WHERE u.fecha_nacimiento = :fecha_nacimiento AND u.id_usuario != :id_usuario";
            if(isset($_SESSION['empleado'])){
                $sqlFechaDeNacimiento .= " AND r.id_rol = :id_rol";
            }
            $stmtResultado = $conn->prepare($sqlFechaDeNacimiento);
            $stmtResultado->bindParam(':fecha_nacimiento', $fechaNacimiento, PDO::PARAM_STR);
            if(isset($_SESSION['empleado'])){
                $rol = $_SESSION['empleado'];
                $stmtResultado->bindParam(':id_rol', $rol, PDO::PARAM_INT);
            }
        } else if(isset($_GET['nombre']) || isset($_GET['apellido'])){
            // Consulta para encontrar usuarios segun el nombre y apellido del empleado
            $nombre = htmlspecialchars(trim($_GET['nombre']));
            $apellido = htmlspecialchars(trim($_GET['apellido']));
            $nombreConcatenado = "%$nombre%";
            $apellidoConcatenado = "%$apellido%";
            $sqlEmpleado = "SELECT * FROM usuarios u 
                            INNER JOIN roles r ON u.id_rol = r.id_rol 
                            WHERE u.nombre LIKE :nombre AND u.apellido LIKE :apellido AND u.id_usuario != :id_usuario";
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
                        WHERE u.usuario LIKE :query 
                        OR u.nombre LIKE :query 
                        OR u.apellido LIKE :query 
                        OR u.email LIKE :query 
                        OR r.nombre_rol LIKE :query 
                        OR u.direccion LIKE :query 
                        OR u.telefono LIKE :query
                        OR u.dni LIKE :query 
                        AND u.id_usuario  != :id_usuario';
            $stmtResultado = $conn->prepare($sqlQuery);
            $queryConcatenado = "%$query%";
            $stmtResultado->bindParam(':query', $queryConcatenado, PDO::PARAM_STR);
            $_SESSION['query'] = htmlspecialchars(trim($query));
        } else {
            // Consulta general de para adquirir todos los datos de la tabla
            $sqlUsarios = "SELECT * FROM usuarios u 
                            INNER JOIN roles r ON u.id_rol = r.id_rol
                            WHERE u.id_usuario != :id_usuario";
            $stmtResultado = $conn->prepare($sqlUsarios);
        }
        $stmtResultado->bindParam(':id_usuario', $usuario, PDO::PARAM_INT);
        $stmtResultado->execute();
        $resultados = $stmtResultado->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e){
        echo 'ERROR: ' . $e->getMessage();
        die();
    }