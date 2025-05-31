<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Cadastrar Tecnicos";

$mensagem = "";
$tipo_mensagem = "info";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $especialidade = $_POST["especialidade"];
    $contato = $_POST["contato"];
    $email = $_POST["email"];

    $sql = "INSERT INTO tb_tecnicos (nome, especialidade, contato, email)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $especialidade, $contato, $email);

    if ($stmt->execute()) {
        $mensagem = "Técnico cadastrado com sucesso!";
        $tipo_mensagem = "success";
    } else {
        $mensagem = "Erro ao cadastrar técnico: " . $conn->error;
        $tipo_mensagem = "danger";
    }

    $stmt->close();
}
$conn->close();
?>

<?php include_once '_header.php'; ?>

<div class="container" style="max-width: 600px; margin-top: 20px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary fw-semibold mb-0">Cadastro de Técnico</h4>
    <a href="listar_tecnicos.php" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-card-list me-1"></i> Listar
    </a>
  </div>

  <?php if (!empty($mensagem)): ?>
    <div class="alert alert-<?= $tipo_mensagem ?> py-2" role="alert" style="font-size: 0.9rem;">
      <?= htmlspecialchars($mensagem) ?>
    </div>
  <?php endif; ?>

  <form method="POST" novalidate>
    <div class="mb-2">
      <label for="nome" class="form-label small">Nome</label>
      <input type="text" name="nome" id="nome" class="form-control form-control-sm" required>
    </div>

    <div class="mb-2">
      <label for="especialidade" class="form-label small">Especialidade</label>
      <input type="text" name="especialidade" id="especialidade" class="form-control form-control-sm">
    </div>

    <div class="mb-2">
      <label for="contato" class="form-label small">Telefone</label>
      <input type="text" name="contato" id="contato" class="form-control form-control-sm">
    </div>

    <div class="mb-3">
      <label for="email" class="form-label small">E-mail</label>
      <input type="email" name="email" id="email" class="form-control form-control-sm">
    </div>

    <div class="d-flex justify-content-end">
      <button type="submit" class="btn btn-sm btn-primary px-4">Cadastrar</button>
    </div>
  </form>
</div>

<?php include_once '_footer.php'; ?>
