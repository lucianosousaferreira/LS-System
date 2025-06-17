<?php
include_once('conexao.php');

function validarData($data) {
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data;
}

try {
    // Recebendo os dados do POST
    $cliente_id = $_POST['cliente_id'];
    $veiculo_id = $_POST['veiculo_id'];
    $data_entrada = date('Y-m-d', strtotime($_POST['data_entrada'] ?? ''));
    $data_saida = !empty($_POST['data_saida']) ? date('Y-m-d', strtotime($_POST['data_saida'])) : null;
    $status = $_POST['status'];
    $relato = $_POST['relato'];
    $laudo = $_POST['laudo'];
    $desconto = $_POST['desconto'] ?? 0;
    $forma_pagamento = $_POST['forma_pagamento'];
    $tecnico_id = $_POST['tecnico_id'];
    $total = $_POST['total'];

    // Itens da OS
    $itens = json_decode($_POST['itens'], true);

    if (!$cliente_id || !$veiculo_id || !$data_entrada || !$status || !$forma_pagamento || !$tecnico_id || !is_array($itens)) {
        throw new Exception("Dados obrigatórios ausentes.");
    }

    if (!validarData($data_entrada)) {
        throw new Exception("Data de entrada inválida.");
    }

    if ($data_saida && !validarData($data_saida)) {
        throw new Exception("Data de saída inválida.");
    }

    // Gerar número único para a OS
    $numero_os = 'OS-' . time();

    // Inserir na tabela principal da OS
    $sql = "INSERT INTO tb_ordens_servico (numero_os, cliente_id, veiculo_id, data_abertura, data_entrada, data_saida, status, relato_problemas, laudo_servico, desconto, forma_pagamento, tecnico_id, total)
            VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisssssdsid", $numero_os, $cliente_id, $veiculo_id, $data_entrada, $data_saida, $status, $relato, $laudo, $desconto, $forma_pagamento, $tecnico_id, $total);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("Erro ao salvar a ordem de serviço.");
    }

    // ID da nova OS
    $ordem_servico_id = $stmt->insert_id;
    $stmt->close();

    // Inserir os itens da OS
    $sql_item = "INSERT INTO tb_itens_os (ordem_servico_id, descricao, tipo, preco, quantidade, total)
                 VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);

    foreach ($itens as $item) {
        $descricao = $item['descricao'];
        $tipo = $item['tipo'];
        $preco = $item['preco'];
        $quantidade = $item['quantidade'];
        $total_item = $item['total'];

        $stmt_item->bind_param("issdii", $ordem_servico_id, $descricao, $tipo, $preco, $quantidade, $total_item);
        $stmt_item->execute();
    }

    $stmt_item->close();
    $conn->close();

    echo json_encode(["status" => "sucesso", "mensagem" => "Ordem de serviço salva com sucesso!", "ordem_servico_id" => $ordem_servico_id]);

} catch (Exception $e) {
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao salvar a ordem de serviço: " . $e->getMessage()]);
}
?>
