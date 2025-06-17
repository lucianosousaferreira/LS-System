<?php
include_once 'verifica_login.php';
include_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_os           = intval($_POST['id_os']);
    $data_entrada    = $_POST['data_entrada'];
    $data_saida      = !empty($_POST['data_saida']) ? $_POST['data_saida'] : null;
    $status          = $_POST['status'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $relato          = $_POST['relato_problemas'];
    $laudo           = $_POST['laudo_servico'];
    $desconto        = floatval($_POST['desconto']);
    $tecnico_id      = intval($_POST['tecnico_id']);

    // Atualiza a OS
    $sql = "UPDATE tb_ordens_servico 
            SET data_entrada = ?, data_saida = ?, status = ?, forma_pagamento = ?, 
                relato_problemas = ?, laudo_servico = ?, desconto = ?, tecnico_id = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssdii", 
        $data_entrada, 
        $data_saida, 
        $status, 
        $forma_pagamento, 
        $relato, 
        $laudo, 
        $desconto, 
        $tecnico_id,
        $id_os
    );
    if (!$stmt->execute()) {
        die("Erro ao atualizar OS: " . $stmt->error);
    }
    $stmt->close();

    // Remove itens antigos
    $stmt_del = $conn->prepare("DELETE FROM tb_itens_os WHERE ordem_servico_id = ?");
    $stmt_del->bind_param("i", $id_os);
    $stmt_del->execute();
    $stmt_del->close();

    // Adiciona os novos itens (sem campo 'total')
    $descricao  = $_POST['descricao'] ?? [];
    $tipo       = $_POST['tipo'] ?? [];
    $preco      = $_POST['preco'] ?? [];
    $quantidade = $_POST['quantidade'] ?? [];

    $total_geral = 0;
    $stmt_item = $conn->prepare("INSERT INTO tb_itens_os (ordem_servico_id, descricao, tipo, preco, quantidade) 
                                 VALUES (?, ?, ?, ?, ?)");
    $stmt_item->bind_param("issdi", $id_os, $desc, $tp, $prc, $qtd);

    for ($i = 0; $i < count($descricao); $i++) {
        $desc  = $descricao[$i];
        $tp    = $tipo[$i];
        $prc   = floatval($preco[$i]);
        $qtd   = intval($quantidade[$i]);
        $total = $prc * $qtd;
        $total_geral += $total;

        if (!$stmt_item->execute()) {
            die("Erro ao inserir item: " . $stmt_item->error);
        }
    }
    $stmt_item->close();

    // Aplica o desconto no total
    $total_com_desconto = $total_geral - ($total_geral * ($desconto / 100));

    // Atualiza o total da OS
    $stmt_total = $conn->prepare("UPDATE tb_ordens_servico SET total = ? WHERE id = ?");
    $stmt_total->bind_param("di", $total_com_desconto, $id_os);
    $stmt_total->execute();
    $stmt_total->close();

    // Redireciona com sucesso
    header("Location: visualizar_ordem.php?id=$id_os&msg=OS atualizada com sucesso");
    exit;
} else {
    header("Location: lista_ordem_servico.php");
    exit;
}
