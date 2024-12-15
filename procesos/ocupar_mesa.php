<?php
session_start();
include_once './conexion.php';

if (isset($_SESSION['id']) || in_array($_SESSION['rol'], ['Gerente', 'Camarero'])) {
    $id_tipoSala = htmlspecialchars(trim($_POST['id_tipoSala']));
    $idCamarero = htmlspecialchars(trim($_SESSION['id']));
    $idSala = htmlspecialchars(trim($_POST['id_sala']));
    $idMesa = htmlspecialchars(trim($_POST['id_mesa']));
    $num_sillas = htmlspecialchars(trim($_POST['num_sillas']));
    $num_sillas_real = htmlspecialchars(trim($_POST['num_sillas_real']));
    $nombreSala = trim($_POST['nombre_sala']);

    try {
        $conn->beginTransaction();

        // Consulta para saber cuanto habra en el stock
        $sqlRestaStock = "SELECT * FROM stock WHERE id_tipoSala = :id_tipoSala";
        $stmtStock = $conn->prepare($sqlRestaStock);
        $stmtStock->bindParam(':id_tipoSala', $id_tipoSala);
        $stmtStock->execute();
        $row = $stmtStock->fetch(PDO::FETCH_ASSOC);
        $VerificaStock = $row['sillas_stock'];

        if ($VerificaStock >= ($num_sillas - 2)) {
            if ($num_sillas != $num_sillas_real) {
                if ($num_sillas > $num_sillas_real) {
                    // Si se aumenta el número de sillas, resta el stock
                    $nuevoStockSillas = $VerificaStock - ($num_sillas - $num_sillas_real);
                } else {
                    // Si se reduce el número de sillas, suma el stock
                    $nuevoStockSillas = $VerificaStock + ($num_sillas_real - $num_sillas);
                }

                //Actualiza el stock de sillas
                $sqlLimitSillas = "UPDATE stock SET sillas_stock = :nuevoStockSillas WHERE id_tipoSala = :id_tipoSala";
                $stmtLimitSillas = $conn->prepare($sqlLimitSillas);
                $stmtLimitSillas->bindParam(':nuevoStockSillas', $nuevoStockSillas);
                $stmtLimitSillas->bindParam(':id_tipoSala', $id_tipoSala);
                $stmtLimitSillas->execute();
            }

            //Actualiza la mesa para marcarla como reservada y ajustar el numero de sillas
            $sql = "UPDATE mesa SET libre = :reservado, num_sillas = :num_sillas WHERE id_mesa = :id_mesa";
            $stmtMesa = $conn->prepare($sql);
            $reservado = 1;
            $stmtMesa->bindParam(":reservado", $reservado, PDO::PARAM_INT);
            $stmtMesa->bindParam(":num_sillas", $num_sillas, PDO::PARAM_INT);
            $stmtMesa->bindParam(":id_mesa", $idMesa, PDO::PARAM_INT);
            $stmtMesa->execute();

            // Insertar en el historial de la mesa ocupada
            $sqlOcupat = "INSERT INTO historial (id_usuario, id_mesa, hora_inicio) VALUES (:id_camarero, :id_mesa, NOW())";
            $stmtOcupat = $conn->prepare($sqlOcupat);
            $stmtOcupat->bindParam(":id_camarero", $idCamarero, PDO::PARAM_INT);
            $stmtOcupat->bindParam(":id_mesa", $idMesa, PDO::PARAM_INT);
            $stmtOcupat->execute();

            // Confirma la transacion
            $conn->commit();
            $_SESSION['errorStock'] = false;
            $_SESSION['successOcupat'] = true;
        } else {
            $_SESSION['errorStock'] = true;
?>
            <form action="../view/mesa.php" method="POST" name="formulario">
                <input type="hidden" name="id_tipoSala" value="<?php echo $id_tipoSala ?>">
                <input type="hidden" name="id_sala" value="<?php echo $idSala ?>">
            </form>
            <script language="JavaScript">
                document.formulario.submit();
            </script>
        <?php
        }
        ?>
        <form action="../view/mesa.php" method="POST" name="formulario">
            <input type="hidden" name="id_tipoSala" value="<?php echo $id_tipoSala ?>">
            <input type="hidden" name="id_sala" value="<?php echo $idSala ?>">
            <input type="hidden" name="nombre_sala" value="<?php echo $nombreSala ?>">
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