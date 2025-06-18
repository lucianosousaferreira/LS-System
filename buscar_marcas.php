<?php
include_once 'conexao.php';

$query = $_GET['query'] ?? '';

$resultado = [];
if (!empty($query)) {
    $sql = "SELECT id, nome FROM tb_marca WHERE nome LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    $busca = '%' . $query . '%';
    $stmt->bind_param("s", $busca);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $resultado[] = ['id' => $row['id'], 'nome' => $row['nome']];
    }

    $stmt->close();
}
$conn->close();

header('Content-Type: application/json');
echo json_encode($resultado);
