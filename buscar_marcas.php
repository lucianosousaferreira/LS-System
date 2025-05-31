<?php
include_once 'conexao.php';
$termo = $_GET['term'] ?? '';
$sql = "SELECT nome FROM tb_marca WHERE nome LIKE ? LIMIT 10";
$stmt = $conn->prepare($sql);
$like = "%$termo%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();
$dados = [];
while ($row = $result->fetch_assoc()) {
    $dados[] = $row['nome'];
}
echo json_encode($dados);
