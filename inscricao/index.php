<?php
// Roteador simples para /inscricao/{code}
// Usa o endpoint local qr_manager.php?action=public_form&code={code} para obter o formulário

// Obter o código da URL
$uri = $_SERVER['REQUEST_URI'];
// Remover query string
$uri = explode('?', $uri, 2)[0];
$parts = explode('/', trim($uri, '/'));
// Procurar o segmento após 'inscricao'
$code = '';
for ($i = 0; $i < count($parts); $i++) {
    if ($parts[$i] === 'inscricao' && isset($parts[$i+1])) {
        $code = $parts[$i+1];
        break;
    }
}

if (empty($code)) {
    http_response_code(404);
    echo '<h1>404 Not Found</h1><p>Código de inscrição não fornecido.</p>';
    exit;
}

// Chamar o endpoint interno para obter o formulário
$endpoint = sprintf('http://localhost/onibus/qr_manager.php?action=public_form&code=%s', urlencode($code));
$opts = [
    'http' => [
        'method' => 'GET',
        'timeout' => 5,
        'header' => "Accept: application/json\r\n"
    ]
];
$context = stream_context_create($opts);
$result = @file_get_contents($endpoint, false, $context);
// debug: output raw result when requested
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    header('Content-Type: application/json; charset=utf-8');
    echo $result === false ? json_encode(['success' => false, 'error' => 'file_get_contents failed']) : $result;
    exit;
}
if ($result === false) {
    http_response_code(500);
    echo '<h1>Erro interno</h1><p>Não foi possível contatar o gerenciador de QR.</p>';
    exit;
}

$data = json_decode($result, true);
if (!$data || !isset($data['success']) || !$data['success']) {
    http_response_code(404);
    $msg = $data['message'] ?? 'Código não encontrado';
    echo '<h1>404 Not Found</h1><p>' . htmlspecialchars($msg) . '</p>';
    exit;
}

// Montar página pública simples contendo o form_html retornado
$form_html = $data['form_html'] ?? '';

?><!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Inscrição</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <?php echo $form_html; ?>
</div>
</body>
</html>
