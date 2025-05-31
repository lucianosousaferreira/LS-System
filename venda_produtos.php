<?php 
include_once 'verifica_login.php'; 
include_once 'conexao.php'; 
include_once '_header.php'; 
?>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<style>
  html, body {
    height: 100%;
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #f8f9fa;
  }
  .container {
    height: 100vh;
    max-width: 1000px;
    margin: auto;
    display: flex;
    flex-direction: column;
    padding: 20px;
    box-sizing: border-box;
  }
  .minimal-input {
    border: none;
    border-bottom: 1px solid #ccc;
    border-radius: 0;
    background: transparent;
  }
  .minimal-input:focus {
    border-color: #007bff;
    outline: none;
    background: transparent;
  }
  .cupom {
    background: #fff;
    font-family: monospace;
    font-size: 14px;
    padding: 10px;
    height: 200px;
    overflow-y: auto;
    border: 1px dashed #ccc;
  }
  .imagem-produto {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 1px dashed #ccc;
  }
</style>

<div class="container">
  <p>Número da Venda: <span id="numero_venda_exibir" style="font-weight:bold;"></span></p>

  <form id="form-venda" method="POST" action="salvar_venda.php">
    <input type="hidden" name="numero_venda" id="numero_venda">

    <div class="mb-2">
      <input type="text" id="cliente" class="form-control minimal-input" placeholder="Cliente" autocomplete="off" required>
      <input type="hidden" id="cliente_id" name="cliente_id" required>
    </div>

    <div class="d-flex gap-2 mb-2">
      <input type="text" id="produto" class="form-control minimal-input" placeholder="Produto ou código" autocomplete="off">
      <input type="hidden" id="produto_id">
      <input type="number" id="quantidade" class="form-control minimal-input" value="1" min="1" style="max-width: 70px;">
      <button type="button" class="btn btn-dark" id="btn-adicionar">+</button>
    </div>

    <div class="d-flex gap-2 mb-3">
      <input type="number" id="desconto" name="desconto" class="form-control minimal-input" placeholder="Desconto %" value="0" min="0" max="100" step="0.1">
      <input type="text" id="total_com_desconto" class="form-control minimal-input" readonly placeholder="Total final">
    </div>

    <input type="hidden" name="forma_pagamento" id="forma_pagamento_hidden" required>
    <input type="hidden" name="itens" id="itens" required>
  </form>

  <div class="d-flex gap-3">
    <div style="flex: 1;">
      <div class="imagem-produto" id="imagem-produto">
        <small class="text-muted">Imagem do produto</small>
      </div>
    </div>
    <div style="flex: 1;">
      <div class="cupom" id="cupom">
        <p class="text-muted">Cupom vazio</p>
      </div>
      <div class="mb-2 mt-2">
        <select id="forma_pagamento" class="form-control minimal-input" required>
          <option value="">Forma de pagamento</option>
          <option value="Dinheiro">Dinheiro</option>
          <option value="Cartão de Crédito">Cartão de Crédito</option>
          <option value="Cartão de Débito">Cartão de Débito</option>
          <option value="PIX">PIX</option>
          <option value="Outros">Outros</option>
        </select>
      </div>
      <button type="button" class="btn btn-outline-primary w-100 mt-2" id="btn-imprimir">Finalizar e Imprimir</button>
    </div>
  </div>
</div>

