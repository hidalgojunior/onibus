<?php
include 'config.php';
$conn = getDatabaseConnection();

// Verificar evento
$evento_nome = 'Bootcamp Jovem Programador';
$evento_query = "SELECT id FROM eventos WHERE nome = '$evento_nome'";
$evento_result = $conn->query($evento_query);

if ($evento_result->num_rows == 0) {
    $conn->query("INSERT INTO eventos (nome, data_inicio, data_fim) VALUES ('$evento_nome', '2025-08-27', '2025-09-05')");
    $evento_id = $conn->insert_id;
} else {
    $evento_row = $evento_result->fetch_assoc();
    $evento_id = $evento_row['id'];
}

// Buscar ônibus disponíveis
$onibus_query = "SELECT id, numero, capacidade FROM onibus WHERE evento_id = $evento_id ORDER BY numero";
$onibus_result = $conn->query($onibus_query);

$onibus = [];
while ($row = $onibus_result->fetch_assoc()) {
    $onibus[] = $row;
}

// Buscar alunos não alocados
$alunos_query = "
    SELECT a.id, a.nome FROM alunos a
    LEFT JOIN alocacoes_onibus ao ON a.id = ao.aluno_id AND ao.evento_id = $evento_id
    WHERE ao.id IS NULL
    ORDER BY a.nome
";
$alunos_result = $conn->query($alunos_query);

$alunos_nao_alocados = [];
while ($row = $alunos_result->fetch_assoc()) {
    $alunos_nao_alocados[] = $row;
}

echo "Alocando " . count($alunos_nao_alocados) . " alunos em " . count($onibus) . " ônibus...\n";

// Distribuir alunos pelos ônibus
$aluno_index = 0;
foreach ($onibus as $bus) {
    $capacidade_restante = $bus['capacidade'];

    while ($capacidade_restante > 0 && $aluno_index < count($alunos_nao_alocados)) {
        $aluno = $alunos_nao_alocados[$aluno_index];

        // Inserir alocação
        $insert_query = "INSERT INTO alocacoes_onibus (aluno_id, onibus_id, evento_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iii", $aluno['id'], $bus['id'], $evento_id);

        if ($stmt->execute()) {
            echo "✓ Alocado: " . $aluno['nome'] . " → Ônibus " . $bus['numero'] . "\n";
        } else {
            echo "✗ Erro ao alocar: " . $aluno['nome'] . " - " . $conn->error . "\n";
        }

        $stmt->close();
        $aluno_index++;
        $capacidade_restante--;
    }
}

echo "\nAlocação concluída!\n";

$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\auto_alocar.php
