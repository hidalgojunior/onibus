<?php
echo "=== TESTE DE CONEXÃO COM BANCO DE DADOS ===\n";

// Incluir configuração
include '../config/config.php';

try {
    echo "Testando conexão...\n";
    $conn = getDatabaseConnection();
    echo "✅ Conexão estabelecida com sucesso!\n";
    echo "Servidor: " . $conn->server_info . "\n";
    
    // Testar uma query simples
    $result = $conn->query("SELECT 1 as test");
    if ($result) {
        echo "✅ Query de teste funcionou!\n";
    } else {
        echo "❌ Erro na query de teste: " . $conn->error . "\n";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
}

echo "\n=== TESTE DE CAMINHOS ===\n";
echo "Arquivo atual: " . __FILE__ . "\n";
echo "Diretório atual: " . __DIR__ . "\n";
echo "Script name: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
?>
