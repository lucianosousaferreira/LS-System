<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Editar Produto/Serviço";

$mensagem = "";
$tipo_mensagem = "info";

// Buscar o ID
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: listar_produtos_servicos.php");
    exit;
}

// Buscar dados atuais
$sql = "SELECT * FROM tb_produtos_servicos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Produto/Serviço não encontrado.";
    exit;
}

$item = $result->fetch_assoc();

// Atualizar dados se enviado via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tipo = $_POST['tipo'];
    $descricao = $_POST['descricao'];
    $referencia_produto = $_POST['referencia_produto'] ?? null;
    $preco_compra = floatval($_POST['preco_compra']);
    $preco_venda = floatval($_POST['preco_venda']);
    $estoque = intval($_POST['estoque'] ?? 0);
    $marca = $_POST['marca'] ?? null;
    $fornecedor = $_POST['fornecedor'] ?? null;
    $imagem_nome = $item['imagem'];

    if ($tipo === "Produto" && isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $extensao = strtolower(pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extensao, $permitidas)) {
            $novo_nome = uniqid("produto_", true) . "." . $extensao;
            $caminho = "imagens/" . $novo_nome;

            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminho)) {
                $imagem_nome = $novo_nome;
            } else {
                $mensagem = "Erro ao salvar nova imagem.";
                $tipo_mensagem = "danger";
            }
        } else {
            $mensagem = "Formato de imagem inválido.";
            $tipo_mensagem = "danger";
        }
    }

    if (empty($mensagem)) {
        $sql = "UPDATE tb_produtos_servicos 
                SET tipo = ?, descricao = ?, referencia_produto = ?, preco_compra = ?, preco_venda = ?, estoque = ?, imagem = ?, marca = ?, fornecedor = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssddisssi", $tipo, $descricao, $referencia_produto, $preco_compra, $preco_venda, $estoque, $imagem_nome, $marca, $fornecedor, $id);

        if ($stmt->execute()) {
            $mensagem = "Alterações salvas com sucesso!";
            $tipo_mensagem = "success";
            // Atualiza dados exibidos após salvar
            $item = array_merge($item, $_POST);
            $item['imagem'] = $imagem_nome;
        } else {
            $mensagem = "Erro ao salvar alterações.";
            $tipo_mensagem = "danger";
        }
    }
}
?>

<?php include_once '_header.php'; ?>

