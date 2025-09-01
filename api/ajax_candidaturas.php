<?php
header('Content-Type: application/json');
require_once '../config/database.php';

function response($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    if (!isset($_REQUEST['action'])) {
        response(false, 'Ação não especificada');
    }

    $action = $_REQUEST['action'];

    switch ($action) {
        case 'get_candidaturas':
            getCandidaturas();
            break;
        
        case 'get_candidatura':
            getCandidatura();
            break;
        
        case 'get_candidatura_detalhes':
            getCandidaturaDetalhes();
            break;
        
        case 'avaliar_candidatura':
            avaliarCandidatura();
            break;
        
        case 'add_candidatura_teste':
            addCandidaturaTeste();
            break;
        
        default:
            response(false, 'Ação inválida');
    }

} catch (Exception $e) {
    response(false, 'Erro no servidor: ' . $e->getMessage());
}

function getCandidaturas() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.*,
                e.nome as evento_nome,
                e.data_evento
            FROM candidaturas c
            LEFT JOIN eventos e ON c.evento_id = e.id
            ORDER BY c.data_candidatura DESC
        ");
        
        $stmt->execute();
        $candidaturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'candidaturas' => $candidaturas
        ]);
        
    } catch (PDOException $e) {
        response(false, 'Erro ao buscar candidaturas: ' . $e->getMessage());
    }
}

function getCandidatura() {
    global $pdo;
    
    if (!isset($_GET['id'])) {
        response(false, 'ID da candidatura não fornecido');
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.*,
                e.nome as evento_nome,
                e.data_evento
            FROM candidaturas c
            LEFT JOIN eventos e ON c.evento_id = e.id
            WHERE c.id = ?
        ");
        
        $stmt->execute([$_GET['id']]);
        $candidatura = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$candidatura) {
            response(false, 'Candidatura não encontrada');
        }
        
        echo json_encode([
            'success' => true,
            'candidatura' => $candidatura
        ]);
        
    } catch (PDOException $e) {
        response(false, 'Erro ao buscar candidatura: ' . $e->getMessage());
    }
}

function getCandidaturaDetalhes() {
    global $pdo;
    
    if (!isset($_GET['id'])) {
        response(false, 'ID da candidatura não fornecido');
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.*,
                e.nome as evento_nome,
                e.data_evento,
                e.descricao as evento_descricao
            FROM candidaturas c
            LEFT JOIN eventos e ON c.evento_id = e.id
            WHERE c.id = ?
        ");
        
        $stmt->execute([$_GET['id']]);
        $candidatura = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$candidatura) {
            response(false, 'Candidatura não encontrada');
        }
        
        echo json_encode([
            'success' => true,
            'candidatura' => $candidatura
        ]);
        
    } catch (PDOException $e) {
        response(false, 'Erro ao buscar detalhes da candidatura: ' . $e->getMessage());
    }
}

function avaliarCandidatura() {
    global $pdo;
    
    if (!isset($_POST['candidatura_id']) || !isset($_POST['status'])) {
        response(false, 'Dados incompletos para avaliação');
    }
    
    $candidatura_id = $_POST['candidatura_id'];
    $status = $_POST['status'];
    $observacoes = $_POST['observacoes'] ?? '';
    $motivo_rejeicao = $_POST['motivo_rejeicao'] ?? '';
    
    // Validar status
    if (!in_array($status, ['aprovado', 'rejeitado', 'pendente'])) {
        response(false, 'Status inválido');
    }
    
    // Se rejeitado, motivo é obrigatório
    if ($status === 'rejeitado' && empty($motivo_rejeicao)) {
        response(false, 'Motivo da rejeição é obrigatório');
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE candidaturas 
            SET status = ?, 
                observacoes = ?, 
                motivo_rejeicao = ?,
                data_avaliacao = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([$status, $observacoes, $motivo_rejeicao, $candidatura_id]);
        
        if ($stmt->rowCount() > 0) {
            response(true, 'Candidatura avaliada com sucesso');
        } else {
            response(false, 'Candidatura não encontrada ou nenhuma alteração feita');
        }
        
    } catch (PDOException $e) {
        response(false, 'Erro ao avaliar candidatura: ' . $e->getMessage());
    }
}

