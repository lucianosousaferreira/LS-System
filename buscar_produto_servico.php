<?php
include_once 'conexao.php';
include_once 'verifica_login.php';

if (isset($_GET['termo'])) {
    $termo = $_GET['termo'];
    $sql = "SELECT id, descricao, tipo, preco_venda FROM tb_produtos_servicos WHERE descricao LIKE ?";
    $stmt = $conn->prepare($sql);
    $termo_like = "%" . $termo . "%";
    $stmt->bind_param("s", $termo_like);
    $stmt->execute();
    $result = $stmt->get_result();
    $produtos = [];

    while ($row = $result->fetch_assoc()) {
        $produtos[] = [
            'id' => $row['id'],
            'descricao' => $row['descricao'],
            'tipo' => $row['tipo'],
            'preco' => $row['preco_venda']
        ];
    }

    echo json_encode($produtos);
    $stmt->close();
}
$conn->close();
?>
