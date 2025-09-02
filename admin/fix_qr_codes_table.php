<?php
include '../config/config.php';
$conn = getDatabaseConnection();

echo "Criando estrutura correta para QR Codes...\n\n";

// Primeiro, vamos renomear a tabela antiga se existir
$conn->query("RENAME TABLE qr_codes TO qr_codes_old");

// Criar nova tabela qr_codes
$create_table = "
CREATE TABLE qr_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    aluno_id INT NOT NULL UNIQUE,
    codigo_qr VARCHAR(255) NOT NULL UNIQUE,
    data_geracao DATETIME NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    validade DATETIME,
    tentativas_uso INT DEFAULT 0,
    ultimo_uso DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE
)";

if ($conn->query($create_table)) {
    echo "✅ Tabela qr_codes criada com sucesso!\n";
} else {
    echo "❌ Erro ao criar tabela: " . $conn->error . "\n";
}

// Gerar QR Codes para alunos existentes
$gerar_qr = "
INSERT INTO qr_codes (aluno_id, codigo_qr, data_geracao, ativo, validade)
SELECT 
    id, 
    CONCAT('ALN-', LPAD(id, 5, '0'), '-', UPPER(SUBSTRING(MD5(CONCAT(id, nome, NOW())), 1, 8))),
    NOW(),
    TRUE,
    DATE_ADD(NOW(), INTERVAL 1 YEAR)
FROM alunos
";

if ($conn->query($gerar_qr)) {
    echo "✅ QR Codes gerados para todos os alunos!\n";
} else {
    echo "❌ Erro ao gerar QR Codes: " . $conn->error . "\n";
}

// Verificar quantos foram criados
$result = $conn->query("SELECT COUNT(*) as total FROM qr_codes");
$total = $result->fetch_assoc()['total'];
echo "📊 Total de QR Codes criados: $total\n";

$conn->close();
?>
