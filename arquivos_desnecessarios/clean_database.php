<?php
include 'config.php';
$conn = getDatabaseConnection();

echo "Limpando base de dados...\n";

// Limpar presenças
$result = $conn->query("DELETE FROM presencas");
echo "✓ Presenças removidas: " . $conn->affected_rows . " registros\n";

// Limpar alocações
$result = $conn->query("DELETE FROM alocacoes_onibus");
echo "✓ Alocações removidas: " . $conn->affected_rows . " registros\n";

// Limpar alunos (opcional - descomente se quiser limpar tudo)
// $result = $conn->query("DELETE FROM alunos");
// echo "✓ Alunos removidos: " . $conn->affected_rows . " registros\n";

// Limpar ônibus (opcional - manter estrutura)
// $result = $conn->query("DELETE FROM onibus");
// echo "✓ Ônibus removidos: " . $conn->affected_rows . " registros\n";

// Limpar eventos (opcional - manter estrutura)
// $result = $conn->query("DELETE FROM eventos");
// echo "✓ Eventos removidos: " . $conn->affected_rows . " registros\n";

echo "\nBase de dados limpa com sucesso!\n";
echo "Agora você pode importar os alunos novamente.\n";

$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\clean_database.php
