<?php
include 'conexao.php';

header('Content-Type: application/json');

// Função para limpar entradas
function limpar($valor) {
    return htmlspecialchars(trim($valor));
}

// Obter os dados enviados pelo formulário
$numero_os = $_POST['numero_os'] ?? '';
$cliente_id = $_POST['cliente_id'] ?? '';
$veiculo_id = $_POST['veiculo_id'] ?? '';
$data_entrada = !empty($_POST['data_entrada']) ? $_POST['data_entrada'] : null;
$data_saida = !empty($_POST['data_saida']) ? $_POST['data_saida'] : null;
$status = $_POST['status'] ?? '';
$tecnico_id = $_POST['tecnico_id'] ?? '';
$forma_pagamento = $_POST['forma_pagamento'] ?? '';
$relato = $_POST['relato_problemas'] ?? '';
$laudo = $_POST['laudo_servico'] ?? '';
$desconto = $_POST['desconto'] ?? 0;
$total = $_POST['total'] ?? 0;
$itens_json = $_POST['itens'] ?? '[]';

$itens = json_decode($itens_json, true);
if (!is_array($itens)) {
    echo json_encode(["status" => "erro", "mensagem" => "Itens inválidos."]);
    exit;
}

// Verificar se os dados obrigatórios foram preenchidos
if (empty($cliente_id) || empty($veiculo_id) || empty($data_entrada) || empty($status) || count($itens) === 0) {
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao salvar a ordem de serviço: Dados obrigatórios ausentes."]);
    exit;
}

$data_abertura = date('Y-m-d H:i:s');

// Inserir a ordem de serviço
$sql_os = "INSERT INTO tb_ordens_servico (numero_os, cliente_id, veiculo_id, data_abertura, data_entrada, data_saida, status, tecnico_id, forma_pagamento, relato_problemas, laudo_servico, desconto, total)
           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_os = $conn->prepare($sql_os);

// Ajusta nulls corretamente
if ($data_saida === null) {
    $stmt_os->bind_param("siissssisssdd", $numero_os, $cliente_id, $veiculo_id, $data_abertura, $data_entrada, $null = NULL, $status, $tecnico_id, $forma_pagamento, $relato, $laudo, $desconto, $total);
} else {
    $stmt_os->bind_param("siissssisssdd", $numero_os, $cliente_id, $veiculo_id, $data_abertura, $data_entrada, $data_saida, $status, $tecnico_id, $forma_pagamento, $relato, $laudo, $desconto, $total);
}

if ($stmt_os->execute()) {
    $ordem_servico_id = $stmt_os->insert_id;

    // Inserir os itens
    $sql_item = "INSERT INTO tb_itens_os (ordem_servico_id, descricao, tipo, preco, quantidade, total)
                 VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);

    foreach ($itens as $item) {
        $descricao = limpar($item['descricao'] ?? '');
        $tipo = limpar($item['tipo'] ?? '');
        $preco = floatval($item['preco'] ?? 0);
        $quantidade = intval($item['quantidade'] ?? 0);
        $total_item = floatval($item['total'] ?? 0);

        $stmt_item->bind_param("issdii", $ordem_servico_id, $descricao, $tipo, $preco, $quantidade, $total_item);
        $stmt_item->execute();
    }

    echo json_encode(["status" => "sucesso", "mensagem" => "Ordem de serviço salva com sucesso!"]);
} else {
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao salvar a ordem de serviço: " . $stmt_os->error]);
}

$conn->close();
?>
