<?php
include 'config.php';
$conn = getDatabaseConnection();

echo "Verificando dados duplicados no banco...\n\n";

// Verificar alunos duplicados
echo "=== ALUNOS DUPLICADOS ===\n";
$result = $conn->query("
    SELECT nome, COUNT(*) as quantidade
    FROM alunos
    GROUP BY nome
    HAVING COUNT(*) > 1
    ORDER BY quantidade DESC, nome
");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Nome: {$row['nome']} - Duplicatas: {$row['quantidade']}\n";
    }
} else {
    echo "Nenhum aluno duplicado encontrado.\n";
}

// Verificar alocações duplicadas (mesmo aluno, mesmo ônibus, mesmo evento)
echo "\n=== ALOCAÇÕES DUPLICADAS ===\n";
$result = $conn->query("
    SELECT a.nome, ao.onibus_id, COUNT(*) as quantidade
    FROM alocacoes_onibus ao
    INNER JOIN alunos a ON ao.aluno_id = a.id
    GROUP BY ao.aluno_id, ao.onibus_id, ao.evento_id
    HAVING COUNT(*) > 1
    ORDER BY quantidade DESC, a.nome
");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Aluno: {$row['nome']} - Ônibus: {$row['onibus_id']} - Duplicatas: {$row['quantidade']}\n";
    }
} else {
    echo "Nenhuma alocação duplicada encontrada.\n";
}

// Verificar presenças duplicadas (mesmo aluno, mesma data)
echo "\n=== PRESENÇAS DUPLICADAS ===\n";
$result = $conn->query("
    SELECT a.nome, p.data, COUNT(*) as quantidade
    FROM presencas p
    INNER JOIN alunos a ON p.aluno_id = a.id
    GROUP BY p.aluno_id, p.data
    HAVING COUNT(*) > 1
    ORDER BY quantidade DESC, p.data DESC, a.nome
");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Aluno: {$row['nome']} - Data: {$row['data']} - Duplicatas: {$row['quantidade']}\n";
    }
} else {
    echo "Nenhuma presença duplicada encontrada.\n";
}

// Estatísticas gerais
echo "\n=== ESTATÍSTICAS GERAIS ===\n";
$tables = ['alunos', 'alocacoes_onibus', 'presencas', 'eventos', 'onibus'];

foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as total FROM $table");
    $row = $result->fetch_assoc();
    echo "Tabela $table: {$row['total']} registros\n";
}

$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\check_duplicates.php
