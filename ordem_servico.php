<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Nova O S";


// Geração do número da nova OS
$result_os = $conn->query("SELECT MAX(numero_os) AS max_os FROM tb_ordens_servico");
$dados_os = $result_os->fetch_assoc();
$nova_os = ($dados_os['max_os'] ?? 0) + 1;
?>

<?php include_once "_header.php"; ?>

<style>
  .form-label { font-weight: 500; margin-bottom: 2px; font-size: 0.9rem; }
  .form-control, .form-select, textarea { padding: 0.3rem 0.5rem; font-size: 0.85rem; }
  .btn, .form-control-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
  .table th, .table td { font-size: 0.75rem; padding: 0.3rem; vertical-align: middle; }
</style>

<div class="container py-3">
  <h5 class="mb-3 text-primary">Nova OS #<?= $nova_os ?></h5>

  <form method="post" action="salvar_ordem_servico.php">
    <input type="hidden" name="numero_os" value="<?= $nova_os ?>">

    <div class="row g-2">
      <div class="col-md-4 position-relative">
        <label class="form-label">Cliente</label>
        <input type="text" class="form-control" id="cliente" placeholder="Cliente" autocomplete="off" required>
        <input type="hidden" name="cliente_id" id="cliente_id">
        <div id="sugestoes-clientes" class="list-group position-absolute w-100" style="z-index: 10;"></div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Veículo</label>
        <select class="form-select" name="veiculo_id" id="veiculo" required>
          <option value="">Selecione</option>
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">Entrada</label>
        <input type="date" class="form-control" name="data_entrada" id="data_entrada" required>
      </div>

      <div class="col-md-2">
        <label class="form-label">Saída</label>
        <input type="date" class="form-control" name="data_saida" id="data_saida">
      </div>

      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select class="form-select" name="status" id="status" required>
          <option value="">Status</option>
          <option value="Aberto">Aberto</option>
          <option value="Em Execução">Em Execução</option>
          <option value="Finalizado">Finalizado</option>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Técnico Responsável</label>
        <select class="form-select" name="tecnico_id" id="tecnico_id" required>
          <option value="">Selecione</option>
          <?php
          $result_tecnicos = $conn->query("SELECT id, nome FROM tb_tecnicos ORDER BY nome ASC");
          while ($tecnico = $result_tecnicos->fetch_assoc()) {
              echo "<option value='{$tecnico['id']}'>{$tecnico['nome']}</option>";
          }
          ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Pagamento</label>
        <select class="form-select" name="forma_pagamento" id="forma_pagamento" required>
          <option value="">Forma</option>
          <option value="À vista">À vista</option>
          <option value="Pix">Pix</option>
          <option value="Cartão de Crédito">Crédito</option>
          <option value="Cartão de Débito">Débito</option>
          <option value="Boleto">Boleto</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Relato</label>
        <textarea class="form-control" name="relato_problemas" id="relato" rows="2" required></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label">Laudo</label>
        <textarea class="form-control" name="laudo_servico" id="laudo" rows="2" required></textarea>
      </div>
    </div>

    <hr class="my-3">

    <div class="mb-2">
      <label class="form-label">Produto/Serviço</label>
      <div class="input-group position-relative produto-autocomplete">
        <input type="text" class="form-control" id="produto" placeholder="Descrição..." autocomplete="off">
        <button class="btn btn-outline-primary" type="button" onclick="window.location.href='cadastro_produto_servico.php'">Novo</button>
        <div id="sugestoes-produto" class="list-group position-absolute w-100" style="z-index: 1050;"></div>
      </div>
      <input type="hidden" id="produto_id">
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-sm" id="tabela-itens">
        <thead class="table-light">
          <tr>
            <th>Descrição</th>
            <th>Tipo</th>
            <th>Preço</th>
            <th>Qtd</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
          <tr>
            <td colspan="4" class="text-end">Total Bruto:</td>
            <td colspan="2"><strong id="valor-bruto">R$ 0,00</strong></td>
          </tr>
          <tr>
            <td colspan="4" class="text-end">Desconto (%)</td>
            <td colspan="2">
              <input type="number" name="desconto" id="desconto" class="form-control form-control-sm" min="0" max="100" value="0" onchange="atualizarTotal()">
            </td>
          </tr>
          <tr>
            <td colspan="4" class="text-end">Total Final:</td>
            <td colspan="2"><strong id="valor-total">R$ 0,00</strong></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="text-end">
      <button type="submit" class="btn btn-success">Salvar OS</button>
    </div>
  </form>
