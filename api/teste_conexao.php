<?php
// Teste simples de conexão
try {
    include '../config/config.php';
    echo "Config incluído com sucesso\n";
    
    $conn = getDatabaseConnection();
    echo "Conexão obtida com sucesso\n";
    
    if ($conn->connect_error) {
        echo "Erro de conexão: " . $conn->connect_error . "\n";
    } else {
        echo "Conectado ao banco de dados com sucesso!\n";
        
        // Teste simples
        $result = $conn->query("SELECT COUNT(*) as total FROM eventos");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "Total de eventos: " . $row['total'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>
