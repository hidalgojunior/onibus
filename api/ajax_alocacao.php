<?php
include '../config/config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    $conn = getDatabaseConnection();

    switch ($action) {
        case 'get_event_data':
            getEventData($conn);
            break;

        case 'get_allocation_preview':
            getAllocationPreview($conn);
            break;

        case 'auto_allocate':
            autoAllocate($conn);
            break;

        case 'allocate_unallocated':
            allocateUnallocated($conn);
            break;

        case 'clear_allocations':
            clearAllocations($conn);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function getEventData($conn) {
    $evento_id = $_GET['evento_id'] ?? '';

    if (empty($evento_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Buscar dados do evento
    $evento_query = "SELECT * FROM eventos WHERE id = ?";
    $evento_stmt = $conn->prepare($evento_query);
    $evento_stmt->bind_param('i', $evento_id);
    $evento_stmt->execute();
    $evento_result = $evento_stmt->get_result();

    if ($evento_result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Evento não encontrado'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $evento = $evento_result->fetch_assoc();

    // Contar total de alunos no evento
    $alunos_query = "SELECT COUNT(*) as total FROM alunos WHERE evento_id = ?";
    $alunos_stmt = $conn->prepare($alunos_query);
    $alunos_stmt->bind_param('i', $evento_id);
    $alunos_stmt->execute();
    $alunos_result = $alunos_stmt->get_result();
    $total_alunos = $alunos_result->fetch_assoc()['total'];

    // Buscar ônibus do evento
    $onibus_query = "SELECT * FROM onibus WHERE evento_id = ? ORDER BY numero ASC";
    $onibus_stmt = $conn->prepare($onibus_query);
    $onibus_stmt->bind_param('i', $evento_id);
    $onibus_stmt->execute();
    $onibus_result = $onibus_stmt->get_result();

    $onibus = [];
    $capacidade_total = 0;
    $total_onibus = 0;

    while ($bus = $onibus_result->fetch_assoc()) {
        $onibus[] = $bus;
        $capacidade_total += $bus['capacidade'];
        $total_onibus++;
    }

    // Buscar alocações atuais
    $alocacoes_query = "SELECT o.*, COUNT(ao.aluno_id) as alunos_count
                       FROM onibus o
                       LEFT JOIN alocacoes_onibus ao ON o.id = ao.onibus_id
                       WHERE o.evento_id = ?
                       GROUP BY o.id
                       ORDER BY o.numero ASC";
    $alocacoes_stmt = $conn->prepare($alocacoes_query);
    $alocacoes_stmt->bind_param('i', $evento_id);
    $alocacoes_stmt->execute();
    $alocacoes_result = $alocacoes_stmt->get_result();

    $alocacoes = [];
    $alunos_alocados = 0;

    while ($alocacao = $alocacoes_result->fetch_assoc()) {
        $alocacoes[] = $alocacao;
        $alunos_alocados += $alocacao['alunos_count'];
    }

    echo json_encode([
        'success' => true,
        'evento' => $evento,
        'total_alunos' => $total_alunos,
        'total_onibus' => $total_onibus,
        'capacidade_total' => $capacidade_total,
        'alunos_alocados' => $alunos_alocados,
        'alocacoes' => $alocacoes
    ]);
}

function getAllocationPreview($conn) {
    $evento_id = $_GET['evento_id'] ?? '';

    if (empty($evento_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Buscar alunos ordenados por data de inscrição (ordem de chegada)
    $alunos_query = "SELECT id, nome FROM alunos WHERE evento_id = ? ORDER BY data_inscricao ASC, id ASC";
    $alunos_stmt = $conn->prepare($alunos_query);
    $alunos_stmt->bind_param('i', $evento_id);
    $alunos_stmt->execute();
    $alunos_result = $alunos_stmt->get_result();

    $alunos = [];
    while ($aluno = $alunos_result->fetch_assoc()) {
        $alunos[] = $aluno;
    }

    // Buscar ônibus disponíveis ordenados por número
    $onibus_query = "SELECT id, numero, tipo, capacidade FROM onibus WHERE evento_id = ? ORDER BY numero ASC";
    $onibus_stmt = $conn->prepare($onibus_query);
    $onibus_stmt->bind_param('i', $evento_id);
    $onibus_stmt->execute();
    $onibus_result = $onibus_stmt->get_result();

    $onibus = [];
    while ($bus = $onibus_result->fetch_assoc()) {
        $onibus[] = $bus;
    }

    if (empty($alunos) || empty($onibus)) {
        echo json_encode(['success' => false, 'message' => 'Não há alunos ou ônibus suficientes para alocação'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Simular alocação
    $preview = simularAlocacao($alunos, $onibus);

    echo json_encode(['success' => true, 'preview' => $preview], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function simularAlocacao($alunos, $onibus) {
    $preview = [];
    $aluno_index = 0;

    // Inicializar contadores para cada ônibus
    foreach ($onibus as $bus) {
        $preview[] = [
            'onibus_id' => $bus['id'],
            'onibus_numero' => $bus['numero'],
            'onibus_tipo' => $bus['tipo'],
            'capacidade' => $bus['capacidade'],
            'alunos_count' => 0,
            'alunos' => []
        ];
    }

    // Distribuir alunos igualmente entre os ônibus
    foreach ($alunos as $aluno) {
        $bus_index = $aluno_index % count($onibus);

        // Verificar se o ônibus ainda tem capacidade
        if ($preview[$bus_index]['alunos_count'] < $preview[$bus_index]['capacidade']) {
            $preview[$bus_index]['alunos'][] = $aluno;
            $preview[$bus_index]['alunos_count']++;
            $aluno_index++;
        } else {
            // Se o ônibus atual está cheio, tentar o próximo
            $placed = false;
            for ($i = 0; $i < count($onibus); $i++) {
                $check_index = ($bus_index + $i + 1) % count($onibus);
                if ($preview[$check_index]['alunos_count'] < $preview[$check_index]['capacidade']) {
                    $preview[$check_index]['alunos'][] = $aluno;
                    $preview[$check_index]['alunos_count']++;
                    $placed = true;
                    break;
                }
            }

            if (!$placed) {
                // Se nenhum ônibus tem capacidade, parar
                break;
            }

            $aluno_index++;
        }
    }

    return $preview;
}

function autoAllocate($conn) {
    $evento_id = $_POST['evento_id'] ?? '';

    if (empty($evento_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // IMPORTANTE: Removido o DELETE automático
    // As alocações devem persistir. Se o usuário quiser realocar,
    // deve usar a função clearAllocations explicitamente
    // $clear_query = "DELETE FROM alocacoes_onibus WHERE onibus_id IN (SELECT id FROM onibus WHERE evento_id = ?)";
    // $clear_stmt = $conn->prepare($clear_query);
    // $clear_stmt->bind_param('i', $evento_id);
    // $clear_stmt->execute();

    // Buscar alunos ordenados por data de inscrição (ordem de chegada)
    $alunos_query = "SELECT id, nome FROM alunos WHERE evento_id = ? ORDER BY data_inscricao ASC, id ASC";
    $alunos_stmt = $conn->prepare($alunos_query);
    $alunos_stmt->bind_param('i', $evento_id);
    $alunos_stmt->execute();
    $alunos_result = $alunos_stmt->get_result();

    $alunos = [];
    while ($aluno = $alunos_result->fetch_assoc()) {
        $alunos[] = $aluno;
    }

    // Buscar ônibus disponíveis ordenados por número
    $onibus_query = "SELECT id, numero, tipo, capacidade FROM onibus WHERE evento_id = ? ORDER BY numero ASC";
    $onibus_stmt = $conn->prepare($onibus_query);
    $onibus_stmt->bind_param('i', $evento_id);
    $onibus_stmt->execute();
    $onibus_result = $onibus_stmt->get_result();

    $onibus = [];
    while ($bus = $onibus_result->fetch_assoc()) {
        $onibus[] = $bus;
    }

    if (empty($alunos) || empty($onibus)) {
        echo json_encode(['success' => false, 'message' => 'Não há alunos ou ônibus suficientes para alocação'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Realizar alocação
    $conn->begin_transaction();

    try {
        $alocacao_query = "INSERT INTO alocacoes_onibus (aluno_id, onibus_id) VALUES (?, ?)";
        $alocacao_stmt = $conn->prepare($alocacao_query);

        $aluno_index = 0;
        $alocados = 0;

        // Contadores de alunos por ônibus
        $contadores_onibus = array_fill(0, count($onibus), 0);

        foreach ($alunos as $aluno) {
            $placed = false;

            // Tentar alocar no ônibus com menos alunos
            for ($tentativa = 0; $tentativa < count($onibus); $tentativa++) {
                $bus_index = $aluno_index % count($onibus);

                if ($contadores_onibus[$bus_index] < $onibus[$bus_index]['capacidade']) {
                    $alocacao_stmt->bind_param('ii', $aluno['id'], $onibus[$bus_index]['id']);
                    $alocacao_stmt->execute();
                    $contadores_onibus[$bus_index]++;
                    $placed = true;
                    $alocados++;
                    break;
                }

                $aluno_index++;
            }

            if (!$placed) {
                // Se não conseguiu alocar, tentar em qualquer ônibus que tenha espaço
                for ($i = 0; $i < count($onibus); $i++) {
                    if ($contadores_onibus[$i] < $onibus[$i]['capacidade']) {
                        $alocacao_stmt->bind_param('ii', $aluno['id'], $onibus[$i]['id']);
                        $alocacao_stmt->execute();
                        $contadores_onibus[$i]++;
                        $placed = true;
                        $alocados++;
                        break;
                    }
                }
            }

            if (!$placed) {
                // Se ainda não conseguiu alocar, significa que todos os ônibus estão cheios
                break;
            }
        }

        $conn->commit();

        $message = "Alocação automática concluída! $alocados alunos foram alocados em " . count($onibus) . " ônibus.";
        echo json_encode(['success' => true, 'message' => $message], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Erro durante a alocação: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function allocateUnallocated($conn) {
    $evento_id = $_POST['evento_id'] ?? '';

    if (empty($evento_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    try {
        $conn->begin_transaction();

        // Buscar alunos não alocados neste evento
        $alunos_nao_alocados_query = "
            SELECT a.id, a.nome
            FROM alunos a
            WHERE a.evento_id = ? AND a.id NOT IN (
                SELECT ao.aluno_id
                FROM alocacoes_onibus ao
                INNER JOIN onibus o ON ao.onibus_id = o.id
                WHERE o.evento_id = ?
            )
            ORDER BY a.data_inscricao ASC, a.id ASC
        ";
        $alunos_stmt = $conn->prepare($alunos_nao_alocados_query);
        $alunos_stmt->bind_param('ii', $evento_id, $evento_id);
        $alunos_stmt->execute();
        $alunos_result = $alunos_stmt->get_result();

        $alunos_nao_alocados = [];
        while ($aluno = $alunos_result->fetch_assoc()) {
            $alunos_nao_alocados[] = $aluno;
        }

        if (empty($alunos_nao_alocados)) {
            echo json_encode(['success' => false, 'message' => 'Não há alunos não alocados para distribuir'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return;
        }

        // Buscar ônibus do evento com capacidade disponível
        $onibus_query = "
            SELECT o.id, o.numero, o.capacidade,
                   COUNT(ao.aluno_id) as alunos_atuais
            FROM onibus o
            LEFT JOIN alocacoes_onibus ao ON o.id = ao.onibus_id
            WHERE o.evento_id = ?
            GROUP BY o.id, o.numero, o.capacidade
            HAVING alunos_atuais < capacidade
            ORDER BY o.numero ASC
        ";
        $onibus_stmt = $conn->prepare($onibus_query);
        $onibus_stmt->bind_param('i', $evento_id);
        $onibus_stmt->execute();
        $onibus_result = $onibus_stmt->get_result();

        $onibus_disponiveis = [];
        while ($bus = $onibus_result->fetch_assoc()) {
            $capacidade_disponivel = $bus['capacidade'] - $bus['alunos_atuais'];
            if ($capacidade_disponivel > 0) {
                $onibus_disponiveis[] = [
                    'id' => $bus['id'],
                    'numero' => $bus['numero'],
                    'capacidade_disponivel' => $capacidade_disponivel
                ];
            }
        }

        if (empty($onibus_disponiveis)) {
            echo json_encode(['success' => false, 'message' => 'Não há capacidade disponível nos ônibus'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return;
        }

        // Distribuir alunos não alocados nos ônibus com capacidade disponível
        $aluno_index = 0;
        $alunos_alocados = 0;

        foreach ($alunos_nao_alocados as $aluno) {
            $bus_index = $aluno_index % count($onibus_disponiveis);
            $bus = $onibus_disponiveis[$bus_index];

            // Verificar se ainda há capacidade neste ônibus
            $capacidade_atual_query = "
                SELECT COUNT(*) as count
                FROM alocacoes_onibus
                WHERE onibus_id = ?
            ";
            $capacidade_stmt = $conn->prepare($capacidade_atual_query);
            $capacidade_stmt->bind_param('i', $bus['id']);
            $capacidade_stmt->execute();
            $capacidade_result = $capacidade_stmt->get_result();
            $capacidade_atual = $capacidade_result->fetch_assoc()['count'];

            // Buscar capacidade máxima do ônibus
            $capacidade_max_query = "SELECT capacidade FROM onibus WHERE id = ?";
            $capacidade_max_stmt = $conn->prepare($capacidade_max_query);
            $capacidade_max_stmt->bind_param('i', $bus['id']);
            $capacidade_max_stmt->execute();
            $capacidade_max_result = $capacidade_max_stmt->get_result();
            $capacidade_max = $capacidade_max_result->fetch_assoc()['capacidade'];

            if ($capacidade_atual < $capacidade_max) {
                // Inserir alocação
                $insert_query = "INSERT INTO alocacoes_onibus (aluno_id, onibus_id, evento_id) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param('iii', $aluno['id'], $bus['id'], $evento_id);
                $insert_stmt->execute();
                $alunos_alocados++;
            }

            $aluno_index++;
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => "$alunos_alocados alunos foram alocados com sucesso nos ônibus disponíveis",
            'alunos_alocados' => $alunos_alocados
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Erro durante a alocação: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function clearAllocations($conn) {
    $evento_id = $_POST['evento_id'] ?? '';
    $confirmacao = $_POST['confirmacao'] ?? '';

    if (empty($evento_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    if ($confirmacao !== 'SIM') {
        echo json_encode(['success' => false, 'message' => 'Para limpar todas as alocações, envie confirmacao: "SIM"'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "DELETE FROM alocacoes_onibus WHERE onibus_id IN (SELECT id FROM onibus WHERE evento_id = ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $evento_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Todas as alocações foram removidas com sucesso'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao remover alocações: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
?>
