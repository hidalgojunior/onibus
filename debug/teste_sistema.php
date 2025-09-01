<?php
/**
 * Script de Teste do Sistema de Controle de Presença
 *
 * Este script testa as funcionalidades implementadas:
 * - Conexão com banco de dados
 * - Funções de email
 * - Sintaxe dos arquivos PHP
 * - Configurações básicas
 */

// =================================================================
// CONFIGURAÇÃO DO TESTE
// =================================================================

// Inclui os arquivos de configuração necessários
if (file_exists('config_email.php')) {
    include 'config_email.php';
}

$TESTES_REALIZADOS = [];
$ERROS_ENCONTRADOS = [];

// =================================================================
// FUNÇÕES DE TESTE
// =================================================================

/**
 * Registra um teste realizado
 */
function registrarTeste($nome, $status, $mensagem = '') {
    global $TESTES_REALIZADOS;

    $TESTES_REALIZADOS[] = [
        'nome' => $nome,
        'status' => $status,
        'mensagem' => $mensagem,
        'timestamp' => date('H:i:s')
    ];

    if ($status === 'ERRO') {
        global $ERROS_ENCONTRADOS;
        $ERROS_ENCONTRADOS[] = "$nome: $mensagem";
    }
}

/**
 * Testa a sintaxe de um arquivo PHP
 */
function testarSintaxePHP($arquivo) {
    $output = shell_exec("php -l \"$arquivo\" 2>&1");

    if (strpos($output, 'No syntax errors detected') !== false) {
        registrarTeste("Sintaxe PHP - $arquivo", 'OK', 'Sintaxe válida');
        return true;
    } else {
        registrarTeste("Sintaxe PHP - $arquivo", 'ERRO', $output);
        return false;
    }
}

/**
 * Testa se um arquivo existe
 */
function testarArquivoExiste($arquivo) {
    if (file_exists($arquivo)) {
        registrarTeste("Arquivo existe - $arquivo", 'OK', 'Arquivo encontrado');
        return true;
    } else {
        registrarTeste("Arquivo existe - $arquivo", 'ERRO', 'Arquivo não encontrado');
        return false;
    }
}

/**
 * Testa a conexão com o banco de dados
 */
function testarConexaoBanco() {
    if (!file_exists('config.php')) {
        registrarTeste('Conexão Banco', 'ERRO', 'Arquivo config.php não encontrado');
        return false;
    }

    include 'config.php';

    // Verifica se a função existe
    if (!function_exists('getDatabaseConfig')) {
        registrarTeste('Conexão Banco', 'ERRO', 'Função getDatabaseConfig não encontrada no config.php');
        return false;
    }

    $config = getDatabaseConfig();

    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['banco']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['usuario'], $config['senha'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->query('SELECT 1');
        registrarTeste('Conexão Banco', 'OK', 'Conexão estabelecida com sucesso');
        return true;
    } catch (PDOException $e) {
        registrarTeste('Conexão Banco', 'ERRO', 'Erro na conexão: ' . $e->getMessage());
        return false;
    }
}

/**
 * Testa as configurações de email
 */
function testarConfigEmail() {
    if (!file_exists('config_email.php')) {
        registrarTeste('Config Email', 'ERRO', 'Arquivo config_email.php não encontrado');
        return false;
    }

    // O config_email.php já foi incluído no início do script
    // Verifica se as funções existem
    if (!function_exists('getEmailDestinatarios')) {
        registrarTeste('Config Email', 'ERRO', 'Função getEmailDestinatarios não encontrada');
        return false;
    }

    if (!function_exists('getEmailConfig')) {
        registrarTeste('Config Email', 'ERRO', 'Função getEmailConfig não encontrada');
        return false;
    }

    $destinatarios = getEmailDestinatarios();
    $config = getEmailConfig();

    // Verifica se há destinatários
    if (empty($destinatarios)) {
        registrarTeste('Config Email', 'ERRO', 'Nenhum destinatário configurado');
        return false;
    }

    // Verifica formato dos emails
    $emailsInvalidos = [];
    foreach ($destinatarios as $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailsInvalidos[] = $email;
        }
    }

    if (!empty($emailsInvalidos)) {
        registrarTeste('Config Email', 'ERRO', 'Emails inválidos: ' . implode(', ', $emailsInvalidos));
        return false;
    }

    // Verifica configurações obrigatórias
    if (empty($config['from_email'])) {
        registrarTeste('Config Email', 'ERRO', 'Email do remetente não configurado');
        return false;
    }

    registrarTeste('Config Email', 'OK', 'Configuração válida - ' . count($destinatarios) . ' destinatários');
    return true;
}

