<?php
include_once 'conexao.php';

if (isset($_GET['cliente_id'])) {
    $cliente_id = $_GET['cliente_id'];

    $sql = "SELECT id, marca, modelo, placa FROM tb_veiculos WHERE cliente_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $veiculos = [];

    while ($veiculo = $result->fetch_assoc()) {
        $veiculos[] = $veiculo;
    }

    echo json_encode($veiculos);
}

$conn->close();
?>
