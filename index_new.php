<?php
// Configurações da página
$pageTitle = 'Dashboard - Sistema de Gerenciamento de Ônibus';
$currentPage = 'home';

// Incluir configuração
include 'config/config.php';

// Obter estatísticas do sistema
$conn = getDatabaseConnection();
$stats = [
    'total_alunos' => 0,
    'total_eventos' => 0,
    'total_onibus' => 0,
    'total_alocacoes' => 0,
    'alunos_presentes_hoje' => 0,
    'alunos_faltaram_hoje' => 0,
    'eventos_ativos' => [],
    'ultimas_presencas' => []
];

if (!$conn->connect_error) {
    // Total de alunos
    $result = $conn->query("SELECT COUNT(*) as total FROM alunos");
    if ($result) $stats['total_alunos'] = $result->fetch_assoc()['total'];

    // Total de eventos
    $result = $conn->query("SELECT COUNT(*) as total FROM eventos");
    if ($result) $stats['total_eventos'] = $result->fetch_assoc()['total'];

    // Total de ônibus
    $result = $conn->query("SELECT COUNT(*) as total FROM onibus");
    if ($result) $stats['total_onibus'] = $result->fetch_assoc()['total'];

    // Total de alocações
    $result = $conn->query("SELECT COUNT(*) as total FROM alocacoes_onibus");
    if ($result) $stats['total_alocacoes'] = $result->fetch_assoc()['total'];

    // Eventos ativos
    $data_hoje = date('Y-m-d');
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

// Conteúdo da página
ob_start();
?>

<!-- Header Hero -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg mb-8">
    <div class="px-6 py-8 text-center text-white">
        <h1 class="text-3xl md:text-4xl font-bold mb-2 flex items-center justify-center gap-3">
            <i class="fas fa-bus"></i>
            Sistema de Gerenciamento de Ônibus
        </h1>
        <p class="text-blue-100 text-lg">Dashboard Principal - Controle de embarque e retorno para eventos institucionais</p>
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
        <div class="mb-6">
            <?php foreach ($alertas as $alerta): ?>
                <div class="alert alert-<?= $alerta['tipo'] ?> mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold"><?= $alerta['titulo'] ?></h4>
                            <p class="mt-1"><?= $alerta['mensagem'] ?></p>
                        </div>
                        <a href="<?= $alerta['acao'] ?>" class="btn-custom btn-warning">
                            <i class="fas fa-arrow-right"></i> Ver
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Estatísticas -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total de Alunos -->
    <div class="card hover:shadow-lg transition-shadow duration-200">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Alunos</p>
                    <p class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_alunos']) ?></p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total de Eventos -->
    <div class="card hover:shadow-lg transition-shadow duration-200">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Eventos</p>
                    <p class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_eventos']) ?></p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-calendar text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total de Ônibus -->
    <div class="card hover:shadow-lg transition-shadow duration-200">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Ônibus</p>
                    <p class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_onibus']) ?></p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-bus text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total de Alocações -->
    <div class="card hover:shadow-lg transition-shadow duration-200">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Alocações</p>
                    <p class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_alocacoes']) ?></p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-random text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Gerenciar Eventos -->
    <div class="card hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
        <div class="card-body text-center">
            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calendar-plus text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2">Gerenciar Eventos</h3>
            <p class="text-gray-600 mb-4">Cadastrar e gerenciar eventos do sistema</p>
            <a href="pages/eventos.php" class="btn-custom btn-primary w-full">
                <i class="fas fa-arrow-right"></i> Acessar
            </a>
        </div>
    </div>

    <!-- Gerenciar Ônibus -->
    <div class="card hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
        <div class="card-body text-center">
            <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-bus text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2">Gerenciar Ônibus</h3>
            <p class="text-gray-600 mb-4">Cadastrar e gerenciar frota de ônibus</p>
            <a href="pages/onibus.php" class="btn-custom btn-success w-full">
                <i class="fas fa-arrow-right"></i> Acessar
            </a>
        </div>
    </div>

    <!-- Alocação Automática -->
    <div class="card hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
        <div class="card-body text-center">
            <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-random text-yellow-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2">Alocação Automática</h3>
            <p class="text-gray-600 mb-4">Alocar alunos automaticamente nos ônibus</p>
            <a href="pages/alocacao.php" class="btn-custom btn-warning w-full">
                <i class="fas fa-arrow-right"></i> Acessar
            </a>
        </div>
    </div>

    <!-- Candidaturas -->
    <div class="card hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
        <div class="card-body text-center">
            <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-purple-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2">Candidaturas</h3>
            <p class="text-gray-600 mb-4">Gerenciar candidaturas de alunos</p>
            <a href="pages/candidaturas.php" class="btn-custom btn-secondary w-full">
                <i class="fas fa-arrow-right"></i> Acessar
            </a>
        </div>
    </div>

    <!-- Alocações -->
    <div class="card hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
        <div class="card-body text-center">
            <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-list text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2">Gerenciar Alocações</h3>
            <p class="text-gray-600 mb-4">Visualizar e editar alocações existentes</p>
            <a href="pages/alocacoes.php" class="btn-custom btn-danger w-full">
                <i class="fas fa-arrow-right"></i> Acessar
            </a>
        </div>
    </div>

    <!-- Administração -->
    <div class="card hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
        <div class="card-body text-center">
            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-cogs text-gray-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold mb-2">Administração</h3>
            <p class="text-gray-600 mb-4">Configurações e ferramentas do sistema</p>
            <a href="admin/" class="btn-custom btn-outline w-full">
                <i class="fas fa-arrow-right"></i> Acessar
            </a>
        </div>
    </div>
</div>

<!-- Eventos Ativos -->
<?php if (!empty($stats['eventos_ativos'])): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fas fa-calendar-check text-green-600"></i>
                    Eventos Ativos
                </h3>
            </div>
            <div class="card-body">
                <div class="space-y-3">
                    <?php foreach ($stats['eventos_ativos'] as $evento): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium"><?= htmlspecialchars($evento['nome']) ?></h4>
                                <p class="text-sm text-gray-600">
                                    <?= date('d/m/Y', strtotime($evento['data_inicio'])) ?> - 
                                    <?= date('d/m/Y', strtotime($evento['data_fim'])) ?>
                                </p>
                            </div>
                            <a href="pages/eventos.php?id=<?= $evento['id'] ?>" class="btn-custom btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Links Úteis -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fas fa-link text-blue-600"></i>
                    Links Úteis
                </h3>
            </div>
            <div class="card-body">
                <div class="space-y-3">
                    <a href="admin/install.php" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-database text-blue-600"></i>
                            <div>
                                <h4 class="font-medium">Instalar Banco de Dados</h4>
                                <p class="text-sm text-gray-600">Configurar estrutura inicial</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="admin/update_database.php" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-sync text-green-600"></i>
                            <div>
                                <h4 class="font-medium">Atualizar Sistema</h4>
                                <p class="text-sm text-gray-600">Aplicar atualizações pendentes</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="debug/" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-bug text-yellow-600"></i>
                            <div>
                                <h4 class="font-medium">Ferramentas de Debug</h4>
                                <p class="text-sm text-gray-600">Diagnóstico do sistema</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$pageContent = ob_get_clean();
include 'includes/layout.php';
?>
