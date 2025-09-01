<?php
// Configurações da página
$page_title = "Dashboard";
$page_description = "Gestão de Frota";

// Incluir configuração do banco
include 'config/config.php';

// Inicializar estatísticas
$stats = [
    'onibus_ativos' => 24,
    'total_onibus' => 28,
    'taxa_pontualidade' => 94.2,
    'passageiros_dia' => 1247,
    'rotas_ativas' => 12,
    'utilizacao_frota' => [
        ['mes' => 'Jan', 'utilizacao' => 85],
        ['mes' => 'Fev', 'utilizacao' => 78],
        ['mes' => 'Mar', 'utilizacao' => 82],
        ['mes' => 'Abr', 'utilizacao' => 88],
        ['mes' => 'Mai', 'utilizacao' => 91],
        ['mes' => 'Jun', 'utilizacao' => 89]
    ],
    'custos_manutencao' => [
        ['tipo' => 'Preventiva', 'valor' => 45000],
        ['tipo' => 'Corretiva', 'valor' => 32000],
        ['tipo' => 'Emergencial', 'valor' => 18000],
        ['tipo' => 'Revisão', 'valor' => 25000]
    ],
    'alertas_recentes' => [
        [
            'titulo' => 'Manutenção Programada',
            'descricao' => 'Ônibus 15 precisa de revisão em 3 dias'
        ],
        [
            'titulo' => 'Combustível Baixo',
            'descricao' => 'Ônibus 08 com nível baixo de combustível'
        ],
        [
            'titulo' => 'Documentação Vencendo',
            'descricao' => 'Licenciamento do ônibus 22 vence em 15 dias'
        ]
    ],
    'manutencoes_programadas' => [
        [
            'onibus' => 'Ônibus 15',
            'tipo' => 'Revisão Geral',
            'data' => '2025-09-04'
        ],
        [
            'onibus' => 'Ônibus 08',
            'tipo' => 'Troca de Óleo',
            'data' => '2025-09-06'
        ]
    ]
];

// Conectar ao banco
$conn = getDatabaseConnection();

if (!$conn->connect_error) {
    // Total de ônibus
    $result = $conn->query("SELECT COUNT(*) as total FROM onibus");
    if ($result) $stats['total_onibus'] = $result->fetch_assoc()['total'];
    
    // Ônibus ativos (como não há coluna status, considerar todos os ônibus como ativos)
    $result = $conn->query("SELECT COUNT(*) as total FROM onibus");
    if ($result) $stats['onibus_ativos'] = $result->fetch_assoc()['total'];
    
    // Eventos como proxy para passageiros/dia
    $result = $conn->query("SELECT COUNT(DISTINCT ao.aluno_id) as total FROM alocacoes_onibus ao JOIN eventos e ON ao.evento_id = e.id WHERE e.data_inicio <= CURDATE() AND e.data_fim >= CURDATE()");
    if ($result) $stats['passageiros_dia'] = $result->fetch_assoc()['total'] ?: 0;
    
    // Total de eventos ativos (proxy para rotas)
    $result = $conn->query("SELECT COUNT(*) as total FROM eventos WHERE data_fim >= CURDATE()");
    if ($result) $stats['rotas_ativas'] = $result->fetch_assoc()['total'] ?: 12;
}

$custom_js = '
// Dados para os gráficos
const utilizacaoData = ' . json_encode($stats['utilizacao_frota']) . ';
const custosData = ' . json_encode($stats['custos_manutencao']) . ';

// Gráfico de Utilização da Frota
const ctxUtilizacao = document.getElementById("utilizacaoChart");
if (ctxUtilizacao) {
    new Chart(ctxUtilizacao, {
        type: "line",
        data: {
            labels: utilizacaoData.map(item => item.mes),
            datasets: [{
                data: utilizacaoData.map(item => item.utilizacao),
                borderColor: "#4299e1",
                backgroundColor: "rgba(66, 153, 225, 0.1)",
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: "#4299e1",
                pointBorderColor: "#ffffff",
                pointBorderWidth: 2,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: "#f7fafc" },
                    ticks: { 
                        color: "#718096",
                        callback: function(value) { return value + "%"; }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: "#718096" }
                }
            }
        }
    });
}

