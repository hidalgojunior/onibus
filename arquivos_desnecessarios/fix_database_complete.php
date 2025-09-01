<?php
include 'config.php';

$conn = getDatabaseConnection();

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

echo "<h1>Verificando e Corrigindo Dados de Eventos</h1>";
echo "<pre>";

// Verificar dados atuais dos eventos
$result = $conn->query("SELECT id, nome, local, descricao FROM eventos");
if ($result && $result->num_rows > 0) {
    echo "Dados atuais dos eventos:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- ID: " . $row['id'] . "\n";
        echo "  Nome: " . $row['nome'] . "\n";
        echo "  Local: " . ($row['local'] ?? 'NULL') . "\n";
        echo "  Descrição: " . ($row['descricao'] ?? 'NULL') . "\n";
        echo "\n";
    }
} else {
    echo "Nenhum evento encontrado.\n";
}

// Atualizar eventos que não têm descrição
$update_sql = "UPDATE eventos SET descricao = 'Evento sem descrição' WHERE descricao IS NULL OR descricao = ''";
if ($conn->query($update_sql) === TRUE) {
    $affected_rows = $conn->affected_rows;
    echo "✓ Atualizados $affected_rows eventos com descrição padrão\n";
} else {
    echo "✗ Erro ao atualizar descrições: " . $conn->error . "\n";
}

// Verificar se há problemas com a coluna evento_id na tabela alunos
$result = $conn->query("SHOW COLUMNS FROM alunos LIKE 'evento_id'");
if ($result->num_rows == 0) {
    echo "\n--- Problema encontrado ---\n";
    echo "A coluna 'evento_id' não existe na tabela 'alunos'!\n";
    echo "Isso pode causar problemas no sistema.\n";

    // Adicionar a coluna evento_id
    $sql = "ALTER TABLE alunos ADD COLUMN evento_id INT NULL AFTER nome";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Coluna 'evento_id' adicionada à tabela 'alunos'\n";
    } else {
        echo "✗ Erro ao adicionar coluna 'evento_id': " . $conn->error . "\n";
    }

    // Adicionar chave estrangeira
    $sql = "ALTER TABLE alunos ADD CONSTRAINT fk_alunos_evento FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE SET NULL";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Chave estrangeira adicionada\n";
    } else {
        echo "✗ Erro ao adicionar chave estrangeira: " . $conn->error . "\n";
    }
} else {
    echo "\n✓ Coluna 'evento_id' já existe na tabela 'alunos'\n";
}

// Verificar se há problemas com a coluna data_inscricao na tabela alunos
$result = $conn->query("SHOW COLUMNS FROM alunos LIKE 'data_inscricao'");
if ($result->num_rows == 0) {
    echo "\n--- Problema encontrado ---\n";
    echo "A coluna 'data_inscricao' não existe na tabela 'alunos'!\n";

    // Adicionar a coluna data_inscricao
    $sql = "ALTER TABLE alunos ADD COLUMN data_inscricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER evento_id";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Coluna 'data_inscricao' adicionada à tabela 'alunos'\n";
    } else {
        echo "✗ Erro ao adicionar coluna 'data_inscricao': " . $conn->error . "\n";
    }
} else {
    echo "\n✓ Coluna 'data_inscricao' já existe na tabela 'alunos'\n";
}

echo "\n--- Verificação Final ---\n";

// Verificar estrutura final das tabelas
$tables_to_check = ['eventos', 'alunos'];
foreach ($tables_to_check as $table) {
    echo "Estrutura da tabela '$table':\n";
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "Erro ao verificar tabela '$table': " . $conn->error . "\n";
    }
    echo "\n";
}

echo "\n</pre>";
echo "<div class='alert alert-success'>";
echo "<h4>Correção concluída!</h4>";
echo "<p>Todas as estruturas de tabelas foram verificadas e corrigidas.</p>";
echo "<p><a href='eventos.php' class='btn btn-primary'>Ir para Gerenciamento de Eventos</a></p>";
echo "<p><a href='alocacao.php' class='btn btn-secondary'>Ir para Alocação Automática</a></p>";
echo "</div>";

$conn->close();
?>
