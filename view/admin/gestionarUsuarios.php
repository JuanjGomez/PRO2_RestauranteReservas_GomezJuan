<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location: ../../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location: viewGerente.php');
        exit();
    }
    // Sweet Alert para saber si se edito un usuario correctamente
    $alertaEditarUsuario = isset($_SESSION['editarUsuario']) && $_SESSION['editarUsuario'];
    unset($_SESSION['editarUsuario']);
    // Sweet Alert para saber si se elimino un usuario correctamente
    $alertaEliminarUsuario = isset($_SESSION['eliminarUsuario']) && $_SESSION['eliminarUsuario'];
    unset($_SESSION['eliminarUsuario']);
    require_once '../../procesos/conexion.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
    <script>
        function cambiarTabla() {
            if (window.matchMedia("(max-width: 900px)").matches) {
                document.getElementById("nombreSala").textContent = 'Sala';
                document.getElementById("numeroMesa").textContent = 'Nº';
                document.getElementById("horaIni").textContent = 'Hora ini.';
            } else {
                document.getElementById("nombreSala").textContent = 'Nombre de sala';
                document.getElementById("numeroMesa").textContent = 'Número de mesa';
                document.getElementById("horaIni").textContent = 'Hora inicio';
            }
        }
        window.onload = cambiarTabla;
        window.onresize = cambiarTabla;
    </script>
    <title>Document</title>
