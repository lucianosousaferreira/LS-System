<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Fornecedores";

$filtro = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 7;
$offset = ($pagina - 1) * $por_pagina;

// Total de registros
$sql_total = "SELECT COUNT(*) as total FROM tb_fornecedores";
$params = [];
$tipos = "";

if (!empty($filtro)) {
    $sql_total .= " WHERE nome LIKE ?";
    $params[] = "%$filtro%";
    $tipos = "s";
}

$stmt_total = $conn->prepare($sql_total);
if (!empty($filtro)) {
    $stmt_total->bind_param($tipos, ...$params);
}
$stmt_total->execute();
$total = $stmt_total->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total / $por_pagina);

// Consulta paginada
$sql = "SELECT * FROM tb_fornecedores";
if (!empty($filtro)) {
    $sql .= " WHERE nome LIKE ?";
}
$sql .= " ORDER BY nome ASC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if (!empty($filtro)) {
    $tipos .= "ii";
    $params[] = $por_pagina;
    $params[] = $offset;
    $stmt->bind_param($tipos, ...$params);
} else {
    $stmt->bind_param("ii", $por_pagina, $offset);
}
$stmt->execute();
$resultado = $stmt->get_result();
?>

<?php include_once '_header.php'; ?>

<div class="container py-5" style="max-width: 1100px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-primary fw-semibold mb-0">Fornecedores</h4>
    <hr class="my-4">
    <a href="cadastro_fornecedor.php" class="btn btn-sm btn-outline-success">
      <i class="bi bi-plus-circle me-1"></i> Novo Fornecedor
    </a>
  </div>

  <form method="get" class="mb-3">
    <div class="input-group">
      <input type="text" name="busca" class="form-control" placeholder="Buscar por nome..." value="<?= htmlspecialchars($filtro) ?>">
      <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nome do Fornecedor</th>
          <th class="text-center">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($resultado->num_rows > 0): ?>
          <?php while ($row = $resultado->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['nome']) ?></td>
              <td class="text-center">
                <a href="editar_fornecedor.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <a href="excluir_fornecedor.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este fornecedor?')" title="Excluir">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="3" class="text-center text-muted">Nenhum fornecedor encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($total_paginas > 1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
          <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
            <a class="page-link" href="?pagina=<?= $i ?>&busca=<?= urlencode($filtro) ?>"><?= $i ?></a>
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
