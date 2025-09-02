<?php
include 'config/config.php';

$conn = getDatabaseConnection();
$result = $conn->query('SELECT id, nome, data_aniversario FROM alunos LIMIT 3');

echo "=== ALUNOS CADASTRADOS ===\n";
while($row = $result->fetch_assoc()) {
    echo 'ID: ' . $row['id'] . ' - Nome: ' . $row['nome'] . ' - Data: ' . ($row['data_aniversario'] ?? 'Não informado') . "\n";
}

$conn->close();
?>
