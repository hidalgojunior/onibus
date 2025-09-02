<?php
echo "=== REMOVENDO ARQUIVOS DUPLICADOS ===\n\n";

// Arquivos que existem tanto na raiz quanto na pasta scripts
$arquivos_duplicados = [
    'check_alocacoes.php',
    'check_duplicates.php', 
    'check_eventos.php',
    'clean_database.php',
    'clear_presencas.php',
    'final_check.php',
    'find_aluno.php',
    'remove_aluno.php',
    'remove_duplicates.php',
    'return_control.php',
    'setup_qr.php'
];

// Arquivos de verificação adicionais que não são necessários
$arquivos_check_extras = [
    'check_alocacoes_table.php',
    'check_alunos_structure.php',
    'check_onibus_structure.php',
    'check_presencas.php',
    'check_structure.php',
    'check_tables.php'
];

$todos_arquivos = array_merge($arquivos_duplicados, $arquivos_check_extras);

$removidos = 0;

foreach ($todos_arquivos as $arquivo) {
    $caminho = __DIR__ . '/' . $arquivo;
    if (file_exists($caminho)) {
        if (unlink($caminho)) {
            echo "✅ Removido: $arquivo\n";
            $removidos++;
        } else {
            echo "❌ Erro ao remover: $arquivo\n";
        }
    } else {
        echo "⚠️  Não encontrado: $arquivo\n";
    }
}

echo "\n=== RESUMO ===\n";
echo "Arquivos duplicados removidos: $removidos\n";
echo "Os arquivos originais permanecem em /scripts/\n";
?>
