<?php
echo "=== LIMPEZA DE ARQUIVOS DESNECESSÁRIOS ===\n\n";

// Lista de arquivos e pastas a serem removidos
$arquivos_para_remover = [
    // Arquivos de teste
    'teste_timezone.php',
    'teste_sistema.php', 
    'teste_readme.php',
    'teste_json.php',
    'teste_evento.php',
    'teste_config.php',
    'teste_conexao_db.php',
    'teste_ajax.php',
    'teste-raiz.php',
    'teste-final-onibus.php',
    'teste-js.html',
    'teste-final-botoes.html',
    'teste-botoes-onibus.html',
    'test_query_presence.php',
    'test_query.php',
    'test_presence.php',
    'test_dates.php',
    'test_ajax.php',
    
    // Arquivos de debug
    'debug_tables.php',
    'debug_onibus.php',
    'debug_alunos.php',
    'debug_alocacoes.php',
    'debug.php',
    'debug-js.php',
    
    // Arquivos de verificação temporários
    'verificar_onibus.php',
    'verificar_eventos.php',
    'verificar_estrutura.php',
    'verificar_dados.php',
    
    // Arquivos de fix duplicados
    'fix_eventos_table.php',
    'fix_database_complete.php',
    'fix_database.php',
    
    // Arquivos backup
    'includes/presence_backup.php',
    
    // Arquivos de diagnóstico
    'diag_ajax_eventos.php',
    'diagnostico_visual.php',
    'diagnostico_candidaturas.php',
    
    // Scripts PowerShell desnecessários
    'inspect_last_bytes.ps1',
    'inspect_bytes.ps1',
    
    // Arquivos de inserção de teste
    'inserir_onibus_teste.php',
    'inserir_alunos_teste.php',
    
    // Arquivos duplicados/antigos
    'qr-codes-professional-fixed.php',
    'onibus-modern.php',
    'eventos-modern.php',
    'index_new.php',
    
    // Relatórios temporários
    'RELATORIO_ATUALIZACAO_ONIBUS.md',
    'LIMPEZA_RECOMENDADA.md',
    'LIMPEZA_CONCLUIDA.md',
    'VISUALIZADOR_AJUDA.md',
];

$pastas_para_remover = [
    'debug',
    'arquivos_desnecessarios'
];

$arquivos_removidos = 0;
$pastas_removidas = 0;

// Remover arquivos
foreach ($arquivos_para_remover as $arquivo) {
    $caminho = __DIR__ . '/' . $arquivo;
    if (file_exists($caminho)) {
        if (unlink($caminho)) {
            echo "✅ Removido: $arquivo\n";
            $arquivos_removidos++;
        } else {
            echo "❌ Erro ao remover: $arquivo\n";
        }
    } else {
        echo "⚠️  Não encontrado: $arquivo\n";
    }
}

// Função para remover diretório recursivamente
function removerDiretorio($dir) {
    if (!is_dir($dir)) return false;
    
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            removerDiretorio($path);
        } else {
            unlink($path);
        }
    }
    return rmdir($dir);
}

// Remover pastas
foreach ($pastas_para_remover as $pasta) {
    $caminho = __DIR__ . '/' . $pasta;
    if (is_dir($caminho)) {
        if (removerDiretorio($caminho)) {
            echo "📁 Pasta removida: $pasta\n";
            $pastas_removidas++;
        } else {
            echo "❌ Erro ao remover pasta: $pasta\n";
        }
    } else {
        echo "⚠️  Pasta não encontrada: $pasta\n";
    }
}

echo "\n=== RESUMO DA LIMPEZA ===\n";
echo "Arquivos removidos: $arquivos_removidos\n";
echo "Pastas removidas: $pastas_removidas\n";
echo "Limpeza concluída!\n";

// Verificar espaço liberado estimado
$espaco_estimado = ($arquivos_removidos * 50) + ($pastas_removidas * 200); // KB estimado
echo "Espaço estimado liberado: " . number_format($espaco_estimado) . " KB\n";
?>
