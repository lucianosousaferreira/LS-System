<?php
include_once 'conexao.php';

$termo = $_GET['term'] ?? '';

$stmt = $conn->prepare("SELECT id, descricao, preco_venda, imagem FROM tb_produtos_servicos WHERE descricao LIKE ? LIMIT 10");
$like = "%$termo%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

$sugestoes = [];
while ($row = $result->fetch_assoc()) {
  $sugestoes[] = [
    'id' => $row['id'],
    'label' => $row['descricao'],   // o texto que aparece na lista
    'value' => $row['descricao'],   // o texto que vai para o input após seleção
    'preco_venda' => $row['preco_venda'],
    'imagem' => $row['imagem'] // caminho para imagem, se tiver
  ];
}

echo json_encode($sugestoes);
?>
