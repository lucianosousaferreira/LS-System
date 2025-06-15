<?php
require 'vendor/autoload.php'; // Cloudinary via Composer
include_once 'conexao.php';
include_once 'verifica_login.php';

$titulo_pagina = "Cadastrar Produto ou Serviço";
$mensagem = "";
$tipo_mensagem = "info";
$tipo_selecionado = $_GET['tipo'] ?? '';

// Cloudinary config
\Cloudinary\Configuration\Configuration::instance([
    'cloud' => [
        'cloud_name' => 'duzn9flso',
        'api_key'    => '767215351625894',
        'api_secret' => 'xAY1QegdsibW3uHd-dLkB6KvsPU'
    ],
    'url' => ['secure' => true]
]);

use Cloudinary\Api\Upload\UploadApi;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo_produto'] ?? null;
    $tipo = $_POST['tipo'];
    $descricao = $_POST['descricao'];
    $referencia_produto = $_POST['referencia_produto'] ?? null;
    $marca = $_POST['marca'] ?? null;
    $fornecedor = $_POST['fornecedor'] ?? null;
    $preco_compra = floatval($_POST['preco_compra']);
    $preco_venda = floatval($_POST['preco_venda']);
    $estoque = ($tipo === "Produto") ? intval($_POST['estoque'] ?? 0) : null;
    $imagem_nome = null;

    if ($tipo === "Produto" && isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $nome_arquivo = $_FILES["imagem"]["name"];
        $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));

        if (in_array($extensao, $extensoes_permitidas)) {
            try {
                $upload = (new UploadApi())->upload($_FILES["imagem"]["tmp_name"], [
                    'folder' => 'produtos',
                    'public_id' => uniqid("produto_"),
                    'overwrite' => true,
                    'resource_type' => 'image'
                ]);
                $imagem_nome = $upload['secure_url'];
            } catch (Exception $e) {
                $mensagem = "Erro ao fazer upload para o Cloudinary: " . $e->getMessage();
                $tipo_mensagem = "danger";
            }
        } else {
            $mensagem = "Formato de imagem inválido.";
            $tipo_mensagem = "danger";
        }
    }

    if (empty($mensagem)) {
        $sql = "INSERT INTO tb_produtos_servicos
                (codigo_produto, tipo, descricao, referencia_produto, marca, fornecedor, preco_compra, preco_venda, estoque, imagem)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssddis", $codigo, $tipo, $descricao, $referencia_produto, $marca, $fornecedor, $preco_compra, $preco_venda, $estoque, $imagem_nome);

        if ($stmt->execute()) {
            $mensagem = "Cadastro realizado com sucesso!";
            $tipo_mensagem = "success";
        } else {
            $mensagem = "Erro ao cadastrar: " . $stmt->error;
            $tipo_mensagem = "danger";
        }
        $stmt->close();
    }
}
$conn->close();
?>
