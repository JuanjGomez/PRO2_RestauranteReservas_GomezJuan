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
    $successInsertSala = isset($_SESSION['successInsertSala']) && $_SESSION['successInsertSala'];
    unset($_SESSION['successInsertSala']);
    // Sweet Alert para saber si se edito un usuario correctamente
    $salaEditada = isset($_SESSION['salaEditada']) && $_SESSION['salaEditada'];
    unset($_SESSION['salaEditada']);
    // Sweet Alert para saber si se elimino un usuario correctamente
    $alertaEliminarUsuario = isset($_SESSION['eliminarUsuario']) && $_SESSION['eliminarUsuario'];
    unset($_SESSION['eliminarUsuario']);
    $successBorrarSala = isset($_SESSION['successBorrarSala']) && $_SESSION['successBorrarSala'];
    unset($_SESSION['successBorrarSala']);
    $errorBorrarSala = isset($_SESSION['errorBorrarSala']) && $_SESSION['errorBorrarSala'];
    unset($_SESSION['errorBorrarSala']);
    require_once '../../procesos/conexion.php';
    require_once '../../procesos/filtrosSalas.php'
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
    <h1>Gestionar Salas</h1>
    <div id="divReiniciar">
        <a href="borrarSesionesSalas.php?salir=1">
            <button class="btn btn-danger">Volver</button>
        </a>
        <a href="crearSala.php">
            <button class="btn btn-success">Crear Sala</button>
        </a>
        <a href="borrarSesionesSalas.php?borrar=5">
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
                        <li><a class="dropdown-item" href="gestionarSalas.php?orden=asc">A - Z</a></li>
                        <li><a class="dropdown-item" href="gestionarSalas.php?orden=desc">Z - A</a></li>
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
                                    echo "<li><a class='dropdown-item' href='gestionarSalas.php?tipoSala=". $tipoSala['id_tipoSala']. "'>". $tipoSala['tipo_sala']. "</a></li>";
                                }
                            } catch(Exception $e){
                                echo "Error: ". $e->getMessage();
                                die();
                            }
                        ?>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Popularidad</a>
                    <ul id="bajar" class="dropdown-menu">
                        <li><a class="dropdown-item" href="gestionarSalas.php?popularidad=desc">Mas popular</a></li>
                        <li><a class="dropdown-item" href="gestionarSalas.php?popularidad=asc">Menos popular</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="gestionarSalas.php?disponibles">Disponibles</a></li>
            </ul>
            <form class="d-flex" role="search" method="GET" action="">
                <input class="form-control me-2" type="search" name="query" value="<?php echo isset($_SESSION['querySalas']) ? $_SESSION['querySalas'] : '' ?>" placeholder="Buscar" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Buscar</button>
            </form>
            <div id="resultados">
            </div>
        </div>
    </div>
    </nav>
    <?php
        if($resultadosSalas){
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Nombre</th>";
            echo "<th>Tipo Sala</th>";
            echo "<th>Imagen</th>";
            echo isset($_GET['popularidad']) ? "<th>Total Reservas</th>" : "";
            echo isset($_GET['disponibles']) ? "<th>Mesas disponibles</th>" : "";
            echo "<th>Acciones</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            foreach($resultadosSalas as $fila){
                $idSala = htmlspecialchars($fila['id_sala']);
                $nombreSala = htmlspecialchars($fila['nombre_sala']);
                $imagenSala = htmlspecialchars($fila['imagen_sala']);
                $tipoSala = htmlspecialchars($fila['tipo_sala']);
                $totalReservas = isset($fila['total_historial']) ? htmlspecialchars($fila['total_historial']) : '';
                $totalMesas = isset($fila['total_mesas']) ? htmlspecialchars($fila['total_mesas']) : '';
                echo "<tr>
                    <td>$nombreSala</td>
                    <td>$tipoSala</td>
                    <td class='tabla-imagen'><img src='../../$imagenSala'></td>";
                echo isset($_GET['popularidad']) ? "<td>$totalReservas</td>" : "";
                echo isset($_GET['disponibles']) ? "<td>$totalMesas</td>" : "";
                echo "<td>
                        <a href='editarSala.php?id=$idSala' class='btn btn-warning'>Editar</a>
                        <a href='#' onclick='confirmarEliminacion($idSala)' class='btn btn-danger'>Eliminar</a>
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
        <?php if($successInsertSala) : ?> // Verifica si es true la alerta
            Swal.fire({
                title: 'Éxito!',
                text: 'La sala fue creada correctamente.',
                icon: 'success'
            })
        <?php endif;?>
        <?php if($salaEditada) : ?>
            Swal.fire({
                title: 'Sala Editado',
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
        <?php if($successBorrarSala) : ?>
            Swal.fire({
                title: 'Sala eliminada',
                text: 'Se ha eliminado la sala exitosamente.',
                icon:'success'
            })
        <?php endif;?>
        <?php if($errorBorrarSala) : ?>
            Swal.fire({
                title: 'Error al eliminar sala',
                text: 'No se pudo eliminar la sala, intentelo más tarde.',
                icon: 'error'
            })
        <?php endif;?>
        function confirmarEliminacion(idSala) {
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
                window.location.href = `../../procesos/eliminarSala.php?id=${idSala}`
            }
        })
    }
    </script>
</body>
</html>