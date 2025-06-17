<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Nova O S";

// Geração do número da nova OS (com prefixo OS-)
$result_os = $conn->query("SELECT MAX(CAST(SUBSTRING(numero_os,4) AS UNSIGNED)) AS max_os FROM tb_ordens_servico");
$dados_os = $result_os->fetch_assoc();
$nova_os_num = ($dados_os['max_os'] ?? 0) + 1;
$nova_os = 'OS-' . $nova_os_num;
?>

<?php include_once "_header.php"; ?>

<style>
  .form-label { font-weight: 500; margin-bottom: 2px; font-size: 0.9rem; }
  .form-control, .form-select, textarea { padding: 0.3rem 0.5rem; font-size: 0.85rem; }
  .btn, .form-control-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
  .table th, .table td { font-size: 0.75rem; padding: 0.3rem; vertical-align: middle; }
  #sugestoes-clientes, #sugestoes-produto { max-height: 200px; overflow-y: auto; }
</style>

<div class="container py-3">
  <h5 class="mb-3 text-primary">Nova OS #<?= $nova_os ?></h5>

  <form id="form-os" method="post" action="salvar_ordem_servico.php">
    <input type="hidden" name="numero_os" value="<?= $nova_os ?>">
    <input type="hidden" name="total" id="total_final" value="0">
    <input type="hidden" name="itens" id="itens_json" value="[]">
    
    <div class="row g-2">
      <div class="col-md-4 position-relative">
        <label class="form-label" for="cliente">Cliente</label>
        <input type="text" class="form-control" id="cliente" placeholder="Cliente" autocomplete="off" required>
        <input type="hidden" name="cliente_id" id="cliente_id" required>
        <div id="sugestoes-clientes" class="list-group position-absolute w-100" style="z-index: 10;"></div>
      </div>

      <div class="col-md-4">
        <label class="form-label" for="veiculo">Veículo</label>
        <select class="form-select" name="veiculo_id" id="veiculo" required>
          <option value="">Selecione</option>
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label" for="data_entrada">Entrada</label>
        <input type="date" class="form-control" name="data_entrada" id="data_entrada" required>
      </div>

      <div class="col-md-2">
        <label class="form-label" for="data_saida">Saída</label>
        <input type="date" class="form-control" name="data_saida" id="data_saida">
      </div>

      <div class="col-md-3">
        <label class="form-label" for="status">Status</label>
        <select class="form-select" name="status" id="status" required>
          <option value="">Status</option>
          <option value="Aberto">Aberto</option>
          <option value="Em Execução">Em Execução</option>
          <option value="Finalizado">Finalizado</option>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label" for="tecnico_id">Técnico Responsável</label>
        <select class="form-select" name="tecnico_id" id="tecnico_id" required>
          <option value="">Selecione</option>
          <?php
          $result_tecnicos = $conn->query("SELECT id, nome FROM tb_tecnicos ORDER BY nome ASC");
          while ($tecnico = $result_tecnicos->fetch_assoc()) {
              echo "<option value='{$tecnico['id']}'>" . htmlspecialchars($tecnico['nome']) . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label" for="forma_pagamento">Pagamento</label>
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
        <label class="form-label" for="relato">Relato</label>
        <textarea class="form-control" name="relato_problemas" id="relato" rows="2" required></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="laudo">Laudo</label>
        <textarea class="form-control" name="laudo_servico" id="laudo" rows="2" required></textarea>
      </div>
    </div>

    <hr class="my-3">

    <div class="mb-2">
      <label class="form-label" for="produto">Produto/Serviço</label>
      <div class="input-group position-relative produto-autocomplete">
        <input type="text" class="form-control" id="produto" placeholder="Descrição..." autocomplete="off">
        <button class="btn btn-outline-primary" type="button" onclick="window.location.href='cadastro_produto_servico.php'">Novo</button>
        <div id="sugestoes-produto" class="list-group position-absolute w-100" style="z-index: 1050; display:none;"></div>
      </div>
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

    <div class="text-end mt-3">
      <button type="submit" class="btn btn-success">Salvar OS</button>
    </div>
  </form>
</div>

<script>
  // --- Autocomplete cliente ---
  const inputCliente = document.getElementById('cliente');
  const inputClienteId = document.getElementById('cliente_id');
  const sugestoesClientes = document.getElementById('sugestoes-clientes');
  const selectVeiculo = document.getElementById('veiculo');

  inputCliente.addEventListener('input', function () {
    const termo = this.value.trim();
    inputClienteId.value = '';
    selectVeiculo.innerHTML = '<option value="">Selecione</option>';
    if (termo.length >= 2) {
      fetch(`buscar_clientes_veiculos.php?term=${encodeURIComponent(termo)}`)
        .then(res => res.json())
        .then(data => {
          sugestoesClientes.innerHTML = '';
          data.forEach(cliente => {
            const div = document.createElement('div');
            div.className = 'list-group-item list-group-item-action';
            div.textContent = cliente.nome;
            div.onclick = () => selecionarCliente(cliente);
            sugestoesClientes.appendChild(div);
          });
          sugestoesClientes.style.display = 'block';
        });
    } else {
      sugestoesClientes.style.display = 'none';
    }
  });

  function selecionarCliente(cliente) {
    inputCliente.value = cliente.nome;
    inputClienteId.value = cliente.cliente_id;
    sugestoesClientes.innerHTML = '';
    sugestoesClientes.style.display = 'none';

    fetch(`buscar_veiculos_por_cliente.php?cliente_id=${cliente.cliente_id}`)
      .then(res => res.json())
      .then(data => {
        selectVeiculo.innerHTML = '<option value="">Selecione</option>';
        data.forEach(v => {
          const option = document.createElement('option');
          option.value = v.id;
          option.textContent = `${v.marca} ${v.modelo} ${v.cor} ${v.ano}`;
          selectVeiculo.appendChild(option);
        });
      });
  }

  // --- Autocomplete produto/serviço ---
  const inputProduto = document.getElementById('produto');
  const sugestoesProduto = document.getElementById('sugestoes-produto');
  const tabelaItens = document.querySelector("#tabela-itens tbody");
  let itens = [];

  inputProduto.addEventListener('input', function () {
    const termo = this.value.trim();
    if (termo.length >= 2) {
      fetch(`buscar_produto_servico.php?termo=${encodeURIComponent(termo)}`)
        .then(res => res.json())
        .then(data => {
          sugestoesProduto.innerHTML = '';
          data.forEach(prod => {
            const div = document.createElement('div');
            div.className = 'list-group-item list-group-item-action';
            div.textContent = `${prod.descricao} (${prod.tipo}) - R$ ${parseFloat(prod.preco).toFixed(2)}`;
            div.onclick = () => adicionarItem(prod);
            sugestoesProduto.appendChild(div);
          });
          sugestoesProduto.style.display = 'block';
        });
    } else {
      sugestoesProduto.style.display = 'none';
    }
  });

  document.addEventListener('click', function (e) {
    if (!document.querySelector('.produto-autocomplete').contains(e.target)) {
      sugestoesProduto.style.display = 'none';
    }
  });

  function adicionarItem(item) {
    sugestoesProduto.style.display = 'none';
    inputProduto.value = '';

    // Verificar se já existe o item para incrementar a quantidade
    let existente = itens.find(i => i.descricao === item.descricao && i.tipo === item.tipo);
    if (existente) {
      existente.quantidade++;
    } else {
      itens.push({
        descricao: item.descricao,
        tipo: item.tipo,
        preco: parseFloat(item.preco),
        quantidade: 1,
        total: parseFloat(item.preco)
      });
    }

    renderizarItens();
  }

  function renderizarItens() {
    tabelaItens.innerHTML = '';
    itens.forEach((item, idx) => {
      const totalItem = (item.preco * item.quantidade);
      const tr = document.createElement('tr');

      tr.innerHTML = `
        <td>${item.descricao}</td>
        <td>${item.tipo}</td>
        <td>R$ ${item.preco.toFixed(2).replace('.', ',')}</td>
        <td><input type="number" min="1" class="form-control form-control-sm quantidade" data-index="${idx}" value="${item.quantidade}"></td>
        <td>R$ ${(totalItem).toFixed(2).replace('.', ',')}</td>
        <td><button type="button" class="btn btn-sm btn-danger" data-index="${idx}">Remover</button></td>
      `;

      tabelaItens.appendChild(tr);
    });

    // Atualizar totais
    atualizarTotal();
  }

  tabelaItens.addEventListener('input', function (e) {
    if (e.target.classList.contains('quantidade')) {
      const idx = e.target.getAttribute('data-index');
      let val = parseInt(e.target.value);
      if (isNaN(val) || val < 1) val = 1;
      e.target.value = val;
      itens[idx].quantidade = val;
      itens[idx].total = itens[idx].preco * val;
      renderizarItens();
    }
  });

  tabelaItens.addEventListener('click', function (e) {
    if (e.target.tagName === 'BUTTON') {
      const idx = e.target.getAttribute('data-index');
      itens.splice(idx, 1);
      renderizarItens();
    }
  });

  function atualizarTotal() {
    let totalBruto = itens.reduce((acc, item) => acc + (item.preco * item.quantidade), 0);
    const descontoPercent = parseFloat(document.getElementById('desconto').value) || 0;
    const valorDesconto = totalBruto * (descontoPercent / 100);
    const totalFinal = totalBruto - valorDesconto;

    document.getElementById('valor-bruto').textContent = 'R$ ' + totalBruto.toFixed(2).replace('.', ',');
    document.getElementById('valor-total').textContent = 'R$ ' + totalFinal.toFixed(2).replace('.', ',');

    // Atualizar hidden do total e JSON de itens
    document.getElementById('total_final').value = totalFinal.toFixed(2);
    document.getElementById('itens_json').value = JSON.stringify(itens);
  }

  // Antes de enviar o formulário, verifica se há itens
  document.getElementById('form-os').addEventListener('submit', function (e) {
    if (itens.length === 0) {
      e.preventDefault();
      alert('Adicione pelo menos um produto ou serviço.');
      return false;
    }
  });
</script>

<?php include_once "_footer.php"; ?>
