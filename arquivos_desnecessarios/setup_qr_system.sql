-- Sistema de QR Codes e Candidaturas para Eventos
-- Este script adiciona as tabelas necessárias para o sistema de QR Codes

-- Tabela para armazenar QR Codes e URLs encurtadas
CREATE TABLE IF NOT EXISTS qr_codes (
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
);

-- Tabela para armazenar candidaturas de alunos aos eventos
CREATE TABLE IF NOT EXISTS candidaturas_eventos (
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
);

-- Adicionar colunas na tabela eventos se não existirem
ALTER TABLE eventos
ADD COLUMN IF NOT EXISTS qr_code_url VARCHAR(500) NULL AFTER descricao,
ADD COLUMN IF NOT EXISTS short_code VARCHAR(20) NULL AFTER qr_code_url,
ADD COLUMN IF NOT EXISTS public_url VARCHAR(255) NULL AFTER short_code,
ADD COLUMN IF NOT EXISTS inscricao_aberta BOOLEAN DEFAULT TRUE AFTER public_url;

-- Inserir alguns dados de exemplo (opcional)
-- INSERT INTO qr_codes (short_code, short_url, qr_code_url, public_url, created_at)
-- VALUES ('teste123', 'https://posicionadosmarilia.com.br/inscricao/teste123',
--         'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=https://posicionadosmarilia.com.br/inscricao/teste123',
--         'https://posicionadosmarilia.com.br/inscricao/teste123', NOW());

COMMIT;</content>
<parameter name="filePath">c:\laragon\www\onibus\setup_qr_system.sql
