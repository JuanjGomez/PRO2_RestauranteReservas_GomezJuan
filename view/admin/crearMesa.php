<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location:../../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'GET'){
        header('Location: gestionarSalas.php');
        exit();
    }
    require_once '../../procesos/conexion.php';
    $errorSalaExistente = isset($_SESSION['errorSalaExistente']) && $_SESSION['errorSalaExistente'];
    unset($_SESSION['errorSalaExistente']);
    $errorTipoImagen = isset($_SESSION['errorTipoImagen']) && $_SESSION['errorTipoImagen'];
    unset($_SESSION['errorTipoImagen']);
    $errorImagen = isset($_SESSION['errorImagen']) && $_SESSION['errorImagen'];
    unset($_SESSION['errorImagen']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/formCRUDusers.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
    <title>Document</title>
</head>
<body class="body2">
    <header>
        <nav>
            <div id="pequeño">
                <img src="../../img/logoRestaurante.png" alt="Logo" id="logo">
            </div>
        </nav>
    </header>
    <form method="GET" action="gestionarMesas.php">
        <button type="submit" class="btn btn-danger">VOLVER</button>
    </form>
    <h3>Editar Mesa</h3>
    <div class="container">
        <form method="POST" id="crear" action="../../procesos/insertMesa.php" enctype="multipart/form-data">
            <div class="form-column">
                <label for="sala">Sala: 
                    <select name="sala" id="sala">
                        <option value="" selected disabled>Seleccione una sala:</option>
                        <?php
                            try {
                                // Consulta para seleccionar salas con menos de 15 mesas
                                $sqlSalas = "SELECT s.id_sala, s.nombre_sala, s.id_tipoSala, COUNT(m.id_mesa) AS total_mesas
                                    FROM sala s
                                    LEFT JOIN mesa m ON s.id_sala = m.id_sala
                                    GROUP BY s.id_sala, s.nombre_sala, s.id_tipoSala
                                    HAVING total_mesas < 15";
                                $stmtSalas = $conn->prepare($sqlSalas);
                                $stmtSalas->execute();
                            
                                while ($filaSala = $stmtSalas->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$filaSala['id_sala']}'>{$filaSala['nombre_sala']}</option>";
                                }
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                die();
                            }
                        ?>
                    </select>
                </label>
                <p id="errorSala" class="error"></p>
                <label for="numSillas">Numero de Sillas: 
                    <select name="numSillas" id="numSillas">
                        <option value="" selected disabled>Seleccione un número de sillas:</option>
                        <?php
                            for ($i = 2; $i <= 10; $i+=2) {
                                echo "<option value='{$i}'>{$i}</option>";
                            }
                        ?>
                    </select>
                </label>
                <p id="errorNumSillas" class="error"></p>
            </div>
            <button type="submit" id="boton" disabled>Enviar</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
    <script src="../../validations/js/validaEditarMesa.js"></script>
</body>
</html>