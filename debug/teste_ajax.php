<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste AJAX Candidaturas</title>
</head>
<body>
    <h1>Teste de AJAX Candidaturas</h1>
    <div id="result"></div>

    <script>
        async function testAjax() {
            const resultDiv = document.getElementById('result');

            try {
                const timestamp = new Date().getTime();
                const response = await fetch(`ajax_candidaturas.php?action=get_candidaturas&t=${timestamp}`);

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                console.log('Content-Type:', response.headers.get('content-type'));

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const text = await response.text();
                console.log('Raw response text length:', text.length);
                console.log('First 200 chars:', text.substring(0, 200));
                console.log('Last 200 chars:', text.substring(text.length - 200));

                // Verificar se começa com { e termina com }
                const startsWithBrace = text.trim().startsWith('{');
                const endsWithBrace = text.trim().endsWith('}');
                console.log('Starts with {:', startsWithBrace);
                console.log('Ends with }:', endsWithBrace);

                // Tentar fazer parse do JSON
                const data = JSON.parse(text);
                console.log('Parsed data success:', data.success);
                console.log('Number of candidaturas:', data.candidaturas ? data.candidaturas.length : 0);

                resultDiv.innerHTML = '<div style="color: green;">✅ Sucesso! JSON válido.</div>';
                resultDiv.innerHTML += '<p>Status: ' + response.status + '</p>';
                resultDiv.innerHTML += '<p>Content-Type: ' + response.headers.get('content-type') + '</p>';
                resultDiv.innerHTML += '<p>Response length: ' + text.length + ' chars</p>';
                resultDiv.innerHTML += '<p>Candidaturas encontradas: ' + (data.candidaturas ? data.candidaturas.length : 0) + '</p>';

            } catch (error) {
                console.error('Erro:', error);
                resultDiv.innerHTML = '<div style="color: red;">❌ Erro: ' + error.message + '</div>';
                resultDiv.innerHTML += '<p>Erro na linha: ' + error.stack.split('\n')[1] + '</p>';
            }
        }

        // Executar teste automaticamente
        testAjax();
    </script>
</body>
</html>
