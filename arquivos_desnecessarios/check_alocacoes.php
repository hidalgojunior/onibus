<?php
include 'config.php';
$conn = getDatabaseConnection();

$result = $conn->query('SELECT COUNT(*) as total FROM alocacoes_onibus');
$alocacoes = $result->fetch_assoc();
echo 'Total de alocações: ' . $alocacoes['total'] . PHP_EOL;

// Verificar alunos alocados no ônibus 1
$result = $conn->query('
    SELECT COUNT(*) as total 
    FROM alocacoes_onibus ao
    INNER JOIN onibus o ON ao.onibus_id = o.id
    WHERE o.numero = 1
');
$alocados_onibus1 = $result->fetch_assoc();
echo 'Alunos alocados no Ônibus 1: ' . $alocados_onibus1['total'] . PHP_EOL;

$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\check_alocacoes.php
