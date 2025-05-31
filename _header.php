<!DOCTYPE html>
<?php 
include_once 'verifica_login.php';
?>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo isset($titulo_pagina) ? $titulo_pagina . " | LS System " : "LS System"; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <!-- jQuery e jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

  <style>
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .sidebar {
      width: 220px;
      background-color: #f1f3f5;
      color: #212529;
      min-height: 100vh;
    }

    .sidebar a {
      color: #212529;
      text-decoration: none;
      padding: 12px 15px;
      display: flex;
      align-items: center;
      font-size: 0.95rem;
    }

    .sidebar a:hover {
      background-color: #dee2e6;
    }

    .sidebar i {
      font-size: 1.1rem;
      margin-right: 10px;
    }

    .main {
      flex-grow: 1;
      background-color: #f8f9fa;
    }

    .topbar {
      height: 40px;
      background-color: #fff;
      border-bottom: 1px solid #dee2e6;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
    }

    .logo-area {
      display: flex;
      align-items: center;
    }

    .logo {
      max-height: 40px;
      margin-right: 10px;
    }

    .logout-btn {
      color: #dc3545;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .logout-btn:hover {
      text-decoration: underline;
    }

    .logout-btn i {
      margin-right: 5px;
    }

    .content-wrapper {
      display: flex;
    }
  </style>
</head>
<body>
  <!-- Topbar com logo e botão de logout -->
  <div class="topbar shadow-sm">
    <div class="logo-area">
      <img src="imagens/logo.png" alt="Logo" class="logo" />
      <span class="fw-bold text-primary"></span>
    </div>
    <a href="logout.php" class="logout-btn d-flex align-items-center">
      <i class="bi bi-box-arrow-right"></i> Sair
    </a>
  </div>

<div class="content-wrapper">
  <!-- Menu lateral -->
  <div class="sidebar d-flex flex-column">
    <a href="index.php"><i class="bi bi-house-door-fill"></i> Início</a>
    <a href="listar_clientes.php"><i class="bi bi-person"></i> Clientes</a>
    <a href="listar_ordem_servico.php"><i class="bi bi-wrench-adjustable-circle"></i> OS</a>
    <a href="listar_veiculos.php"><i class="bi bi-car-front-fill"></i> Veículos</a>
    <a href="listar_produtos_servicos.php"><i class="bi bi-box-seam"></i> Produtos e Serviços</a>
    <a href="listar_fornecedores.php"><i class="bi bi-truck"></i> Fornecedores</a>
    <a href="listar_marcas.php"><i class="bi bi-tags"></i> Marcas</a>
    <a href="listar_tecnicos.php"><i class="bi bi-person-gear"></i> Técnicos</a>
    <a href="relatorios.php"><i class="bi bi-file-earmark-text"></i> Relatórios</a>
    <a href="venda_produtos.php"><i class="bi bi-cart4"></i> Vendas</a>
    <a href="#"><i class="bi bi-gear"></i> Configurações</a>
  </div>


    <!-- Conteúdo principal -->
