<?php
header('Content-Type: application/json');
include '../config/config.php';

$conn = getDatabaseConnection();

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro de conexão: ' . $conn->connect_error
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_unallocated_students':
        getUnallocatedStudents($conn);
        break;

    case 'get_current_allocations':
        getCurrentAllocations($conn);
        break;

    case 'allocate_students':
        allocateStudents($conn);
        break;

    case 'remove_allocation':
        removeAllocation($conn);
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Ação não reconhecida'
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        break;
}

$conn->close();

function getUnallocatedStudents($conn) {
    $evento_id = $_GET['evento_id'] ?? 0;

    $query = "
        SELECT a.* FROM alunos a
        LEFT JOIN alocacoes_onibus ao ON a.id = ao.aluno_id AND ao.evento_id = ?
        WHERE ao.id IS NULL
        ORDER BY a.nome
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $evento_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode([
        'success' => true,
        'students' => $students
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $stmt->close();
}

function getCurrentAllocations($conn) {
    $evento_id = $_GET['evento_id'] ?? 0;

    $query = "
        SELECT o.numero as onibus_numero, a.nome, a.curso, ao.id as alocacao_id
        FROM alocacoes_onibus ao
        INNER JOIN alunos a ON ao.aluno_id = a.id
        INNER JOIN onibus o ON ao.onibus_id = o.id
        WHERE ao.evento_id = ?
        ORDER BY o.numero, a.nome
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $evento_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $allocations = [];
    while ($row = $result->fetch_assoc()) {
        $allocations[] = $row;
    }

    echo json_encode([
        'success' => true,
        'allocations' => $allocations
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $stmt->close();
}

function allocateStudents($conn) {
    $evento_id = 0;
    $onibus_id = $_POST['onibus_id'] ?? 0;
    $alunos_ids = $_POST['alunos'] ?? [];

    if (empty($alunos_ids)) {
        echo json_encode([
            'success' => false,
            'message' => 'Nenhum aluno selecionado'
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }

    if (!$onibus_id) {
        echo json_encode([
            'success' => false,
            'message' => 'Ônibus não selecionado'
        ]);
        return;
    }

    // Buscar evento_id baseado no ônibus
    $query = "SELECT evento_id FROM onibus WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $onibus_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $evento_id = $row['evento_id'];
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ônibus não encontrado'
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return;
    }
    $stmt->close();

    $success_count = 0;
    $error_count = 0;

    foreach ($alunos_ids as $aluno_id) {
        // Verificar se já existe alocação
        $check_query = "SELECT id FROM alocacoes_onibus WHERE aluno_id = ? AND evento_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ii", $aluno_id, $evento_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            $insert_query = "INSERT INTO alocacoes_onibus (aluno_id, onibus_id, evento_id) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iii", $aluno_id, $onibus_id, $evento_id);

            if ($insert_stmt->execute()) {
                $success_count++;
            } else {
                $error_count++;
            }
            $insert_stmt->close();
        } else {
            $error_count++;
        }
        $check_stmt->close();
    }

    $message = '';
    if ($success_count > 0) {
        $message = "$success_count aluno(s) alocado(s) com sucesso!";
        if ($error_count > 0) {
            $message .= " ($error_count erro(s) - alunos já alocados)";
        }
    } else {
        $message = "Nenhum aluno foi alocado. Todos os alunos selecionados já estão alocados em outros ônibus.";
    }

    echo json_encode([
        'success' => $success_count > 0,
        'message' => $message,
        'success_count' => $success_count,
        'error_count' => $error_count
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function removeAllocation($conn) {
    $alocacao_id = $_POST['alocacao_id'] ?? 0;

    if (!$alocacao_id) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de alocação não fornecido'
        ]);
        return;
    }

    $delete_query = "DELETE FROM alocacoes_onibus WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $alocacao_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Alocação removida com sucesso!'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Alocação não encontrada'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao remover alocação: ' . $conn->error
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    $stmt->close();
}
?>
