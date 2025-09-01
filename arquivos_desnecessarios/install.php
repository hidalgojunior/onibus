<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador do Sistema de Gerenciamento de Alunos em Ônibus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h1 class="card-title mb-0">Instalador do Banco de Dados</h1>
                    </div>
                    <div class="card-body">
                        <p>Preencha os dados de conexão com o MySQL para instalar o banco de dados do sistema.</p>

                        <?php
                        // Incluir arquivo de configuração
                        include 'config.php';

                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            $host = $_POST['host'];
                            $usuario = $_POST['usuario'];
                            $senha = $_POST['senha'];
                            $banco = $_POST['banco'];

                            // Conectar ao MySQL
                            $conn = new mysqli($host, $usuario, $senha);                    if ($conn->connect_error) {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">Erro de conexão: ' . $conn->connect_error . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                    } else {
                        // Gerar SQL dinamicamente com o nome do banco
                        $sql = "
-- Script de Instalação do Banco de Dados para o Sistema de Gerenciamento de Alunos em Ônibus
-- Este script apaga o banco existente '$banco' e o recria com todas as tabelas necessárias

-- Apagar o banco se existir
// DROP DATABASE IF EXISTS `$banco`;

// -- Criar o banco
// CREATE DATABASE `$banco` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar o banco
USE `$banco`;

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
                        ";

                        // Dividir o SQL em comandos individuais
                        $commands = array_filter(array_map('trim', explode(';', $sql)));

                        $success = true;
                        $errors = [];

                        foreach ($commands as $command) {
                            if (!empty($command)) {
                                if ($conn->query($command) === FALSE) {
                                    $success = false;
                                    $errors[] = $conn->error;
                                }
                            }
                        }

                        if ($success) {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Banco de dados "' . $banco . '" instalado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        } else {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">Erros durante a instalação:<ul class="mb-0 mt-2">';
                            foreach ($errors as $error) {
                                echo '<li>' . $error . '</li>';
                            }
                            echo '</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        }
                    }

                    $conn->close();
                }
                ?>

                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="host" class="form-label">Host do Servidor MySQL</label>
                                    <input type="text" class="form-control" id="host" name="host" placeholder="localhost" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="usuario" class="form-label">Usuário</label>
                                    <input type="text" class="form-control" id="usuario" name="usuario" placeholder="root" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="senha" class="form-label">Senha</label>
                                    <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite a senha">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="banco" class="form-label">Nome do Banco de Dados</label>
                                    <input type="text" class="form-control" id="banco" name="banco" placeholder="onibus" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Instalar Banco de Dados</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
