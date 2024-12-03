<?php
$servidor = "localhost";
$usuario = "root";
$pwd = "";
$db = "db_restaurante";

try {
    $conn = new PDO("mysql:host=$servidor;dbname=$db", $usuario, $pwd);
} catch (PDOException $e) {
    echo "¡Error!: " . $e->getMessage() . "<br>";
}
