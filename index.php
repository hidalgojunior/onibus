<?php
// Configurações da página
$page_title = "Dashboard";
$page_description = "Painel de Controle - Gestão de Transporte Escolar";

// Incluir configuração do banco
include 'config/config.php';

// Conectar ao banco e buscar dados reais
$conn = getDatabaseConnection();
$stats = [
    'total_alunos' => 0,
    'alunos_ativos' => 0,
    'eventos_ativos' => 0,
    'total_onibus' => 0,
    'presencas_hoje' => 0,
    'alocacoes_ativas' => 0,
    'qr_codes_gerados' => 0,
    'autorizacoes_pendentes' => 0
];

$charts_data = [
    'presencas_semana' => [],
    'eventos_status' => [],
    'alocacoes_onibus' => [],
    'alunos_evolucao' => []
];

if (!$conn->connect_error) {
    // Total de alunos
    $result = $conn->query("SELECT COUNT(*) as total FROM alunos");
    if ($result) $stats['total_alunos'] = $result->fetch_assoc()['total'];
    
    // Alunos ativos (todos os alunos cadastrados)
    $stats['alunos_ativos'] = $stats['total_alunos'];
    
    // Eventos ativos
    $result = $conn->query("SELECT COUNT(*) as total FROM eventos WHERE data_fim >= CURDATE() OR data_fim IS NULL");
    if ($result) $stats['eventos_ativos'] = $result->fetch_assoc()['total'];
    
    // Total de ônibus
    $result = $conn->query("SELECT COUNT(*) as total FROM onibus");
    if ($result) $stats['total_onibus'] = $result->fetch_assoc()['total'];
    
    // Presenças de hoje
    $result = $conn->query("SELECT COUNT(*) as total FROM presencas WHERE DATE(data) = CURDATE()");
    if ($result) $stats['presencas_hoje'] = $result->fetch_assoc()['total'];
    
    // Alocações ativas
    $result = $conn->query("SELECT COUNT(*) as total FROM alocacoes_onibus");
    if ($result) $stats['alocacoes_ativas'] = $result->fetch_assoc()['total'];
    
    // QR Codes gerados
    $result = $conn->query("SELECT COUNT(*) as total FROM qr_codes");
    if ($result) $stats['qr_codes_gerados'] = $result->fetch_assoc()['total'];
    
    // Autorizações pendentes (todas as autorizações)
    $result = $conn->query("SELECT COUNT(*) as total FROM autorizacoes");
    if ($result) $stats['autorizacoes_pendentes'] = $result->fetch_assoc()['total'];
    
    // Dados para gráfico de presenças da semana
    $result = $conn->query("
        SELECT DATE(data) as data_presenca, COUNT(*) as total 
        FROM presencas 
        WHERE data >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(data)
        ORDER BY data_presenca
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $charts_data['presencas_semana'][] = [
                'data' => date('d/m', strtotime($row['data_presenca'])),
                'total' => intval($row['total'])
            ];
        }
    }
    
    // Dados para gráfico de status dos eventos
    $result = $conn->query("
        SELECT 
            CASE 
                WHEN data_fim >= CURDATE() THEN 'ativo'
                WHEN data_fim < CURDATE() THEN 'finalizado'
                ELSE 'programado'
            END as status,
            COUNT(*) as total
        FROM eventos 
        GROUP BY status
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $charts_data['eventos_status'][] = [
                'status' => $row['status'],
                'total' => intval($row['total'])
            ];
        }
    }
    
    // Dados para gráfico de alocações por ônibus
    $result = $conn->query("
        SELECT o.numero as onibus, COUNT(ao.id) as alocacoes
        FROM onibus o
        LEFT JOIN alocacoes_onibus ao ON o.id = ao.onibus_id
        GROUP BY o.id, o.numero
        ORDER BY o.numero
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $charts_data['alocacoes_onibus'][] = [
                'onibus' => 'Ônibus ' . $row['onibus'],
                'alocacoes' => intval($row['alocacoes'])
            ];
        }
    }
    
    // Dados para evolução de alunos (últimos 6 meses)
    $result = $conn->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as mes,
            COUNT(*) as novos_alunos
        FROM alunos 
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY mes
        ORDER BY mes
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $charts_data['alunos_evolucao'][] = [
                'mes' => date('M/y', strtotime($row['mes'] . '-01')),
                'total' => intval($row['novos_alunos'])
            ];
        }
    }
}

$custom_css = '
/* Dashboard Específico */
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    border-left: 4px solid var(--primary-color);
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.stat-card.success { border-left-color: var(--success-color); }
.stat-card.warning { border-left-color: var(--warning-color); }
.stat-card.danger { border-left-color: var(--danger-color); }
.stat-card.info { border-left-color: var(--accent-color); }

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--white);
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
}

