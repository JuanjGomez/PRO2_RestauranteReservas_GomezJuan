<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include "./conexion.php";
    $user = htmlspecialchars(trim($_POST['user']));
    $pwd = htmlspecialchars(trim($_POST['pwd']));

    try {
        $sql = "SELECT * FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id_rol WHERE u.usuario = :user";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user', $user, PDO::PARAM_STR);
        $stmt->execute();
        // Comprobamos si se han obtenido resultados
        if ($stmt->rowCount() > 0) {
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verificamos si la contraseña es correcta
            if (password_verify($pwd, $fila['password'])) {
                // Iniciamos la sesión y almacenamos los datos del camarero
                session_start();
                $_SESSION['id'] = $fila['id_usuario'];
                $_SESSION['nombre'] = $fila['nombre'];
                $_SESSION['usuario'] = $fila['usuario'];
                $_SESSION['rol'] = $fila['nombre_rol'];
                $_SESSION['success'] = true;
                if($_SESSION['rol'] == 'Gerente'){
                    header('Location: ../view/admin/viewGerente.php');
                } else if($_SESSION['rol'] == 'Camarero'){
                    header('Location:../view/index.php');
                } else if($_SESSION['rol'] == 'Mantenimiento'){
                    header('Location:../view/mantenimiento/viewMantenimiento.php');
                } else {
                    header('Location:../index.php?error=5');
                }
                die();
            } else {
                header('Location: ../index.php?error=5');
                die();
            }
        } else {
            header("Location: ../index.php?error=5");
            die();
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
} else {
    header("Location: index.php");
    die();
}