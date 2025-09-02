<?php
require_once 'config/config.php';
$dbConfig = getDatabaseConfig();
$conn = new mysqli($dbConfig['host'], $dbConfig['usuario'], $dbConfig['senha'], $dbConfig['banco']);
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

echo "Atualizando alunos com evento_id...\n";
$result = $conn->query('UPDATE alunos SET evento_id = 1 WHERE evento_id IS NULL OR evento_id = 0');
if ($result) {
    echo "✓ Alunos atualizados com sucesso!\n";
} else {
    echo "✗ Erro ao atualizar alunos: " . $conn->error . "\n";
}

$conn->close();
?>
