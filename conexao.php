<?php

$host = 'trolley.proxy.rlwy.net';
$usuario = 'root';
$senha = 'TSqwfoZpKFxwBPqMsfuElImWOxAIflSB';
$banco = 'oficina';
$porta = '59498';

$conexao = new mysqli($host, $usuario, $senha, $banco, $porta);

if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}
?>
