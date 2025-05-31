<?php
$mes = $_POST['mes'];
$designados = $_POST['designado'] ?? [];
$ajudantes = $_POST['ajudante'] ?? [];

echo "<h2>Designações salvas para: $mes</h2>";
foreach ($designados as $categoria => $partes) {
  echo "<h3>$categoria</h3><ul>";
  foreach ($partes as $parte => $nome) {
    $ajudante = $ajudantes[$categoria][$parte] ?? '';
    echo "<li><strong>$parte:</strong> $nome";
    if ($ajudante) {
      echo " (Ajudante: $ajudante)";
    }
    echo "</li>";
  }
  echo "</ul>";
}
