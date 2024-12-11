<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente', 'Camarero'])){
        header('Location: ../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'GET'){
        header('Location: ../index.php');
        exit();
    }
    $id_tipoSala = htmlspecialchars(trim($_GET['id_tipoSala']));
    $idSala = htmlspecialchars(trim($_GET['id_sala']));
    $idMesa= htmlspecialchars(trim($_GET['id_mesa']));
    require_once '../procesos/conexion.php';
    try {
        // Consulta para obtener los datos de la mesa
        $sqlMesa = "SELECT * FROM mesa WHERE id_mesa = :id_mesa";
        $stmtMesa = $conn->prepare($sqlMesa);
        $stmtMesa->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtMesa->execute();
        $resultMesas = $stmtMesa->fetch(PDO::FETCH_ASSOC);
    
        // Consulta para obtener las horas reservadas para la mesa en la fecha seleccionada
        $sqlReservas = "SELECT DATE_FORMAT(hora_inicio_reserva, '%H') AS hora_reserva
                FROM reservas
                WHERE id_mesa = :id_mesa
                AND fecha_reserva = :fecha_reserva";
        $stmtReservas = $conn->prepare($sqlReservas);
        $stmtReservas->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtReservas->bindParam(':fecha_reserva', $fechaReserva, PDO::PARAM_STR); // Asegúrate de enviar esta fecha
        $stmtReservas->execute();
        $reservas = $stmtReservas->fetchAll(PDO::FETCH_ASSOC);
    
        // Convertir las reservas a un array de horas
        $horasReservadas = array_map(function($reserva) {
            return $reserva['hora_reserva'];
        }, $reservas);

        if(isset($_SESSION['errorReserva']) && $_SESSION['errorReserva']){
            echo "<script> let errorReserva = true;</script>";
            unset($_SESSION['errorReserva']);
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/formCRUDusers.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
    <title>Document</title>
</head>
<body class="body2">
    <header>
        <nav>
            <div id="pequeño">
                <img src="../img/logoRestaurante.png" alt="Logo" id="logo">
            </div>
        </nav>
    </header>
    <form method="POST" action="mesa.php">
        <input type="hidden" name="id_tipoSala" value="<?php echo $id_tipoSala ?>">
        <input type="hidden" name="id_sala" value="<?php echo $idSala ?>">
        <button type="submit" class="btn btn-danger">VOLVER</button>
    </form>
    <h3>Reservar Mesa</h3>
    <div class="container">
        <form method="POST" id="crear" action="../procesos/insertReserva.php">
            <div class="form-column">
                <input type="hidden" name="id_tipoSala" value="<?php echo $id_tipoSala ?>">
                <input type="hidden" name="id_sala" value="<?php echo $idSala ?>">
                <input type="hidden" name="id_mesa" id="id_mesa" value="<?php echo $idMesa ?>">
                <label for="nombreReserva">Nombre de la Reserva 
                    <input type="text" name="nombreReserva" id="nombreReserva" class="form-control">
                </label>
                <p id="errorNombreReserva" class="error"></p>
                <label for="numeroSillas">Cantidad de Sillas a Reservar: 
                    <select name="numeroSillas" id="numeroSillas" class="form-control">
                        <option value="" selected-disabled>Selecciona una opcion: </option>
                        <?php
                            for($i=2; $i<=10; $i+=2){
                                ?>
                                <option value="<?php echo $i?>" class="form-control"><?php echo $i?></option>
                                <?php
                            }
                        ?>
                    </select>
                </label>
                <p id="errorNumeroSillas" class="error"></p>
                <label for="fechaReserva">Fecha de Reserva: 
                    <input type="date" name="fechaReserva" id="fechaReserva" class="form-control">
                </label>
                <p id="errorFechaReserva" class="error"></p>
                <label for="horaReserva">Hora de Reserva: 
                <select name="horaReserva" id="horaReserva" class="form-control">
                    <option value="" selected disabled>Selecciona una opción</option>
                    <?php
                    $horasDisponibles = range(16, 22);
                    foreach ($horasDisponibles as $hora) {
                        $formattedHora = sprintf('%02d:00', $hora);
                        $disabled = in_array((string)$hora, $horasReservadas) ? 'disabled' : '';
                        echo "<option value=\"$formattedHora\" $disabled>$formattedHora</option>";
                    }
                    ?>
                </select>
            </label>
                <p id="errorHoraReserva" class="error"></p>
            </div>
            <button type="submit" id="boton" disabled>Enviar</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
    <script src="../validations/js/validaReserva.js"></script>
    <script>
        if(typeof errorReserva != "undefined" && errorReserva){
            Swal.fire({
                title: 'Error',
                text: 'La mesa ya tiene una reserva en esa hora y fecha',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    </script>
</body>
</html>