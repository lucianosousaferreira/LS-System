<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Editar Marca";

$mensagem = "";
$tipo_mensagem = "info";

// Verifica se o ID foi enviado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: listar_marcas.php");
    exit;
}

$id_marca = intval($_GET['id']);

// Busca os dados da marca
$sql = "SELECT nome FROM tb_marca WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_marca);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $mensagem = "Marca não encontrada.";
    $tipo_mensagem = "danger";
} else {
    $dados_marca = $result->fetch_assoc();
    $nome_marca = $dados_marca['nome'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novo_nome = trim($_POST["nome_marca"]);

    if (!empty($novo_nome)) {
        $sql = "UPDATE tb_marca SET nome = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $novo_nome, $id_marca);

        if ($stmt->execute()) {
            $mensagem = "Marca atualizada com sucesso!";
            $tipo_mensagem = "success";
            $nome_marca = $novo_nome;
        } else {
            $mensagem = "Erro ao atualizar a marca: " . $conn->error;
            $tipo_mensagem = "danger";
        }

        $stmt->close();
    } else {
        $mensagem = "O nome da marca não pode estar em branco.";
        $tipo_mensagem = "warning";
    }
}

$conn->close();
?>

<?php include_once '_header.php'; ?>

<div class="container" style="max-width: 500px; margin-top: 20px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary fw-semibold mb-0">Editar Marca</h4>
    <a href="listar_marcas.php" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>
  </div>

  <?php if (!empty($mensagem)): ?>
    <div class="alert alert-<?= $tipo_mensagem ?> py-2"><?= htmlspecialchars($mensagem) ?></div>
  <?php endif; ?>

  <form method="POST" novalidate>
    <div class="mb-3">
      <label for="nome_marca" class="form-label small">Nome da Marca</label>
      <input type="text" name="nome_marca" id="nome_marca" class="form-control form-control-sm"
             value="<?= htmlspecialchars($nome_marca ?? '') ?>" required>
    </div>

    <div class="d-flex justify-content-end">
      <button type="submit" class="btn btn-sm btn-primary px-4">Salvar</button>
    </div>
  </form>
</div>

<?php include_once '_footer.php'; ?>
