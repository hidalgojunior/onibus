<?php
// Teste rápido: Inserir um evento de exemplo no banco de dados
require_once 'config/config.php';

try {
    $conn = getDatabaseConnection();
    
    // Inserir evento de teste
    $stmt = $conn->prepare("INSERT INTO eventos (nome, data_inicio, data_fim, local, descricao) VALUES (?, ?, ?, ?, ?)");
    
    $nome = "Evento de Teste QR";
    $data_inicio = "2025-09-15";
    $data_fim = "2025-09-15";
    $local = "Escola";
    $descricao = "Evento para testar funcionalidades QR Code";
    
    $stmt->bind_param('sssss', $nome, $data_inicio, $data_fim, $local, $descricao);
    
    if ($stmt->execute()) {
        echo "✅ Evento de teste criado com sucesso! ID: " . $conn->insert_id;
    } else {
        echo "❌ Erro ao criar evento: " . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>

<script>
// Redirecionar para a página de eventos após 2 segundos
setTimeout(() => {
    window.location.href = 'eventos.php';
}, 2000);
</script>
