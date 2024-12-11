<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location: ../../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'GET'){
        header('Location: viewGerente.php');
        exit();
    }
    // Sweet Alert para saber si se creo un usario correctamente
    $successCrearMesa = isset($_SESSION['successCrearMesa']) && $_SESSION['successCrearMesa'];
    unset($_SESSION['successCrearMesa']);
    // Sweet Alert para saber si se edito un usuario correctamente
    $editarMesaExitosa = isset($_SESSION['editarMesaExitosa']) && $_SESSION['editarMesaExitosa'];
    unset($_SESSION['editarMesaExitosa']);;
    $successEliminarMesa = isset($_SESSION['successEliminarMesa']) && $_SESSION['successEliminarMesa'];
    unset($_SESSION['successEliminarMesa']);
    require_once '../../procesos/conexion.php';
    require_once '../../procesos/filtrosMesas.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/admin.css">
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
            <div id="pequeño2">
                <img src="../../img/logoRestaurante.png" alt="Logo" id="logo2">
            </div>
            <div class="sesionIniciada">
                <p>Usuario: <?php echo $_SESSION['nombre']?></p>
            </div>
            <div class="cerrarSesion">
                <a id="bug" href="../filtros.php">
                    <button type="submit" class="btn btn-light"  id="cerrarSesion">Historial</button>
                </a>
                <a href="../../procesos/logout.php">
                    <button type="submit" class="btn btn-dark" id="cerrarSesion">Cerrar Sesión</button>
                </a>
            </div>
        </nav>
    </header>
    <h1>Gestionar Mesas</h1>
    <div id="divReiniciar">
        <a href="borrarSesionesMesas.php?salir=1">
            <button class="btn btn-danger">Volver</button>
        </a>
        <a href="crearMesa.php">
            <button class="btn btn-success">Crear Mesa</button>
        </a>
        <a href="borrarSesionesMesas.php?borrar=5">
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
                    <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Orden Alfabetico</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="gestionarMesas.php?orden=asc">A - Z</a></li>
                        <li><a class="dropdown-item" href="gestionarMesas.php?orden=desc">Z - A</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Tipos salas</a>
                    <ul class="dropdown-menu">
                        <?php
                            try{
                                $sqlTiposSalas = "SELECT * FROM tipo_sala";
                                $stmtTiposSalas = $conn->prepare($sqlTiposSalas);
                                $stmtTiposSalas->execute();
                                $tiposSalas = $stmtTiposSalas->fetchAll();
                                foreach ($tiposSalas as $tipoSala) {
                                    echo "<li><a class='dropdown-item' href='gestionarMesas.php?tipoSala=" . $tipoSala['id_tipoSala'] . "'>" . $tipoSala['tipo_sala'] . "</a></li>";
                                }
                            } catch(Exception $e){
                                echo "Error: ". $e->getMessage();
                                die();
                            }
                        ?>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Salas</a>
                    <ul id="bajar" class="dropdown-menu">
                        <?php
                            try{
                                // Comprueba si se ha seleccionado un tipo de sala en la sesión
                                if (isset($_SESSION['salaTipo'])) {
                                    $tipoSala = htmlspecialchars(trim($_SESSION['salaTipo']));
                                    $sqlSalas = "SELECT * FROM sala WHERE id_tipoSala = :id_tipoSala";
                                    $stmtSalas = $conn->prepare($sqlSalas);
                                    $stmtSalas->bindParam("id_tipoSala", $tipoSala, PDO::PARAM_INT);
                                    $stmtSalas->execute();
                                    while ($fila = $stmtSalas->fetch(PDO::FETCH_ASSOC)) {
                                        $idSala = htmlspecialchars($fila['id_sala']);
                                        $nombreSala = htmlspecialchars($fila['nombre_sala']);
                                        echo "<li><a class='dropdown-item enlace-barra' href='gestionarMesas.php?sala={$idSala}'>$nombreSala</a></li>";
                                    }
                                } else {
                                    $sqlSalas = "SELECT * FROM sala";
                                    $stmtSalas = $conn->prepare($sqlSalas);
                                    $stmtSalas->execute();
                                    while ($fila = $stmtSalas->fetch(PDO::FETCH_ASSOC)) {
                                        $idSala = htmlspecialchars($fila['id_sala']);
                                        $nombreSala = htmlspecialchars($fila['nombre_sala']);
                                        echo "<li><a class='dropdown-item enlace-barra' href='gestionarMesas.php?sala={$idSala}'>$nombreSala</a></li>";
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
                    <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Numero Sillas</a>
                    <ul id="bajar" class="dropdown-menu">
                        <?php
                            if(isset($_SESSION['salaID'])){
                                $idSala = htmlspecialchars($_SESSION['salaID']);
                                $sqlNumSillaSala = "SELECT DISTINCT num_sillas 
                                                    FROM mesa m
                                                    LEFT JOIN sala s ON m.id_sala = s.id_sala
                                                    LEFT JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala 
                                                    WHERE s.id_sala = :idSala";
                                $stmtNumSillaSala = $conn->prepare($sqlNumSillaSala);
                                $stmtNumSillaSala->bindParam(":idSala", $idSala, PDO::PARAM_INT);
                                $stmtNumSillaSala->execute();
                                $filas = $stmtNumSillaSala->fetchAll(PDO::FETCH_ASSOC);
                                foreach($filas as $fila){
                                    echo "<li><a class='dropdown-item enlace-barra' href='gestionarMesas.php?numeroSillas=". $fila['num_sillas']. "'>". $fila['num_sillas']. "</a></li>";
                                }
                            } else {
                                for ($i = 2; $i <= 10; $i+=2) {
                                    echo "<li><a class='dropdown-item enlace-barra' href='gestionarMesas.php?numeroSillas={$i}'>$i</a></li>";
                                }
                            }
                        ?>
                    </ul>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="gestionarMesas.php?disponible">Disponibles</a></li>
            </ul>
            <form class="d-flex" role="search" method="GET" action="">
                <input class="form-control me-2" type="search" name="query" value="<?php echo isset($_SESSION['query']) ? $_SESSION['query'] : '' ?>" placeholder="Buscar" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Buscar</button>
            </form>
            <div id="resultados">
            </div>
        </div>
    </div>
    </nav>
    <?php
        if($result){
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Nº Mesa</th>";
            echo "<th>Sala</th>";
            echo "<th>Tipo Sala</th>";
            echo "<th>Nº Sillas</th>";
            echo "<th>Acciones</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            foreach($result as $fila){
                $idMesa = htmlspecialchars($fila['id_mesa']);
                $nombreSala = htmlspecialchars($fila['nombre_sala']);
                $tipoSala = htmlspecialchars($fila['tipo_sala']);
                $sillas = htmlspecialchars($fila['num_sillas']);
                echo "<tr>
                    <td>$idMesa</td>
                    <td>$nombreSala</td>
                    <td>$tipoSala</td>
                    <td>$sillas</td>
                    <td>
                        <a href='editarMesa.php?id=$idMesa' class='btn btn-warning'>Editar</a>
                        <a href='#' onclick='confirmarEliminacion($idMesa)' class='btn btn-danger'>Eliminar</a>
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
        <?php if($successCrearMesa) : ?> // Verifica si es true la alerta
            Swal.fire({
                title: 'Éxito!',
                text: 'La mesa ha sido creada correctamente.',
                icon: 'success'
            })
        <?php endif;?>
        <?php if($editarMesaExitosa) : ?>
            Swal.fire({
                title: 'Mesa Editada',
                text: 'Los cambios han sido guardados.',
                icon:'success'
            })
        <?php endif; ?>
        <?php if($successEliminarMesa) : ?>
            Swal.fire({
                title: 'Mesa eliminada',
                text: 'Se ha eliminado la mesa exitosamente.',
                icon:'success'
            })
        <?php endif;?>
        function confirmarEliminacion(idMesa) {
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
            // Redirecciona al enlace de eliminación
                window.location.href = `../../procesos/eliminarMesa.php?id=${idMesa}`
            }
        })
    }
    </script>
</body>
</html>