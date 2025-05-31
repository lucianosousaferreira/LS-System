<?php
// Conexão com banco de dados
include_once 'conexao.php';

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT id, nome FROM tb_clientes WHERE nome LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    $like_nome = "%" . $query . "%";
    $stmt->bind_param("s", $like_nome);
    $stmt->execute();
    $result = $stmt->get_result();

    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }

    echo json_encode($clientes);
}

$conn->close();
?>
