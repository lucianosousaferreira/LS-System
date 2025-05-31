
<?php
include_once 'verifica_login.php';
include_once 'conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID inválido.";
    exit;
}

$sql = "DELETE FROM tb_veiculos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: listar_veiculos.php");
    exit;
} else {
    echo "Erro ao excluir: " . $conn->error;
}
?>
