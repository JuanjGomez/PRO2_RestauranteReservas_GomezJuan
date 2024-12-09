<?php
    session_start();
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        $_SESSION['verMesas'] = $_GET['id'];
    }
?>
<form method="GET" action="../view/admin/gestionarMesas.php" name="formulario">
</form>
<script>
    document.formulario.submit();
</script>