<?php
include 'config.php';
$conn = getDatabaseConnection();

echo "Removendo alunos duplicados...\n";

// Primeiro, vamos identificar os alunos duplicados e manter apenas o de menor ID
$result = $conn->query("
    SELECT nome, MIN(id) as id_manter, COUNT(*) as quantidade
    FROM alunos
    GROUP BY nome
    HAVING COUNT(*) > 1
    ORDER BY nome
");

$duplicados_removidos = 0;

while ($row = $result->fetch_assoc()) {
    $nome = $row['nome'];
    $id_manter = $row['id_manter'];

    // Buscar todos os IDs desse aluno exceto o que vamos manter
    $ids_para_remover = $conn->query("
        SELECT id FROM alunos
        WHERE nome = '" . $conn->real_escape_string($nome) . "'
        AND id != $id_manter
    ");

    $ids = [];
    while ($id_row = $ids_para_remover->fetch_assoc()) {
        $ids[] = $id_row['id'];
    }

    if (!empty($ids)) {
        $ids_string = implode(',', $ids);

        // Remover alocações dos alunos duplicados (se existirem)
        $conn->query("DELETE FROM alocacoes_onibus WHERE aluno_id IN ($ids_string)");

        // Remover presenças dos alunos duplicados (se existirem)
        $conn->query("DELETE FROM presencas WHERE aluno_id IN ($ids_string)");

        // Remover os alunos duplicados
        $conn->query("DELETE FROM alunos WHERE id IN ($ids_string)");

        $duplicados_removidos += count($ids);

        echo "✓ Removido duplicata: $nome (mantido ID: $id_manter)\n";
    }
}

echo "\n=== RESULTADO ===\n";
echo "Duplicatas removidas: $duplicados_removidos\n";

// Verificar resultado final
$result = $conn->query("SELECT COUNT(*) as total FROM alunos");
$row = $result->fetch_assoc();
echo "Total de alunos após limpeza: {$row['total']}\n";

// Verificar se ainda há duplicatas
$result = $conn->query("
    SELECT nome, COUNT(*) as quantidade
    FROM alunos
    GROUP BY nome
    HAVING COUNT(*) > 1
");

if ($result->num_rows > 0) {
    echo "\n⚠️ Ainda há duplicatas restantes:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['nome']}: {$row['quantidade']} ocorrências\n";
    }
} else {
    echo "\n✅ Todas as duplicatas foram removidas com sucesso!\n";
}

$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\remove_duplicates.php
