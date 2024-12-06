<?php
    session_start();
    if(!isset($_SESSION['id']) || !in_array($_SESSION['rol'], ['Gerente'])){
        header('Location: ../../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location: gestionarUsuarios.php');
        exit();
    }
    // Sweet Alert avisa si hay duplicado en username
    $errorCrearUsuario = isset($_SESSION['errorCrearUsuario']) && $_SESSION['errorCrearUsuario'];
    unset($_SESSION['errorCrearUsuario']);
    // Sweet Alert avisa si hay duplicado en email
    $errorCrearEmail = isset($_SESSION['errorCrearEmail']) && $_SESSION['errorCrearEmail'];
    unset($_SESSION['errorCrearEmail']);
    // Sweet Alert avisa si hay duplicado en dni
    $errorCrearDni = isset($_SESSION['errorCrearDni']) && $_SESSION['errorCrearDni'];
    unset($_SESSION['errorCrearDni']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../css/formCRUDusers.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
</head>
<body class="body2">
    <header>
        <nav>
            <div id="pequeño">
                <img src="../../img/logoRestaurante.png" alt="Logo" id="logo">
            </div>
        </nav>
    </header>
    <form method="GET" action="gestionarUsuarios.php">
        <button type="submit" class="btn btn-danger">VOLVER</button>
    </form>
    <h3>Dar de Alta Trabajador</h3>
    <div class="container">
        <form method="POST" id="crear" action="../../procesos/insertUsuario.php">
            <div class="form-row">
                <div class="form-column">
                    <label for="nombre">Nombre: 
                        <input type="text" name="nombre" id="nombre" value="<?php echo isset($_POST['nombre']) ? $_POST['nombre'] : ''; ?>">
                    </label>
                    <p id="errorNombre" class="error"></p>
                    <label for="apellido">Apellido: 
                        <input type="text" name="apellido" id="apellido" value="<?php echo isset($_POST['apellido']) ? $_POST['apellido'] : ''; ?>">
                    </label>
                    <p id="errorApellido" class="error"></p>
                    <label for="usuario">Nombre usuario: 
                        <input type="text" name="usuario" id="usuario" value="<?php echo isset($_POST['usuario']) ? $_POST['usuario'] : ''; ?>">
                    </label>
                    <p id="errorUser" class="error"></p>
                    <label for="telefono">Telefono: 
                        <input type="text" name="telefono" id="telefono" value="<?php echo isset($_POST['telefono']) ? $_POST['telefono'] : ''; ?>">
                    </label>
                    <p id="errorTelefono" class="error"></p>
                    <label for="dni">DNI: 
                        <input type="text" name="dni" id="dni" value="<?php echo isset($_POST['dni']) ? $_POST['dni'] : ''; ?>">
                    </label>
                    <p id="errorDni" class="error"></p>
                </div>
                <div class="form-column">
                    <label for="nacimiento">Fecha de Nacimiento: 
                        <input type="date" name="nacimiento" id="nacimiento" value="<?php echo isset($_POST['nacimiento']) ? $_POST['nacimiento'] : ''; ?>">
                    </label>
                    <p id="errorNacimiento" class="error"></p>
                    <label for="email">Email: 
                        <input type="email" name="email" id="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                    </label>
                    <p id="errorEmail" class="error"></p>
                    <label for="pwd">Contrasena: 
                        <input type="password" name="pwd" id="pwd">
                    </label>
                    <p id="errorPwd" class="error"></p>
                    <label for="rPwd">Repetir Contrasena: 
                        <input type="password" name="rPwd" id="rPwd">
                    </label>
                    <p id="errorRpwd" class="error"></p>
                    <label for="direccion">Direccion: 
                        <input type="text" name="direccion" id="direccion" value="<?php echo isset($_POST['direccion']) ? $_POST['direccion'] : ''; ?>">
                    </label>
                    <p id="errorDireccion" class="error"></p>
                </div>
            </div>
            <div class="form-row" id="centrarF">
                <label for="rol">Rol:
                    <select name="rol" id="rol">
                        <option value="" selected-disabled>Selecciona un rol: </option>
                        <?php
                            try{
                                require_once '../../procesos/conexion.php';
                                $query = "SELECT * FROM roles";
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                $roles = $stmt->fetchAll();
                                foreach ($roles as $rol) {
                                    echo '<option value="' . $rol['id_rol'] . '" ' . 
                                        (isset($_POST['rol']) && $_POST['rol'] == $rol['id_rol'] ? 'selected' : '') . '>' . 
                                        $rol['nombre_rol'] . '</option>';
                                }
                            } catch(PDOException $e){
                                echo "Error: ". $e->getMessage();
                                die();
                            }
                        ?>
                    </select>
                </label>
                <p id="errorRol" class="error"></p>
            </div>
            <button type="submit" id="boton" disabled>Enviar</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
    <script src="../../validations/js/validaCrearUser.js"></script>
    <script>
        <?php if($errorCrearUsuario) : ?> // Verifica si es true la alerta
            Swal.fire({
                title: 'Usuario Existente',
                text: 'Introduce otro nombre de usuario',
                icon: 'error',
            })
        <?php endif;?>
        <?php if($errorCrearEmail) : ?>
            Swal.fire({
                title: 'Email Existente',
                text: 'Introduce otro email',
                icon:'error'
            })
        <?php endif; ?>
        <?php if($errorCrearDni) : ?>
            Swal.fire({
                title: 'DNI Existente',
                text: 'Introduce otro DNI',
                icon:'error'
            })
        <?php endif;?>
    </script>
</body>
</html>