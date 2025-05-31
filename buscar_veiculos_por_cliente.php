<?php
include_once 'conexao.php';

$cliente_id = $_GET['cliente_id'] ?? 0;

$sql = "
    SELECT v.id, v.modelo, v.marca, v.cor, v.ano
    FROM tb_veiculos v
    INNER JOIN tb_clientes c ON c.id = v.cliente_id
    WHERE v.cliente_id = ?
    ORDER BY v.modelo
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();

$veiculos = [];
while ($row = $result->fetch_assoc()) {
    $veiculos[] = $row;
}

echo json_encode($veiculos);
?>
