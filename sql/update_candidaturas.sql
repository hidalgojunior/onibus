-- Atualização do banco de dados para o sistema de candidaturas
-- Data: <?php echo date('Y-m-d H:i:s'); ?>

-- Criar tabela de candidaturas se não existir
CREATE TABLE IF NOT EXISTS candidaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    serie INT NOT NULL,
    curso VARCHAR(100) NOT NULL,
    status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
    observacoes TEXT,
    motivo_rejeicao VARCHAR(255),
    data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_avaliacao TIMESTAMP NULL,
    INDEX idx_evento_id (evento_id),
    INDEX idx_status (status),
    INDEX idx_data_candidatura (data_candidatura),
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar colunas se não existirem (para casos de upgrade)
ALTER TABLE candidaturas 
ADD COLUMN IF NOT EXISTS motivo_rejeicao VARCHAR(255) AFTER observacoes,
ADD COLUMN IF NOT EXISTS data_avaliacao TIMESTAMP NULL AFTER data_candidatura;

-- Atualizar índices se necessário
CREATE INDEX IF NOT EXISTS idx_evento_status ON candidaturas(evento_id, status);

-- Inserir dados de exemplo para testes (apenas se não houver dados)
INSERT IGNORE INTO candidaturas (evento_id, nome, telefone, serie, curso, status, data_candidatura) 
SELECT 
    e.id,
    'João Silva',
    '(11) 98765-4321',
    2,
    'Informática',
    'pendente',
    NOW() - INTERVAL 1 DAY
FROM eventos e 
WHERE e.id = (SELECT MIN(id) FROM eventos)
LIMIT 1;

INSERT IGNORE INTO candidaturas (evento_id, nome, telefone, serie, curso, status, observacoes, data_candidatura) 
SELECT 
    e.id,
    'Maria Santos',
    '(11) 99887-7654',
    3,
    'Administração',
    'aprovado',
    'Candidata aprovada com excelente desempenho',
    NOW() - INTERVAL 2 DAY
FROM eventos e 
WHERE e.id = (SELECT MIN(id) FROM eventos)
LIMIT 1;

INSERT IGNORE INTO candidaturas (evento_id, nome, telefone, serie, curso, status, motivo_rejeicao, data_candidatura) 
SELECT 
    e.id,
    'Pedro Oliveira',
    '(11) 91234-5678',
    1,
    'Eletrônica',
    'rejeitado',
    'Idade inadequada para o evento',
    NOW() - INTERVAL 3 DAY
FROM eventos e 
WHERE e.id = (SELECT MIN(id) FROM eventos)
LIMIT 1;
