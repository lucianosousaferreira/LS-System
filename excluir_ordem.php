<?php
include_once 'verifica_login.php';
include_once 'conexao.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: listar_ordens.php");
    exit;
}

$id = intval($_GET['id']);

// Exclui itens relacionados primeiro (se necessário)
$conn->query("DELETE FROM tb_itens_os WHERE ordem_servico_id = $id");

// Exclui a ordem de serviço
$conn->query("DELETE FROM tb_ordens_servico WHERE id = $id");

$conn->close();

header("Location: listar_ordem_servico.php");
exit;
