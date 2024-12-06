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
    $alertaCrearUsuario = isset($_SESSION['crearUsuario']) && $_SESSION['crearUsuario'];
    unset($_SESSION['crearUsuario']);
    // Sweet Alert para saber si se edito un usuario correctamente
    $alertaEditarUsuario = isset($_SESSION['editarUsuario']) && $_SESSION['editarUsuario'];
    unset($_SESSION['editarUsuario']);
    // Sweet Alert para saber si se elimino un usuario correctamente
    $alertaEliminarUsuario = isset($_SESSION['eliminarUsuario']) && $_SESSION['eliminarUsuario'];
    unset($_SESSION['eliminarUsuario']);
    require_once '../../procesos/conexion.php';
    require_once '../../procesos/filtrosGerente.php'
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
    <h1>Gestionar Usuarios</h1>
    <div id="divReiniciar">
        <a href="borrarSesionesAdmin.php?salir=1">
            <button class="btn btn-danger">Volver</button>
        </a>
        <a href="crearUsuario.php">
            <button class="btn btn-success">Crear Usuario</button>
        </a>
        <a href="borrarSesionesAdmin.php?borrar=5">
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
                        <li><a class="dropdown-item" href="gestionarUsuarios.php?orden=asc">A - Z</a></li>
                        <li><a class="dropdown-item" href="gestionarUsuarios.php?orden=desc">Z - A</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Roles</a>
                    <ul class="dropdown-menu">
                        <?php
                            try{
                                $sqlRol = "SELECT * FROM roles";
                                $stmtRol = $conn->query($sqlRol);
                                $stmtRol->execute();
                                $roles = $stmtRol->fetchAll();
                                foreach ($roles as $rol) {
                                    $idRol = $rol['id_rol'];
                                    $nombreRol = $rol['nombre_rol'];
                                    echo "<li><a class='dropdown-item enlace-barra' href='gestionarUsuarios.php?rol={$idRol}'>$nombreRol</a></li>";
                                }
                            } catch(PDOException $e){
                                echo "Error: " . $e->getMessage();
                                die();
                            }
                        ?>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Fecha Nacimiento</a>
                    <ul id="bajar" class="dropdown-menu">
                        <li class="centrar">Introduce una fecha</li>
                        <li><form id="formFecha" method="GET" action="">
                            <input id="inputFecha" type="date" name="fechaNacimiento" id="fechaNacimiento">
                            <button id="btnFecha" class="btn btn-outline-success" type="submit">Buscar</button>
                        </form></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Fecha Contrato</a>
                    <ul id="bajar" class="dropdown-menu">
                        <li class="centrar">Introduce una fecha</li>
                        <li><form id="formFecha" method="GET" action="">
                            <input id="inputFecha" type="date" name="fechaContrato" id="fechaContrato">
                            <button id="btnFecha" class="btn btn-outline-success" type="submit">Buscar</button>
                        </form></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle enlace-barra" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Nombre y Apellido</a>
                    <ul id="bajar" class="dropdown-menu">
                        <li class="centrar">Introduce Nombre y Apellido</li>
                        <li>
                            <form id="formUsuario" method="GET" action="">
                                <input class="input-highlight" type="text" name="nombre" id="nombre" placeholder="Nombre">
                                <input class="input-highlight" type="text" name="apellido" id="apellido" placeholder="Apellido">
                                <button id="btnFecha" class="btn btn-outline-success" type="submit">Buscar</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
            <form class="d-flex" role="search" method="get" action="">
                <input class="form-control me-2" type="search" name="query" value="<?php echo isset($_SESSION['query']) ? $_SESSION['query'] : '' ?>" placeholder="Buscar" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Buscar</button>
            </form>
            <div id="resultados">
            </div>
        </div>
    </div>
    </nav>
    <?php
        if($resultados){
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Usuario</th>";
            echo "<th>Apellido</th>";
            echo "<th>DNI</th>";
            echo "<th>Telefono</th>";
            echo "<th>Correo</th>";
            echo "<th>Rol</th>";
            echo "<th>Acciones</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            foreach($resultados as $fila){
                $idUsuario = htmlspecialchars($fila['id_usuario']);
                $nombreUsuario = htmlspecialchars($fila['usuario']);
                $apellidoUsuario = htmlspecialchars($fila['apellido']);
                $dni = htmlspecialchars($fila['dni']);
                $email = htmlspecialchars($fila['email']);
                $telefono = htmlspecialchars($fila['telefono']);
                $rol = htmlspecialchars($fila['nombre_rol']);
                echo "<tr>
                    <td>$nombreUsuario</td>
                    <td>$apellidoUsuario</td>
                    <td>$dni</td>
                    <td>$telefono</td>
                    <td>$email</td>
                    <td>$rol</td>
                    <td>
                        <a href='editarUsuario.php?id=$idUsuario' class='btn btn-warning'>Editar</a>";
                        if($_SESSION['id'] !== $idUsuario){
                        echo "<a href='#' onclick='confirmarEliminacion($idUsuario)' class='btn btn-danger'>Eliminar</a>";
                        }
                    "</td>
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
        <?php if($alertaCrearUsuario) : ?> // Verifica si es true la alerta
            Swal.fire({
                title: 'Usuario creado con éxito',
                icon: 'success',
            })
        <?php endif;?>
        <?php if($alertaEditarUsuario) : ?>
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
        function confirmarEliminacion(idUsuario) {
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
                window.location.href = `../../procesos/eliminarUsuario.php?id=${idUsuario}`
            }
        })
    }
    </script>
</body>
</html>