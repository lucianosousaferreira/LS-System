<?php
include_once 'verifica_login.php';
include_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_os           = $_POST['id_os'];
    $data_entrada    = $_POST['data_entrada'];
    $data_saida      = $_POST['data_saida'];
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
    $stmt->execute();

    // Remove itens antigos
    $conn->query("DELETE FROM tb_itens_os WHERE ordem_servico_id = $id_os");

    // Adiciona os novos itens
    $descricao  = $_POST['descricao'] ?? [];
    $tipo       = $_POST['tipo'] ?? [];
    $preco      = $_POST['preco'] ?? [];
    $quantidade = $_POST['quantidade'] ?? [];

    $total_geral = 0;
    for ($i = 0; $i < count($descricao); $i++) {
        $desc  = $descricao[$i];
        $tp    = $tipo[$i];
        $prc   = floatval($preco[$i]);
        $qtd   = intval($quantidade[$i]);
        $total = $prc * $qtd;
        $total_geral += $total;

        $stmt = $conn->prepare("INSERT INTO tb_itens_os (ordem_servico_id, descricao, tipo, preco, quantidade, total) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssii", $id_os, $desc, $tp, $prc, $qtd, $total);
        $stmt->execute();
    }

    // Aplica o desconto
    $total_com_desconto = $total_geral - ($total_geral * ($desconto / 100));

    // Atualiza o total na OS
    $stmt = $conn->prepare("UPDATE tb_ordens_servico SET total = ? WHERE id = ?");
    $stmt->bind_param("di", $total_com_desconto, $id_os);
    $stmt->execute();

    header("Location: visualizar_ordem.php?id=$id_os&msg=OS atualizada com sucesso");
    exit;
} else {
    header("Location: lista_ordem_servico.php");
    exit;
}
