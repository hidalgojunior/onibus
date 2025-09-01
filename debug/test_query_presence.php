<?php
include 'config.php';
$conn = getDatabaseConnection();

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Definir datas
$data_hoje = date('Y-m-d');
$data_ontem = date('Y-m-d', strtotime('-1 day'));

// IDs de exemplo (ajuste conforme necessário)
$evento_id = 1; // Ajuste para o ID do evento atual
$onibus_id = 1; // Ajuste para o ID do ônibus atual

// Query corrigida
$alunos_query = "
    SELECT a.*,
           MAX(p_ontem.presenca_embarque) as embarcou_ontem,
           MAX(p_hoje.presenca_embarque) as presenca_embarque,
           CASE WHEN ao.id IS NOT NULL THEN 1 ELSE 0 END as alocado_neste_onibus,
           ao.id as alocacao_id
    FROM alunos a
    LEFT JOIN alocacoes_onibus ao ON a.id = ao.aluno_id AND ao.onibus_id = $onibus_id AND ao.evento_id = $evento_id
    LEFT JOIN presencas p_hoje ON a.id = p_hoje.aluno_id AND p_hoje.data = '$data_hoje'
    LEFT JOIN presencas p_ontem ON a.id = p_ontem.aluno_id AND p_ontem.data = '$data_ontem'
    INNER JOIN alocacoes_onibus ao_evento ON a.id = ao_evento.aluno_id AND ao_evento.evento_id = $evento_id
    GROUP BY a.id, a.nome, a.serie, a.curso, a.telefone, ao.id
    ORDER BY
        CASE WHEN ao.id IS NOT NULL THEN 0 ELSE 1 END ASC,
        MAX(p_ontem.presenca_embarque) DESC,
        CASE WHEN MAX(p_ontem.presenca_embarque) IS NULL THEN 1 ELSE 0 END ASC,
        a.nome ASC
";

$result = $conn->query($alunos_query);

if ($result) {
    echo "✅ Query executada com sucesso!\n";
    echo "Número de alunos encontrados: " . $result->num_rows . "\n";

    if ($result->num_rows > 0) {
        $total_alocados = 0;
        $total_nao_alocados = 0;

        while ($row = $result->fetch_assoc()) {
            if ($row['alocado_neste_onibus'] == 1) {
                $total_alocados++;
            } else {
                $total_nao_alocados++;
            }
        }

        $result->data_seek(0); // Reset pointer

        echo "Alunos alocados: $total_alocados\n";
        echo "Alunos não alocados: $total_nao_alocados\n";

        // Mostrar primeiros 3 alunos como exemplo
        $count = 0;
        while ($aluno = $result->fetch_assoc()) {
            if ($count >= 3) break;
            $status = $aluno['alocado_neste_onibus'] == 1 ? 'Alocado' : 'Disponível';
            echo "- " . $aluno['nome'] . " ($status)\n";
            $count++;
        }
    }
} else {
    echo "❌ Erro na query: " . $conn->error . "\n";
}

$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\test_query_presence.php
