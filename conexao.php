<?php
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

$conn = new mysqli($host, $user, $password, $dbname, (int)$port);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>
