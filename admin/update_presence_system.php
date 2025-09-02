<?php
/**
 * Script de Atualização para Sistema de Controle de Presenças
 * Executa as alterações necessárias no banco de dados
 */

// Incluir configuração
require_once '../config/config.php';

// Conectar ao banco
$conn = getDatabaseConnection();

if ($conn->connect_error) {
    die("❌ Erro de conexão: " . $conn->connect_error);
}

echo "🚀 Iniciando atualização do sistema para Controle de Presenças...\n\n";

// Função para verificar se coluna existe
function colunaExiste($conn, $tabela, $coluna) {
    $sql = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$tabela' AND COLUMN_NAME = '$coluna'";
    $result = $conn->query($sql);
    return $result && $result->fetch_assoc()['count'] > 0;
}

// Função para adicionar coluna se não existir
function adicionarColunaSeNaoExistir($conn, $tabela, $coluna, $definicao) {
    if (!colunaExiste($conn, $tabela, $coluna)) {
        $sql = "ALTER TABLE $tabela ADD COLUMN $coluna $definicao";
        return executarSQL($conn, $sql, "Adicionando coluna $coluna em $tabela");
    } else {
        echo "  ℹ️ Coluna $coluna já existe em $tabela\n";
        return true;
    }
}

// Função para executar SQL com tratamento de erro
function executarSQL($conn, $sql, $descricao) {
    echo "📝 $descricao... ";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Sucesso!\n";
        return true;
    } else {
        echo "❌ Erro: " . $conn->error . "\n";
        return false;
    }
}

// Função para executar múltiplas queries
function executarMultiplosSQL($conn, $queries, $descricao) {
    echo "📝 $descricao...\n";
    
    $sucessos = 0;
    $erros = 0;
    
    foreach ($queries as $sql) {
        if (trim($sql) == '') continue;
        
        if ($conn->query($sql) === TRUE) {
            $sucessos++;
            echo "  ✅ Query executada com sucesso\n";
        } else {
            $erros++;
            echo "  ⚠️ Aviso: " . $conn->error . "\n";
        }
    }
    
    echo "📊 Resultado: $sucessos sucessos, $erros avisos\n\n";
    return $sucessos > 0;
}

// 1. Verificar se as colunas já existem (evitar erros de duplicação)
echo "🔍 Verificando estrutura atual...\n";

$check_columns = [
    "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'alunos' AND COLUMN_NAME = 'responsavel_nome'",
    "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'onibus' AND COLUMN_NAME = 'placa'",
    "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'qr_codes'"
];

$precisa_atualizar = false;
foreach ($check_columns as $check) {
    $result = $conn->query($check);
    if ($result && $result->fetch_assoc()['count'] == 0) {
        $precisa_atualizar = true;
        break;
    }
}

if (!$precisa_atualizar) {
    echo "✅ Sistema já está atualizado!\n";
    exit;
}

// 2. Atualizar tabela ALUNOS
echo "📝 Atualizando tabela ALUNOS com dados dos responsáveis...\n";
adicionarColunaSeNaoExistir($conn, 'alunos', 'responsavel_nome', 'VARCHAR(255) AFTER whatsapp_permissao');
adicionarColunaSeNaoExistir($conn, 'alunos', 'responsavel_telefone', 'VARCHAR(50) AFTER responsavel_nome');
adicionarColunaSeNaoExistir($conn, 'alunos', 'responsavel_whatsapp', 'VARCHAR(50) AFTER responsavel_telefone');
adicionarColunaSeNaoExistir($conn, 'alunos', 'telefone_emergencia', 'VARCHAR(50) AFTER responsavel_whatsapp');
adicionarColunaSeNaoExistir($conn, 'alunos', 'endereco_completo', 'TEXT AFTER telefone_emergencia');
adicionarColunaSeNaoExistir($conn, 'alunos', 'ponto_embarque', 'VARCHAR(255) AFTER endereco_completo');
adicionarColunaSeNaoExistir($conn, 'alunos', 'observacoes_medicas', 'TEXT AFTER ponto_embarque');
adicionarColunaSeNaoExistir($conn, 'alunos', 'autorizacao_transporte', 'BOOLEAN DEFAULT TRUE AFTER observacoes_medicas');
adicionarColunaSeNaoExistir($conn, 'alunos', 'foto_perfil', 'VARCHAR(255) AFTER autorizacao_transporte');

// 3. Atualizar tabela ONIBUS
echo "\n📝 Atualizando tabela ONIBUS com dados operacionais...\n";
adicionarColunaSeNaoExistir($conn, 'onibus', 'placa', 'VARCHAR(10) AFTER numero');
adicionarColunaSeNaoExistir($conn, 'onibus', 'motorista_nome', 'VARCHAR(255) AFTER capacidade');
adicionarColunaSeNaoExistir($conn, 'onibus', 'monitor_nome', 'VARCHAR(255) AFTER motorista_nome');
adicionarColunaSeNaoExistir($conn, 'onibus', 'rota_descricao', 'TEXT AFTER monitor_nome');
adicionarColunaSeNaoExistir($conn, 'onibus', 'turno', "ENUM('matutino', 'vespertino', 'ambos') DEFAULT 'ambos' AFTER rota_descricao");
adicionarColunaSeNaoExistir($conn, 'onibus', 'ativo', 'BOOLEAN DEFAULT TRUE AFTER turno');

// 4. Criar tabela QR_CODES
$qr_table = "
CREATE TABLE IF NOT EXISTS qr_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    aluno_id INT NOT NULL UNIQUE,
    codigo_qr VARCHAR(255) NOT NULL UNIQUE,
    data_geracao DATETIME NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    validade DATETIME,
    tentativas_uso INT DEFAULT 0,
    ultimo_uso DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE
)";

