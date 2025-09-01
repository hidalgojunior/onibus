<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

echo json_encode(['success' => true, 'message' => 'Teste simples funcionando', 'timestamp' => time()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
