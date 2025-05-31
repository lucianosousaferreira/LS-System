<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
include_once '_header.php';
$titulo_pagina = "O S Abertas";

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = 7;
$offset = ($pagina - 1) * $limite;

// Total de registros (com ou sem filtro)
$sql_total = "
  SELECT COUNT(*) AS total
  FROM tb_ordens_servico os
  LEFT JOIN tb_clientes c ON os.cliente_id = c.id
  WHERE c.nome LIKE ?
";
$stmt_total = $conn->prepare($sql_total);
$param_busca = "%$busca%";
$stmt_total->bind_param("s", $param_busca);
$stmt_total->execute();
$total = $stmt_total->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total / $limite);

// Consulta principal
$sql = "
  SELECT os.id, os.numero_os, os.data_entrada, os.status, os.tecnico_id, os.total,
         c.nome AS cliente, 
         v.modelo, v.marca, v.cor, v.ano,
         t.nome AS tecnico
  FROM tb_ordens_servico os
  LEFT JOIN tb_clientes c ON os.cliente_id = c.id
  LEFT JOIN tb_veiculos v ON os.veiculo_id = v.id
  LEFT JOIN tb_tecnicos t ON os.tecnico_id = t.id
  WHERE c.nome LIKE ?
  ORDER BY os.numero_os DESC
  LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $param_busca, $limite, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container py-5" style="max-width: 1100px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-primary fw-semibold mb-0">Ordens de Serviço</h4>
    <hr class="my-4">
    <a href="ordem_servico.php" class="btn btn-sm btn-outline-success">
      <i class="bi bi-plus-circle me-1"></i> Nova OS
    </a>
  </div>

  <!-- Campo de busca -->
  <form method="get" class="mb-4">
    <div class="input-group">
      <input type="text" name="busca" class="form-control" placeholder="Buscar por cliente..." value="<?= htmlspecialchars($busca) ?>">
      <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle table-hover">
      <thead class="table-dark">
        <tr>
          <th>Nº OS</th>
          <th>Cliente</th>
          <th>Veículo</th>
          <th>Data Entrada</th>
          <th>Status</th>
          <th>Total</th>
          <th>Técnico</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): 
            $veiculo = "{$row['marca']} {$row['modelo']} {$row['cor']} {$row['ano']}";
            $dataEntrada = date("d/m/Y", strtotime($row['data_entrada']));
            $valorTotal = number_format($row['total'], 2, ',', '.');
          ?>
            <tr>
              <td><?= $row['numero_os'] ?></td>
              <td><?= htmlspecialchars($row['cliente']) ?></td>
              <td><?= htmlspecialchars($veiculo) ?></td>
              <td><?= $dataEntrada ?></td>
              <td><?= htmlspecialchars($row['status']) ?></td>
              <td>R$ <?= $valorTotal ?></td>
              <td><?= htmlspecialchars($row['tecnico']) ?></td>
              <td class="d-flex gap-1">
                <a href="visualizar_ordem.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Visualizar">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="excluir_ordem.php?id=<?= $row['id'] ?>" 
                   class="btn btn-sm btn-danger" 
                   title="Excluir" 
                   onclick="return confirm('Tem certeza que deseja excluir esta OS?');">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="text-center text-muted">Nenhuma ordem de serviço encontrada.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Paginação -->
  <?php if ($total_paginas > 1): ?>
    <nav>
      <ul class="pagination justify-content-center mt-3">
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
          <li class="page-item <?= ($i == $pagina) ? 'active' : '' ?>">
            <a class="page-link" href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<?php
$stmt->close();
$stmt_total->close();
$conn->close();
include_once '_footer.php';
?>
