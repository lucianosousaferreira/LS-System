<?php
include_once 'verifica_login.php';
include_once 'conexao.php';

// Dados principais da OS
$numero_os = $_POST['numero_os'];
$cliente_id = intval($_POST['cliente_id']);
$veiculo_id = intval($_POST['veiculo_id']);
$data_abertura = date('Y-m-d H:i:s');

$data_entrada = $_POST['data_entrada'];
$data_saida = !empty($_POST['data_saida']) ? $_POST['data_saida'] : null;
$status = $_POST['status'];
$relato_problemas = $_POST['relato_problemas'];
$laudo_servico = $_POST['laudo_servico'];
$forma_pagamento = $_POST['forma_pagamento'];
$tecnico_id = !empty($_POST['tecnico_id']) ? intval($_POST['tecnico_id']) : null;

$desconto = isset($_POST['desconto']) ? floatval($_POST['desconto']) : 0.0;

// Cálculo do total
$total_bruto = 0;
foreach ($_POST['preco'] as $i => $preco) {
    $qtd = $_POST['quantidade'][$i];
    $total_bruto += $preco * $qtd;
}

$valor_desconto = ($total_bruto * $desconto) / 100;
$total_final = $total_bruto - $valor_desconto;

// Inserir ordem de serviço (com todos os campos necessários)
$stmt = $conn->prepare("INSERT INTO tb_ordens_servico 
    (numero_os, cliente_id, veiculo_id, data_abertura, total, data_entrada, data_saida, status, relato_problemas, laudo_servico, desconto, forma_pagamento, tecnico_id) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("siissdsssddsi", 
    $numero_os, 
    $cliente_id, 
    $veiculo_id, 
    $data_abertura, 
    $total_final, 
    $data_entrada, 
    $data_saida, 
    $status, 
    $relato_problemas, 
    $laudo_servico, 
    $desconto, 
    $forma_pagamento, 
    $tecnico_id
);

$stmt->execute();
$id_ordem = $stmt->insert_id;

// Inserir itens da ordem
foreach ($_POST['descricao'] as $i => $descricao) {
    $tipo = $_POST['tipo'][$i];
    $preco = floatval($_POST['preco'][$i]);
    $quantidade = intval($_POST['quantidade'][$i]);
    $subtotal = $preco * $quantidade;

    $stmt_item = $conn->prepare("INSERT INTO tb_itens_os 
        (ordem_servico_id, descricao, tipo, preco, quantidade, total) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_item->bind_param("issdii", $id_ordem, $descricao, $tipo, $preco, $quantidade, $subtotal);
    $stmt_item->execute();
}

$conn->close();

// Redireciona para visualização da ordem
header("Location: visualizar_ordem.php?id=$id_ordem");
exit;
?>
