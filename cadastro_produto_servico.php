<?php

require 'vendor/autoload.php'; // Cloudinary via Composer
include_once 'conexao.php';
include_once 'verifica_login.php';

$titulo_pagina = "Cadastrar Produto ou Serviço";
$mensagem = "";
$tipo_mensagem = "info";
$tipo_selecionado = $_GET['tipo'] ?? '';

// Cloudinary config
\Cloudinary\Configuration\Configuration::instance([
    'cloud' => [
        'cloud_name' => 'duzn9flso',
        'api_key'    => '767215351625894',
        'api_secret' => 'xAY1QegdsibW3uHd-dLkB6KvsPU'
    ],
    'url' => ['secure' => true]
]);

use Cloudinary\Api\Upload\UploadApi;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo_produto'] ?? null;
    $tipo = $_POST['tipo'];
    $descricao = $_POST['descricao'];
    $referencia_produto = $_POST['referencia_produto'] ?? null;
    $marca = $_POST['marca'] ?? null;
    $fornecedor = $_POST['fornecedor'] ?? null;
    $preco_compra = floatval($_POST['preco_compra']);
    $preco_venda = floatval($_POST['preco_venda']);
    $estoque = ($tipo === "Produto") ? intval($_POST['estoque'] ?? 0) : null;
    $imagem_nome = null;

    if ($tipo === "Produto" && isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $nome_arquivo = $_FILES["imagem"]["name"];
        $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));

        if (in_array($extensao, $extensoes_permitidas)) {
            try {
                $upload = (new UploadApi())->upload($_FILES["imagem"]["tmp_name"], [
                    'folder' => 'produtos',
                    'public_id' => uniqid("produto_"),
                    'overwrite' => true,
                    'resource_type' => 'image'
                ]);
                $imagem_nome = $upload['secure_url'];
            } catch (Exception $e) {
                $mensagem = "Erro ao fazer upload para o Cloudinary: " . $e->getMessage();
                $tipo_mensagem = "danger";
            }
        } else {
            $mensagem = "Formato de imagem inválido.";
            $tipo_mensagem = "danger";
        }
    }

    if (empty($mensagem)) {
        $sql = "INSERT INTO tb_produtos_servicos 
            (codigo_produto, tipo, descricao, referencia_produto, marca, fornecedor, preco, preco_compra, preco_venda, estoque, imagem)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssddis", $codigo, $tipo, $descricao, $referencia_produto, $marca, $fornecedor, $preco, $preco_compra, $preco_venda, $estoque, $imagem_nome);

        if ($stmt->execute()) {
            $mensagem = "Cadastro realizado com sucesso!";
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
    <h4 class="text-primary fw-semibold mb-0">Cadastro Produto / Serviço</h4>
    <a href="listar_produtos_servicos.php" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-card-list me-1"></i> Listar
    </a>
  </div>

  <?php if (!empty($mensagem)): ?>
    <div class="alert alert-<?= $tipo_mensagem ?> py-2" role="alert" style="font-size: 0.9rem;">
      <?= htmlspecialchars($mensagem) ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="codigo_produto" id="codigo_produto" />

    <div class="mb-2">
      <label for="tipo" class="form-label small">Tipo</label>
      <select class="form-select form-select-sm" id="tipo" name="tipo" required onchange="toggleCamposProduto()">
        <option value="">Selecione</option>
        <option value="Produto" <?= $tipo_selecionado === 'Produto' ? 'selected' : '' ?>>Produto</option>
        <option value="Serviço" <?= $tipo_selecionado === 'Serviço' ? 'selected' : '' ?>>Serviço</option>
      </select>
    </div>

    <div class="mb-2" id="codigo-campo" style="display: none;">
      <label class="form-label small">Código Gerado</label>
      <input type="text" class="form-control form-control-sm" id="codigo_gerado" disabled />
    </div>

    <div class="mb-2">
      <label for="descricao" class="form-label small">Descrição</label>
      <input type="text" class="form-control form-control-sm" id="descricao" name="descricao" required />
    </div>

    <div class="mb-2" id="referencia-campo" style="display: none;">
      <label for="referencia_produto" class="form-label small">Referência</label>
      <input type="text" class="form-control form-control-sm" id="referencia_produto" name="referencia_produto" />
    </div>

    <div class="mb-2" id="marca-campo" style="display: none;">
      <label for="marca" class="form-label small">Marca</label>
      <input type="text" class="form-control form-control-sm" id="marca" name="marca" />
    </div>

    <div class="mb-2" id="fornecedor-campo" style="display: none;">
      <label for="fornecedor" class="form-label small">Fornecedor</label>
      <input type="text" class="form-control form-control-sm" id="fornecedor" name="fornecedor" />
    </div>

    <div class="row g-2 mb-2">
      <div class="col-6">
        <label for="preco_compra" class="form-label small">Preço Compra (R$)</label>
        <input type="number" step="0.01" class="form-control form-control-sm" id="preco_compra" name="preco_compra" required />
      </div>
      <div class="col-6">
        <label for="preco_venda" class="form-label small">Preço Venda (R$)</label>
        <input type="number" step="0.01" class="form-control form-control-sm" id="preco_venda" name="preco_venda" required />
      </div>
    </div>

    <div class="mb-2" id="estoque-campo" style="display: none;">
      <label for="estoque" class="form-label small">Estoque Inicial</label>
      <input type="number" class="form-control form-control-sm" id="estoque" name="estoque" min="0" />
    </div>

    <div class="mb-3" id="imagem-campo" style="display: none;">
      <label for="imagem" class="form-label small">Imagem</label>
      <input type="file" class="form-control form-control-sm" id="imagem" name="imagem" accept="image/*" />
    </div>

    <div class="d-flex justify-content-end">
      <button type="submit" class="btn btn-sm btn-primary px-4">Salvar</button>
    </div>
  </form>
</div>

<!-- Scripts -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
function toggleCamposProduto() {
  const tipo = document.getElementById('tipo').value;
  const isProduto = tipo === 'Produto';
  document.getElementById('imagem-campo').style.display = isProduto ? 'block' : 'none';
  document.getElementById('referencia-campo').style.display = isProduto ? 'block' : 'none';
  document.getElementById('marca-campo').style.display = isProduto ? 'block' : 'none';
  document.getElementById('fornecedor-campo').style.display = isProduto ? 'block' : 'none';
  document.getElementById('estoque-campo').style.display = isProduto ? 'block' : 'none';
  document.getElementById('codigo-campo').style.display = isProduto ? 'block' : 'none';
}

function gerarCodigoAleatorio() {
  const codigo = Math.floor(100000 + Math.random() * 900000);
  document.getElementById('codigo_produto').value = codigo;
  document.getElementById('codigo_gerado').value = codigo;
}

$(function () {
  gerarCodigoAleatorio();
  toggleCamposProduto();

  let marcas = [];
  let fornecedores = [];

  $("#marca").autocomplete({
    source: function (req, res) {
      $.getJSON("buscar_marcas.php", { term: req.term }, function (data) {
        marcas = data;
        res(data);
      });
    }
  }).blur(function () {
    const val = $(this).val();
    if (val && !marcas.includes(val)) {
      if (confirm("Marca não encontrada. Deseja cadastrá-la?")) {
        window.open("cadastro_marca.php?nome=" + encodeURIComponent(val), "_blank");
      }
    }
  });

  $("#fornecedor").autocomplete({
    source: function (req, res) {
      $.getJSON("buscar_fornecedores.php", { term: req.term }, function (data) {
        fornecedores = data;
        res(data);
      });
    }
  }).blur(function () {
    const val = $(this).val();
    if (val && !fornecedores.includes(val)) {
      if (confirm("Fornecedor não encontrado. Deseja cadastrá-lo?")) {
        window.open("cadastro_fornecedor.php?nome=" + encodeURIComponent(val), "_blank");
      }
    }
  });
});
</script>

<?php include_once '_footer.php'; ?>