.stat-icon.success { background: linear-gradient(135deg, var(--success-color), #38a169); }
.stat-icon.warning { background: linear-gradient(135deg, var(--warning-color), #d69e2e); }
.stat-icon.danger { background: linear-gradient(135deg, var(--danger-color), #e53e3e); }
.stat-icon.info { background: linear-gradient(135deg, var(--accent-color), #3182ce); }

.stat-content h3 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0 0 0.5rem 0;
    line-height: 1;
}

.stat-content p {
    color: var(--text-gray);
    margin: 0;
    font-weight: 500;
    font-size: 0.95rem;
}

.stat-trend {
    display: flex;
    align-items: center;
    margin-top: 1rem;
    font-size: 0.875rem;
}

.trend-positive {
    color: var(--success-color);
}

.trend-negative {
    color: var(--danger-color);
}

.trend-neutral {
    color: var(--text-gray);
}

.charts-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.chart-large {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow);
}

.chart-small {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow);
}

.chart-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-dark);
    margin: 0 0 0.5rem 0;
}

.chart-subtitle {
    color: var(--text-gray);
    font-size: 0.875rem;
    margin: 0 0 2rem 0;
}

.chart-canvas {
    position: relative;
    height: 300px;
    width: 100%;
}

.chart-canvas canvas {
    max-height: 300px !important;
}

.bottom-charts {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.quick-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-top: 2rem;
}

.quick-stat-item {
    background: var(--light-gray);
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
}

.quick-stat-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-dark);
}

.quick-stat-label {
    font-size: 0.8rem;
    color: var(--text-gray);
    margin-top: 0.25rem;
}

@media (max-width: 768px) {
    .charts-section {
        grid-template-columns: 1fr;
    }
    
    .bottom-charts {
        grid-template-columns: 1fr;
    }
    
    .dashboard-stats {
        grid-template-columns: 1fr;
    }
}
';

$custom_js = '
// Dados dos gráficos vindos do PHP
const presencasData = ' . json_encode($charts_data['presencas_semana']) . ';
const eventosData = ' . json_encode($charts_data['eventos_status']) . ';
const alocacoesData = ' . json_encode($charts_data['alocacoes_onibus']) . ';
const alunosData = ' . json_encode($charts_data['alunos_evolucao']) . ';

// Configurações globais do Chart.js
Chart.defaults.font.family = "Inter, sans-serif";
Chart.defaults.color = "#718096";

// Gráfico de Presenças da Semana
const ctxPresencas = document.getElementById("presencasChart");
if (ctxPresencas && presencasData.length > 0) {
    new Chart(ctxPresencas, {
        type: "line",
        data: {
            labels: presencasData.map(item => item.data),
            datasets: [{
                label: "Presenças",
                data: presencasData.map(item => item.total),
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
                legend: { display: false },
                tooltip: {
                    backgroundColor: "rgba(45, 55, 72, 0.9)",
                    titleColor: "#ffffff",
                    bodyColor: "#ffffff"
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: "#f7fafc" },
                    ticks: { color: "#718096" }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: "#718096" }
                }
            }
        }
    });
}

