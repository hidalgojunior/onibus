<?php
// Endpoint diagnóstico para requests feitos pelo front-end
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

// Coletar cabeçalhos (compatível com Apache + PHP built-in)
$headers = function_exists('getallheaders') ? getallheaders() : [];

// Parâmetros
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$get = $_GET ?? [];
$post = $_POST ?? [];

// Obter IP remoto
$remote = $_SERVER['REMOTE_ADDR'] ?? null;

// Teste de conexão ao banco (silencioso)
$db_ok = false;
$db_error = null;
try {
    $conn = @getDatabaseConnection();
    if ($conn && !$conn->connect_error) {
        $db_ok = true;
        $conn->close();
    }
} catch (Exception $e) {
    $db_error = $e->getMessage();
}

echo json_encode([
    'success' => true,
    'method' => $method,
    'headers' => $headers,
    'get' => $get,
    'post' => $post,
    'remote_addr' => $remote,
    'db_ok' => $db_ok,
    'db_error' => $db_error
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
