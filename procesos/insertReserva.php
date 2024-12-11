<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente', 'Camarero'])){
        header('Location: ../index.php');
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        ?>
        <form action="mesa.php" method="POST" name="formulario">
            <input type="hidden" name="id_tipoSala" value="<?php echo $id_tipoSala ?>">
            <input type="hidden" name="id_sala" value="<?php echo $idSala ?>">
        </form>
        <script language="JavaScript">
            document.formulario.submit();
        </script>
        <?php
        exit();
    }
    $idMesa = htmlspecialchars(trim($_POST['id_mesa']));
    $nombreReserva = htmlspecialchars(trim($_POST['nombreReserva']));
    $numeroSillas = htmlspecialchars(trim($_POST['numeroSillas']));
    $fechaReserva = htmlspecialchars(trim($_POST['fechaReserva']));
    $horaReserva = htmlspecialchars(trim($_POST['horaReserva']));
    $id_tipoSalaDevuelta = htmlspecialchars(trim($_POST['id_tipoSala']));
    $idSalaDevuelta = htmlspecialchars(trim($_POST['id_sala']));

    // Crear la fecha completa de la reservar (fecha + hora)
    $horaReservaCompleta = $fechaReserva . ' ' . $horaReserva;

    // Establecer la hora de finalizacion (por ejemplo , 2 horas despues de la de inicio)
    $horaFinalReserva = date('Y-m-d H:i:s', strtotime('+59 minutes +57 seconds', strtotime($horaReservaCompleta)));

    require_once 'conexion.php';

    try{
        // Verificar si la hora de reserva ya esta ocupada
        $sqlComprobarHora = "SELECT * FROM reservas
                            WHERE id_mesa = :id_mesa 
                            AND (:hora_reserva_inicio < hora_final_reserva 
                            AND :hora_reserva_final > hora_inicio_reserva)";
        $stmtComprobarHora = $conn->prepare($sqlComprobarHora);
        $stmtComprobarHora->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtComprobarHora->bindParam(':hora_reserva_inicio', $horaReservaCompleta, PDO::PARAM_STR);
        $stmtComprobarHora->bindParam(':hora_reserva_final', $horaFinalReserva, PDO::PARAM_STR);
        $stmtComprobarHora->execute();

        if($stmtComprobarHora->rowCount() > 0){
            $_SESSION['errorReserva'] = true;
            header("Location: ../view/reservarMesa.php?id_tipoSala=".$id_tipoSalaDevuelta."&id_mesa=".$idMesa."&id_sala=".$idSalaDevuelta);
            exit();
        }

        // Insertar la reserva
        $sqlInsertReserva = "INSERT INTO reservas (id_usuario, id_mesa, nombre_reserva, fecha_reserva, hora_inicio_reserva, hora_final_reserva)
                            VALUES (:id_usuario, :id_mesa, :nombre_reserva, :fecha_reserva, :hora_reserva, :hora_final_reserva)";
        $stmtInsertReserva = $conn->prepare($sqlInsertReserva);
        $stmtInsertReserva->bindParam(':id_usuario', $_SESSION['id'], PDO::PARAM_INT);
        $stmtInsertReserva->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtInsertReserva->bindParam(':nombre_reserva', $nombreReserva, PDO::PARAM_STR);
        $stmtInsertReserva->bindParam(':fecha_reserva', $horaReservaCompleta, PDO::PARAM_STR);
        $stmtInsertReserva->bindParam(':hora_reserva', $horaReservaCompleta, PDO::PARAM_STR);
        $stmtInsertReserva->bindParam(':hora_final_reserva', $horaFinalReserva, PDO::PARAM_STR);
        $stmtInsertReserva->execute();

        $_SESSION['reservaRealizada'] = true;
        ?>
        <form action="../view/mesa.php" method="POST" name="form">
            <input type="hidden" name="id_sala" value="<?php echo $idSalaDevuelta ?>">
            <input type="hidden" name="id_tipoSala" value="<?php echo $id_tipoSalaDevuelta ?>">
        </form>
        <script language="JavaScript">
            document.form.submit();
        </script>
        <?php
        exit();
    } catch(PDOException $e){
        echo 'Error: ' . $e->getMessage();
        die();
    }