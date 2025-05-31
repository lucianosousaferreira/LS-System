<?php
$servername = getenv("DB_HOST") ?: "127.0.0.1";
$username   = getenv("DB_USER") ?: "root";
$password   = getenv("DB_PASSWORD") ?: "";
$dbname     = getenv("DB_NAME") ?: "oficina";

// Conectar ao MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica erro
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
