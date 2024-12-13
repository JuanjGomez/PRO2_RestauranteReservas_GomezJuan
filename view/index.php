<?php
session_start();
include '../procesos/conexion.php';

// Verificamos si el usuario ya está autenticado
if (isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente', 'Camarero'])) {
    try {
        // Consulta para obtener las salas
        $query = "SELECT * FROM tipo_sala";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (isset($_SESSION['success']) && $_SESSION['success']) {
            $user = htmlspecialchars($_SESSION['usuario']);
            echo "<script>let loginSuccess = true; let user='$user';</script>";
            unset($_SESSION['success']);
        }
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bienvenido <?php echo $_SESSION['nombre']; ?>!</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
    </head>

    <body class="body2">
    <?php include '../header.php' ?>
        <div class="container">
            <h1 class="bienvenido">Bienvenido, <?php echo $_SESSION['nombre']; ?>!</h1>
            <h3>Selecciona una sala:</h3>
            <div class="row">
<?php
        foreach($result as $fila){
            echo "<div class='col-md-4 mb-4'>"; // Clase Bootstrap para tres columnas
            echo "<div class= 'container_img grow'>";
            switch($fila['id_tipoSala']){
                case '1':
                    // echo "<a href='tipoSala.php?id=" .htmlspecialchars($fila['id_tipoSala'])."'><img src='../img/terraza 1.webp' class='img-fluid' alt=''></a>";
                    ?>
                    <form class="formImg " action="tipoSala.php" method="post">
                        <input type="hidden" name="id_tipoSala" value="<?php echo $fila['id_tipoSala']?>">
                        <input type="hidden" name="tipoSala" value="<?php echo $fila['tipo_sala'] ?>">
                        <button class="botonImg " type="submit"><img src="../img/terraza 1.webp" alt=""></button>
                    </form>
                    <?php
                    break;
                case '2':
                    // echo "<a href='tipoSala.php?id=" .htmlspecialchars($fila['id_tipoSala'])."'><img src='../img/comedor1.webp' class='img-fluid' alt=''></a>";
                    ?>
                    <form class="formImg" action="tipoSala.php" method="post">
                        <input type="hidden" name="id_tipoSala" value="<?php echo $fila['id_tipoSala']?>">
                        <input type="hidden" name="tipoSala" value="<?php echo $fila['tipo_sala'] ?>">
                        <button class="botonImg" type="submit"><img src="../img/comedor1.webp" alt=""></button>
                    </form>
                    <?php
                    break;
                case '3':
                    ?>
                    <form class="formImg" action="tipoSala.php" method="post">
                        <input type="hidden" name="id_tipoSala" value="<?php echo $fila['id_tipoSala']?>">
                        <input type="hidden" name="tipoSala" value="<?php echo $fila['tipo_sala'] ?>">
                        <button class="botonImg" type="submit"><img src="../img/salapriv.png" alt=""></button>
                    </form>
                    <?php
                    // echo "<a href='tipoSala.php?id=" .htmlspecialchars($fila['id_tipoSala'])."'><img src='../img/salapriv.png' class='img-fluid' alt=''></a>";
                    break;
            }
            echo "</div>";
            echo "<label class=labelTipo>". $fila['tipo_sala']."</label>";
            echo "</div>";
        }
?>
            </div>
            <div>
                <a href="verReservas.php"><button class="btn btn-info">Ver Reservas</button></a>
                <?php echo in_array($_SESSION['rol'], ['Gerente']) ? "<button class='btn btn-secondary' onclick='confirmarCambioRol()'>Volver Gerente</button>" : "" ?>
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
            function confirmarCambioRol() {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Estás a punto de cambiar tu rol a Gerente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirigir al usuario al rol Gerente
                        window.location.href = 'admin/viewGerente.php';
                    }
                })
            }
        </script>
        <footer></footer>
    </body>
    </html>

<?php
} else {
    // Usuario no autenticado - Redirigir al inicio de sesión
    header("Location: index.php");
    exit; // Siempre es buena práctica usar exit después de redirigir
}