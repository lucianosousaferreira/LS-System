<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Cadastrar Marca";
$mensagem = "";
$tipo_mensagem = "info";
$cadastro_sucesso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_marca = trim($_POST["nome_marca"]);

    if (!empty($nome_marca)) {
        $sql = "INSERT INTO tb_marca (nome) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nome_marca);

        if ($stmt->execute()) {
            $mensagem = "Marca cadastrada com sucesso!";
            $tipo_mensagem = "success";
            $cadastro_sucesso = true;
        } else {
            $mensagem = "Erro ao cadastrar marca: " . $conn->error;
            $tipo_mensagem = "danger";
        }

        $stmt->close();
    } else {
        $mensagem = "Por favor, preencha o nome da marca.";
        $tipo_mensagem = "warning";
    }
}

$conn->close();
?>

<?php include_once '_header.php'; ?>

<div class="container" style="max-width: 500px; margin-top: 20px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary fw-semibold mb-0">Cadastro de Marca</h4>
    <a href="listar_marcas.php" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-card-list me-1"></i> Listar
    </a>
  </div>

  <?php if (!empty($mensagem)): ?>
    <div class="alert alert-<?= $tipo_mensagem ?> py-2" role="alert" style="font-size: 0.9rem;">
      <?= htmlspecialchars($mensagem) ?>
    </div>
  <?php endif; ?>

  <?php if (!$cadastro_sucesso): ?>
    <form method="POST" novalidate>
      <div class="mb-3">
        <label for="nome_marca" class="form-label small">Nome da Marca</label>
        <input type="text" name="nome_marca" id="nome_marca" class="form-control form-control-sm" required>
      </div>

      <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-sm btn-primary px-4">Cadastrar</button>
      </div>
    </form>
  <?php else: ?>
    <script>
      if (window.opener) {
        window.opener.location.reload();  // Recarrega a tela de cadastro de produto/serviço
        window.close();                   // Fecha esta aba
      }
    </script>
  <?php endif; ?>
</div>

<?php include_once '_footer.php'; ?>
