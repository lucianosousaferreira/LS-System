<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Editar Veículo";

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID inválido.";
    exit;
}

$sql = "SELECT * FROM tb_veiculos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    echo "Veículo não encontrado.";
    exit;
}

$veiculo = $resultado->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_nome = $_POST['cliente_nome'];
    $placa = strtoupper($_POST['placa']);
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $ano = $_POST['ano'];
    $cor = $_POST['cor'];

    $sql = "UPDATE tb_veiculos SET cliente_nome=?, placa=?, marca=?, modelo=?, ano=?, cor=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $cliente_nome, $placa, $marca, $modelo, $ano, $cor, $id);

    if ($stmt->execute()) {
        header("Location: listar_veiculos.php");
        exit;
    } else {
        echo "Erro ao atualizar: " . $conn->error;
    }
}
?>

<?php
include_once '_header.php';
?>

    <!-- Conteúdo principal -->
    <div class="container py-5">
      <div class="form-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="form-title">Editar de Veículo</h3>
          <a href="listar_veiculos.php" class="btn btn-outline-primary">
            <i class="bi bi-card-list me-1"></i> Listar Veículos
          </a>
        </div>

        <?php if (!empty($mensagem)): ?>
          <div class="alert alert-<?= $tipo_mensagem ?>" role="alert">
            <?= htmlspecialchars($mensagem) ?>
          </div>
        <?php endif; ?>

        <form method="post">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="cliente_nome" class="form-label">Nome do Cliente</label>
              <input type="text" class="form-control" id="cliente_nome" value="<?= htmlspecialchars($veiculo['cliente_nome']) ?>" name="cliente_nome">
            </div>

            <div class="col-md-6">
              <label for="placa" class="form-label">Placa</label>
              <input type="text" class="form-control" id="placa" name="placa" value="<?= htmlspecialchars($veiculo['placa']) ?>" maxlength="10" required>
            </div>

            <div class="col-md-6">
              <label for="marca" class="form-label">Marca</label>
              <input type="text" class="form-control" id="marca" name="marca" value="<?= htmlspecialchars($veiculo['marca']) ?>" required>
            </div>

            <div class="col-md-6">
              <label for="modelo" class="form-label">Modelo</label>
              <input type="text" class="form-control" id="modelo" name="modelo" value="<?= htmlspecialchars($veiculo['modelo']) ?>" required>
            </div>

            <div class="col-md-4">
              <label for="ano" class="form-label">Ano</label>
              <input type="number" class="form-control" id="ano" name="ano" value="<?= $veiculo['ano'] ?>" min="1900" max="2100" required>
            </div>

            <div class="col-md-8">
              <label for="cor" class="form-label">Cor</label>
              <input type="text" class="form-control" id="cor" name="cor" value="<?= htmlspecialchars($veiculo['cor']) ?>">
            </div>
          </div>

          <div class="mt-4 text-end">
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php
  include_once '_footer.php';
  ?>
