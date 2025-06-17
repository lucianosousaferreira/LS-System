<?php
include_once 'verifica_login.php';
include_once 'conexao.php';

header('Content-Type: application/json');

// --- Validação dos campos obrigatórios ---
if (
    empty($_POST['cliente_id']) || empty($_POST['veiculo_id']) || empty($_POST['data_entrada']) ||
    empty($_POST['status']) || empty($_POST['tecnico_id']) || empty($_POST['forma_pagamento']) ||
    empty($_POST['relato_problemas']) || empty($_POST['laudo_servico']) || 
    !isset($_POST['total']) || empty($_POST['itens'])
) {
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao salvar a ordem de serviço: Dados obrigatórios ausentes."]);
    exit;
}

// --- Recebendo e tratando os dados ---
$numero_os = $_POST['numero_os'] ?? '';
$cliente_id = intval($_POST['cliente_id']);
$veiculo_id = intval($_POST['veiculo_id']);
$data_abertura = date('Y-m-d H:i:s');
$data_entrada = $_POST['data_entrada'];
$data_saida = !empty($_POST['data_saida']) ? $_POST['data_saida'] : null;
$status = $_POST['status'];
$tecnico_id = intval($_POST['tecnico_id']);
$forma_pagamento = $_POST['forma_pagamento'];
$relato = $_POST['relato_problemas'];
$laudo = $_POST['laudo_servico'];
$desconto = floatval($_POST['desconto'] ?? 0);
$total = floatval($_POST['total']);
$itens_json = $_POST['itens'];

$itens = json_decode($itens_json, true);
if (!is_array($itens) || count($itens) === 0) {
    echo json_encode(["status" => "erro", "mensagem" => "Erro: Nenhum item enviado."]);
    exit;
}

// --- Inserção na tabela tb_ordens_servico ---
$stmt_os = $conn->prepare("INSERT INTO tb_ordens_servico (
    numero_os, cliente_id, veiculo_id, data_abertura, data_entrada, data_saida,
    status, tecnico_id, forma_pagamento, relato_problemas, laudo_servico, desconto, total
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$data_saida_param = $data_saida ?: null;

$stmt_os->bind_param(
    "siissssisssdd",
    $numero_os,
    $cliente_id,
    $veiculo_id,
    $data_abertura,
    $data_entrada,
    $data_saida_param,
    $status,
    $tecnico_id,
    $forma_pagamento,
    $relato,
    $laudo,
    $desconto,
    $total
);

if (!$stmt_os->execute()) {
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao salvar a OS: " . $stmt_os->error]);
    exit;
}

$ordem_servico_id = $stmt_os->insert_id;
$stmt_os->close();

// --- Inserção dos itens da OS ---
$stmt_item = $conn->prepare("INSERT INTO tb_itens_os (ordem_servico_id, descricao, tipo, preco, quantidade, total) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($itens as $item) {
    $descricao = $item['descricao'];
    $tipo = $item['tipo'];
    $preco = floatval($item['preco']);
    $quantidade = intval($item['quantidade']);
    $total_item = $preco * $quantidade;

    $stmt_item->bind_param("issdii", $ordem_servico_id, $descricao, $tipo, $preco, $quantidade, $total_item);

    if (!$stmt_item->execute()) {
        echo json_encode(["status" => "erro", "mensagem" => "Erro ao salvar item: " . $stmt_item->error]);
        exit;
    }
}

$stmt_item->close();

// --- Retorno de sucesso ---
echo json_encode([
    "status" => "sucesso",
    "mensagem" => "Ordem de serviço salva com sucesso!",
    "ordem_servico_id" => $ordem_servico_id
]);

?>
