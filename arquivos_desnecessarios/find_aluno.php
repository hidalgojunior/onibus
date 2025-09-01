<?php
include 'config.php';
$conn = getDatabaseConnection();

echo "Procurando alunos com 'Miguel' no nome:\n";

$query = "SELECT id, nome FROM alunos WHERE nome LIKE '%Miguel%' ORDER BY nome";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($aluno = $result->fetch_assoc()) {
        echo "- ID: {$aluno['id']}, Nome: {$aluno['nome']}\n";
    }
} else {
    echo "Nenhum aluno encontrado com 'Miguel' no nome\n";
}

echo "\nListando TODOS os alunos para referência:\n";
$query = "SELECT id, nome FROM alunos ORDER BY nome LIMIT 10";
$result = $conn->query($query);

while ($aluno = $result->fetch_assoc()) {
    echo "- ID: {$aluno['id']}, Nome: {$aluno['nome']}\n";
}

$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\find_aluno.php
