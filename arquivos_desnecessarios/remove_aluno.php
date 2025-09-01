<?php
include 'config.php';
$conn = getDatabaseConnection();

$nome_aluno = "Miguel Antonio - Filho do Alessandro";

echo "Procurando aluno: $nome_aluno\n";

// Primeiro, verificar se o aluno existe
$query = "SELECT id, nome FROM alunos WHERE nome = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nome_aluno);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $aluno = $result->fetch_assoc();
    $aluno_id = $aluno['id'];

    echo "Aluno encontrado - ID: $aluno_id\n";

    // Remover alocações do aluno (se existirem)
    $conn->query("DELETE FROM alocacoes_onibus WHERE aluno_id = $aluno_id");
    $alocacoes_removidas = $conn->affected_rows;
    echo "Alocações removidas: $alocacoes_removidas\n";

    // Remover presenças do aluno (se existirem)
    $conn->query("DELETE FROM presencas WHERE aluno_id = $aluno_id");
    $presencas_removidas = $conn->affected_rows;
    echo "Presenças removidas: $presencas_removidas\n";

    // Remover o aluno
    $conn->query("DELETE FROM alunos WHERE id = $aluno_id");
    $alunos_removidos = $conn->affected_rows;
    echo "Alunos removidos: $alunos_removidos\n";

    if ($alunos_removidos > 0) {
        echo "✅ Aluno removido com sucesso!\n";
    } else {
        echo "❌ Erro ao remover aluno\n";
    }

} else {
    echo "❌ Aluno não encontrado na base de dados\n";
}

$stmt->close();
$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\remove_aluno.php
