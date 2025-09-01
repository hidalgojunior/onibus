-- Script de Instalação do Banco de Dados para o Sistema de Gerenciamento de Alunos em Ônibus
-- Este script apaga o banco existente 'onibus' e o recria com todas as tabelas necessárias

-- Apagar o banco se existir
DROP DATABASE IF EXISTS onibus;

-- Criar o banco
CREATE DATABASE onibus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar o banco
USE onibus;

-- Tabela de Eventos
CREATE TABLE eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Alunos
CREATE TABLE alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    rg VARCHAR(20),
    rm VARCHAR(20) UNIQUE,
    serie VARCHAR(50),
    curso VARCHAR(100),
    telefone VARCHAR(50),
    data_aniversario DATE,
    whatsapp_permissao BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Responsáveis
CREATE TABLE responsaveis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    rg VARCHAR(20),
    email VARCHAR(255),
    telefone VARCHAR(50),
    relacao_aluno VARCHAR(50), -- Ex: pai, mãe, responsável
    aluno_id INT NOT NULL,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Ônibus/Veículos
CREATE TABLE onibus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(10) NOT NULL UNIQUE,
    tipo ENUM('ônibus', 'van', 'carro') NOT NULL,
    capacidade INT NOT NULL,
    evento_id INT NOT NULL,
    dias_reservados INT NOT NULL, -- Número de dias reservados
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE
);

-- Tabela de Alocações de Alunos nos Ônibus
CREATE TABLE alocacoes_onibus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    onibus_id INT NOT NULL,
    evento_id INT NOT NULL,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (onibus_id) REFERENCES onibus(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(aluno_id, evento_id) -- Um aluno por evento
);

-- Tabela de Presenças
CREATE TABLE presencas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    evento_id INT NOT NULL,
    data DATE NOT NULL,
    presenca_embarque BOOLEAN DEFAULT FALSE,
    presenca_retorno BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(aluno_id, evento_id, data) -- Uma presença por aluno, evento e data
);

-- Tabela de Autorizações
CREATE TABLE autorizacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    responsavel_id INT,
    tipo ENUM('saida', 'uso_imagem') NOT NULL,
    conteudo TEXT NOT NULL,
    data_geracao DATETIME NOT NULL,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (responsavel_id) REFERENCES responsaveis(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Modelos de Autorização
CREATE TABLE modelos_autorizacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    tipo ENUM('saida', 'uso_imagem') NOT NULL,
    conteudo TEXT NOT NULL, -- Conteúdo do modelo em HTML ou texto
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserir modelo padrão de autorização de saída (baseado no exemplo fornecido)
INSERT INTO modelos_autorizacao (nome, tipo, conteudo) VALUES (
    'Autorização de Saída Padrão',
    'saida',
    '<p>AUTORIZAÇÃO PARA SAÍDA DE ALUNO<br>DURANTE HORÁRIO DE AULA</p><p>Nome do Responsável: {{NomeResponsavel}}</p><p>RG do Responsável: {{RGResponsavel}}</p><p>Nome do Estudante: {{Estudante}}</p><p>RG e RM do Aluno: {{RG_RM}}</p><p>Curso / Turma: {{Curso_Turma}}</p><p>Motivo da Autorização: {{Motivo}}</p><p>( ) Saída Sozinho. ( ) Saída acompanhado de _________________________________</p><p>Data da Saída: ____/____/________. Horário de Saída: _____:______</p><p>_____________________________________________<br>Assinatura do Responsável Legal</p><p>Marília, SP: {{data}}. Recebido por: _______________________________</p>'
);

-- Inserir modelo padrão de autorização de uso de imagem
INSERT INTO modelos_autorizacao (nome, tipo, conteudo) VALUES (
    'Autorização de Uso de Imagem Padrão',
    'uso_imagem',
    '<p>AUTORIZAÇÃO PARA USO DE IMAGEM</p><p>Eu, {{NomeResponsavel}}, responsável pelo aluno {{Estudante}}, autorizo o uso de imagens e vídeos do meu filho(a) em materiais promocionais e registros do evento {{Evento}}.</p><p>Data: {{data}}</p><p>_____________________________________________<br>Assinatura do Responsável</p>'
);

-- Índices para otimização
CREATE INDEX idx_alunos_rm ON alunos(rm);
CREATE INDEX idx_responsaveis_aluno ON responsaveis(aluno_id);
CREATE INDEX idx_onibus_evento ON onibus(evento_id);
CREATE INDEX idx_alocacoes_aluno ON alocacoes_onibus(aluno_id);
CREATE INDEX idx_presencas_aluno ON presencas(aluno_id);
CREATE INDEX idx_autorizacoes_aluno ON autorizacoes(aluno_id);
