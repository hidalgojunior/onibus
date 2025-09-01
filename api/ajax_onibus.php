<?php
include '../config/config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    $conn = getDatabaseConnection();

    switch ($action) {
        case 'get_buses':
            getBuses($conn);
            break;

        case 'create_bus':
            createBus($conn);
            break;

        case 'update_bus':
            updateBus($conn);
            break;

        case 'remove_bus':
            removeBus($conn);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function getBuses($conn) {
    $query = "SELECT o.*, e.nome as evento_nome
              FROM onibus o
              LEFT JOIN eventos e ON o.evento_id = e.id
              ORDER BY o.numero ASC";

    $result = $conn->query($query);

    if ($result) {
        $buses = [];
        while ($row = $result->fetch_assoc()) {
            $buses[] = $row;
        }

        echo json_encode(['success' => true, 'buses' => $buses], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao buscar ônibus: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function createBus($conn) {
    $evento_id = $_POST['evento_id'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $capacidade = $_POST['capacidade'] ?? '';
    $dias_reservados = $_POST['dias_reservados'] ?? '';

    if (empty($evento_id) || empty($numero) || empty($tipo) || empty($capacidade) || empty($dias_reservados)) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Verificar se já existe um ônibus com o mesmo número para este evento
    $check_query = "SELECT id FROM onibus WHERE evento_id = ? AND numero = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('ii', $evento_id, $numero);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Já existe um ônibus com este número para este evento'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "INSERT INTO onibus (evento_id, numero, tipo, capacidade, dias_reservados) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iissi', $evento_id, $numero, $tipo, $capacidade, $dias_reservados);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ônibus cadastrado com sucesso'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar ônibus: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function updateBus($conn) {
    $onibus_id = $_POST['onibus_id'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $capacidade = $_POST['capacidade'] ?? '';
    $dias_reservados = $_POST['dias_reservados'] ?? '';

    if (empty($onibus_id) || empty($numero) || empty($tipo) || empty($capacidade) || empty($dias_reservados)) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Verificar se já existe outro ônibus com o mesmo número para este evento
    $check_query = "SELECT o.id FROM onibus o
                    JOIN onibus o2 ON o.evento_id = o2.evento_id
                    WHERE o.numero = ? AND o.id != ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('ii', $numero, $onibus_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Já existe outro ônibus com este número para este evento'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    $query = "UPDATE onibus SET numero = ?, tipo = ?, capacidade = ?, dias_reservados = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('issii', $numero, $tipo, $capacidade, $dias_reservados, $onibus_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ônibus atualizado com sucesso'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar ônibus: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function removeBus($conn) {
    $onibus_id = $_POST['onibus_id'] ?? '';

    if (empty($onibus_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do ônibus é obrigatório'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    // Remover alocações primeiro
    $delete_allocations = "DELETE FROM alocacoes_onibus WHERE onibus_id = ?";
    $alloc_stmt = $conn->prepare($delete_allocations);
    $alloc_stmt->bind_param('i', $onibus_id);
    $alloc_stmt->execute();

    // Remover o ônibus
    $query = "DELETE FROM onibus WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $onibus_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ônibus removido com sucesso'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao remover ônibus: ' . $conn->error], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
?>
