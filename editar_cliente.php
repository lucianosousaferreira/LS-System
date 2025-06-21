
<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Editar Cliente";

// Verifica se foi passado o ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do cliente não especificado.");
}

$id = (int)$_GET['id'];
$mensagem = "";

// Atualiza o cliente se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $conn->real_escape_string($_POST['nome']);
    $contato = $conn->real_escape_string($_POST['contato']);
    $email = $conn->real_escape_string($_POST['email']);
    $endereco = $conn->real_escape_string($_POST['endereco']);
    $cpf = $conn->real_escape_string($_POST['cpf']);

    $sqlUpdate = "UPDATE tb_clientes 
                  SET nome='$nome', contato='$contato', email='$email', endereco='$endereco', cpf='$cpf' 
                  WHERE id=$id";

    if ($conn->query($sqlUpdate)) {
        $mensagem = "Cliente atualizado com sucesso!";
    } else {
        $mensagem = "Erro ao atualizar cliente: " . $conn->error;
    }
}

// Busca os dados do cliente
$sql = "SELECT * FROM tb_clientes WHERE id = $id";
$resultado = $conn->query($sql);
$cliente = $resultado->fetch_assoc();

include_once '_header.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="form-title">Editar Cliente</h2>
      <a href="listar_veiculos.php" class="btn btn-outline-primary">
            <i class="bi bi-card-list me-1"></i> Listar Clientes
          </a>
        </div>

    <?php if ($mensagem): ?>
      <div class="alert alert-info"><?= $mensagem ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3 shadow-sm p-4 bg-light rounded">
      <div class="col-md-6">
        <label for="nome" class="form-label">Nome</label>
        <input type="text" name="nome" id="nome" class="form-control" required value="<?= htmlspecialchars($cliente['nome']) ?>">
      </div>

      <div class="col-md-6">
        <label for="contato" class="form-label">Contato</label>
        <input type="text" name="contato" id="contato" class="form-control" required value="<?= htmlspecialchars($cliente['contato']) ?>">
      </div>

      <div class="col-md-6">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($cliente['email']) ?>">
      </div>

      <div class="col-md-6">
        <label for="cpf" class="form-label">CPF</label>
        <input type="text" name="cpf" id="cpf" class="form-control" required value="<?= htmlspecialchars($cliente['cpf_cnpj']) ?>">
      </div>

      <div class="col-12">
        <label for="endereco" class="form-label">Endereço</label>
        <input type="text" name="endereco" id="endereco" class="form-control" required value="<?= htmlspecialchars($cliente['endereco']) ?>">
      </div>

      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="listar_clientes.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</main>

<?php include_once '_footer.php'; ?>
