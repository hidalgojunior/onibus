<?php
require_once 'config/config.php';

$dbConfig = getDatabaseConfig();
$conn = new mysqli($dbConfig['host'], $dbConfig['usuario'], $dbConfig['senha'], $dbConfig['banco']);
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

echo "=== LIMPEZA DE DADOS ===\n\n";

// Lista de tabelas para limpar (em ordem para respeitar foreign keys)
$tabelas = [
    'presencas',           // Primeiro - depende de alunos e onibus
    'alocacoes_onibus',    // Segundo - depende de alunos e onibus  
    'candidaturas_eventos', // Terceiro - depende de alunos e eventos
    'qr_codes',            // Quarto - depende de alunos
    'alunos',              // Quinto - pode ter dependências
    'onibus',              // Sexto - pode ter dependências
    'eventos',             // Sétimo - independente
    'autorizacoes',        // Oitavo - pode depender de outras
    'modelos_autorizacao', // Nono - independente
    'responsaveis'         // Último - pode ter dependências
];

echo "Limpando dados das seguintes tabelas:\n";
foreach ($tabelas as $tabela) {
    echo "- $tabela\n";
}

echo "\nConfirmando limpeza em 3 segundos...\n";
sleep(3);

echo "\nIniciando limpeza...\n\n";

foreach ($tabelas as $tabela) {
    echo "Limpando tabela: $tabela... ";
    
    // Verificar se a tabela existe
    $result = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($result->num_rows == 0) {
        echo "TABELA NÃO EXISTE\n";
        continue;
    }
    
    // Contar registros antes
    $result = $conn->query("SELECT COUNT(*) as total FROM $tabela");
    $row = $result->fetch_assoc();
    $total_antes = $row['total'];
    
    // Limpar dados
    $success = $conn->query("DELETE FROM $tabela");
    
    if ($success) {
        // Reset AUTO_INCREMENT se aplicável
        $conn->query("ALTER TABLE $tabela AUTO_INCREMENT = 1");
        echo "OK ($total_antes registros removidos)\n";
    } else {
        echo "ERRO: " . $conn->error . "\n";
    }
}

echo "\n=== VERIFICAÇÃO FINAL ===\n\n";

foreach ($tabelas as $tabela) {
    $result = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($result->num_rows > 0) {
        $result = $conn->query("SELECT COUNT(*) as total FROM $tabela");
        $row = $result->fetch_assoc();
        echo "$tabela: {$row['total']} registros\n";
    }
}

echo "\n✅ Limpeza concluída! O banco está pronto para começar do zero.\n";

$conn->close();
?>
