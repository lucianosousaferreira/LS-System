<?php
require_once 'verifica_login.php';
require_once 'conexao.php';
require_once 'vendor/autoload.php'; // Dompdf

use Dompdf\Dompdf;

// Recebe dados do formulário
$numero_venda = $_POST['numero_venda'];
$cliente_id = $_POST['cliente_id'];
$desconto = $_POST['desconto'];
$forma_pagamento = $_POST['forma_pagamento'];
$itens = json_decode($_POST['itens'], true);
$total = $_POST['total'];

// Insere venda
$stmt = $conn->prepare("INSERT INTO tb_vendas (numero_venda, cliente_id, desconto, forma_pagamento, total, data_venda) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("sidss", $numero_venda, $cliente_id, $desconto, $forma_pagamento, $total);
$stmt->execute();
$venda_id = $stmt->insert_id;

// Insere itens da venda
foreach ($itens as $item) {
    $stmt_item = $conn->prepare("INSERT INTO tb_itens_venda (venda_id, produto_id, descricao, preco, quantidade, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_item->bind_param("iisdid", $venda_id, $item['produto_id'], $item['descricao'], $item['preco'], $item['quantidade'], $item['subtotal']);
    $stmt_item->execute();
}

// Consulta nome do cliente
$cliente_nome = '';
$consulta_cliente = $conn->prepare("SELECT nome FROM tb_clientes WHERE id = ?");
$consulta_cliente->bind_param("i", $cliente_id);
$consulta_cliente->execute();
$consulta_cliente->bind_result($cliente_nome);
$consulta_cliente->fetch();
$consulta_cliente->close();

// Informações da empresa
$empresa_nome = "Oficina Mecânica J Marciano Diesel";
$empresa_cnpj = "49.987.744/0001-30";
$empresa_endereco = "R. Coreia, 150A - Pq. Das Nações - Caucaia/CE";
$empresa_telefone = "(11) 98765-4321";
$empresa_email = "jmarcianodiesel@gmail.com";

// Gera HTML do cupom
$html = "
<style>
body { font-family: monospace; font-size: 14px; }
h2, h3 { text-align: center; margin: 0; }
table { width: 100%; border-collapse: collapse; }
td, th { padding: 2px; }
.total { font-weight: bold; }
</style>

<h2>$empresa_nome</h2>
<h3>CNPJ: $empresa_cnpj</h3>
<p style='text-align:center; margin:0;'>$empresa_endereco<br>
Tel: $empresa_telefone | E-mail: $empresa_email</p>
<hr>

<h3>RECIBO DE VENDA</h3>
<p><strong>Nº Venda:</strong> $numero_venda<br>
<strong>Cliente:</strong> $cliente_nome<br>
<strong>Forma de pagamento:</strong> $forma_pagamento<br>
<strong>Data:</strong> " . date('d/m/Y H:i') . "</p>

<table border='0'>
<tr><th>Produto</th><th>Qtd</th><th>Preço</th><th>Total</th></tr>";

foreach ($itens as $item) {
    $html .= "<tr>
        <td>{$item['descricao']}</td>
        <td>{$item['quantidade']}</td>
        <td>R$ " . number_format($item['preco'], 2, ',', '.') . "</td>
        <td>R$ " . number_format($item['subtotal'], 2, ',', '.') . "</td>
    </tr>";
}

$total_com_desconto = $total - ($total * $desconto / 100);
$html .= "</table><hr>
<p><strong>Subtotal:</strong> R$ " . number_format($total, 2, ',', '.') . "<br>
<strong>Desconto:</strong> {$desconto}%<br>
<strong>Total Final:</strong> R$ " . number_format($total_com_desconto, 2, ',', '.') . "</p>";

$html .= "<p style='text-align:center;'>Obrigado pela preferência!</p>";

// Gera PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A6', 'portrait');
$dompdf->render();
$dompdf->stream("cupom_venda_$numero_venda.pdf", ["Attachment" => false]);
exit;
?>
