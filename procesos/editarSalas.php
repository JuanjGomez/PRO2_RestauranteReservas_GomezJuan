<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location:../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location:../view/admin/gestionarSalas.php');
        exit();
    }
    require_once 'conexion.php';
    $idSala = trim($_POST['idSala']);
    $nombreSala = trim($_POST['nombreS']);
    $tipoSala = trim($_POST['tipoSala']);
    try{
        $sqlEvitarDuplicados = "SELECT * FROM sala 
                                WHERE nombre_sala = :nombreSala AND id_sala != :idSala";
        $stmtEvitarDuplicados = $conn->prepare($sqlEvitarDuplicados);
        $stmtEvitarDuplicados->bindParam(':nombreSala', $nombreSala);
        $stmtEvitarDuplicados->bindParam(':idSala', $idSala);
        $stmtEvitarDuplicados->execute();
        $resultado = $stmtEvitarDuplicados->fetch();
        if($resultado){
            $_SESSION['errorSalaExistente'] = true;
            header("Location:../view/admin/editarSala?id={$idSala}.php");
            exit();
        }
        // Obtener la imagen anterior si existe
        $sqlImagenAnterior = "SELECT imagen_sala FROM sala WHERE id_sala = :id_sala";
        $stmtImagenAnterior = $conn->prepare($sqlImagenAnterior);
        $stmtImagenAnterior->bindParam(':id_sala', $idSala);
        $stmtImagenAnterior->execute();
        $filaImagen = $stmtImagenAnterior->fetch(PDO::FETCH_ASSOC);

        $imagenAnterior = $filaImagen['imagen_sala'] ?? null;
        $rutaImagen = null;

        // Manejo de la nueva imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombreImagen = $_FILES['imagen']['name'];
            $tmpImagen = $_FILES['imagen']['tmp_name'];
            $extension = pathinfo($nombreImagen, PATHINFO_EXTENSION);
        
            // Validar tipo de archivo
            $mime = mime_content_type($tmpImagen);
            if (in_array($mime, ['image/jpeg', 'image/png'])) {
                $nombreLimpio = str_replace(' ', '_', strtolower($nombreSala));
                $nuevoNombreImagen = $nombreLimpio . "." . $extension;
                $rutaDirectorio = __DIR__ . "/../img/";
        
                // Crear directorio si no existe
                if (!is_dir($rutaDirectorio)) {
                    mkdir($rutaDirectorio, 0755, true);
                }
        
                $rutaImagen = $rutaDirectorio . $nuevoNombreImagen;
        
                // Eliminar imagen anterior
                $rutaCompletaAnterior = __DIR__ . '/../' . $imagenAnterior;
                if (file_exists($rutaCompletaAnterior)) {
                    unlink($rutaCompletaAnterior);
                }
                
                $rutaRelativa = "img/" . $nuevoNombreImagen;
                // Mover la nueva imagen
                if (!move_uploaded_file($tmpImagen, $rutaImagen)) {
                    $_SESSION['errorImagen'] = true;
                    header("Location:../view/admin/editarSala?id={$idSala}.php");
                    exit();
                }
            } else {
                $_SESSION['errorTipoImagen'] = true;
                header("Location:../view/admin/editarSala?id={$idSala}.php");
                exit();
            }
        }        

        // Actualizar sala
        $sqlActualizarSala = "UPDATE sala SET nombre_sala = :nombreSala, id_tipoSala = :tipoSala" .
                            ($rutaRelativa ? ", imagen_sala = :imagen" : "") .
                            " WHERE id_sala = :idSala";

        $stmtActualizarSala = $conn->prepare($sqlActualizarSala);
        $stmtActualizarSala->bindParam(":nombreSala", $nombreSala, PDO::PARAM_STR);
        $stmtActualizarSala->bindParam(":tipoSala", $tipoSala, PDO::PARAM_INT);
        $stmtActualizarSala->bindParam(":idSala", $idSala, PDO::PARAM_INT);

        if ($rutaImagen) {
            $stmtActualizarSala->bindParam(":imagen", $rutaRelativa, PDO::PARAM_STR);
        }

        $stmtActualizarSala->execute();

        $_SESSION['salaEditada'] = true;
        ?>
        <form method="GET" name="formulario" action="../view/admin/gestionarSalas.php"></form>
        <script> document.formulario.submit(); </script>
        <?php
        exit();
    } catch (PDOException $e){
        echo "Error: ". $e->getMessage();
        die();
    }