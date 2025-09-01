<?php
include 'config.php';

$conn = getDatabaseConnection();

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

echo "<h1>Atualizando Banco de Dados - Novos Recursos</h1>";
echo "<pre>";

// Criar tabela de eventos se não existir
$create_eventos_table = "
CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    local VARCHAR(255) NOT NULL,
    descricao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($create_eventos_table) === TRUE) {
    echo "✓ Tabela 'eventos' criada/verificada com sucesso\n";
} else {
    echo "✗ Erro ao criar tabela 'eventos': " . $conn->error . "\n";
}

// Criar tabela de ônibus se não existir
$create_onibus_table = "
CREATE TABLE IF NOT EXISTS onibus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    numero INT NOT NULL,
    tipo ENUM('ônibus', 'van', 'micro-ônibus') NOT NULL DEFAULT 'ônibus',
    capacidade INT NOT NULL DEFAULT 50,
    dias_reservados INT NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_onibus_evento (evento_id, numero)
)";

if ($conn->query($create_onibus_table) === TRUE) {
    echo "✓ Tabela 'onibus' criada/verificada com sucesso\n";
} else {
    echo "✗ Erro ao criar tabela 'onibus': " . $conn->error . "\n";
}

// Verificar se a tabela alunos tem a coluna evento_id, se não, adicionar
$check_alunos_evento = "SHOW COLUMNS FROM alunos LIKE 'evento_id'";
$result = $conn->query($check_alunos_evento);

if ($result->num_rows == 0) {
    // Adicionar coluna evento_id à tabela alunos
    $add_evento_id = "ALTER TABLE alunos ADD COLUMN evento_id INT NULL AFTER nome,
                      ADD CONSTRAINT fk_alunos_evento FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE SET NULL";

    if ($conn->query($add_evento_id) === TRUE) {
        echo "✓ Coluna 'evento_id' adicionada à tabela 'alunos'\n";
    } else {
        echo "✗ Erro ao adicionar coluna 'evento_id' à tabela 'alunos': " . $conn->error . "\n";
    }
} else {
    echo "✓ Coluna 'evento_id' já existe na tabela 'alunos'\n";
}

// Verificar se a tabela alunos tem a coluna data_inscricao, se não, adicionar
$check_alunos_data_inscricao = "SHOW COLUMNS FROM alunos LIKE 'data_inscricao'";
$result = $conn->query($check_alunos_data_inscricao);

if ($result->num_rows == 0) {
    // Adicionar coluna data_inscricao à tabela alunos
    $add_data_inscricao = "ALTER TABLE alunos ADD COLUMN data_inscricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER evento_id";

    if ($conn->query($add_data_inscricao) === TRUE) {
        echo "✓ Coluna 'data_inscricao' adicionada à tabela 'alunos'\n";
    } else {
        echo "✗ Erro ao adicionar coluna 'data_inscricao' à tabela 'alunos': " . $conn->error . "\n";
    }
} else {
    echo "✓ Coluna 'data_inscricao' já existe na tabela 'alunos'\n";
}

// Criar tabela de alocações se não existir
$create_alocacoes_table = "
CREATE TABLE IF NOT EXISTS alocacoes_onibus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    onibus_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (onibus_id) REFERENCES onibus(id) ON DELETE CASCADE,
    UNIQUE KEY unique_aluno_onibus (aluno_id, onibus_id)
)";

if ($conn->query($create_alocacoes_table) === TRUE) {
    echo "✓ Tabela 'alocacoes_onibus' criada/verificada com sucesso\n";
} else {
    echo "✗ Erro ao criar tabela 'alocacoes_onibus': " . $conn->error . "\n";
}

// Verificar se existem eventos de exemplo
$check_eventos = "SELECT COUNT(*) as total FROM eventos";
$result = $conn->query($check_eventos);
$total_eventos = $result->fetch_assoc()['total'];

if ($total_eventos == 0) {
    echo "\n--- Criando evento de exemplo ---\n";

    // Inserir um evento de exemplo
    $insert_evento = "INSERT INTO eventos (nome, data_inicio, data_fim, local, descricao) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_evento);
    $nome = 'Evento de Exemplo - Semana Santa';
    $data_inicio = date('Y-m-d', strtotime('+7 days'));
    $data_fim = date('Y-m-d', strtotime('+14 days'));
    $local = 'Centro de Eventos';
    $descricao = 'Evento de exemplo para demonstração do sistema';

    $stmt->bind_param('sssss', $nome, $data_inicio, $data_fim, $local, $descricao);

    if ($stmt->execute()) {
        echo "✓ Evento de exemplo criado com sucesso\n";

        // Inserir alguns ônibus de exemplo
        $evento_id = $conn->insert_id;

        $onibus_exemplo = [
            [1, 'ônibus', 50, 10],
            [2, 'van', 15, 10],
            [3, 'micro-ônibus', 30, 10]
        ];

        $insert_onibus = "INSERT INTO onibus (numero, tipo, capacidade, dias_reservados, evento_id) VALUES (?, ?, ?, ?, ?)";
        $stmt_onibus = $conn->prepare($insert_onibus);

        foreach ($onibus_exemplo as $onibus) {
            $stmt_onibus->bind_param('issii', $onibus[0], $onibus[1], $onibus[2], $onibus[3], $evento_id);
            if ($stmt_onibus->execute()) {
                echo "✓ Ônibus {$onibus[1]} {$onibus[0]} criado com sucesso\n";
            } else {
                echo "✗ Erro ao criar ônibus {$onibus[1]} {$onibus[0]}: " . $conn->error . "\n";
            }
        }
    } else {
        echo "✗ Erro ao criar evento de exemplo: " . $conn->error . "\n";
    }
} else {
    echo "✓ Já existem $total_eventos eventos cadastrados\n";
}

echo "\n--- Verificação Final ---\n";

// Verificar estrutura das tabelas
$tables_to_check = ['eventos', 'onibus', 'alunos', 'alocacoes_onibus'];

foreach ($tables_to_check as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✓ Tabela '$table' existe\n";
    } else {
        echo "✗ Tabela '$table' não encontrada\n";
    }
}

echo "\n</pre>";
echo "<div class='alert alert-success'>";
echo "<h4>Atualização concluída!</h4>";
echo "<p>As novas funcionalidades estão disponíveis:</p>";
echo "<ul>";
echo "<li><a href='eventos.php' class='alert-link'>Gerenciar Eventos</a></li>";
echo "<li><a href='onibus.php' class='alert-link'>Gerenciar Ônibus</a></li>";
echo "<li><a href='alocacao.php' class='alert-link'>Alocação Automática</a></li>";
echo "</ul>";
echo "</div>";

$conn->close();
?>
