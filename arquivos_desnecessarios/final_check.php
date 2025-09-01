<?php
include 'config.php';
$conn = getDatabaseConnection();

echo "=== VERIFICAÇÃO FINAL ===\n";

// Verificar se ainda há duplicatas
$result = $conn->query("
    SELECT nome, COUNT(*) as quantidade
    FROM alunos
    GROUP BY nome
    HAVING COUNT(*) > 1
");

if ($result->num_rows > 0) {
    echo "❌ Ainda há duplicatas:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['nome']}: {$row['quantidade']} ocorrências\n";
    }
} else {
    echo "✅ Nenhuma duplicata encontrada!\n";
}

// Estatísticas finais
echo "\n=== ESTATÍSTICAS FINAIS ===\n";
$tables = ['alunos', 'alocacoes_onibus', 'presencas', 'eventos', 'onibus'];

foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as total FROM $table");
    $row = $result->fetch_assoc();
    echo "Tabela $table: {$row['total']} registros\n";
}

// Mostrar alguns alunos para verificar
echo "\n=== AMOSTRA DE ALUNOS ===\n";
$result = $conn->query("SELECT id, nome FROM alunos ORDER BY nome LIMIT 5");
while ($row = $result->fetch_assoc()) {
    echo "{$row['id']}: {$row['nome']}\n";
}

$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\final_check.php
