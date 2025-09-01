<?php
// Debug: Verificar estrutura das tabelas
require_once '../config/config.php';

echo "<h2>Debug: Verificando tabelas do banco de dados</h2>";

// Verificar se as tabelas existem
$tables = ['eventos', 'alunos', 'onibus', 'qr_codes', 'alocacoes_onibus', 'presencas'];

foreach ($tables as $table) {
    echo "<h3>Tabela: $table</h3>";
    
    // Verificar se a tabela existe
    $check_table = $conn->query("SHOW TABLES LIKE '$table'");
    
    if ($check_table->num_rows > 0) {
        echo "<p style='color: green;'>✓ Tabela existe</p>";
        
        // Mostrar estrutura da tabela
        $structure = $conn->query("DESCRIBE $table");
        echo "<table border='1' style='margin: 10px 0;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th><th>Extra</th></tr>";
        
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Contar registros
        $count = $conn->query("SELECT COUNT(*) as total FROM $table");
        $total = $count->fetch_assoc()['total'];
        echo "<p>Total de registros: <strong>$total</strong></p>";
        
    } else {
        echo "<p style='color: red;'>✗ Tabela NÃO existe</p>";
    }
    
    echo "<hr>";
}

// Testar a query específica da função getEvents
echo "<h3>Testando Query dos Eventos:</h3>";

$query = "SELECT e.*,
                 COUNT(DISTINCT a.id) as total_alunos,
                 COUNT(DISTINCT o.id) as total_onibus,
                 MAX(q.short_code) as short_code,
                 MAX(q.public_url) as public_url
          FROM eventos e
          LEFT JOIN alunos a ON a.evento_id = e.id
          LEFT JOIN onibus o ON o.evento_id = e.id
          LEFT JOIN qr_codes q ON q.evento_id = e.id
          GROUP BY e.id
          ORDER BY e.data_inicio DESC";

$result = $conn->query($query);

if ($result) {
    echo "<p style='color: green;'>✓ Query executada com sucesso</p>";
    echo "<p>Número de eventos encontrados: " . $result->num_rows . "</p>";
    
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Data Início</th><th>Data Fim</th><th>Local</th><th>Total Alunos</th><th>Total Ônibus</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['nome'] . "</td>";
            echo "<td>" . $row['data_inicio'] . "</td>";
            echo "<td>" . $row['data_fim'] . "</td>";
            echo "<td>" . $row['local'] . "</td>";
            echo "<td>" . $row['total_alunos'] . "</td>";
            echo "<td>" . $row['total_onibus'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>✗ Erro na query: " . $conn->error . "</p>";
}

$conn->close();
?>