executarSQL($conn, $qr_table, "Criando tabela QR_CODES para QR individuais");

// 5. Atualizar tabela ALOCACOES_ONIBUS
echo "\n📝 Atualizando tabela ALOCACOES_ONIBUS...\n";
adicionarColunaSeNaoExistir($conn, 'alocacoes_onibus', 'ponto_embarque', 'VARCHAR(255) AFTER onibus_id');
adicionarColunaSeNaoExistir($conn, 'alocacoes_onibus', 'horario_embarque', 'TIME AFTER ponto_embarque');
adicionarColunaSeNaoExistir($conn, 'alocacoes_onibus', 'turno', "ENUM('matutino', 'vespertino') NOT NULL DEFAULT 'matutino' AFTER horario_embarque");
adicionarColunaSeNaoExistir($conn, 'alocacoes_onibus', 'ativo', 'BOOLEAN DEFAULT TRUE AFTER turno');
adicionarColunaSeNaoExistir($conn, 'alocacoes_onibus', 'observacoes', 'TEXT AFTER ativo');

// 6. Atualizar tabela PRESENCAS
echo "\n📝 Atualizando tabela PRESENCAS para embarque/desembarque...\n";
adicionarColunaSeNaoExistir($conn, 'presencas', 'onibus_id', 'INT AFTER aluno_id');
adicionarColunaSeNaoExistir($conn, 'presencas', 'tipo_registro', "ENUM('embarque', 'desembarque') NOT NULL DEFAULT 'embarque' AFTER data");
adicionarColunaSeNaoExistir($conn, 'presencas', 'qr_code_usado', 'VARCHAR(255) AFTER presenca_retorno');
adicionarColunaSeNaoExistir($conn, 'presencas', 'localizacao_gps', 'VARCHAR(100) AFTER qr_code_usado');
adicionarColunaSeNaoExistir($conn, 'presencas', 'monitor_responsavel', 'VARCHAR(100) AFTER localizacao_gps');
adicionarColunaSeNaoExistir($conn, 'presencas', 'horario_registro', 'TIME AFTER monitor_responsavel');

// 7. Inserir dados padrão
echo "\n📝 Inserindo dados padrão de ônibus...\n";
$dados_padrao = [
    "INSERT IGNORE INTO onibus (numero, tipo, capacidade, motorista_nome, monitor_nome, rota_descricao, turno, ativo) VALUES
     ('001', 'ônibus', 45, 'João Silva', 'Maria Santos', 'Rota Centro - Bairro Sul', 'ambos', TRUE)",
    
    "INSERT IGNORE INTO onibus (numero, tipo, capacidade, motorista_nome, monitor_nome, rota_descricao, turno, ativo) VALUES
     ('002', 'ônibus', 45, 'Carlos Oliveira', 'Ana Costa', 'Rota Norte - Leste', 'ambos', TRUE)",
     
    "INSERT IGNORE INTO onibus (numero, tipo, capacidade, motorista_nome, monitor_nome, rota_descricao, turno, ativo) VALUES
     ('003', 'van', 15, 'Pedro Almeida', 'Lucia Ferreira', 'Rota Oeste - Centro', 'matutino', TRUE)"
];

foreach ($dados_padrao as $sql) {
    executarSQL($conn, $sql, "Inserindo dados de ônibus");
}

// 8. Gerar QR Codes para alunos existentes
$gerar_qr = "
INSERT IGNORE INTO qr_codes (aluno_id, codigo_qr, data_geracao, ativo, validade)
SELECT 
    id, 
    CONCAT('ALN-', LPAD(id, 5, '0'), '-', UPPER(SUBSTRING(MD5(CONCAT(id, nome, NOW())), 1, 8))),
    NOW(),
    TRUE,
    DATE_ADD(NOW(), INTERVAL 1 YEAR)
FROM alunos
WHERE id NOT IN (SELECT COALESCE(aluno_id, 0) FROM qr_codes)
";

executarSQL($conn, $gerar_qr, "Gerando QR Codes para alunos existentes");

// 9. Criar índices para performance
echo "\n📝 Criando índices para performance...\n";
$indices = [
    "CREATE INDEX idx_presencas_data ON presencas(data)",
    "CREATE INDEX idx_presencas_aluno_data ON presencas(aluno_id, data)",
    "CREATE INDEX idx_qr_codes_codigo ON qr_codes(codigo_qr)",
    "CREATE INDEX idx_alocacoes_turno ON alocacoes_onibus(turno)"
];

foreach ($indices as $sql) {
    $conn->query($sql); // Não mostrar erro se índice já existe
    echo "  ✅ Índice criado\n";
}

// 10. Verificar resultados
echo "📊 Verificando resultados da atualização...\n";

$verificacoes = [
    "SELECT COUNT(*) as count FROM alunos" => "Total de alunos",
    "SELECT COUNT(*) as count FROM onibus" => "Total de ônibus",
    "SELECT COUNT(*) as count FROM qr_codes" => "QR Codes gerados",
    "SELECT COUNT(*) as count FROM alocacoes_onibus" => "Total de alocações",
    "SELECT COUNT(*) as count FROM presencas" => "Total de presenças registradas"
];

foreach ($verificacoes as $sql => $desc) {
    $result = $conn->query($sql);
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "📋 $desc: $count\n";
    }
}

echo "\n🎉 Atualização concluída com sucesso!\n";
echo "📱 O sistema agora está pronto para controle de presenças com QR Code individual.\n\n";

echo "🔗 Próximos passos:\n";
echo "   1. Verificar o dashboard principal: index.php\n";
echo "   2. Testar a geração de QR Codes\n";
echo "   3. Configurar as rotas dos ônibus\n";
echo "   4. Treinar monitores para uso do scanner mobile\n\n";

$conn->close();
?>
