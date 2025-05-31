<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Cadastrar Fornecedor";

$mensagem = "";
$tipo_mensagem = "info";
$cadastro_sucesso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);

    if (!empty($nome)) {
        $sql = "INSERT INTO tb_fornecedores (nome) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nome);

        if ($stmt->execute()) {
            $mensagem = "Fornecedor cadastrado com sucesso!";
            $tipo_mensagem = "success";
            $cadastro_sucesso = true;
        } else {
            $mensagem = "Erro ao cadastrar: " . $conn->error;
            $tipo_mensagem = "danger";
        }

        $stmt->close();
    } else {
        $mensagem = "O nome do fornecedor é obrigatório.";
        $tipo_mensagem = "warning";
    }
}
$conn->close();
?>

<?php include_once '_header.php'; ?>

<div class="container" style="max-width: 600px; margin-top: 20px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary fw-semibold mb-0">Cadastro de Fornecedor</h4>
    <a href="listar_fornecedores.php" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-card-list me-1"></i> Listar
    </a>
  </div>

  <?php if (!empty($mensagem)): ?>
    <div class="alert alert-<?= $tipo_mensagem ?> py-2"><?= htmlspecialchars($mensagem) ?></div>
  <?php endif; ?>

  <?php if (!$cadastro_sucesso): ?>
    <form method="POST" novalidate>
      <div class="mb-3">
        <label for="nome" class="form-label small">Nome do Fornecedor</label>
        <input type="text" name="nome" id="nome" class="form-control form-control-sm" required>
      </div>

      <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-sm btn-primary px-4">Cadastrar</button>
      </div>
    </form>
  <?php else: ?>
    <script>
      if (window.opener) {
        window.opener.location.reload();  // Recarrega a tela de produto/serviço
        window.close();                   // Fecha a aba de cadastro de fornecedor
      }
    </script>
  <?php endif; ?>
</div>

<?php include_once '_footer.php'; ?>
