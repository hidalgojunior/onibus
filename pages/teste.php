<?php
$current_page = "test";
include '../includes/page-layout.php';

// Incluir configuração do banco
include '../config/config.php';

// Teste de conexão
try {
    $conn = getDatabaseConnection();
    $connection_status = "✅ Conexão funcionando";
    $conn->close();
} catch (Exception $e) {
    $connection_status = "❌ Erro: " . $e->getMessage();
}

// Configuração do breadcrumb
$breadcrumb = [
    ['label' => 'Teste']
];

// Ações do header
$actions = [
    [
        'url' => 'pages/eventos.php', 
        'icon' => 'fas fa-calendar-plus', 
        'label' => 'Novo Evento'
    ],
    [
        'url' => 'pages/onibus.php', 
        'icon' => 'fas fa-bus', 
        'label' => 'Gerenciar Frota'
    ]
];

// Renderizar header personalizado
renderHeader("Teste do Sistema", "Verificação de funcionalidades", [
    'icon' => 'fas fa-cog',
    'breadcrumb' => $breadcrumb,
    'actions' => $actions
]);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Teste do Sistema</h2>
        
        <div class="space-y-4">
            <div class="p-4 rounded-lg bg-gray-100">
                <h3 class="font-semibold">Status da Conexão com Banco:</h3>
                <p><?= $connection_status ?></p>
            </div>
            
            <div class="p-4 rounded-lg bg-gray-100">
                <h3 class="font-semibold">Detecção de Contexto:</h3>
                <p>Arquivo atual: <?= __FILE__ ?></p>
                <p>Script name: <?= $_SERVER['SCRIPT_NAME'] ?></p>
                <p>Diretório base: <?= basename(dirname($_SERVER['SCRIPT_NAME'])) ?></p>
            </div>
            
            <div class="p-4 rounded-lg bg-gray-100">
                <h3 class="font-semibold">Teste de Navegação:</h3>
                <div class="space-y-2">
                    <a href="../index.php" class="btn-custom btn-primary">Voltar ao Dashboard</a>
                    <a href="eventos.php" class="btn-custom btn-success">Ir para Eventos</a>
                    <a href="candidaturas.php" class="btn-custom btn-warning">Ir para Candidaturas</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php renderFooter(); ?>
