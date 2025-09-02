-- ============================================
-- SCRIPT DE ATUALIZAÇÃO DO BANCO DE DADOS
-- Sistema de Controle de Presenças em Ônibus
-- ============================================

USE `onibus`;

-- 1. ATUALIZAR TABELA ALUNOS - Adicionar dados dos responsáveis
ALTER TABLE alunos 
ADD COLUMN responsavel_nome VARCHAR(255) AFTER whatsapp_permissao,
ADD COLUMN responsavel_telefone VARCHAR(50) AFTER responsavel_nome,
ADD COLUMN responsavel_whatsapp VARCHAR(50) AFTER responsavel_telefone,
ADD COLUMN telefone_emergencia VARCHAR(50) AFTER responsavel_whatsapp,
ADD COLUMN endereco_completo TEXT AFTER telefone_emergencia,
ADD COLUMN ponto_embarque VARCHAR(255) AFTER endereco_completo,
ADD COLUMN observacoes_medicas TEXT AFTER ponto_embarque,
ADD COLUMN autorizacao_transporte BOOLEAN DEFAULT TRUE AFTER observacoes_medicas,
ADD COLUMN foto_perfil VARCHAR(255) AFTER autorizacao_transporte;

-- 2. ATUALIZAR TABELA ONIBUS - Remover dependência de eventos e adicionar dados operacionais
ALTER TABLE onibus 
DROP FOREIGN KEY onibus_ibfk_1,
DROP COLUMN evento_id,
DROP COLUMN dias_reservados,
ADD COLUMN placa VARCHAR(10) AFTER numero,
ADD COLUMN motorista_nome VARCHAR(255) AFTER capacidade,
ADD COLUMN monitor_nome VARCHAR(255) AFTER motorista_nome,
ADD COLUMN rota_descricao TEXT AFTER monitor_nome,
ADD COLUMN turno ENUM('matutino', 'vespertino', 'ambos') DEFAULT 'ambos' AFTER rota_descricao,
ADD COLUMN ativo BOOLEAN DEFAULT TRUE AFTER turno;

-- 3. ATUALIZAR TABELA ALOCACOES_ONIBUS - Focar em rotas e turnos
ALTER TABLE alocacoes_onibus 
DROP FOREIGN KEY alocacoes_onibus_ibfk_3,
DROP COLUMN evento_id,
ADD COLUMN ponto_embarque VARCHAR(255) AFTER onibus_id,
ADD COLUMN horario_embarque TIME AFTER ponto_embarque,
ADD COLUMN turno ENUM('matutino', 'vespertino') NOT NULL AFTER horario_embarque,
ADD COLUMN ativo BOOLEAN DEFAULT TRUE AFTER turno,
ADD COLUMN observacoes TEXT AFTER ativo,
DROP INDEX UNIQUE,
ADD UNIQUE KEY unique_aluno_turno (aluno_id, turno);

-- 4. CRIAR TABELA QR_CODES para QR individuais dos alunos
CREATE TABLE IF NOT EXISTS qr_codes (
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
);

-- 5. ATUALIZAR TABELA PRESENCAS - Focar em embarque/desembarque diário
ALTER TABLE presencas 
DROP FOREIGN KEY presencas_ibfk_2,
DROP COLUMN evento_id,
ADD COLUMN onibus_id INT AFTER aluno_id,
ADD COLUMN tipo_registro ENUM('embarque', 'desembarque') NOT NULL AFTER data,
ADD COLUMN qr_code_usado VARCHAR(255) AFTER presenca_retorno,
ADD COLUMN localizacao_gps VARCHAR(100) AFTER qr_code_usado,
ADD COLUMN monitor_responsavel VARCHAR(100) AFTER localizacao_gps,
ADD COLUMN horario_registro TIME AFTER monitor_responsavel,
DROP INDEX UNIQUE,
ADD UNIQUE KEY unique_presenca_diaria (aluno_id, data, tipo_registro),
ADD FOREIGN KEY (onibus_id) REFERENCES onibus(id) ON DELETE SET NULL;

-- 6. ATUALIZAR TABELA AUTORIZACOES - Simplificar para transporte e WhatsApp
ALTER TABLE autorizacoes 
DROP FOREIGN KEY autorizacoes_ibfk_2,
DROP COLUMN responsavel_id,
MODIFY COLUMN tipo ENUM('transporte', 'whatsapp', 'emergencia') NOT NULL,
ADD COLUMN autorizado_por VARCHAR(255) AFTER tipo,
ADD COLUMN data_autorizacao DATETIME NOT NULL AFTER autorizado_por,
ADD COLUMN validade DATE AFTER data_autorizacao,
ADD COLUMN ativo BOOLEAN DEFAULT TRUE AFTER validade;

-- 7. INSERIR DADOS PADRÃO

-- Inserir dados básicos de ônibus se não existirem
INSERT IGNORE INTO onibus (numero, tipo, capacidade, motorista_nome, monitor_nome, rota_descricao, turno, ativo) VALUES
('001', 'ônibus', 45, 'João Silva', 'Maria Santos', 'Rota Centro - Bairro Sul', 'ambos', TRUE),
('002', 'ônibus', 45, 'Carlos Oliveira', 'Ana Costa', 'Rota Norte - Leste', 'ambos', TRUE),
('003', 'van', 15, 'Pedro Almeida', 'Lucia Ferreira', 'Rota Oeste - Centro', 'matutino', TRUE);

-- 8. GERAR QR CODES para alunos existentes (se houver)
INSERT IGNORE INTO qr_codes (aluno_id, codigo_qr, data_geracao, ativo, validade)
SELECT 
    id, 
    CONCAT('ALN-', LPAD(id, 5, '0'), '-', UPPER(SUBSTRING(MD5(CONCAT(id, nome, NOW())), 1, 8))),
    NOW(),
    TRUE,
    DATE_ADD(NOW(), INTERVAL 1 YEAR)
FROM alunos;

-- 9. CRIAR ÍNDICES PARA PERFORMANCE
CREATE INDEX idx_presencas_data ON presencas(data);
CREATE INDEX idx_presencas_aluno_data ON presencas(aluno_id, data);
CREATE INDEX idx_qr_codes_codigo ON qr_codes(codigo_qr);
CREATE INDEX idx_alocacoes_turno ON alocacoes_onibus(turno);

-- ============================================
-- SCRIPT CONCLUÍDO
-- ============================================
