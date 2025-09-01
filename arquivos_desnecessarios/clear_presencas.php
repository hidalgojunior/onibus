<?php
include 'config.php';
$conn = getDatabaseConnection();

echo "Apagando dados da tabela presencas...\n";

// Apagar todas as presenças
$result = $conn->query("DELETE FROM presencas");
$registros_removidos = $conn->affected_rows;

echo "✓ $registros_removidos registros removidos da tabela presencas\n";
echo "Tabela presencas limpa com sucesso!\n";

$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\clear_presencas.php
