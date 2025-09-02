<?php
// scanner.php - Leitor de QR Code para celular
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner QR Code - Presença</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7fafc; }
        #reader { width: 100%; max-width: 400px; margin: 0 auto; }
        .result-box { margin-top: 20px; padding: 15px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4 text-center">Scanner de QR Code</h2>
    <div id="reader"></div>
    <div id="result" class="result-box text-center mt-3">
        <span class="text-muted">Aguardando leitura...</span>
    </div>
    <div class="text-center mt-3">
        <button class="btn btn-secondary" onclick="window.location.reload()">Reiniciar Scanner</button>
    </div>
</div>
<script src="https://unpkg.com/html5-qrcode@2.3.10/html5-qrcode.min.js"></script>
<script>
    const resultBox = document.getElementById('result');
    function onScanSuccess(decodedText, decodedResult) {
        resultBox.innerHTML = `<b>QR Code lido:</b><br><span class='text-success fs-5'>${decodedText}</span>`;
        // Aqui você pode fazer um fetch/ajax para processar o valor lido
        // Exemplo: buscar presença, aluno, etc.
        // fetch('processa-presenca.php?codigo=' + encodeURIComponent(decodedText))
    }
    function onScanFailure(error) {
        // Não exibe erro para cada frame, apenas ignora
    }
    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", { fps: 10, qrbox: 250, rememberLastUsedCamera: true }, false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
</body>
</html>
