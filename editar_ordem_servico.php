<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Editar O.S";

$id_os = $_GET['id'] ?? null;
if (!$id_os) {
    header("Location: lista_ordens_servico.php");
    exit;
}

// Consulta OS + veículo
$sql_os = "SELECT 
              os.*, 
              v.marca, 
              v.modelo, 
              v.cor, 
              v.ano, 
              v.placa,
              c.nome AS cliente_nome
           FROM tb_ordens_servico AS os
           JOIN tb_veiculos AS v ON os.veiculo_id = v.id
           JOIN tb_clientes AS c ON os.cliente_id = c.id
           WHERE os.id = ?";
$stmt = $conn->prepare($sql_os);
$stmt->bind_param("i", $id_os);
$stmt->execute();
$result = $stmt->get_result();
$ordem = $result->fetch_assoc();

$sql_itens = "SELECT * FROM tb_itens_os WHERE ordem_servico_id = ?";
$stmt_itens = $conn->prepare($sql_itens);
$stmt_itens->bind_param("i", $id_os);
$stmt_itens->execute();
$itens = $stmt_itens->get_result()->fetch_all(MYSQLI_ASSOC);

$result_tecnicos = $conn->query("SELECT id, nome FROM tb_tecnicos ORDER BY nome");
?>

<?php include_once '_header.php'; ?>

<style>
  .form-label { font-weight: 500; margin-bottom: 2px; font-size: 0.9rem; }
  .form-control, .form-select, textarea { padding: 0.3rem 0.5rem; font-size: 0.85rem; }
  .btn, .form-control-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
  .table th, .table td { font-size: 0.75rem; padding: 0.3rem; vertical-align: middle; }
</style>

<div class="container py-3">
  <h5 class="mb-3 text-primary">Editar OS #<?= htmlspecialchars($ordem['numero_os']) ?></h5>

  <form method="post" action="atualizar_ordem_servico.php">
    <input type="hidden" name="id_os" value="<?= $ordem['id'] ?>">

    <div class="row g-2">
      <div class="col-md-4">
        <label class="form-label">Cliente</label>
        <input type="text" class="form-control" name="cliente_nome" value="<?= htmlspecialchars($ordem['cliente_nome']) ?>" readonly style="background-color: #e9ecef; color: #6c757d; cursor: not-allowed; padding-right: 30px;">
      </div>

      <div class="col-md-4">
        <label class="form-label">Veículo</label>
        <input type="text" class="form-control" name="veiculo_nome" value="<?= htmlspecialchars($ordem['marca'].' '.$ordem['modelo'].' '.$ordem['cor'].' '.$ordem['ano'].' - '.$ordem['placa']) ?>" readonly style="background-color: #e9ecef; color: #6c757d; cursor: not-allowed; padding-right: 30px;">
      </div>

      <div class="col-md-2">
        <label class="form-label">Entrada</label>
        <input type="date" name="data_entrada" class="form-control" value="<?= $ordem['data_entrada'] ?>" required>
      </div>

      <div class="col-md-2">
        <label class="form-label">Saída</label>
        <input type="date" name="data_saida" class="form-control" value="<?= $ordem['data_saida'] ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
          <option value="Aberto" <?= $ordem['status'] == 'Aberto' ? 'selected' : '' ?>>Aberto</option>
          <option value="Em Execução" <?= $ordem['status'] == 'Em Execução' ? 'selected' : '' ?>>Em Execução</option>
          <option value="Finalizado" <?= $ordem['status'] == 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Técnico</label>
        <select name="tecnico_id" class="form-select" required>
          <option value="">Selecione</option>
          <?php while ($t = $result_tecnicos->fetch_assoc()): ?>
            <option value="<?= $t['id'] ?>" <?= $t['id'] == $ordem['tecnico_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($t['nome']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Pagamento</label>
        <select class="form-select" name="forma_pagamento" required>
          <option value="">Selecione</option>
          <option value="À vista" <?= $ordem['forma_pagamento'] == 'À vista' ? 'selected' : '' ?>>À vista</option>
          <option value="Pix" <?= $ordem['forma_pagamento'] == 'Pix' ? 'selected' : '' ?>>Pix</option>
          <option value="Cartão de Crédito" <?= $ordem['forma_pagamento'] == 'Cartão de Crédito' ? 'selected' : '' ?>>Crédito</option>
          <option value="Cartão de Débito" <?= $ordem['forma_pagamento'] == 'Cartão de Débito' ? 'selected' : '' ?>>Débito</option>
          <option value="Boleto" <?= $ordem['forma_pagamento'] == 'Boleto' ? 'selected' : '' ?>>Boleto</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Relato</label>
        <textarea name="relato_problemas" rows="2" class="form-control"><?= htmlspecialchars($ordem['relato_problemas']) ?></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label">Laudo</label>
        <textarea name="laudo_servico" rows="2" class="form-control"><?= htmlspecialchars($ordem['laudo_servico']) ?></textarea>
      </div>
    </div>

    <div class="my-3">
      <label class="form-label">Produto/Serviço</label>
      <div class="input-group position-relative produto-autocomplete">
        <input type="text" class="form-control" id="produto" placeholder="Descrição..." autocomplete="off">
        <button class="btn btn-outline-primary" type="button" onclick="window.location.href='cadastro_produto_servico.php'">Novo</button>
        <div id="sugestoes-produto" class="list-group position-absolute w-100" style="z-index:1050;"></div>
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
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($itens as $item): ?>
          <tr>
            <td><input type="hidden" name="descricao[]" value="<?= htmlspecialchars($item['descricao']) ?>"><?= htmlspecialchars($item['descricao']) ?></td>
            <td><input type="hidden" name="tipo[]" value="<?= htmlspecialchars($item['tipo']) ?>"><?= htmlspecialchars($item['tipo']) ?></td>
            <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?><input type="hidden" name="preco[]" value="<?= $item['preco'] ?>"></td>
            <td><input type="number" name="quantidade[]" value="<?= $item['quantidade'] ?>" min="1" class="form-control form-control-sm" onchange="atualizarTotal()"></td>
            <td class="total-item">R$ <?= number_format($item['total'], 2, ',', '.') ?></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removerItem(this)">X</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4" class="text-end">Total Bruto:</td>
            <td colspan="2"><strong id="valor-bruto">R$ 0,00</strong></td>
          </tr>
          <tr>
            <td colspan="4" class="text-end">Desconto (%)</td>
            <td colspan="2"><input type="number" name="desconto" id="desconto_porcentagem" class="form-control form-control-sm" min="0" max="100" value="<?= $ordem['desconto'] ?>" onchange="atualizarTotal()"></td>
          </tr>
          <tr>
            <td colspan="4" class="text-end">Total Final:</td>
            <td colspan="2"><strong id="valor-total">R$ 0,00</strong></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-success">Salvar Alterações</button>
      <a href="lista_ordens_servico.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function atualizarTotal() {
  let totalBruto = 0;
  const linhas = document.querySelectorAll("#tabela-itens tbody tr");

  linhas.forEach(linha => {
    const preco = parseFloat(linha.querySelector('input[name="preco[]"]').value) || 0;
    const qtd = parseInt(linha.querySelector('input[name="quantidade[]"]').value) || 0;
    const subtotal = preco * qtd;
    linha.querySelector(".total-item").innerText = 'R$ ' + subtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
    totalBruto += subtotal;
  });

  const descontoPerc = parseFloat(document.getElementById('desconto_porcentagem').value) || 0;
  const descontoValor = totalBruto * (descontoPerc / 100);
  const totalFinal = totalBruto - descontoValor;

  document.getElementById('valor-bruto').innerText = 'R$ ' + totalBruto.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
  document.getElementById('valor-total').innerText = 'R$ ' + totalFinal.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
}

