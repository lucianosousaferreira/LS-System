<?php
$host = getenv('DB_HOST');
$usuario = getenv('DB_USER');
$senha = getenv('DB_PASSWORD');
$banco = getenv('DB_NAME');
$porta = (int) getenv('DB_PORT');

$conexao = new mysqli($host, $usuario, $senha, $banco, $porta);

if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}
?>