function addCandidaturaTeste() {
    global $pdo;
    
    try {
        // Verificar se existe pelo menos um evento
        $stmt = $pdo->prepare("SELECT id FROM eventos ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$evento) {
            response(false, 'Nenhum evento encontrado. Crie um evento primeiro.');
        }
        
        // Dados de teste aleatórios
        $nomes_teste = [
            'João Silva', 'Maria Santos', 'Pedro Oliveira', 'Ana Costa',
            'Carlos Souza', 'Lucia Ferreira', 'Roberto Lima', 'Patricia Gomes'
        ];
        
        $cursos_teste = [
            'Informática', 'Administração', 'Eletrônica', 'Mecânica',
            'Enfermagem', 'Contabilidade', 'Marketing', 'Logística'
        ];
        
        $nome = $nomes_teste[array_rand($nomes_teste)];
        $curso = $cursos_teste[array_rand($cursos_teste)];
        $serie = rand(1, 3);
        $telefone = '(11) ' . rand(90000, 99999) . '-' . rand(1000, 9999);
        
        $stmt = $pdo->prepare("
            INSERT INTO candidaturas (
                evento_id, nome, telefone, serie, curso, 
                status, data_candidatura
            ) VALUES (?, ?, ?, ?, ?, 'pendente', NOW())
        ");
        
        $stmt->execute([
            $evento['id'], $nome, $telefone, $serie, $curso
        ]);
        
        response(true, 'Candidatura de teste adicionada com sucesso');
        
    } catch (PDOException $e) {
        response(false, 'Erro ao adicionar candidatura de teste: ' . $e->getMessage());
    }
}
?>

    if ($result) {
        $candidaturas = [];
        while ($row = $result->fetch_assoc()) {
            // Sanitizar dados para evitar problemas com JSON
            $candidatura = [];
            foreach ($row as $key => $value) {
                if ($value !== null) {
                    // Converter para UTF-8 e escapar caracteres especiais
                    $candidatura[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                } else {
                    $candidatura[$key] = null;
                }
            }
            $candidaturas[] = $candidatura;
        }

        echo json_encode(['success' => true, 'candidaturas' => $candidaturas], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao buscar candidaturas: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function avaliarCandidatura($conn) {
    $candidatura_id = $_POST['candidatura_id'] ?? '';
    $status = $_POST['status'] ?? '';
    $observacao_admin = $_POST['observacao_admin'] ?? '';

    if (empty($candidatura_id) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'ID da candidatura e status são obrigatórios'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $valid_statuses = ['aprovada', 'reprovada', 'cancelada'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Status inválido'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "UPDATE candidaturas_eventos
              SET status = ?, observacao_admin = ?, data_avaliacao = NOW()
              WHERE id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssi', $status, $observacao_admin, $candidatura_id);

    if ($stmt->execute()) {
        // Se aprovado, adicionar à tabela alunos
        if ($status === 'aprovada') {
            // Buscar dados da candidatura
            $candidatura_query = "SELECT * FROM candidaturas_eventos WHERE id = ?";
            $candidatura_stmt = $conn->prepare($candidatura_query);
            $candidatura_stmt->bind_param('i', $candidatura_id);
            $candidatura_stmt->execute();
            $candidatura_result = $candidatura_stmt->get_result();

            if ($candidatura_result->num_rows > 0) {
                $candidatura = $candidatura_result->fetch_assoc();

                // Inserir na tabela alunos
                $aluno_query = "INSERT INTO alunos (nome, telefone, serie, curso, evento_id, data_cadastro)
                               VALUES (?, ?, ?, ?, ?, NOW())";
                $aluno_stmt = $conn->prepare($aluno_query);
                $aluno_stmt->bind_param('sssssi',
                    $candidatura['nome'],
                    $candidatura['telefone'],
                    $candidatura['serie'],
                    $candidatura['curso'],
                    $candidatura['evento_id']
                );
                $aluno_stmt->execute();
                $aluno_stmt->close();
            }
            $candidatura_stmt->close();
        }

        echo json_encode(['success' => true, 'message' => 'Candidatura avaliada com sucesso!'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao avaliar candidatura: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    $stmt->close();
}

function getCandidatura($conn) {
    $candidatura_id = $_GET['id'] ?? '';

    if (empty($candidatura_id)) {
        echo json_encode(['success' => false, 'message' => 'ID da candidatura é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "SELECT c.*,
                     e.nome as evento_nome,
                     e.data_inicio,
                     e.data_fim,
                     e.local
              FROM candidaturas_eventos c
              LEFT JOIN eventos e ON c.evento_id = e.id
              WHERE c.id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $candidatura_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $candidatura = $result->fetch_assoc();
        // Sanitizar dados
        $candidatura_sanitizada = [];
        foreach ($candidatura as $key => $value) {
            $candidatura_sanitizada[$key] = $value !== null ? mb_convert_encoding($value, 'UTF-8', 'UTF-8') : null;
        }
        echo json_encode(['success' => true, 'candidatura' => $candidatura_sanitizada], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Candidatura não encontrada'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    $stmt->close();
}

function adicionarCandidaturaTeste($conn) {
    // Verificar se já existe evento
    $evento_query = "SELECT id FROM eventos WHERE nome LIKE '%Bootcamp%' LIMIT 1";
    $evento_result = $conn->query($evento_query);

    $evento_id = null;
    if ($evento_result->num_rows > 0) {
        $evento_row = $evento_result->fetch_assoc();
        $evento_id = $evento_row['id'];
    } else {
        // Criar evento de teste
        $conn->query("INSERT INTO eventos (nome, data_inicio, data_fim, local) VALUES ('Bootcamp Jovem Programador - Teste', '2025-08-27', '2025-09-05', 'Escola Municipal')");
        $evento_id = $conn->insert_id;
    }

    // Dados de teste
    $candidatos_teste = [
        ['João Silva', '11999999999', '1', 'Ensino Fundamental I', 'joao@email.com', 'Interessado em programação'],
        ['Maria Santos', '11888888888', '2', 'Ensino Fundamental II', 'maria@email.com', 'Gosta de tecnologia'],
        ['Pedro Oliveira', '11777777777', '3', 'Ensino Médio', 'pedro@email.com', 'Quer aprender desenvolvimento']
    ];

    $adicionados = 0;
    foreach ($candidatos_teste as $candidato) {
        $query = "INSERT INTO candidaturas_eventos (evento_id, nome, telefone, serie, curso, email, observacoes, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'pendente')";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('issssss', $evento_id, $candidato[0], $candidato[1], $candidato[2], $candidato[3], $candidato[4], $candidato[5]);

        if ($stmt->execute()) {
            $adicionados++;
        }
        $stmt->close();
    }

    if ($adicionados > 0) {
        echo json_encode(['success' => true, 'message' => $adicionados . ' candidaturas de teste adicionadas com sucesso!'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao adicionar candidaturas de teste'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function excluirCandidatura($conn) {
    $candidatura_id = $_POST['candidatura_id'] ?? '';

    if (empty($candidatura_id)) {
        echo json_encode(['success' => false, 'message' => 'ID da candidatura é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "DELETE FROM candidaturas_eventos WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $candidatura_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Candidatura excluída com sucesso!'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            echo json_encode(['success' => false, 'message' => 'Candidatura não encontrada'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir candidatura: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    $stmt->close();
}
?>
