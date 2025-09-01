<?php
require_once 'config.php';

$config = getDatabaseConfig();
$host = $config['host'] ?? 'localhost';
$user = $config['usuario'] ?? '';
$pass = $config['senha'] ?? '';
$db   = $config['banco'] ?? '';

// Tentar conexão manual para capturar erros sem die()
$mysqli = @new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    echo json_encode([
        'success' => false,
        'errno' => $mysqli->connect_errno,
        'error' => $mysqli->connect_error,
        'host' => $host,
        'user' => $user
    ], JSON_UNESCAPED_UNICODE);
    exit(1);
}

echo json_encode([
    'success' => true,
    'message' => 'Conectado com sucesso ao MySQL',
    'host' => $host,
    'user' => $user
], JSON_UNESCAPED_UNICODE);
$mysqli->close();
?>