</div>

<script>
// autocomplete cliente
document.getElementById('cliente').addEventListener('input', function () {
  const termo = this.value.trim();
  if (termo.length >= 2) {
    fetch(`buscar_clientes_veiculos.php?term=${encodeURIComponent(termo)}`)
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById('sugestoes-clientes');
        container.innerHTML = '';
        data.forEach(cliente => {
          const div = document.createElement('div');
          div.className = 'list-group-item list-group-item-action';
          div.textContent = cliente.nome;
          div.onclick = () => selecionarCliente(cliente);
          container.appendChild(div);
        });
      });
  }
});

function selecionarCliente(cliente) {
  document.getElementById('cliente').value = cliente.nome;
  document.getElementById('cliente_id').value = cliente.cliente_id;
  document.getElementById('sugestoes-clientes').innerHTML = '';
  fetch(`buscar_veiculos_por_cliente.php?cliente_id=${cliente.cliente_id}`)
    .then(res => res.json())
    .then(data => {
      const veiculoSelect = document.getElementById('veiculo');
      veiculoSelect.innerHTML = '<option value="">Selecione</option>';
      data.forEach(v => {
        const option = document.createElement('option');
        option.value = v.id;
        option.textContent = `${v.marca} ${v.modelo} ${v.cor} ${v.ano}`;
        veiculoSelect.appendChild(option);
      });
    });
}

// autocomplete produto/serviço
document.getElementById('produto').addEventListener('input', function () {
  const termo = this.value.trim();
  if (termo.length >= 2) {
    fetch(`buscar_produto_servico.php?termo=${encodeURIComponent(termo)}`)
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById('sugestoes-produto');
        container.innerHTML = '';
        data.forEach(prod => {
          const div = document.createElement('div');
          div.className = 'list-group-item list-group-item-action';
          div.textContent = `${prod.descricao} (${prod.tipo}) - R$ ${parseFloat(prod.preco).toFixed(2)}`;
          div.onclick = () => adicionarItem(prod);
          container.appendChild(div);
        });
        container.style.display = 'block';
      });
  } else {
    document.getElementById('sugestoes-produto').style.display = 'none';
  }
});

document.addEventListener('click', function (e) {
  if (!document.querySelector('.produto-autocomplete').contains(e.target)) {
    document.getElementById('sugestoes-produto').style.display = 'none';
  }
});

function adicionarItem(item) {
  document.getElementById('produto').value = '';
  document.getElementById('sugestoes-produto').innerHTML = '';
  document.getElementById('sugestoes-produto').style.display = 'none';

  const tbody = document.querySelector("#tabela-itens tbody");
  const linha = document.createElement("tr");

  linha.innerHTML = `
    <td><input type="hidden" name="descricao[]" value="${item.descricao}">${item.descricao}</td>
    <td><input type="hidden" name="tipo[]" value="${item.tipo}">${item.tipo}</td>
    <td>R$ ${parseFloat(item.preco).toFixed(2)}<input type="hidden" name="preco[]" value="${item.preco}"></td>
    <td><input type="number" name="quantidade[]" value="1" min="1" class="form-control form-control-sm quantidade" onchange="atualizarTotal()"></td>
    <td class="total-item">R$ ${parseFloat(item.preco).toFixed(2)}</td>
    <td><button type="button" class="btn btn-sm btn-danger" onclick="removerItem(this)">Remover</button></td>
  `;
  tbody.appendChild(linha);
  atualizarTotal();
}

function removerItem(botao) {
  botao.closest("tr").remove();
  atualizarTotal();
}

function atualizarTotal() {
  let totalBruto = 0;
  const linhas = document.querySelectorAll("#tabela-itens tbody tr");
  linhas.forEach(linha => {
    const preco = parseFloat(linha.querySelector('input[name="preco[]"]').value);
    const qtd = parseInt(linha.querySelector('input[name="quantidade[]"]').value);
    const total = preco * qtd;
    linha.querySelector('.total-item').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
    totalBruto += total;
  });

  const desconto = parseFloat(document.getElementById('desconto').value) || 0;
  const valorDesconto = totalBruto * (desconto / 100);
  const totalFinal = totalBruto - valorDesconto;

  document.getElementById('valor-bruto').textContent = 'R$ ' + totalBruto.toFixed(2).replace('.', ',');
  document.getElementById('valor-total').textContent = 'R$ ' + totalFinal.toFixed(2).replace('.', ',');
}
</script>

<?php include_once "_footer.php"; ?>

