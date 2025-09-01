<?php
include 'config.php';

echo "=== DIAGNÓSTICO DO SISTEMA DE CANDIDATURAS ===\n\n";

try {
    $conn = getDatabaseConnection();
    echo "✅ Conexão com banco de dados: OK\n";

    // Verificar se tabela existe
    $result = $conn->query("SHOW TABLES LIKE 'candidaturas_eventos'");
    if ($result->num_rows > 0) {
        echo "✅ Tabela candidaturas_eventos: Existe\n";

        // Verificar estrutura da tabela
        $result = $conn->query("DESCRIBE candidaturas_eventos");
        echo "📋 Estrutura da tabela:\n";
        while ($row = $result->fetch_assoc()) {
            echo "  - {$row['Field']}: {$row['Type']}\n";
        }

        // Verificar se há dados
        $result = $conn->query("SELECT COUNT(*) as total FROM candidaturas_eventos");
        $row = $result->fetch_assoc();
        echo "📊 Total de candidaturas: {$row['total']}\n";

    } else {
        echo "❌ Tabela candidaturas_eventos: NÃO existe\n";
        echo "🔧 Criando tabela...\n";

        $sql = "CREATE TABLE candidaturas_eventos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            telefone VARCHAR(20),
            serie VARCHAR(50),
            curso VARCHAR(100),
            email VARCHAR(255),
            observacoes TEXT,
            evento_id INT,
            status ENUM('pendente', 'aprovada', 'reprovada', 'cancelada') DEFAULT 'pendente',
            observacao_admin TEXT,
            data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_avaliacao TIMESTAMP NULL,
            FOREIGN KEY (evento_id) REFERENCES eventos(id)
        )";

        if ($conn->query($sql)) {
            echo "✅ Tabela criada com sucesso!\n";
        } else {
            echo "❌ Erro ao criar tabela: " . $conn->error . "\n";
        }
    }

    $conn->close();

} catch (Exception $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
}

echo "\n=== FIM DO DIAGNÓSTICO ===\n";
?>
