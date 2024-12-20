<?php
    session_start();
    if(!isset($_SESSION['id']) && $_SESSION['rol'] !== 'Gerente'){
        header('Location: ../../index.php');
    }
    if (isset($_SESSION['success']) && $_SESSION['success']) {
        $user = htmlspecialchars($_SESSION['usuario']);
        echo "<script>let loginSuccess = true; let user='$user';</script>";
        unset($_SESSION['success']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido <?php echo $_SESSION['nombre']; ?>!</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
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
    <div class="container">
        <h1 class="bienvenido">Bienvenido, <?php echo $_SESSION['nombre']; ?>!</h1>
        <h3>Selecciona algún área de trabajo:</h3>
        <div class="row text-center justify-content-center">
            <div class="col-md-3">
                <div class="container_img grow">
                    <form class="formImg" action="gestionarUsuarios.php" method="GET">
                        <button class="botonImg" type="submit"><img src="../../img/Aha-Soft-Software-User-group.ico" alt=""></button>
                    </form>
                </div>
                <p>Gestionar Usuarios</p>
            </div>
            <div class="col-md-3">
                <div class="container_img grow">
                    <form class="formImg" action="gestionarSalas.php" method="GET">
                        <button class="botonImg" type="submit"><img src="../../img/administrarSala.jpg" alt=""></button>
                    </form>
                </div>
                <p>Gestionar Salas</p>
            </div>
            <div class="col-md-3">
                <div class="container_img grow">
                    <form class="formImg" action="gestionarMesas.php" method="GET">
                        <button class="botonImg" type="submit"><img src="../../img/almacen2.jpg" alt=""></button>
                    </form>
                </div>
                <p>Gestionar Mesas</p>
            </div>
            <div class="col-md-3">
                <div class="container_img grow">
                    <form class="formImg" action="../index.php" method="GET" onsubmit="return confirmarCambioRol(event)">
                        <button class="botonImg" type="submit"><img src="../../img/rolCamarero.jpg" alt=""></button>
                    </form>
                </div>
                <p>Camarero</p>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
    <script>
        if(typeof loginSuccess !== 'undefined' && loginSuccess){
            swal.fire({
                title: 'Sesion iniciada',
                text: "Bienvenido " + user + "!",
                icon: 'success'
            })
        }
        function confirmarCambioRol(event) {
            event.preventDefault(); // Previene el envío del formulario por defecto
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Estás a punto de cambiar tu rol a Camarero.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir al usuario al rol Gerente
                    window.location.href = '../index.php';
                }
            })
        }
    </script>
</body>
</html>