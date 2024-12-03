<!-- header.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Header</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
  <header>
    <nav>
        <div class="sesionIniciada">
            <p>Usuario: <?php echo $_SESSION['nombre']?></p>
        </div>
        <div class="cerrarSesion">
          <a id="bug" href="../view/filtros.php">
            <button type="submit" class="btn btn-light"  id="cerrarSesion">Filtrar</button>
          </a>
            <a href="../procesos/logout.php">
            <button type="submit" class="btn btn-dark" id="cerrarSesion">Cerrar Sesión</button>
            </a>
        </div>


    </nav>
  </header>