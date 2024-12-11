<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location:../../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location: gestionarReservas.php');
        exit();
    }
    $errorTipoImagen = isset($_SESSION['errorTipoImagen']) && $_SESSION['errorTipoImagen'];
    unset($_SESSION['errorTipoImagen']);
    $errorImagen = isset($_SESSION['errorImagen']) && $_SESSION['errorImagen'];
    unset($_SESSION['errorImagen']);
    $errorSalaExistente = isset($_SESSION['errorSalaExistente']) && $_SESSION['errorSalaExistente'];
    unset($_SESSION['errorSalaExistente']);
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
    <form method="GET" action="gestionarSalas.php">
        <button type="submit" class="btn btn-danger">VOLVER</button>
    </form>
    <h3>Construir Sala</h3>
    <div class="container">
        <form method="POST" id="crear" action="../../procesos/insertSala.php" enctype="multipart/form-data">
            <div class="form-column">
                <label for="nombreS">Nombre de la Sala: 
                    <input type="text" id="nombreS" name="nombreS" value="<?php echo isset($_POST['nombreS']) ? $_POST['nombreS'] : '' ?>">
                </label>
                <p id="errorNombreS" class="error"></p>
                <label for="tipoSala">Tipo de Sala: 
                    <select id="tipoSala" name="tipoSala">
                        <option value="" selected-disabled>Seleccione una sala: </option>
                        <?php
                            try{
                                require_once '../../procesos/conexion.php';
                                $sqlTipoSala = "SELECT * FROM tipo_sala";
                                $stmtTipoSala = $conn->prepare($sqlTipoSala);
                                $stmtTipoSala->execute();
                                $resultados = $stmtTipoSala->fetchAll(PDO::FETCH_ASSOC);
                                foreach($resultados as $fila){
                                    echo '<option value="' . $fila['id_tipoSala'] . '" ' . 
                                        (isset($_POST['salaTipo']) && $_POST['salaTipo'] == $fila['id_tipoSala'] ? 'selected' : '') . '>' .
                                        $fila['tipo_sala']. '</option>';
                                }
                            } catch(PDOException $e){
                                echo "Error: ". $e->getMessage();
                                die();
                            }
                        ?>
                    </select>
                </label>
                <p id="errorTipoSala" class="error"></p>
                <label for="imagen">Selecciona una foto: 
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png, image/jpg">
                </label>
                <p id="errorImagen" class="error"></p>
            </div>
            <button type="submit" id="boton" disabled>Enviar</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
    <script src="../../validations/js/validaSala.js"></script>
    <script>
        <?php if($errorTipoImagen) : ?>
            Swal.fire({
                title: 'Error',
                text: "La imagen debe ser de tipo JPEG, PNG o JPG.",
                icon: 'error'
            });
        <?php endif ?>
        <?php if($errorImagen) :?>
            Swal.fire({
                title: 'Error',
                text: "Error al guardar la imagen.",
                icon: 'error'
            });
        <?php endif?>
        <?php if($errorSalaExistente) :?>
            Swal.fire({
                title: 'Error',
                text: "Ya existe una sala con este nombre.",
                icon: 'error'
            });
    <?php endif?>
    </script>
</body>
</html>