// Gráfico de Status dos Eventos
const ctxEventos = document.getElementById("eventosChart");
if (ctxEventos && eventosData.length > 0) {
    const cores = {
        "ativo": "#38a169",
        "finalizado": "#718096", 
        "programado": "#4299e1"
    };
    
    new Chart(ctxEventos, {
        type: "doughnut",
        data: {
            labels: eventosData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
            datasets: [{
                data: eventosData.map(item => item.total),
                backgroundColor: eventosData.map(item => cores[item.status] || "#718096"),
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

// Gráfico de Alocações por Ônibus
const ctxAlocacoes = document.getElementById("alocacoesChart");
if (ctxAlocacoes && alocacoesData.length > 0) {
    new Chart(ctxAlocacoes, {
        type: "bar",
        data: {
            labels: alocacoesData.map(item => item.onibus),
            datasets: [{
                label: "Alocações",
                data: alocacoesData.map(item => item.alocacoes),
                backgroundColor: "#4299e1",
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
                    ticks: { color: "#718096" }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: "#718096" }
                }
            }
        }
    });
}

// Gráfico de Evolução de Alunos
const ctxAlunos = document.getElementById("alunosChart");
if (ctxAlunos && alunosData.length > 0) {
    new Chart(ctxAlunos, {
        type: "bar",
        data: {
            labels: alunosData.map(item => item.mes),
            datasets: [{
                label: "Novos Alunos",
                data: alunosData.map(item => item.total),
                backgroundColor: "#38a169",
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
                    ticks: { color: "#718096" }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: "#718096" }
                }
            }
        }
    });
}

// Atualizar dados a cada 5 minutos
setInterval(() => {
    window.location.reload();
}, 300000);
';

ob_start();
?>

<!-- Dashboard Statistics -->
<div class="dashboard-stats">
    <!-- Total de Alunos -->
    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-content">
                <h3><?php echo $stats['total_alunos']; ?></h3>
                <p>Total de Alunos</p>
            </div>
            <div class="stat-icon success">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>
        <div class="stat-trend">
            <span class="trend-positive">
                <i class="fas fa-arrow-up"></i>
                <?php echo $stats['alunos_ativos']; ?> ativos
            </span>
        </div>
    </div>
    
    <!-- Eventos Ativos -->
    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-content">
                <h3><?php echo $stats['eventos_ativos']; ?></h3>
                <p>Eventos Ativos</p>
            </div>
            <div class="stat-icon info">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
        <div class="stat-trend">
            <span class="trend-neutral">
                <i class="fas fa-clock"></i>
                Em andamento
            </span>
        </div>
    </div>
    
    <!-- Frota de Ônibus -->
    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-content">
                <h3><?php echo $stats['total_onibus']; ?></h3>
                <p>Ônibus na Frota</p>
            </div>
            <div class="stat-icon warning">
                <i class="fas fa-bus"></i>
            </div>
        </div>
        <div class="stat-trend">
            <span class="trend-positive">
                <i class="fas fa-check"></i>
                Operacional
            </span>
        </div>
    </div>
    
    <!-- Alocações Ativas -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-content">
                <h3><?php echo $stats['alocacoes_ativas']; ?></h3>
                <p>Alocações Ativas</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-route"></i>
            </div>
        </div>
        <div class="stat-trend">
            <span class="trend-positive">
                <i class="fas fa-users"></i>
                Alunos alocados
            </span>
        </div>
    </div>
</div>

<!-- Main Charts Section -->
<div class="charts-section">
    <!-- Presenças da Semana -->
    <div class="chart-large">
        <h3 class="chart-title">Presenças da Semana</h3>
        <p class="chart-subtitle">Controle de presença dos últimos 7 dias</p>
        <div class="chart-canvas">
            <canvas id="presencasChart"></canvas>
        </div>
    </div>
    
    <!-- Status dos Eventos -->
    <div class="chart-small">
        <h3 class="chart-title">Status dos Eventos</h3>
        <p class="chart-subtitle">Distribuição por situação</p>
        <div class="chart-canvas">
            <canvas id="eventosChart"></canvas>
        </div>
        
        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="quick-stat-item">
                <div class="quick-stat-value"><?php echo $stats['presencas_hoje']; ?></div>
                <div class="quick-stat-label">Presenças Hoje</div>
            </div>
            <div class="quick-stat-item">
                <div class="quick-stat-value"><?php echo $stats['qr_codes_gerados']; ?></div>
                <div class="quick-stat-label">QR Codes</div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Charts -->
<div class="bottom-charts">
    <!-- Alocações por Ônibus -->
    <div class="chart-large">
        <h3 class="chart-title">Alocações por Ônibus</h3>
        <p class="chart-subtitle">Distribuição de alunos por veículo</p>
        <div class="chart-canvas">
            <canvas id="alocacoesChart"></canvas>
        </div>
    </div>
    
    <!-- Evolução de Cadastros -->
    <div class="chart-large">
        <h3 class="chart-title">Novos Alunos</h3>
        <p class="chart-subtitle">Cadastros nos últimos meses</p>
        <div class="chart-canvas">
            <canvas id="alunosChart"></canvas>
        </div>
    </div>
</div>

<!-- Sistema de Alertas -->
<?php if ($stats['autorizacoes_pendentes'] > 0): ?>
<div class="chart-large" style="margin-top: 2rem;">
    <div style="background: rgba(214, 158, 46, 0.1); border-left: 4px solid var(--warning-color); padding: 1rem; border-radius: 8px;">
        <h4 style="color: var(--warning-color); margin: 0 0 0.5rem 0;">
            <i class="fas fa-exclamation-triangle"></i>
            Atenção Necessária
        </h4>
        <p style="margin: 0; color: var(--text-dark);">
            Existem <strong><?php echo $stats['autorizacoes_pendentes']; ?> autorizações pendentes</strong> aguardando aprovação.
        </p>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();

// Incluir layout profissional
include 'includes/layout-professional.php';
?>