function removerItem(botao) {
  botao.closest("tr").remove();
  atualizarTotal();
}

document.addEventListener('DOMContentLoaded', atualizarTotal);

// Autocomplete
document.getElementById('produto').addEventListener('input', function () {
  const termo = this.value.trim();
  const sugestoes = document.getElementById('sugestoes-produto');
  if (termo.length >= 2) {
    fetch(`buscar_produto_servico.php?termo=${encodeURIComponent(termo)}`)
      .then(res => res.json())
      .then(data => {
        sugestoes.innerHTML = '';
        data.forEach(item => {
          const div = document.createElement('div');
          div.className = 'list-group-item list-group-item-action';
          div.textContent = `${item.descricao} - R$ ${parseFloat(item.preco).toFixed(2)}`;
          div.onclick = function () {
            const novaLinha = `
              <tr>
                <td><input type="hidden" name="descricao[]" value="${item.descricao}">${item.descricao}</td>
                <td><input type="hidden" name="tipo[]" value="${item.tipo}">${item.tipo}</td>
                <td>R$ ${parseFloat(item.preco).toFixed(2)}<input type="hidden" name="preco[]" value="${item.preco}"></td>
                <td><input type="number" name="quantidade[]" value="1" min="1" class="form-control form-control-sm" onchange="atualizarTotal()"></td>
                <td class="total-item">R$ ${parseFloat(item.preco).toFixed(2)}</td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removerItem(this)">X</button></td>
              </tr>`;
            document.querySelector("#tabela-itens tbody").insertAdjacentHTML('beforeend', novaLinha);
            atualizarTotal();
            document.getElementById('produto').value = '';
            sugestoes.innerHTML = '';
          };
          sugestoes.appendChild(div);
        });
      });
  } else {
    sugestoes.innerHTML = '';
  }
});
</script>
<?php include_once '_footer.php'; ?>