<?php
$current_page = "home";
include 'includes/page-layout.php';

// Incluir configuração
include 'config/config.php';

// Obter estatísticas do sistema
$conn = getDatabaseConnection();
$stats = [
    'total_alunos' => 0,
    'total_eventos' => 0,
    'total_onibus' => 0,
    'alocacoes_hoje' => 0,
    'alunos_presentes_hoje' => 0,
    'alunos_faltaram_hoje' => 0,
    'eventos_ativos' => [],
    'ultimas_presencas' => []
];

if (!$conn->connect_error) {
    // Total de alunos
    $result = $conn->query("SELECT COUNT(*) as total FROM alunos");
    if ($result) $stats['total_alunos'] = $result->fetch_assoc()['total'];
    
    // Total de eventos ativos
    $data_hoje = date('Y-m-d');
    $result = $conn->query("SELECT COUNT(*) as total FROM eventos WHERE data_fim >= '$data_hoje'");
    if ($result) $stats['total_eventos'] = $result->fetch_assoc()['total'];
    
    // Total de ônibus
    $result = $conn->query("SELECT COUNT(*) as total FROM onibus");
    if ($result) $stats['total_onibus'] = $result->fetch_assoc()['total'];
    
    // Alocações hoje
    $result = $conn->query("SELECT COUNT(*) as total FROM alocacoes_onibus ao 
                           JOIN eventos e ON ao.evento_id = e.id 
                           WHERE DATE(e.data_inicio) = '$data_hoje'");
    if ($result) $stats['alocacoes_hoje'] = $result->fetch_assoc()['total'];
    
    // Buscar eventos ativos para exibição
    $result = $conn->query("SELECT id, nome, data_inicio, data_fim
        FROM eventos 
        WHERE data_fim >= '$data_hoje' 
        ORDER BY data_inicio ASC LIMIT 3");
    if ($result) {
        while ($evento = $result->fetch_assoc()) {
            $stats['eventos_ativos'][] = $evento;
        }
    }
}

// Obter estatísticas para o header
$stats_header = [
    ['number' => $stats['total_eventos'] ?? 0, 'label' => 'Eventos Ativos'],
    ['number' => $stats['total_alunos'] ?? 0, 'label' => 'Alunos Cadastrados'],
    ['number' => $stats['total_onibus'] ?? 0, 'label' => 'Ônibus na Frota'],
    ['number' => $stats['alocacoes_hoje'] ?? 0, 'label' => 'Alocações Hoje']
];

// Configuração do breadcrumb
$breadcrumb = [
    ['label' => 'Dashboard']
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

// Renderizar header simplificado
renderHeader("Dashboard", null, [
    'icon' => 'fas fa-tachometer-alt'
]);
?>

<!-- Container Principal -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

<!-- Hero Section -->
<div class="bg-gradient-to-r from-azul-escuro to-azul-claro rounded-xl p-8 mb-8 text-white">
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="mb-6 md:mb-0">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">Sistema de Transporte Escolar</h1>
            <p class="text-lg opacity-90">Gerencie eventos, ônibus e alocações de forma inteligente</p>
        </div>
        <div class="text-center">
            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                <i class="fas fa-bus text-4xl mb-2"></i>
                <p class="text-sm opacity-80">Versão 2.0</p>
            </div>
        </div>
    </div>
</div>

<!-- Alertas do Sistema -->
<?php if (!$conn->connect_error): ?>
    <?php
    $alertas = [];
    
    // Verificar alunos sem alocação
    $result = $conn->query("SELECT COUNT(DISTINCT a.id) as alunos_sem_alocacao
        FROM alunos a
        LEFT JOIN alocacoes_onibus ao ON a.id = ao.aluno_id
        LEFT JOIN eventos e ON ao.evento_id = e.id AND e.data_fim >= '$data_hoje'
        WHERE ao.id IS NULL OR e.id IS NULL");
    
    if ($result && $result->num_rows > 0) {
        $sem_alocacao = $result->fetch_assoc()['alunos_sem_alocacao'];
        if ($sem_alocacao > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'titulo' => 'Alunos sem Alocação',
                'mensagem' => "$sem_alocacao alunos não estão alocados em nenhum evento ativo.",
                'acao' => 'pages/alocacoes.php'
            ];
        }
    }
    ?>

    <?php if (!empty($alertas)): ?>
        <div class="mb-8">
            <?php foreach ($alertas as $alerta): ?>
                <div class="bg-gradient-to-r from-amarelo-onibus/20 to-yellow-100 border-l-4 border-amarelo-onibus rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-amarelo-onibus/20 p-3 rounded-full mr-4">
                                <i class="fas fa-exclamation-triangle text-yellow-700 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-yellow-800 text-lg"><?= $alerta['titulo'] ?></h4>
                                <p class="text-yellow-700 mt-1"><?= $alerta['mensagem'] ?></p>
                            </div>
                        </div>
                        <a href="<?= $alerta['acao'] ?>" class="bg-amarelo-onibus hover:bg-yellow-500 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-arrow-right mr-2"></i> Resolver
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Dashboard Estatísticas - Design Moderno -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total de Alunos -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total de Alunos</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= number_format($stats['total_alunos'] ?? 0) ?></p>
                    <div class="flex items-center mt-2">
                        <span class="text-green-600 text-sm font-medium">
                            <i class="fas fa-arrow-up mr-1"></i>Ativos
                        </span>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total de Eventos -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Eventos Ativos</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= number_format($stats['total_eventos'] ?? 0) ?></p>
                    <div class="flex items-center mt-2">
                        <span class="text-green-600 text-sm font-medium">
                            <i class="fas fa-calendar mr-1"></i>Em andamento
                        </span>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-verde-medio to-green-600 p-4 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-calendar-alt text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total de Ônibus -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Ônibus na Frota</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= number_format($stats['total_onibus'] ?? 0) ?></p>
                    <div class="flex items-center mt-2">
                        <span class="text-blue-600 text-sm font-medium">
                            <i class="fas fa-cog mr-1"></i>Operacionais
                        </span>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-amarelo-onibus to-yellow-500 p-4 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-bus text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total de Alocações -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 group">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Alocações Hoje</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= number_format($stats['alocacoes_hoje'] ?? 0) ?></p>
                    <div class="flex items-center mt-2">
                        <span class="text-purple-600 text-sm font-medium">
                            <i class="fas fa-map-marker-alt mr-1"></i>Confirmadas
                        </span>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-4 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-route text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Módulos do Sistema - Design Profissional -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Módulos do Sistema</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Gerenciar Eventos -->
        <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <i class="fas fa-calendar-alt text-3xl mb-2 opacity-90"></i>
                        <h3 class="text-xl font-bold">Eventos</h3>
                    </div>
                    <div class="bg-white/20 rounded-full p-2">
                        <i class="fas fa-arrow-right text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Cadastrar e gerenciar eventos do sistema com QR Code para inscrições</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <i class="fas fa-users mr-2"></i>
                    <span><?= number_format($stats['total_eventos'] ?? 0) ?> eventos ativos</span>
                </div>
                <a href="pages/eventos.php" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i> Gerenciar Eventos
                </a>
            </div>
        </div>

        <!-- Gerenciar Ônibus -->
        <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
            <div class="bg-gradient-to-br from-verde-medio to-green-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <i class="fas fa-bus text-3xl mb-2 opacity-90"></i>
                        <h3 class="text-xl font-bold">Frota</h3>
                    </div>
                    <div class="bg-white/20 rounded-full p-2">
                        <i class="fas fa-arrow-right text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Cadastrar e gerenciar frota de ônibus com informações de capacidade</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <i class="fas fa-bus mr-2"></i>
                    <span><?= number_format($stats['total_onibus'] ?? 0) ?> ônibus na frota</span>
                </div>
                <a href="pages/onibus.php" class="w-full bg-green-50 hover:bg-green-100 text-green-700 font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-cog mr-2"></i> Gerenciar Frota
                </a>
            </div>
        </div>

        <!-- Candidaturas -->
        <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <i class="fas fa-user-graduate text-3xl mb-2 opacity-90"></i>
                        <h3 class="text-xl font-bold">Candidaturas</h3>
                    </div>
                    <div class="bg-white/20 rounded-full p-2">
                        <i class="fas fa-arrow-right text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Visualizar e gerenciar inscrições recebidas via QR Code</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    <span>Inscrições em tempo real</span>
                </div>
                <a href="pages/candidaturas.php" class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-list mr-2"></i> Ver Candidaturas
                </a>
            </div>
        </div>

        <!-- Alocação Automática -->
        <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
            <div class="bg-gradient-to-br from-amarelo-onibus to-yellow-500 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <i class="fas fa-magic text-3xl mb-2 opacity-90"></i>
                        <h3 class="text-xl font-bold">Alocação IA</h3>
                    </div>
                    <div class="bg-white/20 rounded-full p-2">
                        <i class="fas fa-arrow-right text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Sistema inteligente de alocação baseado na ordem de inscrição</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <i class="fas fa-robot mr-2"></i>
                    <span>Algoritmo otimizado</span>
                </div>
                <a href="pages/alocacao.php" class="w-full bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-play mr-2"></i> Iniciar Alocação
                </a>
            </div>
        </div>

        <!-- Gerenciar Alocações -->
        <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
            <div class="bg-gradient-to-br from-red-500 to-red-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <i class="fas fa-map-marked-alt text-3xl mb-2 opacity-90"></i>
                        <h3 class="text-xl font-bold">Alocações</h3>
                    </div>
                    <div class="bg-white/20 rounded-full p-2">
                        <i class="fas fa-arrow-right text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Visualizar, editar e gerenciar alocações de alunos nos ônibus</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <i class="fas fa-route mr-2"></i>
                    <span><?= number_format($stats['alocacoes_hoje'] ?? 0) ?> alocações hoje</span>
                </div>
                <a href="pages/alocacoes.php" class="w-full bg-red-50 hover:bg-red-100 text-red-700 font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-edit mr-2"></i> Gerenciar
                </a>
            </div>
        </div>

        <!-- Sistema de Administração -->
        <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
            <div class="bg-gradient-to-br from-gray-600 to-gray-700 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <i class="fas fa-cogs text-3xl mb-2 opacity-90"></i>
                        <h3 class="text-xl font-bold">Administração</h3>
                    </div>
                    <div class="bg-white/20 rounded-full p-2">
                        <i class="fas fa-arrow-right text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Configurações avançadas, relatórios e ferramentas do sistema</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <i class="fas fa-shield-alt mr-2"></i>
                    <span>Área administrativa</span>
                </div>
                <a href="#" class="w-full bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-tools mr-2"></i> Configurações
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Eventos Ativos e Links Úteis -->
<?php if (!empty($stats['eventos_ativos'])): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Eventos Ativos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <i class="fas fa-calendar-check text-2xl"></i>
                    Eventos Ativos
                </h3>
                <p class="opacity-90 mt-1">Eventos em andamento no sistema</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php foreach ($stats['eventos_ativos'] as $evento): ?>
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200 hover:shadow-md transition-all duration-200">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($evento['nome']) ?></h4>
                                <div class="flex items-center mt-2 text-sm text-gray-600">
                                    <i class="fas fa-calendar mr-2 text-green-600"></i>
                                    <span><?= date('d/m/Y', strtotime($evento['data_inicio'])) ?> - <?= date('d/m/Y', strtotime($evento['data_fim'])) ?></span>
                                </div>
                            </div>
                            <a href="pages/eventos.php?id=<?= $evento['id'] ?>" 
                               class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                                <i class="fas fa-eye"></i>
                                <span>Ver</span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Links Úteis -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
                <h3 class="text-xl font-bold flex items-center gap-3">
                    <i class="fas fa-link text-2xl"></i>
                    Links Úteis
                </h3>
                <p class="opacity-90 mt-1">Ferramentas administrativas</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <a href="admin/install.php" 
                       class="group block p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center gap-4">
                            <div class="bg-blue-500 p-3 rounded-lg text-white group-hover:scale-110 transition-transform duration-200">
                                <i class="fas fa-database text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 group-hover:text-blue-700">Instalar Banco de Dados</h4>
                                <p class="text-sm text-gray-600 mt-1">Configurar estrutura inicial do sistema</p>
                            </div>
                            <i class="fas fa-arrow-right text-blue-500 group-hover:translate-x-1 transition-transform duration-200"></i>
                        </div>
                    </a>
                    
                    <a href="admin/update_database.php" 
                       class="group block p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center gap-4">
                            <div class="bg-green-500 p-3 rounded-lg text-white group-hover:scale-110 transition-transform duration-200">
                                <i class="fas fa-sync text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 group-hover:text-green-700">Atualizar Sistema</h4>
                                <p class="text-sm text-gray-600 mt-1">Aplicar atualizações e correções pendentes</p>
                            </div>
                            <i class="fas fa-arrow-right text-green-500 group-hover:translate-x-1 transition-transform duration-200"></i>
                        </div>
                    </a>
                    
                    <a href="debug/" 
                       class="group block p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg border border-yellow-200 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center gap-4">
                            <div class="bg-yellow-500 p-3 rounded-lg text-white group-hover:scale-110 transition-transform duration-200">
                                <i class="fas fa-bug text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 group-hover:text-yellow-700">Ferramentas de Debug</h4>
                                <p class="text-sm text-gray-600 mt-1">Diagnóstico e monitoramento do sistema</p>
                            </div>
                            <i class="fas fa-arrow-right text-yellow-500 group-hover:translate-x-1 transition-transform duration-200"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php renderFooter(); ?>
