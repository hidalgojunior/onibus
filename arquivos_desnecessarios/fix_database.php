<?php
include 'config.php';

$conn = getDatabaseConnection();

if ($conn) {
    echo "Conectado ao banco de dados.<br>";

    // Verificar se as colunas existem
    $result = $conn->query("DESCRIBE autorizacoes");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    if (!in_array('tipo', $columns)) {
        echo "Adicionando coluna 'tipo'...<br>";
        $conn->query("ALTER TABLE autorizacoes ADD COLUMN tipo ENUM('saida', 'uso_imagem') NOT NULL AFTER responsavel_id");
    }

    if (!in_array('conteudo', $columns)) {
        echo "Adicionando coluna 'conteudo'...<br>";
        $conn->query("ALTER TABLE autorizacoes ADD COLUMN conteudo TEXT NOT NULL AFTER tipo");
    }

    if (!in_array('data_geracao', $columns)) {
        echo "Adicionando coluna 'data_geracao'...<br>";
        $conn->query("ALTER TABLE autorizacoes ADD COLUMN data_geracao DATETIME NOT NULL AFTER conteudo");
    }

    // Verificar se tabela modelos_autorizacao existe
    $result = $conn->query("SHOW TABLES LIKE 'modelos_autorizacao'");
    if ($result->num_rows == 0) {
        echo "Criando tabela modelos_autorizacao...<br>";
        $sql = "CREATE TABLE modelos_autorizacao (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            tipo ENUM('saida', 'uso_imagem') NOT NULL,
            conteudo TEXT NOT NULL,
            ativo BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->query($sql);

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
        echo "Modelos padrão inseridos.<br>";
    }

    echo "Banco de dados atualizado com sucesso!<br>";
} else {
    echo "Erro na conexão com o banco de dados.<br>";
}

$conn->close();
?>
