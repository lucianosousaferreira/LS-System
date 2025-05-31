<?php
include_once 'verifica_login.php';
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
include_once 'conexao.php';

$id = $_GET['id'] ?? 0;

// Consulta da ordem de serviço com dados do cliente e veículo
$sql = "SELECT os.*, 
               c.nome AS cliente_nome, 
               c.cpf_cnpj, 
               c.contato, 
               c.endereco, 
               c.email, 
               v.placa, 
               v.marca, 
               v.modelo 
        FROM tb_ordens_servico os
        JOIN tb_clientes c ON os.cliente_id = c.id
        JOIN tb_veiculos v ON os.veiculo_id = v.id
        WHERE os.id = $id";

$res = $conn->query($sql);
$ordem = $res->fetch_assoc();

$sql_itens = "SELECT * FROM tb_itens_os WHERE ordem_servico_id = $id";
$itens = $conn->query($sql_itens);

// Desconto (pode ser zero)
$desconto = $ordem['desconto'] ?? 0;

$pdf = new TCPDF();
$pdf->AddPage();

// Logo e informações da empresa
$pdf->Image('imagens/logo.jpg', 10, 12, 30);
$pdf->SetXY(50, 12);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 5, 'OFICINA MECÂNICA J MARCIANO DIESEL', 0, 1, 'C');
$pdf->SetX(50);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'CNPJ: 49.9877.44/0001-30 - Tel: (85) 99287-1867 / (85) 99140-4281', 0, 1, 'C');
$pdf->SetX(50);
$pdf->Cell(0, 5, 'Rua Coreia, 150 - Parque Das Nações - Caucaia/CE', 0, 1, 'C');
$pdf->Ln(10);

// Dados do Cliente e Ordem
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(100, 6, "Cliente: " . $ordem['cliente_nome']);
$pdf->Cell(50, 6, "Nº OS: " . $ordem['numero_os']);
$pdf->Ln();
$pdf->Cell(100, 6, "CPF/CNPJ: " . $ordem['cpf_cnpj']);
$pdf->Cell(90, 6, "Contato: " . $ordem['contato']);
$pdf->Ln();
$pdf->Cell(100, 6, "Endereço: " . $ordem['endereco']);
$pdf->Cell(90, 6, "E-mail: " . $ordem['email']);
$pdf->Ln();
$pdf->Cell(100, 6, "Veículo: {$ordem['marca']} {$ordem['modelo']} - {$ordem['placa']}");
$pdf->Cell(50, 6, "Status: " . $ordem['status']);
$pdf->Ln();
$pdf->Cell(100, 6, "Entrada: " . date('d/m/Y', strtotime($ordem['data_entrada'])));
$pdf->Ln();
$pdf->Cell(100, 6, "Saída: " . ($ordem['data_saida'] ? date('d/m/Y', strtotime($ordem['data_saida'])) : '---'));
$pdf->Ln(10);

// Relato de Problemas
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'Relato de Problemas:', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->MultiCell(0, 15, $ordem['relato_problemas'], 1);
$pdf->Ln(4);

// Laudo do Serviço
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'Laudo do Serviço:', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->MultiCell(0, 15, $ordem['laudo_servico'], 1);
$pdf->Ln(6);

// Tabela de Produtos
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetFillColor(230, 230, 230);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(190, 8, 'Informações Dos Produtos', 0, 1, 'C', true);

$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(0, 51, 102);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(80, 7, 'Descrição', 1);
$pdf->Cell(20, 7, 'Qtd', 1, 0, 'C');
$pdf->Cell(40, 7, 'Valor Unit.', 1, 0, 'R');
$pdf->Cell(50, 7, 'Valor Total', 1, 1, 'R');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0, 0, 0);

$total_geral = 0;
foreach ($itens as $item) {
    if ($item['tipo'] === 'produto') {
        $subtotal = $item['preco'] * $item['quantidade'];
        $total_geral += $subtotal;
        $pdf->Cell(80, 6, $item['descricao'], 1);
        $pdf->Cell(20, 6, $item['quantidade'], 1, 0, 'C');
        $pdf->Cell(40, 6, 'R$ ' . number_format($item['preco'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(50, 6, 'R$ ' . number_format($subtotal, 2, ',', '.'), 1, 1, 'R');
    }
}
$pdf->Ln(4);

// Reposicionar ponteiro para percorrer itens novamente
mysqli_data_seek($itens, 0);

// Tabela de Serviços
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetFillColor(230, 230, 230);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(190, 8, 'Informações Dos Serviços', 0, 1, 'C', true);

$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(80, 7, 'Descrição', 1);
$pdf->Cell(20, 7, 'Qtd', 1, 0, 'C');
$pdf->Cell(40, 7, 'Valor Unit.', 1, 0, 'R');
$pdf->Cell(50, 7, 'Valor Total', 1, 1, 'R');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0, 0, 0);

foreach ($itens as $item) {
    if ($item['tipo'] === 'serviço') {
        $subtotal = $item['preco'] * $item['quantidade'];
        $total_geral += $subtotal;
        $pdf->Cell(80, 6, $item['descricao'], 1);
        $pdf->Cell(20, 6, $item['quantidade'], 1, 0, 'C');
        $pdf->Cell(40, 6, 'R$ ' . number_format($item['preco'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(50, 6, 'R$ ' . number_format($subtotal, 2, ',', '.'), 1, 1, 'R');
    }
}
$pdf->Ln(4);

// Desconto, Total e Forma de Pagamento
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetFillColor(230, 230, 230);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(190, 8, 'Informações Financeiras', 0, 1, 'C', true);

$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetTextColor(153, 0, 0);
$pdf->Cell(150, 6, 'Desconto', 1);
$desconto = $desconto/100;
$desconto = $desconto * $total_geral;
$pdf->Cell(40, 6, 'R$ ' . number_format($desconto, 2, ',', '.'), 1, 1, 'R');


$pdf->SetTextColor(0, 102, 0);
$pdf->Cell(150, 6, 'Total:', 1);
$pdf->Cell(40, 6, 'R$ ' . number_format($total_geral - $desconto, 2, ',', '.'), 1, 1, 'R');

// Forma de pagamento
$pdf->SetTextColor(0, 0, 153);
$pdf->Cell(150, 6, 'Forma de Pagamento', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(40, 6, $ordem['forma_pagamento'] ?? '-', 1, 1, 'R');

$pdf->Ln(10);

// Garantia
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', 'I', 9);
$pdf->MultiCell(0, 5, 'Garantia de 90 dias para os serviços realizados, conforme o Código de Defesa do Consumidor. A garantia cobre somente os serviços executados e peças fornecidas pela oficina. Não cobre mau uso, desgaste natural ou intervenções de terceiros.', 0, 'C');
$pdf->Ln(10);

// Assinaturas
$pdf->SetX(22);
$pdf->Cell(65, 6, 'Assinatura do Cliente', 0, 0, 'L');
$pdf->SetX(115);
$pdf->Cell(65, 6, 'Assinatura da Oficina', 0, 1, 'R');
$pdf->Ln(10);
$pdf->Cell(90, 6, '________________________________', 0, 0, 'L');
$pdf->Cell(90, 6, '________________________________', 0, 1, 'R');

// Abre a caixa de impressão automaticamente
$pdf->IncludeJS("print();");

// Gerar PDF na tela
$pdf->Output("ordem_servico_{$ordem['numero_os']}.pdf", 'I');
?>
