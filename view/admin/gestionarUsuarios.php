<?php
    session_start();
    if(!isset($_SESSION['id']) && ($_SESSION['rol'] !== 'Gerente')){
        header('Location: ../../index.php');
        exit();
    }
    if($_SESSION['REQUEST_METHOD'] !== 'GET'){
        header('Location: viewGerente.php');
        exit();
    }
    require_once '../../procesos/conexion.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
    <script>
        function cambiarTabla() {
            if (window.matchMedia("(max-width: 900px)").matches) {
                document.getElementById("nombreSala").textContent = 'Sala';
                document.getElementById("numeroMesa").textContent = 'Nº';
                document.getElementById("horaIni").textContent = 'Hora ini.';
            } else {
                document.getElementById("nombreSala").textContent = 'Nombre de sala';
                document.getElementById("numeroMesa").textContent = 'Número de mesa';
                document.getElementById("horaIni").textContent = 'Hora inicio';
            }
        }
        window.onload = cambiarTabla;
        window.onresize = cambiarTabla;
    </script>
    <title>Document</title>
</head>
<body>
    <?php
        $sqlUsarios = "SELECT * FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id_rol";
        $stmtUsuarios = $conn->prepare($sqlUsarios);
        $stmtUsuarios->execute();
        $resultados = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
        if($resultados){
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Nombre</th>";
            echo "<th class='ocultarSala'>Sala</th>";
            echo "<th id='nombreSala'>Nombre de sala</th>";
            echo "<th id='numeroMesa'>Número de mesa</th>";
            echo "<th id='horaIni'>Hora inicio</th>";
            echo "<th>Hora fin</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            foreach($resultados as $fila){
                $idUsuario = htmlspecialchars($fila['id_usuario']);
                $nombreUsuario = htmlspecialchars($fila['nombre']);
                $apellidoUsuario = htmlspecialchars($fila['apellido']);
                $usuario = htmlspecialchars($fila['usuario']);
                $rol = htmlspecialchars($fila['nombre_rol']);
                echo "<tr>
                    <td>$idUsuario</td>
                    <td>$nombreUsuario</td>
                    <td>$apellidoUsuario</td>
                    <td>$usuario</td>
                    <td>$rol</td>
                    <td><form method='POST' action='editarUser.php'>
                        <input type='hidden' name='id' id='id' value='$idUsuario'>
                        <button type='submit' class='btn btn-warning'>Editar</button>
                    </form></td>
                    <td><form method='POST' action='../../procesos/eliminarUser.php'>
                        <input type='hidden' name='id' id='id' value='$idUsuario'>
                        <button type='submit' class='btn btn-danger'>Eliminar</button>
                    </form></td>
                </tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>No hay resultados</p>";
        }
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
</body>
</html>