<script>
  const produtos = {};
  let itensVenda = [];

  function formatar(valor) {
    return 'R$ ' + valor.toFixed(2).replace('.', ',');
  }

  function atualizarTotal() {
    let total = itensVenda.reduce((acc, item) => acc + item.subtotal, 0);
    const desconto = parseFloat(document.getElementById('desconto').value) || 0;
    const totalDesc = total - (total * desconto / 100);
    document.getElementById('total_com_desconto').value = formatar(totalDesc);
  }

  function gerarNumeroVendaAleatorio() {
    return 'V' + Math.floor(100000 + Math.random() * 900000);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const numVenda = gerarNumeroVendaAleatorio();
    document.getElementById('numero_venda').value = numVenda;
    document.getElementById('numero_venda_exibir').textContent = numVenda;
  });

  $("#cliente").autocomplete({
    source: "buscar_cliente_autocomplete.php",
    minLength: 2,
    select: function(_, ui) {
      $("#cliente").val(ui.item.label);
      $("#cliente_id").val(ui.item.id);
      return false;
    }
  });

  $("#produto").autocomplete({
    source: "buscar_produto_autocomplete.php",
    minLength: 2,
    select: function(_, ui) {
      $("#produto").val(ui.item.label);
      $("#produto_id").val(ui.item.id);
      produtos[ui.item.label] = {
        id: ui.item.id,
        descricao: ui.item.label,
        preco_venda: parseFloat(ui.item.preco_venda),
        imagem: ui.item.imagem
      };
      $("#imagem-produto").html(ui.item.imagem
        ? `<img src="imagens/${ui.item.imagem}" class="img-fluid h-100" style="object-fit:contain;">`
        : `<small class="text-muted">Sem imagem</small>`);
      return false;
    }
  });

  document.getElementById('btn-adicionar').addEventListener('click', () => {
    const nome = document.getElementById('produto').value.trim();
    const quantidade = parseInt(document.getElementById('quantidade').value);
    const produto = produtos[nome];

    if (!produto) {
      alert('Selecione um produto válido.');
      return;
    }
    if (quantidade < 1) {
      alert('Quantidade inválida.');
      return;
    }

    const subtotal = +(produto.preco_venda * quantidade).toFixed(2);
    itensVenda.push({ 
      produto_id: produto.id,
      descricao: produto.descricao, 
      quantidade, 
      preco: produto.preco_venda, 
      subtotal 
    });

    let html = '<table style="width:100%; font-family: monospace;">';
    itensVenda.forEach(i => {
      html += `<tr>
        <td>${i.descricao}</td>
        <td>x${i.quantidade}</td>
        <td style="text-align:right;">${formatar(i.subtotal)}</td>
      </tr>`;
    });
    html += '</table>';
    document.getElementById('cupom').innerHTML = html;

    atualizarTotal();

    document.getElementById('produto').value = '';
    document.getElementById('produto_id').value = '';
    document.getElementById('quantidade').value = 1;
    $("#imagem-produto").html('<small class="text-muted">Imagem do produto</small>');
  });

  document.getElementById('desconto').addEventListener('input', atualizarTotal);

  document.getElementById('btn-imprimir').addEventListener('click', () => {
    const cliente_id = document.getElementById('cliente_id').value;
    const forma_pagamento = document.getElementById('forma_pagamento').value;

    if (!cliente_id) {
      alert('Selecione o cliente.');
      return;
    }
    if (itensVenda.length === 0) {
      alert('Adicione ao menos um item.');
      return;
    }
    if (!forma_pagamento) {
      alert('Selecione a forma de pagamento.');
      return;
    }

    document.getElementById('forma_pagamento_hidden').value = forma_pagamento;
    document.getElementById('itens').value = JSON.stringify(itensVenda);

    let total = itensVenda.reduce((acc, item) => acc + item.subtotal, 0);
    const desconto = parseFloat(document.getElementById('desconto').value) || 0;
    const totalDesc = total - (total * desconto / 100);

    let form = document.getElementById('form-venda');
    let inputTotal = document.getElementById('total_input');
    if (!inputTotal) {
      inputTotal = document.createElement('input');
      inputTotal.type = 'hidden';
      inputTotal.name = 'total';
      inputTotal.id = 'total_input';
      form.appendChild(inputTotal);
    }
    inputTotal.value = totalDesc.toFixed(2);

    form.submit();
  });
</script>

<?php include_once '_footer.php'; ?>
