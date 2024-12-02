<?php

$server = 'localhost';
$usuario = 'root';
$password = '';
$bd = 'imoveisbd';

$conn = new mysqli($server, $usuario, $password, $bd);

if ($conn->connect_error) {
    die("Falha ao se conectar ao banco de dados: " . $conn->connect_error);
}


?>

