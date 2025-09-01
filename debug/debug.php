<?php
include 'config.php';
$conn = getDatabaseConnection();

// Verificar alunos
$result = $conn->query('SELECT COUNT(*) as total FROM alunos');
$alunos = $result->fetch_assoc();
echo 'Total de alunos: ' . $alunos['total'] . PHP_EOL;

// Verificar alocações
$result = $conn->query('SELECT COUNT(*) as total FROM alocacoes_onibus');
$alocacoes = $result->fetch_assoc();
echo 'Total de alocações: ' . $alocacoes['total'] . PHP_EOL;

// Verificar eventos
$result = $conn->query('SELECT * FROM eventos');
echo 'Eventos: ' . PHP_EOL;
while ($row = $result->fetch_assoc()) {
    echo '- ID: ' . $row['id'] . ', Nome: ' . $row['nome'] . PHP_EOL;
}

// Verificar ônibus
$result = $conn->query('SELECT * FROM onibus');
echo 'Ônibus: ' . PHP_EOL;
while ($row = $result->fetch_assoc()) {
    echo '- ID: ' . $row['id'] . ', Número: ' . $row['numero'] . ', Evento: ' . $row['evento_id'] . PHP_EOL;
}

// Verificar alunos e suas alocações
$result = $conn->query('
    SELECT a.nome, ao.onibus_id, o.numero as onibus_numero
    FROM alunos a
    LEFT JOIN alocacoes_onibus ao ON a.id = ao.aluno_id
    LEFT JOIN onibus o ON ao.onibus_id = o.id
    ORDER BY a.nome
');
echo PHP_EOL . 'Alunos e suas alocações: ' . PHP_EOL;
while ($row = $result->fetch_assoc()) {
    $alocacao = $row['onibus_id'] ? 'Ônibus ' . $row['onibus_numero'] : 'Não alocado';
    echo '- ' . $row['nome'] . ': ' . $alocacao . PHP_EOL;
}

$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\debug.php
