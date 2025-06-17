<?php
include_once('conexao.php');

function validarData($data) {
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data;
}

try {
    // Recebendo os dados do POST e usando null coalescente para evitar warnings
    $cliente_id = $_POST['cliente_id'] ?? null;
    $veiculo_id = $_POST['veiculo_id'] ?? null;
    $data_entrada = isset($_POST['data_entrada']) ? date('Y-m-d', strtotime($_POST['data_entrada'])) : null;
    $data_saida = !empty($_POST['data_saida']) ? date('Y-m-d', strtotime($_POST['data_saida'])) : null;
    $status = $_POST['status'] ?? null;
    $relato = $_POST['relato_problemas'] ?? null; // ajustado
    $laudo = $_POST['laudo_servico'] ?? null; // ajustado
    $desconto = floatval($_POST['desconto'] ?? 0);
    $forma_pagamento = $_POST['forma_pagamento'] ?? null;
    $tecnico_id = $_POST['tecnico_id'] ?? null;

    // Captura dos arrays dos itens (o formulário envia assim)
    $descricoes = $_POST['descricao'] ?? [];
    $tipos = $_POST['tipo'] ?? [];
    $precos = $_POST['preco'] ?? [];
    $quantidades = $_POST['quantidade'] ?? [];

    // Validação dos dados obrigatórios
    if (!$cliente_id || !$veiculo_id || !$data_entrada || !$status || !$forma_pagamento || !$tecnico_id) {
        throw new Exception("Dados obrigatórios ausentes.");
    }

    if (!validarData($data_entrada)) {
        throw new Exception("Data de entrada inválida.");
    }
    if ($data_saida && !validarData($data_saida)) {
        throw new Exception("Data de saída inválida.");
    }
    if (!$relato || !$laudo) {
        throw new Exception("Relato e laudo são obrigatórios.");
    }
    if (count($descricoes) === 0) {
        throw new Exception("Informe pelo menos um item na ordem de serviço.");
    }

    // Gerar número único para a OS
    $numero_os = 'OS-' . time();

    // Calcular total bruto dos itens
    $total_bruto = 0;
    for ($i=0; $i < count($descricoes); $i++) {
        $preco = floatval($precos[$i]);
        $qtd = intval($quantidades[$i]);
        $total_bruto += $preco * $qtd;
    }
    // Aplicar desconto percentual
    $total_final = $total_bruto - ($total_bruto * ($desconto / 100));

    // Inserir na tabela principal da OS
    $sql = "INSERT INTO tb_ordens_servico (numero_os, cliente_id, veiculo_id, data_abertura, data_entrada, data_saida, status, relato_problemas, laudo_servico, desconto, forma_pagamento, tecnico_id, total)
            VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . $conn->error);
    }
    // Tipos: s=string, i=int, d=double/float
    $stmt->bind_param("siisssssdsisd", $numero_os, $cliente_id, $veiculo_id, $data_entrada, $data_saida, $status, $relato, $laudo, $desconto, $forma_pagamento, $tecnico_id, $total_final);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("Erro ao salvar a ordem de serviço.");
    }

    $ordem_servico_id = $stmt->insert_id;
    $stmt->close();

    // Inserir itens da OS
    $sql_item = "INSERT INTO tb_itens_os (ordem_servico_id, descricao, tipo, preco, quantidade, total)
                 VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);
    if (!$stmt_item) {
        throw new Exception("Erro na preparação da consulta de itens: " . $conn->error);
    }

    for ($i=0; $i < count($descricoes); $i++) {
        $descricao = $descricoes[$i];
        $tipo = $tipos[$i];
        $preco = floatval($precos[$i]);
        $quantidade = intval($quantidades[$i]);
        $total_item = $preco * $quantidade;

        // Ajustando tipos: i = int, s = string, d = float/double
        $stmt_item->bind_param("issdii", $ordem_servico_id, $descricao, $tipo, $preco, $quantidade, $total_item);
        $stmt_item->execute();
    }
    $stmt_item->close();

    $conn->close();

    echo json_encode([
        "status" => "sucesso",
        "mensagem" => "Ordem de serviço salva com sucesso!",
        "ordem_servico_id" => $ordem_servico_id
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao salvar a ordem de serviço: " . $e->getMessage()]);
}
?>