// Gráfico de Custos de Manutenção
const ctxCustos = document.getElementById("custosChart");
if (ctxCustos) {
    new Chart(ctxCustos, {
        type: "bar",
        data: {
            labels: custosData.map(item => item.tipo),
            datasets: [{
                data: custosData.map(item => item.valor),
                backgroundColor: ["#2d3748", "#2d3748", "#2d3748", "#2d3748"],
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: "#f7fafc" },
                    ticks: { 
                        color: "#718096",
                        callback: function(value) { 
                            return "R$ " + (value/1000).toFixed(0) + "k"; 
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { 
                        color: "#718096",
                        maxRotation: 45
                    }
                }
            }
        }
    });
}
';

ob_start();
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <!-- Ônibus Ativos -->
    <div class="stat-card">
        <div class="stat-header">
            <h3 class="stat-title">Ônibus Ativos</h3>
            <div class="stat-icon primary">
                <i class="fas fa-bus"></i>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['onibus_ativos']; ?></div>
            <p class="stat-subtitle">de <?php echo $stats['total_onibus']; ?> total</p>
        </div>
        <div class="stat-footer">
            <span class="stat-change positive">+5.2%</span>
            <span class="stat-period">vs. mês anterior</span>
        </div>
    </div>
    
    <!-- Taxa de Pontualidade -->
    <div class="stat-card">
        <div class="stat-header">
            <h3 class="stat-title">Taxa de Pontualidade</h3>
            <div class="stat-icon success">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['taxa_pontualidade']; ?>%</div>
            <p class="stat-subtitle">média mensal</p>
        </div>
        <div class="stat-footer">
            <span class="stat-change positive">+2.1%</span>
            <span class="stat-period">vs. mês anterior</span>
        </div>
    </div>
    
    <!-- Passageiros/Dia -->
    <div class="stat-card">
        <div class="stat-header">
            <h3 class="stat-title">Passageiros/Dia</h3>
            <div class="stat-icon info">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($stats['passageiros_dia'], 0, ',', '.'); ?></div>
            <p class="stat-subtitle">média diária</p>
        </div>
        <div class="stat-footer">
            <span class="stat-change negative">-1.8%</span>
            <span class="stat-period">vs. mês anterior</span>
        </div>
    </div>
    
    <!-- Rotas Ativas -->
    <div class="stat-card">
        <div class="stat-header">
            <h3 class="stat-title">Rotas Ativas</h3>
            <div class="stat-icon warning">
                <i class="fas fa-route"></i>
            </div>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo $stats['rotas_ativas']; ?></div>
            <p class="stat-subtitle">em operação</p>
        </div>
        <div class="stat-footer">
            <span class="stat-change neutral">+0%</span>
            <span class="stat-period">vs. mês anterior</span>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="charts-grid">
    <!-- Uso da Frota Chart -->
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">Uso da Frota</h3>
            <p class="chart-subtitle">Percentual de utilização dos ônibus nos últimos 6 meses</p>
        </div>
        <div class="chart-wrapper">
            <canvas id="utilizacaoChart"></canvas>
        </div>
    </div>
    
    <!-- Custos de Manutenção Chart -->
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">Custos de Manutenção</h3>
            <p class="chart-subtitle">Distribuição dos custos por tipo de manutenção</p>
        </div>
        <div class="chart-wrapper">
            <canvas id="custosChart"></canvas>
        </div>
    </div>
</div>

<!-- Bottom Row -->
<div class="bottom-row">
    <!-- Alertas Recentes -->
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">
                <i class="fas fa-exclamation-triangle" style="color: var(--warning-color); margin-right: 0.5rem;"></i>
                Alertas Recentes
            </h3>
            <p class="chart-subtitle">Notificações e eventos importantes do sistema</p>
        </div>
        
        <?php if (empty($stats['alertas_recentes'])): ?>
            <div style="padding: 2rem; text-align: center; color: var(--text-gray);">
                <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 1rem; color: var(--success-color);"></i>
                <p style="margin: 0;">Nenhum alerta no momento</p>
            </div>
        <?php else: ?>
            <?php foreach ($stats['alertas_recentes'] as $alerta): ?>
                <div class="alert-item">
                    <div class="alert-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title"><?php echo htmlspecialchars($alerta['titulo']); ?></div>
                        <div class="alert-description"><?php echo htmlspecialchars($alerta['descricao']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Manutenções Programadas -->
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">
                <i class="fas fa-tools" style="color: var(--accent-color); margin-right: 0.5rem;"></i>
                Manutenções Programadas
            </h3>
            <p class="chart-subtitle">Próximas manutenções agendadas para a frota</p>
        </div>
        
        <?php if (empty($stats['manutencoes_programadas'])): ?>
            <div style="padding: 2rem; text-align: center; color: var(--text-gray);">
                <i class="fas fa-calendar-check" style="font-size: 2rem; margin-bottom: 1rem; color: var(--success-color);"></i>
                <p style="margin: 0;">Nenhuma manutenção programada</p>
            </div>
        <?php else: ?>
            <?php foreach ($stats['manutencoes_programadas'] as $manutencao): ?>
                <div class="alert-item" style="border-left-color: var(--accent-color);">
                    <div class="alert-icon" style="color: var(--accent-color);">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title"><?php echo htmlspecialchars($manutencao['onibus']); ?> - <?php echo htmlspecialchars($manutencao['tipo']); ?></div>
                        <div class="alert-description">Agendado para <?php echo date('d/m/Y', strtotime($manutencao['data'])); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();

// Incluir layout
include 'includes/layout-professional.php';
?>
