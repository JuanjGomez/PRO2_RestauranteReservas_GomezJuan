<?php
session_start();
include_once './conexion.php';

if (isset($_SESSION['id']) || in_array($_SESSION['rol'], ['Gerente', 'Camarero'])) {
    // Escapar variables de entrada
    $id_tipoSala = htmlspecialchars(trim($_POST['id_tipoSala']));
    $idSala = htmlspecialchars(trim($_POST['id_sala']));
    $idMesa = htmlspecialchars(trim($_POST['id_mesa']));
    $num_sillas = htmlspecialchars(trim($_POST['num_sillas']));
    $num_sillas_real = htmlspecialchars(trim($_POST['num_sillas_real']));

    try {
        $conn->beginTransaction();

        $sqlRestaStock = "SELECT * FROM stock WHERE id_tipoSala = :id_tipoSala";
        $stmtStock = $conn->prepare($sqlRestaStock);
        $stmtStock->bindParam(':id_tipoSala', $id_tipoSala, PDO::PARAM_INT);
        $stmtStock->execute();
        $row = $stmtStock->fetch(PDO::FETCH_ASSOC);
        $VerificaStock = $row['sillas_stock'];

        // Si el numero de sillas ha cambiado, ajustar el stock
        if ($num_sillas != $num_sillas_real) {
            $nuevoStockSillas = $num_sillas_real - $num_sillas + $VerificaStock;

            $sqlLimitSillas = "UPDATE stock SET sillas_stock = :nuevoSillasStock WHERE id_tipoSala = :id_tipoSala";
            $stmtLimitSillas = $conn->prepare($sqlLimitSillas);
            $stmtLimitSillas->bindParam(':nuevoSillasStock', $nuevoStockSillas, PDO::PARAM_INT);
            $stmtLimitSillas->bindParam(':id_tipoSala', $id_tipoSala, PDO::PARAM_INT);
            $stmtLimitSillas->execute();
        }

        // Actualizar la mesa para marcarla como libre y ajustar el numero de sillas
        $sqlMesa = "UPDATE mesa SET libre = :reservado, num_sillas = :num_sillas WHERE id_mesa = :id_mesa";
        $stmtMesa = $conn->prepare($sqlMesa);
        $reservado = 0;
        $stmtMesa->bindParam(':reservado', $reservado, PDO::PARAM_INT);
        $stmtMesa->bindParam(":num_sillas", $num_sillas, PDO::PARAM_INT);
        $stmtMesa->bindParam(":id_mesa", $idMesa, PDO::PARAM_INT);
        $stmtMesa->execute();

        // Consultar el historial donde la hora_fin sea nula
        $null = '0000-00-00 00:00:00';
        $sqlIDH = "SELECT * FROM historial WHERE id_mesa = :id_mesa AND hora_fin = :hora_fin";
        $stmtIDH = $conn->prepare($sqlIDH);
        $stmtIDH->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtIDH->bindParam(':hora_fin', $null, PDO::PARAM_STR);
        $stmtIDH->execute();
        $fila = $stmtIDH->fetch(PDO::FETCH_ASSOC);
        $idH = $fila['id_historial'];

        // Actualizar el historial para establecer la hora fin
        $sqlDesocupat = "UPDATE historial SET hora_fin = NOW() WHERE id_mesa = :id_mesa AND id_historial = :id_historial";
        $stmtDesocupat = $conn->prepare($sqlDesocupat);
        $stmtDesocupat->bindParam(':id_mesa', $idMesa, PDO::PARAM_INT);
        $stmtDesocupat->bindParam(":id_historial", $idH, PDO::PARAM_INT);
        $stmtDesocupat->execute();

        // Confirmar la transaccion
        $conn->commit();
        $_SESSION['successDesocupat'] = true;

?>
        <form action="../view/mesa.php" method="POST" name="formulario">
            <input type="hidden" name="id_tipoSala" value="<?php echo $id_tipoSala ?>">
            <input type="hidden" name="id_sala" value="<?php echo $idSala ?>">
        </form>
        <script language="JavaScript">
            document.formulario.submit();
        </script>
<?php
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>