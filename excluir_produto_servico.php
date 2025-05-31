<?php
include_once 'verifica_login.php';
include_once 'conexao.php';

// Verifica se o ID foi passado
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Buscar a imagem para excluir do diretório, se existir
    $sql_img = "SELECT imagem FROM tb_produtos_servicos WHERE id = ?";
    $stmt_img = $conn->prepare($sql_img);
    $stmt_img->bind_param("i", $id);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();

    if ($result_img->num_rows > 0) {
        $row = $result_img->fetch_assoc();
        if (!empty($row['imagem']) && file_exists("imagens/" . $row['imagem'])) {
            unlink("imagens/" . $row['imagem']);
        }
    }

    // Exclui o registro do banco de dados
    $sql = "DELETE FROM tb_produtos_servicos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: listar_produtos_servicos.php?msg=excluido");
        exit;
    } else {
        echo "Erro ao excluir o registro.";
    }
} else {
    echo "ID inválido.";
}

$conn->close();
?>
