<?php
include '../config/config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    $conn = getDatabaseConnection();

    switch ($action) {
        case 'get_events':
            getEvents($conn);
            break;

        case 'get_event':
            getEvent($conn);
            break;

        case 'create_event':
            createEvent($conn);
            break;

        case 'update_event':
            updateEvent($conn);
            break;

        case 'remove_event':
            removeEvent($conn);
            break;

        case 'get_event_students':
            getEventStudents($conn);
            break;

        case 'generate_qr':
            generateQRCode($conn);
            break;

        case 'get_qr':
            getQRCode($conn);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function getEvents($conn) {
    $query = "SELECT e.*,
                     COUNT(DISTINCT a.id) as total_alunos,
                     COUNT(DISTINCT o.id) as total_onibus,
                     MAX(q.short_code) as short_code,
                     MAX(q.public_url) as public_url
              FROM eventos e
              LEFT JOIN alunos a ON a.evento_id = e.id
              LEFT JOIN onibus o ON o.evento_id = e.id
              LEFT JOIN qr_codes q ON q.evento_id = e.id
              GROUP BY e.id
              ORDER BY e.data_inicio DESC";

    $result = $conn->query($query);

    if ($result) {
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }

        echo json_encode(['success' => true, 'events' => $events], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao buscar eventos: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function getEvent($conn) {
    $evento_id = $_GET['evento_id'] ?? '';

    if (empty($evento_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "SELECT e.*,
                     COUNT(DISTINCT a.id) as total_alunos,
                     COUNT(DISTINCT o.id) as total_onibus,
                     q.short_code,
                     q.public_url
              FROM eventos e
              LEFT JOIN alunos a ON a.evento_id = e.id
              LEFT JOIN onibus o ON o.evento_id = e.id
              LEFT JOIN qr_codes q ON q.evento_id = e.id
              WHERE e.id = ?
              GROUP BY e.id";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $evento_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $event = $result->fetch_assoc();
            echo json_encode(['success' => true, 'event' => $event], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            echo json_encode(['success' => false, 'message' => 'Evento não encontrado'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao buscar evento: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    $stmt->close();
}

function createEvent($conn) {
    $nome = $_POST['nome'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';
    $local = $_POST['local'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    if (empty($nome) || empty($data_inicio) || empty($data_fim) || empty($local)) {
        echo json_encode(['success' => false, 'message' => 'Nome, datas e local são obrigatórios'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Verificar se as datas são válidas
    if (strtotime($data_fim) < strtotime($data_inicio)) {
        echo json_encode(['success' => false, 'message' => 'A data de fim deve ser posterior à data de início'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "INSERT INTO eventos (nome, data_inicio, data_fim, local, descricao) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssss', $nome, $data_inicio, $data_fim, $local, $descricao);

    if ($stmt->execute()) {
        $evento_id = $conn->insert_id;

        // Salvar alunos associados ao evento
        if (isset($_POST['alunos']) && is_array($_POST['alunos'])) {
            foreach ($_POST['alunos'] as $alunoData) {
                if (!empty($alunoData['nome']) && !empty($alunoData['telefone']) && !empty($alunoData['serie']) && !empty($alunoData['curso'])) {
                    $stmt_aluno = $conn->prepare("INSERT INTO alunos (nome, telefone, serie, curso, evento_id, data_cadastro) VALUES (?, ?, ?, ?, ?, NOW())");
                    $stmt_aluno->bind_param("ssssis", $alunoData['nome'], $alunoData['telefone'], $alunoData['serie'], $alunoData['curso'], $evento_id);
                    $stmt_aluno->execute();
                    $stmt_aluno->close();
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Evento e alunos cadastrados com sucesso!', 'evento_id' => $evento_id], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar evento: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function updateEvent($conn) {
    $evento_id = $_POST['evento_id'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';
    $local = $_POST['local'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    if (empty($evento_id) || empty($nome) || empty($data_inicio) || empty($data_fim) || empty($local)) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Verificar se as datas são válidas
    if (strtotime($data_fim) < strtotime($data_inicio)) {
        echo json_encode(['success' => false, 'message' => 'A data de fim deve ser posterior à data de início'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "UPDATE eventos SET nome = ?, data_inicio = ?, data_fim = ?, local = ?, descricao = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssssi', $nome, $data_inicio, $data_fim, $local, $descricao, $evento_id);

    if ($stmt->execute()) {
        // Remover alunos existentes do evento
        $delete_students = "DELETE FROM alunos WHERE evento_id = ?";
        $delete_stmt = $conn->prepare($delete_students);
        $delete_stmt->bind_param('i', $evento_id);
        $delete_stmt->execute();
        $delete_stmt->close();

        // Salvar alunos associados ao evento (se houver)
        if (isset($_POST['alunos']) && is_array($_POST['alunos'])) {
            foreach ($_POST['alunos'] as $alunoData) {
                if (!empty($alunoData['nome']) && !empty($alunoData['telefone']) && !empty($alunoData['serie']) && !empty($alunoData['curso'])) {
                    $stmt_aluno = $conn->prepare("INSERT INTO alunos (nome, telefone, serie, curso, evento_id, data_cadastro) VALUES (?, ?, ?, ?, ?, NOW())");
                    $stmt_aluno->bind_param("ssssis", $alunoData['nome'], $alunoData['telefone'], $alunoData['serie'], $alunoData['curso'], $evento_id);
                    $stmt_aluno->execute();
                    $stmt_aluno->close();
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Evento atualizado com sucesso'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar evento: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function removeEvent($conn) {
    $evento_id = $_POST['evento_id'] ?? '';

    if (empty($evento_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Remover alocações primeiro
    $delete_allocations = "DELETE FROM alocacoes_onibus WHERE onibus_id IN (SELECT id FROM onibus WHERE evento_id = ?)";
    $alloc_stmt = $conn->prepare($delete_allocations);
    $alloc_stmt->bind_param('i', $evento_id);
    $alloc_stmt->execute();

    // Remover ônibus
    $delete_buses = "DELETE FROM onibus WHERE evento_id = ?";
    $bus_stmt = $conn->prepare($delete_buses);
    $bus_stmt->bind_param('i', $evento_id);
    $bus_stmt->execute();

    // Remover presenças
    $delete_presences = "DELETE FROM presencas WHERE aluno_id IN (SELECT id FROM alunos WHERE evento_id = ?)";
    $pres_stmt = $conn->prepare($delete_presences);
    $pres_stmt->bind_param('i', $evento_id);
    $pres_stmt->execute();

    // Remover alunos
    $delete_students = "DELETE FROM alunos WHERE evento_id = ?";
    $stud_stmt = $conn->prepare($delete_students);
    $stud_stmt->bind_param('i', $evento_id);
    $stud_stmt->execute();

    // Remover o evento
    $query = "DELETE FROM eventos WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $evento_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Evento removido com sucesso'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao remover evento: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function getEventStudents($conn) {
    $evento_id = $_GET['evento_id'] ?? '';

    if (empty($evento_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "SELECT id, nome, telefone, serie, curso FROM alunos WHERE evento_id = ? ORDER BY nome";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $evento_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $students = [];

        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }

        echo json_encode(['success' => true, 'students' => $students], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao buscar alunos: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    $stmt->close();
}

function generateQRCode($conn) {
    $evento_id = $_POST['evento_id'] ?? '';

    if (empty($evento_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Verificar se o evento existe
    $check_event = $conn->prepare("SELECT nome FROM eventos WHERE id = ?");
    $check_event->bind_param('i', $evento_id);
    $check_event->execute();
    $result = $check_event->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Evento não encontrado'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $event = $result->fetch_assoc();
    $check_event->close();

    // Gerar código único
    $short_code = strtoupper(substr(md5($evento_id . time()), 0, 8));
    $public_url = "http://localhost/onibus/public/evento.php?code=" . $short_code;

    // Verificar se já existe QR code para este evento
    $check_qr = $conn->prepare("SELECT id FROM qr_codes WHERE evento_id = ?");
    $check_qr->bind_param('i', $evento_id);
    $check_qr->execute();
    $qr_result = $check_qr->get_result();

    if ($qr_result->num_rows > 0) {
        // Atualizar QR code existente
        $update_qr = $conn->prepare("UPDATE qr_codes SET short_code = ?, public_url = ?, data_criacao = NOW() WHERE evento_id = ?");
        $update_qr->bind_param('ssi', $short_code, $public_url, $evento_id);
        
        if ($update_qr->execute()) {
            echo json_encode(['success' => true, 'message' => 'QR Code atualizado com sucesso!', 'short_code' => $short_code, 'public_url' => $public_url], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar QR Code: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        
        $update_qr->close();
    } else {
        // Criar novo QR code
        $insert_qr = $conn->prepare("INSERT INTO qr_codes (evento_id, short_code, public_url, data_criacao) VALUES (?, ?, ?, NOW())");
        $insert_qr->bind_param('iss', $evento_id, $short_code, $public_url);
        
        if ($insert_qr->execute()) {
            echo json_encode(['success' => true, 'message' => 'QR Code gerado com sucesso!', 'short_code' => $short_code, 'public_url' => $public_url], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao gerar QR Code: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        
        $insert_qr->close();
    }

    $check_qr->close();
}

function getQRCode($conn) {
    $evento_id = $_GET['evento_id'] ?? '';

    if (empty($evento_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "SELECT q.*, e.nome as evento_nome FROM qr_codes q 
              JOIN eventos e ON e.id = q.evento_id 
              WHERE q.evento_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $evento_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $qr_data = $result->fetch_assoc();
            echo json_encode(['success' => true, 'qr_data' => $qr_data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            echo json_encode(['success' => false, 'message' => 'QR Code não encontrado'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao buscar QR Code: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    $stmt->close();
}
?>
