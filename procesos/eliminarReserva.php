<?php
session_start();

if (!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente', 'Camarero'])) {
    header('Location:../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Location:../view/verReservas.php');
    exit();
}

require_once 'conexion.php';

$idReserva = htmlspecialchars(trim($_GET['id']));
if (!filter_var($idReserva, FILTER_VALIDATE_INT)) {
    header('Location:../view/verReservas.php');
    exit();
}

try {
    $conn->beginTransaction();

    // Eliminar primero el historial de la reserva
    $sqlEliminarReservaHistorial = "DELETE FROM historial 
                                WHERE id_mesa = (SELECT id_mesa FROM reservas WHERE id_reserva = :id_reserva)
                                AND hora_inicio = (SELECT hora_inicio_reserva FROM reservas WHERE id_reserva = :id_reserva)
                                AND hora_fin = (SELECT hora_final_reserva FROM reservas WHERE id_reserva = :id_reserva)";
    $stmtEliminarReservaHistorial = $conn->prepare($sqlEliminarReservaHistorial);
    $stmtEliminarReservaHistorial->bindParam(':id_reserva', $idReserva, PDO::PARAM_INT);
    $stmtEliminarReservaHistorial->execute();

    // Eliminar la reserva
    $sqlEliminarReserva = "DELETE FROM reservas WHERE id_reserva = :id_reserva";
    $stmtEliminarReserva = $conn->prepare($sqlEliminarReserva);
    $stmtEliminarReserva->bindParam(':id_reserva', $idReserva, PDO::PARAM_INT);
    $stmtEliminarReserva->execute();

    $conn->commit();

    $_SESSION['reservaEliminada'] = true;
    header('Location:../view/verReservas.php');
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    echo "Error al eliminar la reserva: ". $e->getMessage();
    header('Location:../view/verReservas.php');
    die();
}
