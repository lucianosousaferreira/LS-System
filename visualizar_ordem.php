<?php
include_once "conexao.php";
include_once "verifica_login.php";
$titulo_pagina = "Visualizar OS";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID da OS não informado.";
    exit;
}

$id = intval($_GET['id']);

// Consulta principal
$sql = "SELECT os.*, c.nome AS nome_cliente, v.placa, v.modelo, v.marca, v.cor, v.ano, t.nome AS nome_tecnico
        FROM tb_ordens_servico os
        LEFT JOIN tb_clientes c ON os.cliente_id = c.id
        LEFT JOIN tb_veiculos v ON os.veiculo_id = v.id
        LEFT JOIN tb_tecnicos t ON os.tecnico_id = t.id
        WHERE os.id = $id";

$result = $conn->query($sql);
$os = $result->fetch_assoc();

if (!$os) {
    echo "Ordem de serviço não encontrada.";
    exit;
}

$sql_itens = "SELECT * FROM tb_itens_os WHERE ordem_servico_id = $id";
$result_itens = $conn->query($sql_itens);
?>

<?php include_once '_header.php'; ?>
<div class="container my-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-3 text-primary">Ordem de Serviço Nº <?php echo htmlspecialchars($os['numero_os']); ?></h4>

            <div class="row mb-2">
                <div class="col-md-6"><strong>Cliente:</strong> <?php echo htmlspecialchars($os['nome_cliente']); ?></div>
                <div class="col-md-6"><strong>Veículo:</strong> <?php echo htmlspecialchars($os['marca'] . ' ' . $os['modelo'] . ' - ' . $os['placa']); ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Entrada:</strong> <?php echo date('d/m/Y', strtotime($os['data_entrada'])); ?></div>
                <div class="col-md-4"><strong>Saída:</strong> <?php echo !empty($os['data_saida']) ? date('d/m/Y', strtotime($os['data_saida'])) : '-'; ?></div>
                <div class="col-md-4"><strong>Status:</strong> <?php echo htmlspecialchars($os['status']); ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6"><strong>Técnico:</strong> <?php echo htmlspecialchars($os['nome_tecnico']); ?></div>
            </div>

            <div class="mb-3">
                <strong>Problema relatado:</strong>
                <p class="bg-light p-2 rounded border"><?php echo nl2br(htmlspecialchars($os['relato_problemas'])); ?></p>
            </div>

            <div class="mb-4">
                <strong>Laudo técnico:</strong>
                <p class="bg-light p-2 rounded border"><?php echo nl2br(htmlspecialchars($os['laudo_servico'])); ?></p>
            </div>

            <h5 class="text-secondary">Itens</h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>Preço</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_geral = 0;
                        if ($result_itens->num_rows > 0) {
                            while ($item = $result_itens->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($item['descricao']) . "</td>";
                                echo "<td>" . htmlspecialchars($item['tipo']) . "</td>";
                                echo "<td>" . $item['quantidade'] . "</td>";
                                echo "<td>R$ " . number_format($item['preco'], 2, ',', '.') . "</td>";
                                echo "<td>R$ " . number_format($item['total'], 2, ',', '.') . "</td>";
                                echo "</tr>";
                                $total_geral += $item['total'];
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>Nenhum item encontrado.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
                <?php $v = $os['desconto']/100; 
                $desconto = $total_geral * $v;
                ?>
            <div class="row mt-3">
                <div class="col-md-4"><strong>Desconto:</strong> (%) <?php echo number_format($os['desconto'], 2, ',', '.'); ?></div>
                <div class="col-md-4"><strong>Total:</strong> R$ <?php echo number_format($total_geral - $desconto, 2, ',', '.'   ); ?></div>   
                <div class="col-md-4"><strong>Forma de pagamento:</strong> <?php echo htmlspecialchars($os['forma_pagamento']); ?></div>
            </div>

            <hr class="my-4">

          <div class="d-flex justify-content-end">
            <div class="btn-group">
              <a href="ordem_servico.php" class="btn btn-sm btn-secondary">
                <i class="bi bi-plus-lg"></i> Nova OS
              </a>
              <a href="editar_ordem_servico.php?id=<?= $os['id'] ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil"></i> Editar
              </a>
              <a href="gerar_pdf_ordem.php?id=<?= $os['id'] ?>" class="btn btn-sm btn-outline-danger" target="_blank">
                <i class="bi bi-printer"></i> Imprimir
              </a>
            </div>
    </div>
        </div>
    </div>
</div>
<?php include_once '_footer.php'; ?>


