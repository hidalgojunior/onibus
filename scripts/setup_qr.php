<?php
include 'config.php';

$conn = getDatabaseConnection();

echo "Configurando sistema QR Code...\n";

// Criar tabela qr_codes
$sql1 = "CREATE TABLE IF NOT EXISTS qr_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NULL,
    short_code VARCHAR(20) UNIQUE NOT NULL,
    short_url VARCHAR(255) NOT NULL,
    original_url VARCHAR(255) NULL,
    qr_code_url VARCHAR(500) NOT NULL,
    public_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_evento (evento_id),
    INDEX idx_short_code (short_code)
)";

if($conn->query($sql1)) {
    echo "✓ Tabela qr_codes criada com sucesso\n";
} else {
    echo "✗ Erro ao criar tabela qr_codes: " . $conn->error . "\n";
}

// Criar tabela candidaturas_eventos
$sql2 = "CREATE TABLE IF NOT EXISTS candidaturas_eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    serie ENUM('1', '2', '3') NOT NULL,
    curso VARCHAR(100) NOT NULL,
    email VARCHAR(255) NULL,
    observacoes TEXT NULL,
    data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente', 'aprovada', 'reprovada', 'cancelada') DEFAULT 'pendente',
    observacao_admin TEXT NULL,
    data_avaliacao TIMESTAMP NULL,
    INDEX idx_evento (evento_id),
    INDEX idx_status (status),
    INDEX idx_data (data_candidatura)
)";

if($conn->query($sql2)) {
    echo "✓ Tabela candidaturas_eventos criada com sucesso\n";
} else {
    echo "✗ Erro ao criar tabela candidaturas_eventos: " . $conn->error . "\n";
}

// Verificar e adicionar colunas na tabela eventos
$columns_to_add = [
    'qr_code_url' => 'VARCHAR(500) NULL',
    'short_code' => 'VARCHAR(20) NULL',
    'public_url' => 'VARCHAR(255) NULL',
    'inscricao_aberta' => 'BOOLEAN DEFAULT TRUE'
];

// Verificar quais colunas já existem
$result = $conn->query("DESCRIBE eventos");
$existing_columns = [];
while ($row = $result->fetch_assoc()) {
    $existing_columns[] = $row['Field'];
}

foreach($columns_to_add as $column => $definition) {
    if (!in_array($column, $existing_columns)) {
        $sql = "ALTER TABLE eventos ADD COLUMN $column $definition";
        if ($column === 'qr_code_url') {
            $sql .= " AFTER descricao";
        } elseif ($column === 'short_code') {
            $sql .= " AFTER qr_code_url";
        } elseif ($column === 'public_url') {
            $sql .= " AFTER short_code";
        } elseif ($column === 'inscricao_aberta') {
            $sql .= " AFTER public_url";
        }

        if($conn->query($sql)) {
            echo "✓ Coluna $column adicionada com sucesso\n";
        } else {
            echo "✗ Erro ao adicionar coluna $column: " . $conn->error . "\n";
        }
    } else {
        echo "✓ Coluna $column já existe\n";
    }
}

echo "\nSistema QR Code configurado com sucesso!\n";
$conn->close();
?></content>
<parameter name="filePath">c:\laragon\www\onibus\setup_qr.php
