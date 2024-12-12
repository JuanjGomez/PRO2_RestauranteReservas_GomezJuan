<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente', 'Camarero'])){
        header('Location:../index.php');
        exit();
    }
    require_once '../procesos/conexion.php';
    require_once '../procesos/filtrosReservas.php';

    $reservaEliminada = isset($_SESSION['reservaEliminada']) && $_SESSION['reservaEliminada'];
    unset($_SESSION['reservaEliminada']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
</head>
<body class="body2">
    <header>
        <nav>
            <div id="pequeño2">
                <img src="../img/logoRestaurante.png" alt="Logo" id="logo2">
            </div>
            <div class="sesionIniciada">
                <p>Usuario: <?php echo $_SESSION['nombre']?></p>
            </div>
            <div class="cerrarSesion">
                <a id="bug" href="filtros.php">
                    <button type="submit" class="btn btn-light"  id="cerrarSesion">Historial</button>
                </a>
                <a href="../procesos/logout.php">
                    <button type="submit" class="btn btn-dark" id="cerrarSesion">Cerrar Sesión</button>
                </a>
            </div>
        </nav>
    </header>
    <div id="divReiniciar">
        <a href="../procesos/borrarSesionesReservas.php?salir=1">
            <button class="btn btn-danger">Volver</button>
        </a>
        <a href="../procesos/borrarSesionesReservas.php?borrar=5">
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
                            <li><a class="dropdown-item" href="verReservas.php?orden=asc">Nuevo</a></li>
                            <li><a class="dropdown-item" href="verReservas.php?orden=desc">Último</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Responsable</a>
                        <ul class="dropdown-menu">
                            <?php
                                try{
                                    $sqlCamarero = "SELECT * FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id_rol";
                                    $stmt = $conn->prepare($sqlCamarero);//Ejecuta la consulta
                                    $stmt->execute();
                                    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)){
                                        $idCamarero = htmlspecialchars($fila['id_usuario']);
                                        $nomCamarero = htmlspecialchars($fila['nombre']);
                                        echo "<li><a class='dropdown-item enlace-barra' href='verReservas.php?camarero={$idCamarero}'>$nomCamarero</a></li>";
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
                                        echo "<li><a class='dropdown-item enlace-barra' href='verReservas.php?tipoSala={$idTipoSala}'>$nombreTipoSala</a>";
                                    }
                                } catch(PDOException $e){
                                    echo "Error: " . $e->getMessage();
                                    die();
                                }
                            ?>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Salas</a>
                        <ul class="dropdown-menu">
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
                                            echo "<li><a class='dropdown-item enlace-barra' href='verReservas.php?sala={$idSala}'>$nombreSala</a></li>";
                                        }
                                    } else {
                                        $sqlSalas = "SELECT * FROM sala";
                                        $stmtSalas = $conn->prepare($sqlSalas);
                                        $stmtSalas->execute();
                                        while ($fila = $stmtSalas->fetch(PDO::FETCH_ASSOC)) {
                                            $idSala = htmlspecialchars($fila['id_sala']);
                                            $nombreSala = htmlspecialchars($fila['nombre_sala']);
                                            echo "<li><a class='dropdown-item enlace-barra' href='verReservas.php?sala={$idSala}'>$nombreSala</a></li>";
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
                                            echo "<li><a class='dropdown-item' href='verReservas.php?mesa=$idMesa'>$idMesa</a></li>";
                                        }
                                    } else {
                                        $sqlMesas = "SELECT * FROM mesa";
                                        $stmtMesas = $conn->prepare($sqlMesas);
                                        $stmtMesas->execute();
                                        while($fila = $stmtMesas->fetch(PDO::FETCH_ASSOC)){
                                            $idMesa = htmlspecialchars($fila['id_mesa']);
                                            echo "<li><a class='dropdown-item' href='verReservas.php?mesa=$idMesa'>$idMesa</a></li>";
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
                            <li>
                                <form id="formFecha" method="GET" action="">
                                    <input id="inputFecha" type="datetime-local" name="tiempo" id="tiempo">
                                    <button id="btnFecha" class="btn btn-outline-success" type="submit">Buscar</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
                <form class="d-flex" role="search" method="get" action="">
                    <input class="form-control me-2" type="search" name="query" value="<?php echo isset($_SESSION['query']) ? $_SESSION['query'] : "" ?>" placeholder="Buscar" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Buscar</button>
                </form>
                <div id="resultados">
                </div>
            </div>
        </div>
    </nav>
    <?php
        if($reservas){
            echo "<h1>RESERVAS</h1>";
            echo "<table>";
                echo "<thead>";
                    echo "<tr>";
                        echo "<th>Nº Mesa</th>";
                        echo "<th>Nombre Reserva</th>";
                        echo "<th>Encargado</th>";
                        echo "<th>Sala</th>";
                        echo "<th>Hora Inicio</th>";
                        echo "<th>Hora Fin</th>";
                        echo "<th>Accion</th>";
                    echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                    foreach($reservas as $reserva){
                        $idReserva = $reserva['id_reserva'];
                        $nombreReserva = htmlspecialchars($reserva['nombre_reserva']);
                        $encargado = htmlspecialchars($reserva['usuario']);
                        $sala = htmlspecialchars($reserva['nombre_sala']);
                        $horaInicio = htmlspecialchars($reserva['hora_inicio_reserva']);
                        $horaFin = htmlspecialchars($reserva['hora_final_reserva']);
                        $idMesa = htmlspecialchars($reserva['id_mesa']);
                        $puedeEliminar = $reserva['puede_eliminar'];
                        echo "<tr>";
                            echo "<td>$idMesa</td>";
                            echo "<td>$nombreReserva</td>";
                            echo "<td>$encargado</td>";
                            echo "<td>$sala</td>";
                            echo "<td>$horaInicio</td>";
                            echo "<td>$horaFin</td>";
                            echo "<td>";
                            if ($puedeEliminar) {
                                echo "<a href='#' onclick='confirmarEliminacion($idReserva)' class='btn btn-danger'>Eliminar</a>";
                            }
                            echo "</td>";;
                        echo "</tr>";
                    }
                echo "</tbody>";
            echo "</table>";
        } else {
            echo "<h1>No hay datos</h1>";
        }
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
<script>
    <?php if($reservaEliminada) : ?>
        Swal.fire(
            'Reserva Eliminada!',
            'La reserva se ha realizado correctamente.',
            'success'
        );
    <?php endif; ?>
    function confirmarEliminacion(idReserva) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `../procesos/eliminarReserva.php?id=${idReserva}`;
            }
        });
    }
</script>
