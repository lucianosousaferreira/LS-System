<?php
include_once 'verifica_login.php';
include_once 'conexao.php';

// Verifica se veio uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acesso inválido.");
}

// Recebe os dados do formulário
$numero_os = $_POST['numero_os'];
$cliente_id = $_POST['cliente_id'];
$veiculo_id = $_POST['veiculo_id'];
$data_entrada = $_POST['data_entrada'];
$data_saida = !empty($_POST['data_saida']) ? $_POST['data_saida'] : null;
$status = $_POST['status'];
$relato_problemas = $_POST['relato_problemas'];
$laudo_servico = $_POST['laudo_servico'];
$desconto = floatval($_POST['desconto']);
$forma_pagamento = $_POST['forma_pagamento'];
$tecnico_id = $_POST['tecnico_id'];
$total = floatval($_POST['total']);
$itens_json = $_POST['itens'];

$itens = json_decode($itens_json, true);

if (!$itens || count($itens) === 0) {
    die("Erro: Nenhum item foi enviado.");
}

// Insere a ordem de serviço
$stmt = $conn->prepare("INSERT INTO tb_ordens_servico 
    (numero_os, cliente_id, veiculo_id, data_entrada, data_saida, status, relato_problemas, laudo_servico, desconto, forma_pagamento, tecnico_id, total)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("siisssssdsid",
    $numero_os,
    $cliente_id,
    $veiculo_id,
    $data_entrada,
    $data_saida,
    $status,
    $relato_problemas,
    $laudo_servico,
    $desconto,
    $forma_pagamento,
    $tecnico_id,
    $total
);

if (!$stmt->execute()) {
    die("Erro ao salvar a ordem de serviço: " . $stmt->error);
}

$id_os = $stmt->insert_id;
$stmt->close();

// Insere os itens da OS
$stmt_item = $conn->prepare("INSERT INTO tb_itens_os (ordem_servico_id, descricao, tipo, preco, quantidade) VALUES (?, ?, ?, ?, ?)");
$stmt_item->bind_param("isssd", $ordem_servico_id, $descricao, $tipo, $preco, $quantidade);


foreach ($itens as $item) {
    $descricao = $item['descricao'];
    $tipo = $item['tipo'];
    $preco = floatval($item['preco']);
    $quantidade = intval($item['quantidade']);
    $total_item = $preco * $quantidade;

    $stmt_item->bind_param("issdii", $id_os, $descricao, $tipo, $preco, $quantidade, $total_item);
    if (!$stmt_item->execute()) {
        die("Erro ao salvar item: " . $stmt_item->error);
    }
}

$stmt_item->close();

// Redireciona ou exibe mensagem
header("Location: visualizar_ordem.php?id=$id_os");
exit;
?>