<div class="container" style="max-width: 600px; margin-top: 20px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="text-primary fw-semibold mb-3">Editar Produto / Serviço</h4>
  <a href="listar_produtos_servicos.php" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-card-list me-1"></i> Listar
    </a>
  </div>

  <?php if (!empty($mensagem)): ?>
    <div class="alert alert-<?= $tipo_mensagem ?> py-2">
      <?= htmlspecialchars($mensagem) ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" novalidate>
    <div class="mb-2">
      <label for="tipo" class="form-label small">Tipo</label>
      <select class="form-select form-select-sm" id="tipo" name="tipo" onchange="toggleCamposProduto()" required>
        <option value="">Selecione</option>
        <option value="Produto" <?= $item['tipo'] == 'Produto' ? 'selected' : '' ?>>Produto</option>
        <option value="Serviço" <?= $item['tipo'] == 'Serviço' ? 'selected' : '' ?>>Serviço</option>
      </select>
    </div>

    <div class="mb-2">
      <label for="descricao" class="form-label small">Descrição</label>
      <input type="text" class="form-control form-control-sm" name="descricao" value="<?= htmlspecialchars($item['descricao']) ?>" required>
    </div>

    <div class="mb-2" id="referencia-campo">
      <label for="referencia_produto" class="form-label small">Referência</label>
      <input type="text" class="form-control form-control-sm" name="referencia_produto" value="<?= htmlspecialchars($item['referencia_produto']) ?>">
    </div>

    <div class="row g-2 mb-2">
      <div class="col-6">
        <label for="preco_compra" class="form-label small">Preço Compra</label>
        <input type="number" step="0.01" class="form-control form-control-sm" name="preco_compra" value="<?= $item['preco_compra'] ?>" required>
      </div>
      <div class="col-6">
        <label for="preco_venda" class="form-label small">Preço Venda</label>
        <input type="number" step="0.01" class="form-control form-control-sm" name="preco_venda" value="<?= $item['preco_venda'] ?>" required>
      </div>
    </div>

    <div class="mb-2" id="estoque-campo">
      <label for="estoque" class="form-label small">Estoque</label>
      <input type="number" class="form-control form-control-sm" name="estoque" value="<?= htmlspecialchars($item['estoque'] ?? 0) ?>">
    </div>

    <div class="mb-2" id="marca-campo">
      <label for="marca" class="form-label small">Marca</label>
      <input type="text" class="form-control form-control-sm" name="marca" id="marca" value="<?= htmlspecialchars($item['marca'] ?? '') ?>" autocomplete="off" list="lista-marcas" onchange="verificaMarca(this.value)">
      <datalist id="lista-marcas">
        <?php
        $res = $conn->query("SELECT nome FROM tb_marca ORDER BY nome");
        while ($m = $res->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($m['nome']) . "'>";
        }
        ?>
      </datalist>
    </div>

    <div class="mb-2" id="fornecedor-campo">
      <label for="fornecedor" class="form-label small">Fornecedor</label>
      <input type="text" class="form-control form-control-sm" name="fornecedor" id="fornecedor" value="<?= htmlspecialchars($item['fornecedor'] ?? '') ?>" autocomplete="off" list="lista-fornecedores" onchange="verificaFornecedor(this.value)">
      <datalist id="lista-fornecedores">
        <?php
        $resf = $conn->query("SELECT nome FROM tb_fornecedores ORDER BY nome");
        while ($f = $resf->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($f['nome']) . "'>";
        }
        ?>
      </datalist>
    </div>

    <div class="mb-3" id="imagem-campo">
      <label for="imagem" class="form-label small">Imagem</label>
      <?php if (!empty($item['imagem'])): ?>
        <div class="mb-1">
          <img src="imagens/<?= htmlspecialchars($item['imagem']) ?>" style="max-width: 100px;">
        </div>
      <?php endif; ?>
      <input type="file" class="form-control form-control-sm" name="imagem" accept="image/*">
    </div>

    <div class="d-flex justify-content-end">
      <button type="submit" class="btn btn-sm btn-primary">Salvar Alterações</button>
    </div>
  </form>
</div>

<script>
const idProduto = <?= json_encode($id) ?>;

function toggleCamposProduto() {
  const tipo = document.getElementById('tipo').value;
  document.getElementById('referencia-campo').style.display = tipo === 'Produto' ? 'block' : 'none';
  document.getElementById('imagem-campo').style.display = tipo === 'Produto' ? 'block' : 'none';
  document.getElementById('marca-campo').style.display = tipo === 'Produto' ? 'block' : 'none';
  document.getElementById('fornecedor-campo').style.display = tipo === 'Produto' ? 'block' : 'none';
  document.getElementById('estoque-campo').style.display = tipo === 'Produto' ? 'block' : 'none';
}

function verificaMarca(valor) {
  fetch('verifica_marca.php?marca=' + encodeURIComponent(valor))
    .then(res => res.text())
    .then(resp => {
      if (resp === '0') {
        if (confirm('Marca não encontrada. Deseja cadastrar agora?')) {
          const retorno = 'editar_produto.php?id=' + idProduto;
          window.location.href = 'cadastro_marca.php?retorno=' + encodeURIComponent(retorno);
        }
      }
    });
}

function verificaFornecedor(valor) {
  fetch('verifica_fornecedor.php?fornecedor=' + encodeURIComponent(valor))
    .then(res => res.text())
    .then(resp => {
      if (resp === '0') {
        if (confirm('Fornecedor não encontrado. Deseja cadastrar agora?')) {
          const retorno = 'editar_produto.php?id=' + idProduto;
          window.location.href = 'cadastro_fornecedor.php?retorno=' + encodeURIComponent(retorno);
        }
      }
    });
}

document.addEventListener("DOMContentLoaded", toggleCamposProduto);
</script>

<?php include_once '_footer.php'; $conn->close(); ?>
