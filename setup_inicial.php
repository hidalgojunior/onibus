<?php
require_once 'config/config.php';

$dbConfig = getDatabaseConfig();
$conn = new mysqli($dbConfig['host'], $dbConfig['usuario'], $dbConfig['senha'], $dbConfig['banco']);

if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

// Criar evento padrão para o sistema de transporte
$stmt = $conn->prepare("INSERT INTO eventos (nome, descricao, data_inicio, data_fim, local, inscricao_aberta, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");

$nome = 'Sistema de Transporte Escolar';
$descricao = 'Evento padrão para gerenciamento do sistema de transporte escolar';
$data_inicio = '2025-01-01';
$data_fim = '2025-12-31';
$local = 'Escola';
$inscricao_aberta = 1;

$stmt->bind_param("sssssi", $nome, $descricao, $data_inicio, $data_fim, $local, $inscricao_aberta);

if ($stmt->execute()) {
    $evento_id = $conn->insert_id;
    echo "✓ Evento criado com sucesso! ID: $evento_id\n";
    
    // Agora inserir os ônibus com o evento_id correto
    $onibus_teste = [
        [
            'numero' => '001',
            'placa' => 'ABC-1234',
            'tipo' => 'ônibus',
            'capacidade' => 40,
            'motorista_nome' => 'José Silva',
            'monitor_nome' => 'Maria Santos',
            'responsavel_emergencia_nome' => 'Carlos Oliveira',
            'responsavel_emergencia_whatsapp' => '11987654321',
            'rota_descricao' => 'Rota Centro - Vila Esperança - Jardim América',
            'turno' => 'matutino',
            'ativo' => 1,
            'evento_id' => $evento_id,
            'dias_reservados' => 0
        ],
        [
            'numero' => '002',
            'placa' => 'DEF-5678',
            'tipo' => 'ônibus',
            'capacidade' => 45,
            'motorista_nome' => 'Pedro Costa',
            'monitor_nome' => 'Ana Rodrigues',
            'responsavel_emergencia_nome' => 'Lucia Pereira',
            'responsavel_emergencia_whatsapp' => '11987651234',
            'rota_descricao' => 'Rota Terminal - Escolas - Centro',
            'turno' => 'vespertino',
            'ativo' => 1,
            'evento_id' => $evento_id,
            'dias_reservados' => 0
        ],
        [
            'numero' => '003',
            'placa' => 'GHI-9012',
            'tipo' => 'van',
            'capacidade' => 15,
            'motorista_nome' => 'Roberto Lima',
            'monitor_nome' => 'Sandra Alves',
            'responsavel_emergencia_nome' => 'João Mendes',
            'responsavel_emergencia_whatsapp' => '11987659876',
            'rota_descricao' => 'Rota Especial - Atendimento Personalizado',
            'turno' => 'ambos',
            'ativo' => 1,
            'evento_id' => $evento_id,
            'dias_reservados' => 0
        ]
    ];

    echo "\nInserindo ônibus...\n";

    foreach ($onibus_teste as $onibus) {
        $stmt2 = $conn->prepare("INSERT INTO onibus (numero, placa, tipo, capacidade, motorista_nome, monitor_nome, responsavel_emergencia_nome, responsavel_emergencia_whatsapp, rota_descricao, turno, ativo, evento_id, dias_reservados, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $stmt2->bind_param("sssisssssssii", 
            $onibus['numero'], 
            $onibus['placa'], 
            $onibus['tipo'], 
            $onibus['capacidade'], 
            $onibus['motorista_nome'], 
            $onibus['monitor_nome'], 
            $onibus['responsavel_emergencia_nome'], 
            $onibus['responsavel_emergencia_whatsapp'], 
            $onibus['rota_descricao'], 
            $onibus['turno'], 
            $onibus['ativo'], 
            $onibus['evento_id'], 
            $onibus['dias_reservados']
        );
        
        if ($stmt2->execute()) {
            echo "✓ Ônibus '{$onibus['numero']}' inserido com sucesso!\n";
        } else {
            echo "✗ Erro ao inserir ônibus '{$onibus['numero']}': " . $stmt2->error . "\n";
        }
        
        $stmt2->close();
    }
    
} else {
    echo "✗ Erro ao criar evento: " . $stmt->error . "\n";
}

$stmt->close();

echo "\n=== Configuração inicial concluída! ===\n";

$conn->close();
?>
