<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Clientes";

$limite = 7;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $limite;

$busca = isset($_GET['busca']) ? trim($conn->real_escape_string($_GET['busca'])) : '';
$filtro = $busca ? "WHERE nome LIKE '%$busca%' OR cpf_cnpj LIKE '%$busca%' OR contato LIKE '%$busca%' OR email LIKE '%$busca%' OR endereco LIKE '%$busca%'" : '';

$sql_total = "SELECT COUNT(*) AS total FROM tb_clientes $filtro";
$total_registros = $conn->query($sql_total)->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $limite);

$sql = "SELECT * FROM tb_clientes $filtro ORDER BY id DESC LIMIT $limite OFFSET $offset";
$resultado = $conn->query($sql);
?>

<?php include_once '_header.php'; ?>

<div class="container py-5" style="max-width: 1100px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-primary fw-semibold mb-0">Clientes</h4>
    <hr class="my-4">
    <a href="cadastro_cliente.php" class="btn btn-sm btn-outline-success">
      <i class="bi bi-person-plus me-1"></i> Novo Cliente
    </a>
  </div>

  <!-- Formulário de busca -->
  <form method="GET" class="mb-4">
    <div class="input-group">
      <input type="text" name="busca" class="form-control" placeholder="Buscar por nome, CPF/CNPJ, e-mail..." value="<?= htmlspecialchars($busca) ?>">
      <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
    </div>
  </form>

  <!-- Tabela de clientes -->
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>CPF/CNPJ</th>
          <th>Contato</th>
          <th>Endereço</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($resultado->num_rows): ?>
          <?php while ($row = $resultado->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['nome'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['cpf_cnpj'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['contato'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['endereco'] ?? '') ?></td>
              <td>
                <a href="editar_cliente.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <a href="excluir_cliente.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este cliente?')" title="Excluir">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center text-muted">Nenhum cliente encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Paginação -->
  <?php if ($total_paginas > 1): ?>
    <nav>
      <ul class="pagination justify-content-center mt-4">
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
