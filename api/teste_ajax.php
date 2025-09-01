<?php
header('Content-Type: application/json');

// Teste específico para ajax_eventos
try {
    include '../config/config.php';
    
    $conn = getDatabaseConnection();
    
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão: ' . $conn->connect_error]);
        exit;
    }
    
    // Verificar se a tabela eventos existe
    $result = $conn->query("SHOW TABLES LIKE 'eventos'");
    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Tabela eventos não existe']);
        exit;
    }
    
    // Testar query simples
    $result = $conn->query("SELECT COUNT(*) as total FROM eventos");
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Erro na query: ' . $conn->error]);
        exit;
    }
    
    $row = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Conexão funcionando',
        'total_eventos' => $row['total']
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>
