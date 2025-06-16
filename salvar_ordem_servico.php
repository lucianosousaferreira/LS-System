<?php
include_once 'verifica_login.php';
include_once 'conexao.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set('America/Sao_Paulo');

// Dados principais
$numero_os = $_POST['numero_os'] ?? '';
$cliente_id = intval($_POST['cliente_id'] ?? 0);
$veiculo_id = intval($_POST['veiculo_id'] ?? 0);
$data_abertura = date('Y-m-d H:i:s');
$data_entrada = $_POST['data_entrada'] ?? '';
$data_saida = $_POST['data_saida'] ?: null;
$status = $_POST['status'] ?? '';
$relato_problemas = $_POST['relato_problemas'] ?? '';
$laudo_servico = $_POST['laudo_servico'] ?? '';
$desconto = floatval($_POST['desconto'] ?? 0);
$forma_pagamento = $_POST['forma_pagamento'] ?? '';
$tecnico_id = $_POST['tecnico_id'] ?: null;

// Itens da OS
$descricao = $_POST['descricao'] ?? [];
$tipo = $_POST['tipo'] ?? [];
$preco = $_POST['preco'] ?? [];
$quantidade = $_POST['quantidade'] ?? [];

// Validação
if (empty($descricao) || count($descricao) !== count($tipo) || count($descricao) !== count($preco) || count($descricao) !== count($quantidade)) {
    die("Erro: Nenhum item adicionado ou dados inconsistentes.");
}

// Cálculo do total final
$total = 0;
for ($i = 0; $i < count($descricao); $i++) {
    $total += floatval($preco[$i]) * intval($quantidade[$i]);
}
$total_final = $total - ($total * ($desconto / 100));

// Tratamento de campos nulos
$data_saida = empty($data_saida) ? null : $data_saida;
$tecnico_id = empty($tecnico_id) ? null : $tecnico_id;

try {
    $conn->begin_transaction();

    // Inserção da ordem de serviço
    $stmt = $conn->prepare("INSERT INTO tb_ordens_servico 
        (numero_os, cliente_id, veiculo_id, data_abertura, total, data_entrada, data_saida, status, relato_problemas, laudo_servico, desconto, forma_pagamento, tecnico_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "iiissdsssdsis",
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
    $ordem_servico_id = $stmt->insert_id;
    $stmt->close();

    // Inserção dos itens
    $stmt_item = $conn->prepare("INSERT INTO tb_itens_os (ordem_servico_id, descricao, tipo, preco, quantidade, total) VALUES (?, ?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($descricao); $i++) {
        $desc = $descricao[$i];
        $tip = $tipo[$i];
        $prec = floatval($preco[$i]);
        $qtd = intval($quantidade[$i]);
        $tot = $prec * $qtd;

        $stmt_item->bind_param("issdid", $ordem_servico_id, $desc, $tip, $prec, $qtd, $tot);
        $stmt_item->execute();
    }

    $stmt_item->close();
    $conn->commit();

    header("Location: visualizar_ordem.php?id=" . $ordem_servico_id);
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo "Erro ao salvar a ordem de serviço: " . $e->getMessage();
}
?>
