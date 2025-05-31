<?php
require_once 'conexao.php'; // Deve conter $conn = new mysqli(...);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Verifica se o cliente existe
    $stmt = $conn->prepare("SELECT * FROM tb_clientes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Exclui o cliente
        $delete = $conn->prepare("DELETE FROM tb_clientes WHERE id = ?");
        $delete->bind_param("i", $id);
        $delete->execute();

        header("Location: listar_clientes.php?msg=excluido");
        exit();
    } else {
        header("Location: listar_clientes.php?msg=nao_encontrado");
        exit();
    }
} else {
    header("Location: listar_clientes.php?msg=erro");
    exit();
}
?>
