<?php
include_once 'conexao.php';

$termo = $_GET['term'] ?? '';

$sql = "SELECT id AS cliente_id, nome FROM tb_clientes WHERE nome LIKE ? ORDER BY nome LIMIT 10";
$stmt = $conn->prepare($sql);
$like = "%$termo%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

$clientes = [];
while ($row = $result->fetch_assoc()) {
    $clientes[] = $row;
}

echo json_encode($clientes);
?>
