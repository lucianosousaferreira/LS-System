<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Cadastrar Cliente";

$mensagem = "";
$tipo_mensagem = "info";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $cp_cn = $_POST['cpf'];
    $contato = $_POST["contato"];
    $endereco = $_POST["endereco"];
    $email = $_POST["email"];

    $sql = "INSERT INTO tb_clientes (nome, cpf_cnpj, contato, endereco, email)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nome, $cp_cn, $contato, $endereco, $email);

    if ($stmt->execute()) {
        $mensagem = "Cliente cadastrado com sucesso!";
        $tipo_mensagem = "success";
    } else {
        $mensagem = "Erro ao cadastrar cliente: " . $conn->error;
        $tipo_mensagem = "danger";
    }

    $stmt->close();
}
$conn->close();
?>

<?php include_once '_header.php'; ?>

<div class="container" style="max-width: 600px; margin-top: 20px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary fw-semibold mb-0">Cadastro de Cliente</h4>
    <a href="listar_clientes.php" class="btn btn-sm btn-outline-secondary">
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
      <label for="cpf" class="form-label small">CPF/CNPJ</label>
      <input type="text" name="cpf" id="cpf" class="form-control form-control-sm">
    </div>

    <div class="mb-2">
      <label for="contato" class="form-label small">Telefone</label>
      <input type="text" name="contato" id="contato" class="form-control form-control-sm">
    </div>

    <div class="mb-2">
      <label for="endereco" class="form-label small">Endereço</label>
      <input type="text" name="endereco" id="endereco" class="form-control form-control-sm">
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
