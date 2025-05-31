
<?php 
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Veículos";

// Configurações de paginação
$limite = 7;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina - 1) * $limite;

// Campo de busca
$busca = '';
$filtro = '';
if (isset($_GET['busca']) && !empty($_GET['busca'])) {
    $busca = $conn->real_escape_string($_GET['busca']);
    $filtro = "WHERE marca LIKE '%$busca%' 
                OR modelo LIKE '%$busca%' 
                OR placa LIKE '%$busca%' 
                OR cliente_nome LIKE '%$busca%'";
}

// Total de registros para paginação
$sql_total = "SELECT COUNT(*) AS total FROM tb_veiculos $filtro";
$result_total = $conn->query($sql_total);
$total_registros = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $limite);

// Consulta com limite e offset
$sql = "SELECT * FROM tb_veiculos $filtro ORDER BY id DESC LIMIT $limite OFFSET $offset";
$resultado = $conn->query($sql);
?>

<?php include_once '_header.php'; ?>

<div class="container-fluid py-5 px-4" style="max-width: 1100px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-primary fw-semibold mb-0">Veículos</h4>
    <hr class="my-4">
    <a href="cadastro_veiculo.php" class="btn btn-sm btn-outline-success">
      <i class="bi bi-car-front-fill me-1"></i> Novo Veículo
    </a>
  </div>

  <!-- Campo de Busca -->
  <form method="GET" class="mb-4">
    <div class="input-group">
      <input type="text" name="busca" class="form-control" placeholder="Buscar por marca, modelo, placa ou cliente..." value="<?= htmlspecialchars($busca) ?>">
      <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
    </div>
  </form>

  <!-- Tabela -->
  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Marca</th>
          <th>Modelo</th>
          <th>Ano</th>
          <th>Placa</th>
          <th>Cor</th>
          <th>Cliente</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($resultado->num_rows > 0): ?>
          <?php while ($row = $resultado->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['marca']) ?></td>
              <td><?= htmlspecialchars($row['modelo']) ?></td>
              <td><?= $row['ano'] ?></td>
              <td><?= strtoupper($row['placa']) ?></td>
              <td><?= htmlspecialchars($row['cor']) ?></td>
              <td><?= htmlspecialchars($row['cliente_nome']) ?></td>
              <td>
                <a href="editar_veiculo.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <a href="excluir_veiculo.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este veículo?')">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="text-center">Nenhum veículo encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Paginação -->
  <?php if ($total_paginas > 1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
          <li class="page-item <?= ($i == $pagina) ? 'active' : '' ?>">
            <a class="page-link" href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<?php include_once '_footer.php'; ?>
