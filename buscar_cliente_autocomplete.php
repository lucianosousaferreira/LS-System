<?php
include_once 'conexao.php';

$termo = $_GET['term'];

$sql = "SELECT id, nome FROM tb_clientes WHERE nome LIKE ? LIMIT 10";
$stmt = $conn->prepare($sql);
$search = "%{$termo}%";
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

$clientes = [];
while ($row = $result->fetch_assoc()) {
    $clientes[] = [
        'id' => $row['id'],
        'label' => $row['nome']
    ];
}

echo json_encode($clientes);
?>
