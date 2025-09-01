<?php
include 'config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

try {
    $conn = getDatabaseConnection();

    switch ($action) {
        case 'generate_qr':
            generateQRCode($conn);
            break;

        case 'get_short_url':
            getShortUrl($conn);
            break;

        case 'create_short_url':
            createShortUrl($conn);
            break;

        case 'public_form':
            showPublicForm($conn);
            break;

        case 'submit_candidatura':
            submitCandidatura($conn);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function generateQRCode($conn) {
    $evento_id = $_GET['evento_id'] ?? '';

    if (empty($evento_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Verificar se já existe QR Code para este evento
    $query = "SELECT * FROM qr_codes WHERE evento_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $evento_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $qr_data = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'qr_code' => $qr_data['qr_code_url'],
            'short_url' => $qr_data['short_url'],
            'public_url' => 'https://posicionadosmarilia.com.br/inscricao/' . $qr_data['short_code']
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Gerar código curto único
    $short_code = generateShortCode();

    // URL do formulário público
    $public_url = 'https://posicionadosmarilia.com.br/inscricao/' . $short_code;

    // Gerar QR Code usando Google Charts API
    $qr_code_url = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($public_url);

    // Salvar no banco
    $insert_query = "INSERT INTO qr_codes (evento_id, short_code, short_url, qr_code_url, public_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_query);
    $short_url = 'https://posicionadosmarilia.com.br/inscricao/' . $short_code;
    $insert_stmt->bind_param('issss', $evento_id, $short_code, $short_url, $qr_code_url, $public_url);

    if ($insert_stmt->execute()) {
        echo json_encode([
            'success' => true,
            'qr_code' => $qr_code_url,
            'short_url' => $short_url,
            'public_url' => $public_url,
            'short_code' => $short_code
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao gerar QR Code: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function generateShortCode($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';

    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $code;
}

function getShortUrl($conn) {
    $short_code = $_GET['code'] ?? '';

    if (empty($short_code)) {
        echo json_encode(['success' => false, 'message' => 'Código é obrigatório']);
        return;
    }

    $query = "SELECT * FROM qr_codes WHERE short_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $short_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $qr_data = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'evento_id' => $qr_data['evento_id'],
            'short_url' => $qr_data['short_url'],
            'public_url' => $qr_data['public_url']
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Código não encontrado'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function createShortUrl($conn) {
    $url = $_POST['url'] ?? '';
    $evento_id = $_POST['evento_id'] ?? null;

    if (empty($url)) {
        echo json_encode(['success' => false, 'message' => 'URL é obrigatória'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $short_code = generateShortCode();
    $short_url = 'https://posicionadosmarilia.com.br/inscricao/' . $short_code;

    $insert_query = "INSERT INTO qr_codes (evento_id, short_code, short_url, original_url, public_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_query);
    $public_url = 'https://posicionadosmarilia.com.br/inscricao/' . $short_code;
    $insert_stmt->bind_param('issss', $evento_id, $short_code, $short_url, $url, $public_url);

    if ($insert_stmt->execute()) {
        echo json_encode([
            'success' => true,
            'short_code' => $short_code,
            'short_url' => $short_url,
            'public_url' => $public_url
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar URL curta: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function showPublicForm($conn) {
    $short_code = $_GET['code'] ?? '';

    if (empty($short_code)) {
        echo json_encode(['success' => false, 'message' => 'Código de inscrição inválido'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "SELECT q.*, e.nome as evento_nome, e.data_inicio, e.data_fim, e.local, e.descricao
              FROM qr_codes q
              LEFT JOIN eventos e ON q.evento_id = e.id
              WHERE q.short_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $short_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'evento' => $data,
            'form_html' => generateFormHTML($data)
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Código de inscrição não encontrado'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function generateFormHTML($evento) {
    $data_inicio = date('d/m/Y', strtotime($evento['data_inicio']));
    $data_fim = date('d/m/Y', strtotime($evento['data_fim']));

    return '
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">📝 Inscrição para Evento</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h5>' . htmlspecialchars($evento['evento_nome']) . '</h5>
                <p><strong>Período:</strong> ' . $data_inicio . ' - ' . $data_fim . '</p>
                <p><strong>Local:</strong> ' . htmlspecialchars($evento['local']) . '</p>
                ' . (!empty($evento['descricao']) ? '<p><strong>Descrição:</strong> ' . htmlspecialchars($evento['descricao']) . '</p>' : '') . '
            </div>

            <form id="candidatura-form">
                <input type="hidden" name="evento_id" value="' . $evento['evento_id'] . '">
                <input type="hidden" name="short_code" value="' . $evento['short_code'] . '">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Telefone *</label>
                            <input type="tel" class="form-control" name="telefone" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Série *</label>
                            <select class="form-select" name="serie" required>
                                <option value="">Selecione a série</option>
                                <option value="1">1ª Série</option>
                                <option value="2">2ª Série</option>
                                <option value="3">3ª Série</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Curso *</label>
                            <select class="form-select" name="curso" required>
                                <option value="">Selecione o curso</option>
                                <option value="MTEC PI Desenvolvimento de Sistemas">MTEC PI Desenvolvimento de Sistemas</option>
                                <option value="Administração">Administração</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email (opcional)</label>
                    <input type="email" class="form-control" name="email">
                </div>

                <div class="mb-3">
                    <label class="form-label">Observações (opcional)</label>
                    <textarea class="form-control" name="observacoes" rows="3"></textarea>
                </div>

                <div class="alert alert-warning">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Ao se inscrever, você concorda em fornecer seus dados para participar do evento.
                        Seus dados serão utilizados apenas para organização do evento e controle de presença.
                    </small>
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100">
                    <i class="fas fa-paper-plane me-2"></i>Enviar Inscrição
                </button>
            </form>
        </div>
    </div>';
}

function submitCandidatura($conn) {
    $evento_id = $_POST['evento_id'] ?? '';
    $short_code = $_POST['short_code'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $serie = $_POST['serie'] ?? '';
    $curso = $_POST['curso'] ?? '';
    $email = $_POST['email'] ?? '';
    $observacoes = $_POST['observacoes'] ?? '';

    if (empty($nome) || empty($telefone) || empty($serie) || empty($curso)) {
        echo json_encode(['success' => false, 'message' => 'Nome, telefone, série e curso são obrigatórios']);
        return;
    }

    // Verificar se o evento existe
    $query = "SELECT id FROM eventos WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $evento_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Evento não encontrado'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Inserir candidatura
    $insert_query = "INSERT INTO candidaturas_eventos (evento_id, nome, telefone, serie, curso, email, observacoes, data_candidatura, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'pendente')";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param('issssss', $evento_id, $nome, $telefone, $serie, $curso, $email, $observacoes);

    if ($insert_stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Inscrição enviada com sucesso! Você será notificado sobre o status da sua candidatura.'
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao enviar inscrição: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
