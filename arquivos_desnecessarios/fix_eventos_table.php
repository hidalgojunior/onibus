<?php
include 'config.php';

$conn = getDatabaseConnection();

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

echo "<h1>Corrigindo Estrutura da Tabela 'eventos'</h1>";
echo "<pre>";

// Verificar se a coluna 'local' existe
$result = $conn->query("SHOW COLUMNS FROM eventos LIKE 'local'");
if ($result->num_rows == 0) {
    // Adicionar coluna 'local'
    $sql = "ALTER TABLE eventos ADD COLUMN local VARCHAR(255) NOT NULL AFTER data_fim";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Coluna 'local' adicionada com sucesso\n";
    } else {
        echo "✗ Erro ao adicionar coluna 'local': " . $conn->error . "\n";
    }
} else {
    echo "✓ Coluna 'local' já existe\n";
}

// Verificar se a coluna 'descricao' existe
$result = $conn->query("SHOW COLUMNS FROM eventos LIKE 'descricao'");
if ($result->num_rows == 0) {
    // Adicionar coluna 'descricao'
    $sql = "ALTER TABLE eventos ADD COLUMN descricao TEXT AFTER local";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Coluna 'descricao' adicionada com sucesso\n";
    } else {
        echo "✗ Erro ao adicionar coluna 'descricao': " . $conn->error . "\n";
    }
} else {
    echo "✓ Coluna 'descricao' já existe\n";
}

// Verificar se a coluna 'updated_at' existe
$result = $conn->query("SHOW COLUMNS FROM eventos LIKE 'updated_at'");
if ($result->num_rows == 0) {
    // Adicionar coluna 'updated_at'
    $sql = "ALTER TABLE eventos ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Coluna 'updated_at' adicionada com sucesso\n";
    } else {
        echo "✗ Erro ao adicionar coluna 'updated_at': " . $conn->error . "\n";
    }
} else {
    echo "✓ Coluna 'updated_at' já existe\n";
}

echo "\n--- Verificação Final ---\n";

// Mostrar estrutura final da tabela
$result = $conn->query("DESCRIBE eventos");
if ($result) {
    echo "Estrutura final da tabela 'eventos':\n";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Erro ao verificar estrutura: " . $conn->error . "\n";
}

// Verificar se há dados existentes que precisam ser atualizados
$result = $conn->query("SELECT COUNT(*) as total FROM eventos WHERE local = '' OR local IS NULL");
$row = $result->fetch_assoc();
if ($row['total'] > 0) {
    echo "\n--- Aviso ---\n";
    echo "Encontrados " . $row['total'] . " eventos sem local definido.\n";
    echo "Eles serão atualizados com um local padrão.\n";

    // Atualizar eventos sem local
    $update_sql = "UPDATE eventos SET local = 'Local não informado' WHERE local = '' OR local IS NULL";
    if ($conn->query($update_sql) === TRUE) {
        echo "✓ Eventos sem local atualizados com sucesso\n";
    } else {
        echo "✗ Erro ao atualizar eventos: " . $conn->error . "\n";
    }
}

echo "\n</pre>";
echo "<div class='alert alert-success'>";
echo "<h4>Correção concluída!</h4>";
echo "<p>A estrutura da tabela 'eventos' foi corrigida e agora inclui todas as colunas necessárias.</p>";
echo "<p><a href='eventos.php' class='btn btn-primary'>Ir para Gerenciamento de Eventos</a></p>";
echo "</div>";

$conn->close();
?>
