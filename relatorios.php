<?php
include_once 'verifica_login.php';
include_once 'conexao.php';
$titulo_pagina = "Relatórios";

require 'vendor/autoload.php'; // para exportar Excel (PhpSpreadsheet) ou PDF (Dompdf)
?>

<?php include_once '_header.php'; ?>

<div class="container py-5" style="max-width: 1100px;">
  <h4 class="text-primary fw-semibold mb-0">Relatórios</h4>
  <hr class="my-4">

  <form method="get" class="mb-4">
    <div class="row g-3 align-items-end">
      <div class="col-md-3">
        <label for="relatorio" class="form-label">Tipo de Relatório</label>
        <select name="relatorio" id="relatorio" class="form-select" required>
          <option value="">Selecione...</option>
          <option value="clientes">Clientes</option>
          <option value="produtos_servicos">Produtos/Serviços</option>
          <option value="ordens_servico">Ordens de Serviço</option>
          <option value="veiculos">Veículos</option>
          <option value="fornecedores">Fornecedores</option>
          <option value="tecnicos">Técnicos</option>
          <option value="usuarios">Usuários</option>
        </select>
      </div>

      <div class="col-md-3">
        <label for="data_inicio" class="form-label">Data Início</label>
        <input type="date" name="data_inicio" id="data_inicio" class="form-control">
      </div>

      <div class="col-md-3">
        <label for="data_fim" class="form-label">Data Fim</label>
        <input type="date" name="data_fim" id="data_fim" class="form-control">
      </div>

      <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search me-1"></i> Gerar</button>
        <?php if (isset($_GET['relatorio'])): ?>
          <a href="exportar_relatorio.php?<?= http_build_query($_GET) ?>&tipo=pdf" class="btn btn-outline-danger me-2">Exportar PDF</a>
          <a href="exportar_relatorio.php?<?= http_build_query($_GET) ?>&tipo=excel" class="btn btn-outline-success">Exportar Excel</a>
        <?php endif; ?>
      </div>
    </div>
  </form>

  <?php
  if (isset($_GET['relatorio'])) {
    $tipo = $_GET['relatorio'];
    $data_inicio = $_GET['data_inicio'] ?? '';
    $data_fim = $_GET['data_fim'] ?? '';

    $where = '';
    if ($data_inicio && $data_fim) {
      $where = " WHERE data_abertura BETWEEN '$data_inicio' AND '$data_fim' ";
    } elseif ($data_inicio) {
      $where = " WHERE data_abertura >= '$data_inicio' ";
    } elseif ($data_fim) {
      $where = " WHERE data_abertura <= '$data_fim' ";
    }

    switch ($tipo) {
      case 'clientes':
        $sql = "SELECT * FROM tb_clientes ORDER BY nome ASC";
        $titulo = "Relatório de Clientes";
        $cabecalhos = ['Nome', 'Telefone', 'Email'];
        break;

      case 'produtos_servicos':
        $sql = "SELECT * FROM tb_produtos_servicos ORDER BY descricao ASC";
        $titulo = "Relatório de Produtos e Serviços";
        $cabecalhos = ['Descrição', 'Tipo', 'Preço'];
        break;

      case 'ordens_servico':
        $sql = "SELECT o.numero_os, c.nome AS cliente, o.data_abertura, o.total
                FROM tb_ordens_servico o
                LEFT JOIN tb_clientes c ON o.cliente_id = c.id $where
                ORDER BY o.data_abertura DESC";
        $titulo = "Relatório de Ordens de Serviço";
        $cabecalhos = ['Nº OS', 'Cliente', 'Data Abertura', 'Total'];
        break;

      case 'veiculos':
        $sql = "SELECT v.*, c.nome as cliente FROM tb_veiculos v
                LEFT JOIN tb_clientes c ON v.cliente_id = c.id
                ORDER BY c.nome";
        $titulo = "Relatório de Veículos";
        $cabecalhos = ['Cliente', 'Placa', 'Modelo', 'Marca', 'Ano'];
        break;

      case 'fornecedores':
        $sql = "SELECT * FROM tb_fornecedores ORDER BY nome ASC";
        $titulo = "Relatório de Fornecedores";
        $cabecalhos = ['Nome'];
        break;

      case 'tecnicos':
        $sql = "SELECT * FROM tb_tecnicos ORDER BY nome ASC";
        $titulo = "Relatório de Técnicos";
        $cabecalhos = ['Nome', 'Especialidade'];
        break;

      case 'usuarios':
        $sql = "SELECT * FROM tb_usuarios ORDER BY nome ASC";
        $titulo = "Relatório de Usuários";
        $cabecalhos = ['Nome', 'Usuário'];
        break;

      default:
        $sql = "";
    }

    if (!empty($sql)) {
      $resultado = $conn->query($sql);
      echo "<h5 class='mt-4 mb-3'>$titulo</h5>";
      echo "<div class='table-responsive'>";
      echo "<table class='table table-bordered table-hover'>";
      echo "<thead class='table-dark'><tr>";

      foreach ($cabecalhos as $cab) {
        echo "<th>$cab</th>";
      }

      echo "</tr></thead><tbody>";

      if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
          echo "<tr>";
          switch ($tipo) {
            case 'clientes':
              echo "<td>{$row['nome']}</td><td>{$row['telefone']}</td><td>{$row['email']}</td>";
              break;
            case 'produtos_servicos':
              echo "<td>{$row['descricao']}</td><td>{$row['tipo']}</td><td>R$ " . number_format($row['preco_venda'], 2, ',', '.') . "</td>";
              break;
            case 'ordens_servico':
              echo "<td>{$row['numero_os']}</td><td>{$row['cliente']}</td><td>" . date('d/m/Y', strtotime($row['data_abertura'])) . "</td><td>R$ " . number_format($row['total'], 2, ',', '.') . "</td>";
              break;
            case 'veiculos':
              echo "<td>{$row['cliente']}</td><td>{$row['placa']}</td><td>{$row['modelo']}</td><td>{$row['marca']}</td><td>{$row['ano']}</td>";
              break;
            case 'fornecedores':
              echo "<td>{$row['nome']}</td>";
              break;
            case 'tecnicos':
              echo "<td>{$row['nome']}</td><td>{$row['especialidade']}</td>";
              break;
            case 'usuarios':
              echo "<td>{$row['nome']}</td><td>{$row['usuario']}</td>";
              break;
          }
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='" . count($cabecalhos) . "' class='text-center text-muted'>Nenhum dado encontrado.</td></tr>";
      }

      echo "</tbody></table></div>";
    }
  }
  ?>
</div>

<?php include_once '_footer.php'; ?>
