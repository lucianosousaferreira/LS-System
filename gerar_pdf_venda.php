<?php
require_once 'conexao.php';
require_once 'vendor/autoload.php'; // Dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

$venda_id = intval($_GET['venda_id'] ?? 0);

if ($venda_id <= 0) {
    exit('ID da venda inválido.');
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
    exit('Venda não encontrada.');
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

$subtotal = 0;
$itens_html = '';
while ($item = $result_itens->fetch_assoc()) {
    $subtotal += $item['total_item'];
    $itens_html .= "
        <tr>
            <td>{$item['descricao']}</td>
            <td>{$item['quantidade']}</td>
            <td>R$ " . number_format($item['preco_unitario'], 2, ',', '.') . "</td>
            <td>R$ " . number_format($item['total_item'], 2, ',', '.') . "</td>
        </tr>";
}

// Gerar HTML do PDF
$html = "
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h2 { text-align: center; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    th { background-color: #f2f2f2; }
    .totais th, .totais td { text-align: right; }
    .totais tr:last-child th, .totais tr:last-child td { font-weight: bold; }
</style>

<h2>Comprovante de Venda Nº {$venda_id}</h2>

<p><strong>Data da Venda:</strong> " . date('d/m/Y H:i', strtotime($venda['data_venda'])) . "<br>
<strong>Forma de Pagamento:</strong> {$venda['forma_pagamento']}<br>
<strong>Desconto:</strong> R$ " . number_format($venda['desconto'], 2, ',', '.') . "</p>

<h4>Itens Vendidos</h4>
<table>
    <thead>
        <tr>
            <th>Descrição</th>
            <th>Qtd</th>
            <th>Preço Unit.</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        {$itens_html}
    </tbody>
</table>

<table class='totais'>
    <tr>
        <td colspan='3'><strong>Subtotal:</strong></td>
        <td>R$ " . number_format($subtotal, 2, ',', '.') . "</td>
    </tr>
    <tr>
        <td colspan='3'><strong>Desconto:</strong></td>
        <td>- R$ " . number_format($venda['desconto'], 2, ',', '.') . "</td>
    </tr>
    <tr>
        <td colspan='3'><strong>Total Final:</strong></td>
        <td>R$ " . number_format($venda['total'], 2, ',', '.') . "</td>
    </tr>
</table>

<p style='margin-top: 40px; text-align: center;'>Obrigado pela preferência!</p>
";

// Gerar PDF
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("comprovante_venda_{$venda_id}.pdf", ["Attachment" => false]);
exit;