/**
 * Testa a função mail() do PHP
 */
function testarFuncaoMail() {
    // Tenta enviar um email de teste
    $teste = mail('teste@invalido.com', 'Teste Sistema', 'Este é um email de teste do sistema de presença.');

    if ($teste) {
        registrarTeste('Função Mail', 'OK', 'Função mail() está funcionando');
        return true;
    } else {
        registrarTeste('Função Mail', 'AVISO', 'Função mail() pode não estar configurada no servidor');
        return false;
    }
}

/**
 * Testa se as funções JavaScript estão presentes no presence.php
 */
function testarFuncoesJavaScript() {
    if (!file_exists('presence.php')) {
        registrarTeste('JavaScript', 'ERRO', 'Arquivo presence.php não encontrado');
        return false;
    }

    $conteudo = file_get_contents('presence.php');

    $funcoesNecessarias = [
        'atualizarEstatisticas',
        'mostrarFeedback',
        'enviarRelatorioPresenca',
        'toggleStatus'
    ];

    $funcoesEncontradas = 0;

    foreach ($funcoesNecessarias as $funcao) {
        if (strpos($conteudo, "function $funcao") !== false) {
            $funcoesEncontradas++;
        }
    }

    if ($funcoesEncontradas === count($funcoesNecessarias)) {
        registrarTeste('JavaScript', 'OK', 'Todas as funções JavaScript encontradas');
        return true;
    } else {
        registrarTeste('JavaScript', 'ERRO', ($funcoesEncontradas) . '/' . count($funcoesNecessarias) . ' funções encontradas');
        return false;
    }
}

// =================================================================
// EXECUÇÃO DOS TESTES
// =================================================================

echo "🚀 Iniciando testes do Sistema de Controle de Presença...\n";
echo str_repeat("=", 60) . "\n\n";

// Testa arquivos essenciais
testarArquivoExiste('config.php');
testarArquivoExiste('config_email.php');
testarArquivoExiste('presence.php');

// Testa sintaxe PHP
testarSintaxePHP('config.php');
testarSintaxePHP('config_email.php');
testarSintaxePHP('presence.php');

// Testa conexão com banco
testarConexaoBanco();

// Testa configurações de email
testarConfigEmail();

// Testa função mail
testarFuncaoMail();

// Testa funções JavaScript
testarFuncoesJavaScript();

// =================================================================
// RELATÓRIO FINAL
// =================================================================

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 RELATÓRIO DE TESTES\n";
echo str_repeat("=", 60) . "\n\n";

$totalTestes = count($TESTES_REALIZADOS);
$testesOk = count(array_filter($TESTES_REALIZADOS, fn($t) => $t['status'] === 'OK'));
$testesErro = count(array_filter($TESTES_REALIZADOS, fn($t) => $t['status'] === 'ERRO'));
$testesAviso = count(array_filter($TESTES_REALIZADOS, fn($t) => $t['status'] === 'AVISO'));

foreach ($TESTES_REALIZADOS as $teste) {
    $status = match($teste['status']) {
        'OK' => '✅',
        'ERRO' => '❌',
        'AVISO' => '⚠️'
    };

    echo sprintf("%s %-25s [%s] %s\n",
        $status,
        $teste['nome'],
        $teste['timestamp'],
        $teste['mensagem']
    );
}

echo "\n" . str_repeat("-", 60) . "\n";
echo "📈 RESUMO:\n";
echo "   Total de testes: $totalTestes\n";
echo "   ✅ Aprovados: $testesOk\n";
echo "   ❌ Erros: $testesErro\n";
echo "   ⚠️ Avisos: $testesAviso\n";

if ($testesErro > 0) {
    echo "\n❌ PROBLEMAS ENCONTRADOS:\n";
    foreach ($ERROS_ENCONTRADOS as $erro) {
        echo "   - $erro\n";
    }
    echo "\n🔧 Corrija os erros antes de usar o sistema em produção.\n";
} else {
    echo "\n✅ Todos os testes passaram! O sistema está pronto para uso.\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🏁 Testes concluídos em " . date('H:i:s') . "\n";

?></content>
<parameter name="filePath">c:\laragon\www\onibus\teste_sistema.php
