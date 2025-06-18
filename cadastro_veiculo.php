<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Cadastrar Veículo";

$mensagem = "";
$tipo_mensagem = "info";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['cliente_id'])) {
        $mensagem = "Erro: Selecione um cliente cadastrado antes de cadastrar o veículo.";
        $tipo_mensagem = "danger";
    } else {
        $cliente_id = $_POST['cliente_id'];
        $cliente_nome = $_POST['cliente_nome'];
        $placa = strtoupper(trim($_POST['placa']));
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $ano = $_POST['ano'];
        $cor = $_POST['cor'];

        $sql = "INSERT INTO tb_veiculos (cliente_nome, placa, marca, modelo, ano, cor, cliente_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $cliente_nome, $placa, $marca, $modelo, $ano, $cor, $cliente_id);

        if ($stmt->execute()) {
            $mensagem = "Veículo cadastrado com sucesso!";
            $tipo_mensagem = "success";
        } else {
            $mensagem = "Erro ao cadastrar: " . $stmt->error;
            $tipo_mensagem = "danger";
        }

        $stmt->close();
    }
}
$conn->close();
?>

<?php include_once '_header.php'; ?>

<div class="container" style="max-width: 600px; margin-top: 20px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary fw-semibold mb-0">Cadastro de Veículo</h4>
    <a href="listar_veiculos.php" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-card-list me-1"></i> Listar
    </a>
  </div>

  <?php if (!empty($mensagem)): ?>
    <div class="alert alert-<?= $tipo_mensagem ?> py-2" role="alert" style="font-size: 0.9rem;">
      <?= htmlspecialchars($mensagem) ?>
    </div>
  <?php endif; ?>

  <form method="POST" novalidate>
    <div class="mb-2 position-relative">
      <label for="cliente_nome" class="form-label small">Cliente</label>
      <input type="text" class="form-control form-control-sm" id="cliente_nome" name="cliente_nome" autocomplete="off" required>
      <input type="hidden" id="cliente_id" name="cliente_id">
    </div>

    <div class="mb-2">
      <label for="placa" class="form-label small">Placa</label>
      <input type="text" class="form-control form-control-sm" id="placa" name="placa" maxlength="10" required>
    </div>

    <div class="mb-2 position-relative">
      <label for="marca" class="form-label small">Marca</label>
      <input type="text" class="form-control form-control-sm" id="marca" name="marca" autocomplete="off" required>
    </div>

    <div class="mb-2">
      <label for="modelo" class="form-label small">Modelo</label>
      <input type="text" class="form-control form-control-sm" id="modelo" name="modelo" required>
    </div>

    <div class="mb-2">
      <label for="ano" class="form-label small">Ano</label>
      <input type="number" class="form-control form-control-sm" id="ano" name="ano" min="1900" max="2100" required>
    </div>

    <div class="mb-3">
      <label for="cor" class="form-label small">Cor</label>
      <input type="text" class="form-control form-control-sm" id="cor" name="cor">
    </div>

    <div class="text-end">
      <button type="submit" class="btn btn-sm btn-primary px-4">Cadastrar</button>
    </div>
  </form>
</div>

<!-- Scripts de Autocomplete -->
<script>
function autocompleteInput(inputId, url, valueKey, hiddenId = null) {
  const input = document.getElementById(inputId);

  input.addEventListener('input', function () {
    const query = this.value;
    const parent = this.parentNode;
    const existingList = parent.querySelector('ul.list-group');
    if (existingList) existingList.remove();

    if (query.length >= 2) {
      fetch(url + '?query=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
          let list = document.createElement('ul');
          list.classList.add('list-group', 'position-absolute', 'w-100');
          list.style.zIndex = 9999;

          if (data.length === 0) {
            let item = document.createElement('li');
            item.classList.add('list-group-item', 'text-danger', 'small');
            item.textContent = 'Nenhum resultado encontrado';
            list.appendChild(item);
          } else {
            data.forEach(obj => {
              let item = document.createElement('li');
              item.classList.add('list-group-item', 'small');
              item.textContent = obj[valueKey];

              item.addEventListener('click', function () {
                input.value = obj[valueKey];
                if (hiddenId && obj['id']) {
                  document.getElementById(hiddenId).value = obj['id'];
                }
                list.remove();
              });

              list.appendChild(item);
            });
          }

          parent.appendChild(list);
        });
    }
  });

  document.addEventListener('click', function (event) {
    if (!event.target.closest('#' + inputId)) {
      const list = document.querySelector('#' + inputId + ' + ul.list-group');
      if (list) list.remove();
    }
  });
}

// Cliente (com ID oculto)
autocompleteInput('cliente_nome', 'buscar_clientes.php', 'nome', 'cliente_id');

// Marca (sem ID oculto)
autocompleteInput('marca', 'buscar_marcas.php', 'nome');

document.querySelector('form').addEventListener('submit', function (e) {
  const clienteId = document.getElementById('cliente_id').value;
  if (!clienteId) {
    e.preventDefault();
    alert('Selecione um cliente cadastrado antes de cadastrar o veículo.');
  }
});
</script>

<?php include_once '_footer.php'; ?>
