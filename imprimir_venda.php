<?php
require_once 'conexao.php';
require_once '_header.php';

$venda_id = intval($_GET['venda_id'] ?? 0);

if ($venda_id <= 0) {
    echo "<div class='alert alert-danger'>ID da venda inválido.</div>";
    require_once '_footer.php';
    exit;
}

// Buscar dados da venda
$sql_venda = "SELECT id, data_venda, total, desconto, forma_pagamento FROM tb_vendas WHERE id = ?";
$stmt = $conn->prepare($sql_venda);
$stmt->bind_param("i", $venda_id);
$stmt->execute();
$result_venda = $stmt->get_result();
$venda = $result_venda->fetch_assoc();
$stmt->close();

if (!$venda) {
    echo "<div class='alert alert-warning'>Venda não encontrada.</div>";
    require_once '_footer.php';
    exit;
}

// Buscar itens da venda
$sql_itens = "SELECT iv.quantidade, iv.preco_unitario, iv.total_item, p.descricao 
              FROM tb_itens_venda iv
              INNER JOIN tb_produtos_servicos p ON iv.produto_id = p.id
              WHERE iv.venda_id = ?";
$stmt = $conn->prepare($sql_itens);
$stmt->bind_param("i", $venda_id);
$stmt->execute();
$result_itens = $stmt->get_result();
?>

<div class="container my-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-primary mb-0">Venda Nº <?php echo $venda_id; ?></h4>
                <div class="btn-group">
                    <a href="venda.php" class="btn btn-sm btn-secondary">
                        <i class="bi bi-plus-lg"></i> Nova Venda
                    </a>
                    <a href="editar_venda.php?venda_id=<?php echo $venda_id; ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="gerar_pdf_venda.php?venda_id=<?php echo $venda_id; ?>" class="btn btn-sm btn-outline-danger" target="_blank">
                        <i class="bi bi-printer"></i> Imprimir
                    </a>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>Data da Venda:</strong> <?php echo date('d/m/Y H:i', strtotime($venda['data_venda'])); ?></div>
                <div class="col-md-4"><strong>Forma de Pagamento:</strong> <?php echo htmlspecialchars($venda['forma_pagamento']); ?></div>
                <div class="col-md-4"><strong>Desconto:</strong> R$ <?php echo number_format($venda['desconto'], 2, ',', '.'); ?></div>
            </div>

            <h5 class="text-secondary">Itens Vendidos</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Descrição</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Total Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subtotal = 0;
                        while ($item = $result_itens->fetch_assoc()):
                            $subtotal += $item['total_item'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['descricao']); ?></td>
                            <td><?php echo (int)$item['quantidade']; ?></td>
                            <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($item['total_item'], 2, ',', '.'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Subtotal:</th>
                            <th>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Desconto:</th>
                            <th>- R$ <?php echo number_format($venda['desconto'], 2, ',', '.'); ?></th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Total Final:</th>
                            <th>R$ <?php echo number_format($venda['total'], 2, ',', '.'); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '_footer.php'; ?>
