<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Produtos/Serviços";

// Tipo selecionado: 'produto' ou 'serviço'
$tipo_aba = isset($_GET['tipo']) && in_array($_GET['tipo'], ['produto', 'serviço']) ? $_GET['tipo'] : 'produto';
$filtro = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

$limite = 7;
$offset = ($pagina - 1) * $limite;

// Contar total de registros do tipo
$sql_total = "SELECT COUNT(*) AS total FROM tb_produtos_servicos WHERE tipo = ?";
$params = [$tipo_aba];
$tipos = "s";

if (!empty($filtro)) {
    $sql_total .= " AND descricao LIKE ?";
    $params[] = "%$filtro%";
    $tipos .= "s";
}

$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param($tipos, ...$params);
$stmt_total->execute();
$total = $stmt_total->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total / $limite);

// Consulta com paginação e tipo
$sql = "SELECT * FROM tb_produtos_servicos WHERE tipo = ?";
$params = [$tipo_aba];
$tipos = "s";

if (!empty($filtro)) {
    $sql .= " AND descricao LIKE ?";
    $params[] = "%$filtro%";
    $tipos .= "s";
}

$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limite;
$params[] = $offset;
$tipos .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($tipos, ...$params);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<?php include_once '_header.php'; ?>

<div class="container py-5" style="max-width: 1100px;">

  <h4 class="text-primary fw-semibold mb-0">Produtos e Serviços</h4>

  <hr class="my-4">

  <!-- Menu de abas -->
  <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link <?= ($tipo_aba === 'produto') ? 'active' : '' ?>" href="?tipo=produto">Produtos</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= ($tipo_aba === 'serviço') ? 'active' : '' ?>" href="?tipo=serviço">Serviços</a>
    </li>
  </ul>

  <!-- Campo de busca -->
  <form method="get" class="mb-4">
    <div class="input-group">
      <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_aba) ?>">
      <input type="text" name="busca" class="form-control" placeholder="Buscar por descrição..." value="<?= htmlspecialchars($filtro) ?>">
      <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
    </div>
  </form>

  <!-- Botões Novo Produto / Novo Serviço -->
  <div class="mb-3 text-end">
    <a href="cadastro_produto_servico.php?tipo=Produto" class="btn btn-outline-success btn-sm <?= ($tipo_aba === 'produto') ? '' : 'd-none' ?>">
      <i class="bi bi-plus-circle me-1"></i> Novo Produto
    </a>

    <a href="cadastro_produto_servico.php?tipo=Serviço" class="btn btn-outline-primary btn-sm <?= ($tipo_aba === 'serviço') ? '' : 'd-none' ?>">
      <i class="bi bi-plus-circle me-1"></i> Novo Serviço
    </a>
  </div>

  <!-- Tabela -->
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>Codigo</th>
          <th>Descrição</th>
          <?php if ($tipo_aba === 'produto'): ?>
            <th>Referência</th>
            <th>Marca</th>
            <th>Imagem</th>
            <th>Estoque</th>
          <?php endif; ?>
          <th>Preço</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($resultado->num_rows > 0): ?>
          <?php while ($row = $resultado->fetch_assoc()): ?>
            <tr>
              <td><?= $row['codigo_produto'] ?></td>
              <td><?= htmlspecialchars($row['descricao']) ?></td>
              <?php if ($tipo_aba === 'produto'): ?>
                <td><?= !empty($row['referencia_produto']) ? htmlspecialchars($row['referencia_produto']) : '—' ?></td>
                <td><?= htmlspecialchars($row['marca']) ?></td>
                <td>
                  <?php if (!empty($row['imagem'])): ?>
                    <img src="<?= htmlspecialchars($row['imagem']) ?>" style="max-width: 30px;">
                  <?php else: ?>
                    <span class="text-muted">---</span>
                  <?php endif; ?>
                </td>
                <td><?= $row['estoque'] ?></td>
              <?php endif; ?>
              <td>R$ <?= number_format($row['preco_venda'], 2, ',', '.') ?></td>
              <td>
                <a href="editar_produto_servico.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <a href="excluir_produto_servico.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este item?')" title="Excluir">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="<?= ($tipo_aba === 'produto') ? 7 : 4 ?>" class="text-center text-muted">Nenhum registro encontrado.</td>
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
            <a class="page-link" href="?pagina=<?= $i ?>&tipo=<?= $tipo_aba ?>&busca=<?= urlencode($filtro) ?>"><?= $i ?></a>
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
