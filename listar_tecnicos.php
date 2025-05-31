<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Técnicos";

$filtro = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = 7;
$offset = ($pagina - 1) * $limite;

// Contagem total para paginação
$sql_total = "SELECT COUNT(*) AS total FROM tb_tecnicos";
if (!empty($filtro)) {
    $sql_total .= " WHERE nome LIKE ?";
    $stmt_total = $conn->prepare($sql_total);
    $param = "%$filtro%";
    $stmt_total->bind_param("s", $param);
    $stmt_total->execute();
    $res_total = $stmt_total->get_result()->fetch_assoc();
} else {
    $res_total = $conn->query($sql_total)->fetch_assoc();
}
$total_registros = $res_total['total'];
$total_paginas = ceil($total_registros / $limite);

// Consulta principal
$sql = "SELECT * FROM tb_tecnicos";
if (!empty($filtro)) {
    $sql .= " WHERE nome LIKE ?";
}
$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if (!empty($filtro)) {
    $stmt->bind_param("sii", $param, $limite, $offset);
} else {
    $stmt->bind_param("ii", $limite, $offset);
}
$stmt->execute();
$resultado = $stmt->get_result();
?>

<?php include_once '_header.php'; ?>

<div class="container py-5" style="max-width: 1100px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-primary fw-semibold mb-0">Técnicos</h4>
    <hr class="my-4">
    <a href="cadastro_tecnicos.php" class="btn btn-outline-primary">
      <i class="bi bi-person-gear me-1"></i> Novo Técnico
    </a>
  </div>

  <form method="get" class="mb-4">
    <div class="input-group">
      <input type="text" name="busca" class="form-control" placeholder="Buscar por nome..." value="<?= htmlspecialchars($filtro) ?>">
      <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle table-hover">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Especialidade</th>
          <th>Contato</th>
          <th>E-mail</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($resultado->num_rows > 0): ?>
          <?php while ($row = $resultado->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['nome']) ?></td>
              <td><?= htmlspecialchars($row['especialidade']) ?></td>
              <td><?= htmlspecialchars($row['contato']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td>
                <a href="editar_tecnico.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <a href="excluir_tecnico.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este técnico?')" title="Excluir">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center text-muted">Nenhum técnico encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Paginação -->
  <nav>
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
        <li class="page-item <?= ($i == $pagina) ? 'active' : '' ?>">
          <a class="page-link" href="?pagina=<?= $i ?>&busca=<?= urlencode($filtro) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<?php include_once '_footer.php'; $conn->close(); ?>
