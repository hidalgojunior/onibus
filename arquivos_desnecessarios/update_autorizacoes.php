<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Autorizações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h1 class="card-title mb-0">Atualizar Autorizações</h1>
                        <small>Atualize a estrutura das tabelas para a versão mais recente</small>
                    </div>
                    <div class="card-body">
                        <p class="alert alert-info">
                            Esta página permite atualizar a estrutura do banco de dados para incluir novas funcionalidades,
                            como a geração de autorizações.
                        </p>

                        <form method="POST">
                            <button type="submit" name="update_autorizacoes" class="btn btn-warning">
                                Atualizar Tabela de Autorizações
                            </button>
                            <a href="index.php" class="btn btn-secondary">Voltar ao Início</a>
                        </form>

                        <?php
                        if (isset($_POST['update_autorizacoes'])) {
                            include 'config.php';
                            $conn = getDatabaseConnection();

                            if ($conn) {
                                echo '<div class="mt-3">';

                                // Verificar se a tabela autorizacoes existe
                                $result = $conn->query("SHOW TABLES LIKE 'autorizacoes'");
                                if ($result->num_rows == 0) {
                                    // Criar tabela se não existir
                                    $sql = "CREATE TABLE autorizacoes (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        aluno_id INT NOT NULL,
                                        responsavel_id INT,
                                        tipo ENUM('saida', 'uso_imagem') NOT NULL,
                                        conteudo TEXT NOT NULL,
                                        data_geracao DATETIME NOT NULL,
                                        FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
                                        FOREIGN KEY (responsavel_id) REFERENCES responsaveis(id) ON DELETE CASCADE,
                                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                    )";

                                    if ($conn->query($sql) === TRUE) {
                                        echo '<div class="alert alert-success">Tabela autorizacoes criada com sucesso!</div>';
                                    } else {
                                        echo '<div class="alert alert-danger">Erro ao criar tabela: ' . $conn->error . '</div>';
                                    }
                                } else {
                                    // Verificar estrutura atual
                                    $result = $conn->query("DESCRIBE autorizacoes");
                                    $columns = [];
                                    while ($row = $result->fetch_assoc()) {
                                        $columns[] = $row['Field'];
                                    }

                                    // Se não tem as colunas novas, recriar a tabela
                                    if (!in_array('tipo', $columns) || !in_array('conteudo', $columns)) {
                                        // Fazer backup dos dados existentes se houver
                                        $backup_sql = "CREATE TABLE autorizacoes_backup AS SELECT * FROM autorizacoes";
                                        $conn->query($backup_sql);

                                        // Dropar e recriar tabela
                                        $conn->query("DROP TABLE autorizacoes");

                                        $sql = "CREATE TABLE autorizacoes (
                                            id INT AUTO_INCREMENT PRIMARY KEY,
                                            aluno_id INT NOT NULL,
                                            responsavel_id INT,
                                            tipo ENUM('saida', 'uso_imagem') NOT NULL,
                                            conteudo TEXT NOT NULL,
                                            data_geracao DATETIME NOT NULL,
                                            FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
                                            FOREIGN KEY (responsavel_id) REFERENCES responsaveis(id) ON DELETE CASCADE,
                                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                        )";

                                        if ($conn->query($sql) === TRUE) {
                                            echo '<div class="alert alert-success">Tabela autorizacoes atualizada com sucesso!</div>';
                                            echo '<div class="alert alert-info">Dados antigos foram salvos em autorizacoes_backup.</div>';
                                        } else {
                                            echo '<div class="alert alert-danger">Erro ao atualizar tabela: ' . $conn->error . '</div>';
                                        }
                                    } else {
                                        echo '<div class="alert alert-info">Tabela autorizacoes já está atualizada.</div>';
                                    }
                                }

                                // Verificar se tabela modelos_autorizacao existe
                                $result = $conn->query("SHOW TABLES LIKE 'modelos_autorizacao'");
                                if ($result->num_rows == 0) {
                                    $sql = "CREATE TABLE modelos_autorizacao (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        nome VARCHAR(255) NOT NULL,
                                        tipo ENUM('saida', 'uso_imagem') NOT NULL,
                                        conteudo TEXT NOT NULL,
                                        ativo BOOLEAN DEFAULT TRUE,
                                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                    )";

                                    if ($conn->query($sql) === TRUE) {
                                        echo '<div class="alert alert-success">Tabela modelos_autorizacao criada com sucesso!</div>';

                                        // Inserir modelos padrão
                                        $modelos = [
                                            [
                                                'nome' => 'Autorização de Saída Padrão',
                                                'tipo' => 'saida',
                                                'conteudo' => '<p>AUTORIZAÇÃO PARA SAÍDA DE ALUNO<br>DURANTE HORÁRIO DE AULA</p><p>Nome do Responsável: {{NomeResponsavel}}</p><p>RG do Responsável: {{RGResponsavel}}</p><p>Nome do Estudante: {{Estudante}}</p><p>RG e RM do Aluno: {{RG_RM}}</p><p>Curso / Turma: {{Curso_Turma}}</p><p>Motivo da Autorização: {{Motivo}}</p><p>( ) Saída Sozinho. ( ) Saída acompanhado de _________________________________</p><p>Data da Saída: ____/____/________. Horário de Saída: _____:______</p><p>_____________________________________________<br>Assinatura do Responsável Legal</p><p>Marília, SP: {{data}}. Recebido por: _______________________________</p>'
                                            ],
                                            [
                                                'nome' => 'Autorização de Uso de Imagem Padrão',
                                                'tipo' => 'uso_imagem',
                                                'conteudo' => '<p>AUTORIZAÇÃO PARA USO DE IMAGEM</p><p>Eu, {{NomeResponsavel}}, responsável pelo aluno {{Estudante}}, autorizo o uso de imagens e vídeos do meu filho(a) em materiais promocionais e registros do evento {{Evento}}.</p><p>Data: {{data}}</p><p>_____________________________________________<br>Assinatura do Responsável</p>'
                                            ]
                                        ];

                                        foreach ($modelos as $modelo) {
                                            $stmt = $conn->prepare("INSERT INTO modelos_autorizacao (nome, tipo, conteudo) VALUES (?, ?, ?)");
                                            $stmt->bind_param("sss", $modelo['nome'], $modelo['tipo'], $modelo['conteudo']);
                                            $stmt->execute();
                                        }

                                        echo '<div class="alert alert-success">Modelos de autorização padrão inseridos!</div>';
                                    } else {
                                        echo '<div class="alert alert-danger">Erro ao criar tabela modelos_autorizacao: ' . $conn->error . '</div>';
                                    }
                                } else {
                                    echo '<div class="alert alert-info">Tabela modelos_autorizacao já existe.</div>';
                                }

                                echo '</div>';
                            } else {
                                echo '<div class="alert alert-danger">Erro na conexão com o banco de dados.</div>';
                            }

                            $conn->close();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
