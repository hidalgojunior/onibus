<?php
include 'config.php';
$conn = getDatabaseConnection();

echo "<h3>Verificando estrutura da tabela 'eventos'</h3>";

$result = $conn->query('DESCRIBE eventos');
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Chave</th><th>Padrão</th><th>Extra</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Erro ao consultar tabela: " . $conn->error . "</p>";
}

echo "<h3>Verificando dados existentes</h3>";
$result2 = $conn->query('SELECT * FROM eventos LIMIT 5');
if ($result2 && $result2->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Data Início</th><th>Data Fim</th><th>Local</th><th>Descrição</th></tr>";

    while ($row = $result2->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
        echo "<td>" . $row['data_inicio'] . "</td>";
        echo "<td>" . $row['data_fim'] . "</td>";
        echo "<td>" . (isset($row['local']) ? htmlspecialchars($row['local']) : '<span style="color: red;">COLUNA NÃO EXISTE</span>') . "</td>";
        echo "<td>" . (isset($row['descricao']) ? htmlspecialchars(substr($row['descricao'], 0, 50)) . '...' : '<span style="color: red;">COLUNA NÃO EXISTE</span>') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum evento encontrado ou erro na consulta.</p>";
}

$conn->close();
?>
