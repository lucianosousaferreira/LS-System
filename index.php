<?php
include_once 'verifica_login.php';
$titulo_pagina = "Página Inicial";
include_once '_header.php';
?>

<style>
  .main {
    flex-grow: 1;
    padding: 40px 20px 20px;
    background-color: #f8f9fa;
  }

  .card-hover {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
  }

  .card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
  }

  .card-icon {
    font-size: 2.2rem;
  }

  .card-title {
    font-weight: 600;
  }

  .card-text {
    font-size: 0.95rem;
    color: #666;
  }

  .main {
  flex-grow: 1;
  padding-top: 30px; /* distância do topo da .topbar */
  display: flex;
  justify-content: center;
  align-items: flex-start; /* Alinha ao topo, não ao centro vertical */
}

.main .row {
  max-width: 1000px;
  width: 100%;
  margin: 0 auto;
}

</style>

<!-- Conteúdo principal -->
<div class="main">

  <div class="row g-4 justify-content-center">
    <h3 class="mb-4 text-center fw-semibold">Bem-vindo ao Sistema de Oficina</h3>

    <!-- Clientes -->
    <div class="col-sm-6 col-md-4 col-lg-3">
      <a href="cadastro_cliente.php" class="text-decoration-none text-dark">
        <div class="card shadow-sm h-100 card-hover text-center py-3">
          <i class="bi bi-person card-icon text-primary mb-2"></i>
          <h5 class="card-title mb-1">Clientes</h5>
          <p class="card-text">Gerencie seus clientes.</p>
        </div>
      </a>
    </div>

    <!-- Ordem de Serviço -->
    <div class="col-sm-6 col-md-4 col-lg-3">
      <a href="ordem_servico.php" class="text-decoration-none text-dark">
        <div class="card shadow-sm h-100 card-hover text-center py-3">
          <i class="bi bi-wrench card-icon text-success mb-2"></i>
          <h5 class="card-title mb-1">Ordem de Serviço</h5>
          <p class="card-text">Crie e acompanhe O.S.</p>
        </div>
      </a>
    </div>

    <!-- Veículos -->
    <div class="col-sm-6 col-md-4 col-lg-3">
      <a href="cadastro_veiculo.php" class="text-decoration-none text-dark">
        <div class="card shadow-sm h-100 card-hover text-center py-3">
          <i class="bi bi-car-front-fill card-icon text-danger mb-2"></i>
          <h5 class="card-title mb-1">Veículos</h5>
          <p class="card-text">Cadastro e histórico.</p>
        </div>
      </a>
    </div>

    <!-- Vendas -->
    <div class="col-sm-6 col-md-4 col-lg-3">
      <a href="vendas.php" class="text-decoration-none text-dark">
        <div class="card shadow-sm h-100 card-hover text-center py-3">
          <i class="bi bi-cash-coin card-icon text-warning mb-2"></i>
          <h5 class="card-title mb-1">Vendas</h5>
          <p class="card-text">Registre suas vendas.</p>
        </div>
      </a>
    </div>

  </div>
</div>

</div>

<?php include_once '_footer.php'; ?>