</head>
<body class="body2">
    <header>
        <nav>
            <div class="sesionIniciada">
                <p>Usuario: <?php echo $_SESSION['nombre']?></p>
            </div>
            <div class="cerrarSesion">
                <a id="bug" href="../filtros.php">
                    <button type="submit" class="btn btn-light"  id="cerrarSesion">Filtrar</button>
                </a>
                <a href="../../procesos/logout.php">
                    <button type="submit" class="btn btn-dark" id="cerrarSesion">Cerrar Sesión</button>
                </a>
            </div>
        </nav>
    </header>
    <div id="divReiniciar">
        <a href="../procesos/borrarSesiones.php?salir=1">
            <button class="btn btn-danger">Volver</button>
        </a>
        <strong><h2>Gestionar Usuarios</h2></strong>
        <a href="../procesos/borrarSesiones.php?borrar=5">
            <button class="btn btn-warning">Reiniciar Filtros</button>
        </a>
    </div>
    <nav class="navbar navbar-expand-lg barra">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Orden de tiempo</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="filtros.php?orden=asc">Más antiguo</a></li>
                        <li><a class="dropdown-item" href="filtros.php?orden=desc">Último</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Camarero</a>
                    <ul class="dropdown-menu">
                        <?php
                            try{
                                $camareroHistorial = 'Camarero';
                                $gerente = htmlspecialchars(trim($_SESSION['rol']));
                                $sqlCamarero = "SELECT * FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id_rol WHERE nombre_rol = :camarero AND nombre_rol = :gerente";
                                $stmt = $conn->prepare($sqlCamarero);//Ejecuta la consulta
                                $stmt->bindParam(':camarero', $camareroHistorial, PDO::PARAM_STR);
                                $stmt->bindParam(':gerente', $gerente, PDO::PARAM_STR);
                                $stmt->execute();
                                while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)){
                                    $idCamarero = htmlspecialchars($fila['id_usuario']);
                                    $nomCamarero = htmlspecialchars($fila['nombre']);
                                    echo "<li><a class='dropdown-item enlace-barra' href='filtros.php?camarero={$idCamarero}'>$nomCamarero</a></li>";
                                }
                            } catch(PDOException $e){
                                echo "Error: " . $e->getMessage();
                                die();
                            }
                        ?>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Tipo de sala</a>
                    <ul class="dropdown-menu">
                        <?php
                            try{
                                $sqlNumTipoSala = 'SELECT * FROM tipo_Sala';
                                $stmt = $conn->prepare($sqlNumTipoSala);//Ejecuta la consulta
                                $stmt->execute();
                                while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $idTipoSala = htmlspecialchars($fila['id_tipoSala']);
                                    $nombreTipoSala = htmlspecialchars($fila['tipo_sala']);
                                    echo "<li><a class='dropdown-item enlace-barra' href='filtros.php?tipoSala={$idTipoSala}'>$nombreTipoSala</a>";
                                }
                            } catch(PDOException $e){
                                echo "Error: " . $e->getMessage();
                                die();
                            }
                        ?>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" <?php echo !isset($_SESSION['tipoSala']) ? 'disabled' : ''; ?> href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Salas</a>
                    <ul class="dropdown-menu">
                </li>
                    <?php
                        try{
                            // Comprueba si se ha seleccionado un tipo de sala en la sesión
                            if (isset($_SESSION['tipoSala'])) {
                                $tipoSala = htmlspecialchars(trim($_SESSION['tipoSala']));
                                $sqlSalas = "SELECT * FROM sala WHERE id_tipoSala = :id_tipoSala";
                                $stmtSalas = $conn->prepare($sqlSalas);
                                $stmtSalas->bindParam("id_tipoSala", $tipoSala, PDO::PARAM_INT);
                                $stmtSalas->execute();
                                while ($fila = $stmtSalas->fetch(PDO::FETCH_ASSOC)) {
                                    $idSala = htmlspecialchars($fila['id_sala']);
                                    $nombreSala = htmlspecialchars($fila['nombre_sala']);
                                    echo "<li><a class='dropdown-item enlace-barra' href='filtros.php?sala={$idSala}'>$nombreSala</a></li>";
                                }
                            } else {
                                echo "<li class='dropdown-item disabled'>Seleccione un tipo de sala primero</li>";
                            }
                        } catch (PDOException $e) {
                            echo "Error: ". $e->getMessage();
                            die();
                        }
                    ?>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Numero Mesa</a>
                    <ul class="dropdown-menu scrollable-dropdown">
                        <?php
                            try{
                                if(isset($_SESSION['sala'])){
                                    $sala = htmlspecialchars(trim($_SESSION['sala']));
                                    $sqlMesaS ="SELECT m.id_mesa FROM mesa m INNER JOIN sala s ON m.id_sala = s.id_sala WHERE s.id_sala = :id_sala";
                                    $stmtMesaS = $conn->prepare($sqlMesaS);
                                    $stmtMesaS->bindParam(":id_sala", $sala, PDO::PARAM_INT);
                                    $stmtMesaS->execute();
                                    while($fila = $stmtMesaS->fetch(PDO::FETCH_ASSOC)){
                                        $idMesa = htmlspecialchars($fila['id_mesa']);
                                        echo "<li><a class='dropdown-item' href='filtros.php?mesa=$idMesa'>$idMesa</a></li>";
                                    }
                                } else {
                                    $sqlMesas = "SELECT * FROM mesa";
                                    $stmtMesas = $conn->prepare($sqlMesas);
                                    $stmtMesas->execute();
                                    while($fila = $stmtMesas->fetch(PDO::FETCH_ASSOC)){
                                        $idMesa = htmlspecialchars($fila['id_mesa']);
                                        echo "<li><a class='dropdown-item' href='filtros.php?mesa=$idMesa'>$idMesa</a></li>";
                                    }
                                }
                            } catch (PDOException $e) {
                                echo "Error: ". $e->getMessage();
                                die();
                            }
                        ?>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Tiempo</a>
                    <ul id="bajar" class="dropdown-menu">
                        <li class="centrar">Introduce una fecha</li>
                        <li><form id="formFecha" method="GET" action="">
                            <input id="inputFecha" type="datetime-local" name="tiempo" id="tiempo">
                            <button id="btnFecha" class="btn btn-outline-success" type="submit">Buscar</button>
                        </form></li>
                    </ul>
                </li>
            </ul>
            <form class="d-flex" role="search" method="get" action="">
                <input class="form-control me-2" type="search" name="query" placeholder="Buscar" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Buscar</button>
            </form>
            <div id="resultados">
            </div>
        </div>
    </div>
    </nav>
    <?php
        $sqlUsarios = "SELECT * FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id_rol";
        $stmtUsuarios = $conn->prepare($sqlUsarios);
        $stmtUsuarios->execute();
        $resultados = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
        if($resultados){
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Usuario</th>";
            echo "<th>Nombre</th>";
            echo "<th>Apellido</th>";
            echo "<th>Correo</th>";
            echo "<th>Rol</th>";
            echo "<th>Acciones</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            foreach($resultados as $fila){
                $idUsuario = htmlspecialchars($fila['id_usuario']);
                $nombreUsuario = htmlspecialchars($fila['usuario']);
                $nombre = htmlspecialchars($fila['nombre']);
                $apellidoUsuario = htmlspecialchars($fila['apellido']);
                $email = htmlspecialchars($fila['email']);
                $rol = htmlspecialchars($fila['nombre_rol']);
                echo "<tr>
                    <td>$nombreUsuario</td>
                    <td>$nombre</td>
                    <td>$apellidoUsuario</td>
                    <td>$email</td>
                    <td>$rol</td>
                    <td>
                        <a href='editarUsuario.php?id=$idUsuario' class='btn btn-warning'>Editar</a>
                        <a href='../../procesos/eliminarUsuario.php?id=$idUsuario' class='btn btn-danger'>Eliminar</a>
                    </td>
                </tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>No hay resultados</p>";
        }
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
    <script>
        <?php if($alertaEditarUsuario) : ?> // Verifica si es true la alerta
            Swal.fire({
                title: 'Usuario Editado',
                text: 'Los cambios han sido guardados.',
                icon:'success'
            })
        <?php endif; ?>
        <?php if($alertaEliminarUsuario) : ?>
            Swal.fire({
                title: 'Usuario Eliminado',
                text: 'El usuario ha sido eliminado.',
                icon:'success'
            })
        <?php endif;?>
    </script>
</body>
</html>