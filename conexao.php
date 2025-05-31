<?php
// Conexão com o banco
$host = getenv("localhost");
$usuario = getenv("root");
$senha = getenv("");
$banco = getenv("oficina");

$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

?>