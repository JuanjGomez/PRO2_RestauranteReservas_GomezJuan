<?php
session_start();
include_once '../procesos/conexion.php';
if (!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente', 'Camarero'])) {
    header('location:../index.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./index.php');
    exit();
} else {
    try {
        // Sanitización de inputs
        $id = trim($_POST['id_tipoSala']);
        $id_sala = trim($_POST['id_sala']);
        $nombreSala = trim($_POST['nombre_sala']);

        // Preparar consulta
        $query = "SELECT m.*, 
                r.id_reserva IS NOT NULL AS reservada,
                r.hora_inicio_reserva,
                r.hora_final_reserva
                FROM mesa m
                LEFT JOIN reservas r 
                ON m.id_mesa = r.id_mesa 
                AND NOW() BETWEEN r.hora_inicio_reserva AND r.hora_final_reserva
                WHERE m.id_sala = :id_sala";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
        $stmt->execute();

        // Obtener resultados
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (isset($_SESSION['successOcupat']) && $_SESSION['successOcupat']) {
            unset($_SESSION['successOcupat']);
            echo "<script>let ocupat = true;</script>";
        }
        if (isset($_SESSION['successDesocupat']) && $_SESSION['successDesocupat']) {
            unset($_SESSION['successDesocupat']);
            echo "<script>let desocupat = true;</script>";
        }
        if (isset($_SESSION['errorStock'])) {
            echo "<script>let errorStock = true;</script>";
            unset($_SESSION['errorStock']);
        }
        if(isset($_SESSION['reservaRealizada']) && $_SESSION['reservaRealizada']) {
            unset($_SESSION['reservaRealizada']);
            echo "<script>let reservaRealizada = true;</script>";
        }
        if(isset($_SESSION['reservaRealizada']) && $_SESSION['reservaRealizada']){
            echo "<script> let reservaRealizada = true;</script>";
            unset($_SESSION['reservaRealizada']);
        }

        $numero = count($result);
        $nuevoNumero = 4;
        switch ($numero) {
            case 4:
                $nuevoNumero = 5;
                break;
            case 5:
            case 6:
                $nuevoNumero = 4;
                break;
        }
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/imagenes.css">
        <link rel="stylesheet" href="../css/style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <title>Selecciona una mesa</title>
    </head>

    <body class="body2">
        <?php

        include '../header.php';

        ?>
        <a href="../view/index.php">
                <button class="btn btn-danger">Volver</button>
            </a>
        <div id="divMesa">
            <h1 id="centrar"><?php echo $nombreSala ?></h1>
            <h3 id="centrar">Selecciona una ubicación!</h3>
        </div>

        <div class="container">
            <div class="row"> <!-- Agregado para crear un nuevo row de Bootstrap -->

                <?php
                foreach ($result as $fila) {
                    echo "<div class='col-md-$nuevoNumero mb-4'>"; // Clase Bootstrap para cuatro columnas
                    echo "<div class='container_img text-center'>";
                    if($fila['reservada']){
                        echo "<img class='imagen mb-2' src='../img/reservada.png' alt='Reservada'>";
                        echo "<label class='labelTipo d-block'>Reservada de: {$fila['hora_inicio_reserva']} a {$fila['hora_final_reserva']}</label>";
                    } else if ($fila['libre'] == 0) {
                ?>
                        <form class="formImgComedor" action="../procesos/ocupar_mesa.php" method="POST">
                            <input type="hidden" name="id_tipoSala" value="<?php echo $id ?>">
                            <input type="hidden" name="id_mesa" value="<?php echo $fila['id_mesa'] ?>">
                            <input type="hidden" name="id_sala" value="<?php echo $fila['id_sala'] ?>">
                            <input type="hidden" name="num_sillas_real" value="<?php echo $fila['num_sillas'] ?>">
                            <input type="hidden" name="num_sillas" value="<?php echo $fila['num_sillas'] ?>">
                            <input type="hidden" name="nombre_sala" value="<?php echo $nombreSala ?>">
                            <button class="botonImg mb-2" type="button" onclick="confirmAction(this.form)"><img class="imagen" src="../img/<?php
                                                                                                                                        if ($fila['num_sillas'] == 1 || $fila['num_sillas'] == 2) {
                                                                                                                                            echo $fila['libre'] .  2;
                                                                                                                                        } elseif ($fila['num_sillas'] == 3 || $fila['num_sillas'] == 4) {
                                                                                                                                            echo $fila['libre'] . 4;
                                                                                                                                        } elseif ($fila['num_sillas'] == 5 || $fila['num_sillas'] == 6) {
                                                                                                                                            echo $fila['libre'] . 6;
                                                                                                                                        } elseif ($fila['num_sillas'] == 7 || $fila['num_sillas'] == 8) {
                                                                                                                                            echo $fila['libre'] . 8;
                                                                                                                                        } elseif ($fila['num_sillas'] == 9 || $fila['num_sillas'] == 10) {
                                                                                                                                            echo $fila['libre'] . 10;
                                                                                                                                        }
                                                                                                                                        ?>.png" alt=""></button>
                        </form>
                    <?php
                    } else {

                    ?>
                        <form class="formImgComedor" action="../procesos/desocupar_mesa.php" method="POST">
                            <input type="hidden" name="id_tipoSala" value="<?php echo $id ?>">
                            <input type="hidden" name="id_mesa" value="<?php echo $fila['id_mesa'] ?>">
                            <input type="hidden" name="id_sala" value="<?php echo $fila['id_sala'] ?>">
                            <input type="hidden" name="num_sillas_real" value="<?php echo $fila['num_sillas'] ?>">
                            <input type="hidden" name="num_sillas" value="<?php echo $fila['num_sillas'] ?>">
                            <input type="hidden" name="nombre_sala" value="<?php echo $nombreSala ?>">
                            <button class="botonImg mb-2" type="button" onclick="desocupar(this.form)"><img class="imagen" src="../img/<?php
                                                                                                                                    if ($fila['num_sillas'] == 1 || $fila['num_sillas'] == 2) {
                                                                                                                                        echo $fila['libre'] .  2;
                                                                                                                                    } elseif ($fila['num_sillas'] == 3 || $fila['num_sillas'] == 4) {
                                                                                                                                        echo $fila['libre'] . 4;
                                                                                                                                    } elseif ($fila['num_sillas'] == 5 || $fila['num_sillas'] == 6) {
                                                                                                                                        echo $fila['libre'] . 6;
                                                                                                                                    } elseif ($fila['num_sillas'] == 7 || $fila['num_sillas'] == 8) {
                                                                                                                                        echo $fila['libre'] . 8;
                                                                                                                                    } elseif ($fila['num_sillas'] == 9 || $fila['num_sillas'] == 10) {
                                                                                                                                        echo $fila['libre'] . 10;
                                                                                                                                    }
                                                                                                                                    ?>.png" alt=""></button>
                        </form>

                <?php
                    }
                    echo "</div>";
                    echo "<a href='reservarMesa.php?id_tipoSala={$id}&id_mesa={$fila['id_mesa']}&id_sala={$fila['id_sala']}&nombre_sala={$nombreSala}'><button class='btn btn-primary'>Reservar</button></a><br>";
                    echo "<label class='labelTipo'> Nº Sillas: " . $fila['num_sillas'] . "</label>";
                    echo "</div>";
                }

                ?>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
        <script>
            function confirmAction(form) {
                // Crear dinamicamente las opciones del select
                Swal.fire({
                    title: "Estás seguro de ocupar la mesa para " + form.num_sillas.value + "?",
                    text: "cambia el número de sillas aqui: ",
                    icon: "warning",
                    input: "text",
                    inputValue: form.num_sillas.value,
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Confirmar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (result.value <= 10 && result.value >= 2) {
                            form.num_sillas.value = result.value;
                            form.submit();
                        } else {
                            Swal.fire({
                                title: "Limite es 10!",
                                text: "No puedes pedir mas de 10 sillas",
                                icon: "warning",
                                confirmButtonText: "Aceptar"
                            });
                        }
                    }
                });
            }

            function desocupar(form) {
                Swal.fire({
                    title: "Estás seguro de desocupar la mesa para " + form.num_sillas.value + "?",
                    text: "cambia el número de sillas aqui: ",
                    icon: "warning",
                    input: "text",
                    inputValue: form.num_sillas.value < 4 ? form.num_sillas.value : 2,
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Confirmar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (result.value <= 10 && result.value >= 2) {
                            form.num_sillas.value = result.value;
                            form.submit();
                        } else {
                            Swal.fire({
                                title: "Rango no aceptado",
                                text: "Solo puedes pedir de 2 a 10",
                                icon: "warning",
                                confirmButtonText: "Aceptar"
                            });
                        }
                    }
                });
            }
            if (typeof errorStock !== 'undefined' && errorStock) {
                Swal.fire({
                    title: "No disponemos de sillas!",
                    text: "En este momento no contamos con tantas sillas",
                    icon: "error",
                    confirmButtonText: "Aceptar"
                });
            }
            if (typeof ocupat !== "undefined" && ocupat) {
                Swal.fire({
                    title: "Mesa Ocupada!",
                    text: "La mesa ha sido ocupada exitosamente!",
                    icon: "success",
                    confirmButtonText: "Aceptar"
                });
            }
            if (typeof desocupat !== "undefined" && desocupat) {
                Swal.fire({
                    title: "Mesa Desocupada!",
                    text: "La mesa ha sido desocupada exitosamente!",
                    icon: "success",
                    confirmButtonText: "Aceptar"
                });
            }
            if(typeof reservaRealizada !== "undefined" && reservaRealizada) {
                Swal.fire({
                    title: "Reserva realizada!",
                    text: "La reserva se ha realizado correctamente!",
                    icon: "success",
                    confirmButtonText: "Aceptar"
                });
            }
            if(typeof reservaRealizada !== "undefined" && reservaRealizada) {
                Swal.fire({
                    title: "Reserva realizada!",
                    text: "La reserva se ha realizado correctamente!",
                    icon: "success",
                    confirmButtonText: "Aceptar"
                });
            }
        </script>
        <footer></footer>
    </body>

    </html>

<?php
}

?>