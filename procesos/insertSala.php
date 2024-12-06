<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location: ../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location: ../view/admin/gestionarSalas.php');
        exit();
    }
    require_once 'conexion.php';
    $nombreS = htmlspecialchars(trim($_POST['nombreS']));
    $tipoSala = htmlspecialchars(trim($_POST['tipoSala']));
    $imagen = $_FILES['imagen'];

    try{
        $sqlVerificarSala = "SELECT * FROM sala s 
                            INNER JOIN tipo_sala tp ON s.id_tipoSala = tp.id_tipoSala 
                            WHERE s.nombre_sala = :nombreSala";
        $stmtVerificarSala = $conn->prepare($sqlVerificarSala);
        $stmtVerificarSala->bindParam(':nombreSala', $nombreS);
        $stmtVerificarSala->execute();
        $sala = $stmtVerificarSala->fetch();
        if($sala){
            $_SESSION['errorSalaExistente'] = true;
            ?>
            <form method="POST" action="../view/admin/crearSala.php" name="formulario">
                <input type="hidden" name="nombreS" value="<?php echo $nombreS; ?>">
                <input type="hidden" name="tipoSala" value="<?php echo $tipoSala;?>">
            </form>
            <script>
                document.formulario.submit();
            </script>
            <?php
        }
    } catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        die();
    }
        
    // Procesar la imagen
    // Validar extension del archivo
    $extensionesPermitidas = ['jpg', 'png', 'jpeg'];
    $extension = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
    if(!in_array($extension, $extensionesPermitidas)){
        $_SESSION['errorExtension'] = true;
        header('Location:../view/admin/crearSala.php');
        exit();
    }

    // Validar tipo de MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipoMYME = finfo_file($finfo, $imagen['tmp_name']);
    finfo_close($finfo);

    $TiposMYMESPermitidas = ['image/jpeg', 'image/png'];
    if(!in_array($tipoMYME, $TiposMYMESPermitidas)){
        $_SESSION['errorTipoImagen'] = true;
        header('Location:../view/admin/crearSala.php');
        exit();
    }

    // Crear un nombre limpio para la imagen basado en el nombre de la sala
    $nombreLimpio = preg_replace('/[^a-zA-Z0-9\s]/', '', $nombreS); // Quitar caracteres especiales
    $nombreLimpio = str_replace(' ', '_', $nombreLimpio); // Reemplazar espacios con "_"

    // Ruta relativa para guardar la imagen
    $pathRelativa = 'img/' . $nombreLimpio . '.' . $extension;

    // Ruta absoluta para guardar la imagen
    $uploadDir = __DIR__ . '/../img/';
    if(!is_dir($uploadDir)){
        mkdir($uploadDir, 0755, true); // Crea el directorio si no existe
    } 

    $absolutaPath = $uploadDir . $nombreLimpio . '.' . $extension; // Ruta absoluta para guardar el archivo
    if(!move_uploaded_file($imagen['tmp_name'], $absolutaPath)){
        $_SESSION['errorImagen'] = true;
        header('Location:../view/admin/crearSala.php');
        exit();
    }

    //Realizar el insert a la base de datos
    try{
        $sqlInsertSala = "INSERT INTO sala (nombre_sala, id_tipoSala, imagen_sala)
                            VALUES (:nombreS, :tipoSala, :imagen)";
        $stmtInsertSala = $conn->prepare($sqlInsertSala);
        $stmtInsertSala->bindParam(':nombreS', $nombreS);
        $stmtInsertSala->bindParam(':tipoSala', $tipoSala);
        $stmtInsertSala->bindParam(':imagen', $pathRelativa);// Guarda la ruta relativa
        $stmtInsertSala->execute();
        $_SESSION['successInsertSala'] = true;
        header('Location:../view/admin/gestionarSalas.php');
        exit();
    } catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        die();
    }