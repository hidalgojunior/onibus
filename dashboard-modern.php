<?php
$current_page = "dashboard";
include 'config/config.php';

// Obter estatísticas do sistema
$conn = getDatabaseConnection();
$stats = [
    'total_alunos' => 0,
    'total_eventos' => 0,
    'total_onibus' => 0,
    'eventos_ativos' => 0,
    'alocacoes_hoje' => 0,
    'alunos_presentes_hoje' => 0,
    'ultimos_eventos' => [],
    'proximos_eventos' => [],
    'onibus_status' => [],
    'alertas' => []
];

if (!$conn->connect_error) {
    // Estatísticas básicas
    $result = $conn->query("SELECT COUNT(*) as total FROM alunos");
    if ($result) $stats['total_alunos'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM eventos");
    if ($result) $stats['total_eventos'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM onibus");
    if ($result) $stats['total_onibus'] = $result->fetch_assoc()['total'];
    
    // Eventos ativos (hoje ou futuros)
    $data_hoje = date('Y-m-d');
    $result = $conn->query("SELECT COUNT(*) as total FROM eventos WHERE data_fim >= '$data_hoje'");
    if ($result) $stats['eventos_ativos'] = $result->fetch_assoc()['total'];
    
    // Próximos eventos
    $result = $conn->query("SELECT * FROM eventos WHERE data_inicio >= '$data_hoje' ORDER BY data_inicio ASC LIMIT 5");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $stats['proximos_eventos'][] = $row;
        }
    }
    
    // Últimos eventos
    $result = $conn->query("SELECT * FROM eventos ORDER BY id DESC LIMIT 5");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $stats['ultimos_eventos'][] = $row;
        }
    }
    
    // Status dos ônibus (simulado)
    $result = $conn->query("SELECT *, 'Operacional' as status FROM onibus ORDER BY nome");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $stats['onibus_status'][] = $row;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TransportePro</title>
    
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .chart-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .event-item {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .event-item:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateX(5px);
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-online { background: #28a745; animation: pulse 2s infinite; }
        .status-warning { background: #ffc107; }
        .status-offline { background: #dc3545; }
        
        @keyframes pulse {
            0% { opacity: 1; box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { opacity: 0.7; box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
            100% { opacity: 1; box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }
        
        .gradient-primary { background: linear-gradient(135deg, #667eea, #764ba2); }
        .gradient-success { background: linear-gradient(135deg, #28a745, #20c997); }
        .gradient-warning { background: linear-gradient(135deg, #ffc107, #fd7e14); }
        .gradient-info { background: linear-gradient(135deg, #17a2b8, #6f42c1); }
        .gradient-danger { background: linear-gradient(135deg, #dc3545, #e83e8c); }
    </style>
</head>

<body>
    <?php include 'includes/navbar-modern.php'; ?>
    
    <div class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-6 fw-bold text-dark mb-1">Dashboard</h1>
                        <p class="text-muted mb-0">Visão geral do sistema de transporte escolar</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary rounded-pill px-4" onclick="atualizarDados()">
                            <i class="fas fa-sync-alt me-2"></i>Atualizar
                        </button>
                        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#novoEventoModal">
                            <i class="fas fa-plus me-2"></i>Novo Evento
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stats-card p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon gradient-primary me-3">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total de Alunos</div>
                            <div class="h3 fw-bold text-dark mb-0"><?= number_format($stats['total_alunos']) ?></div>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> +12% este mês</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon gradient-success me-3">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Eventos Ativos</div>
                            <div class="h3 fw-bold text-dark mb-0"><?= $stats['eventos_ativos'] ?></div>
                            <small class="text-info"><i class="fas fa-info-circle"></i> Atualmente</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon gradient-warning me-3">
                            <i class="fas fa-bus"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Frota Total</div>
                            <div class="h3 fw-bold text-dark mb-0"><?= $stats['total_onibus'] ?></div>
                            <small class="text-success"><i class="fas fa-check-circle"></i> Todos operacionais</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon gradient-info me-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total de Eventos</div>
                            <div class="h3 fw-bold text-dark mb-0"><?= $stats['total_eventos'] ?></div>
                            <small class="text-primary"><i class="fas fa-calendar-check"></i> Todos os tempos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row g-4">
            <!-- Charts Section -->
            <div class="col-xl-8">
                <div class="chart-container p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0">Estatísticas de Eventos</h5>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="chartPeriod" id="chart7d" checked>
                            <label class="btn btn-outline-primary btn-sm" for="chart7d">7 dias</label>
                            <input type="radio" class="btn-check" name="chartPeriod" id="chart30d">
                            <label class="btn btn-outline-primary btn-sm" for="chart30d">30 dias</label>
                            <input type="radio" class="btn-check" name="chartPeriod" id="chart90d">
                            <label class="btn btn-outline-primary btn-sm" for="chart90d">90 dias</label>
                        </div>
                    </div>
                    <canvas id="eventsChart" height="100"></canvas>
                </div>
                
                <!-- Recent Events -->
                <div class="chart-container p-4">
                    <h5 class="fw-bold text-dark mb-3">Eventos Recentes</h5>
                    <div class="row g-3">
                        <?php if (empty($stats['ultimos_eventos'])): ?>
                            <div class="col-12">
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-3">Nenhum evento encontrado</p>
                                    <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#novoEventoModal">
                                        <i class="fas fa-plus me-2"></i>Criar Primeiro Evento
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($stats['ultimos_eventos'] as $evento): ?>
                                <div class="col-md-6">
                                    <div class="event-item p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="fw-semibold text-dark mb-1"><?= htmlspecialchars($evento['nome']) ?></h6>
                                                <p class="text-muted small mb-2"><?= htmlspecialchars($evento['local']) ?></p>
                                                <div class="d-flex align-items-center gap-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?= date('d/m/Y', strtotime($evento['data_inicio'])) ?>
                                                    </small>
                                                    <?php if ($evento['data_inicio'] >= date('Y-m-d')): ?>
                                                        <span class="badge bg-success">Ativo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Finalizado</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-link text-muted" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="eventos.php?edit=<?= $evento['id'] ?>">
                                                        <i class="fas fa-edit me-2"></i>Editar
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="verDetalhes(<?= $evento['id'] ?>)">
                                                        <i class="fas fa-eye me-2"></i>Ver Detalhes
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-xl-4">
                <!-- Próximos Eventos -->
                <div class="chart-container p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3">Próximos Eventos</h5>
                    <?php if (empty($stats['proximos_eventos'])): ?>
                        <div class="text-center py-3">
                            <i class="fas fa-calendar-plus text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">Nenhum evento programado</p>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($stats['proximos_eventos'] as $evento): ?>
                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                    <div class="me-3">
                                        <div class="bg-primary text-white rounded-2 p-2 text-center" style="min-width: 50px;">
                                            <div class="fw-bold"><?= date('d', strtotime($evento['data_inicio'])) ?></div>
                                            <small style="font-size: 0.7rem;"><?= strtoupper(date('M', strtotime($evento['data_inicio']))) ?></small>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-semibold mb-1"><?= htmlspecialchars($evento['nome']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($evento['local']) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Status da Frota -->
                <div class="chart-container p-4">
                    <h5 class="fw-bold text-dark mb-3">Status da Frota</h5>
                    <?php if (empty($stats['onibus_status'])): ?>
                        <div class="text-center py-3">
                            <i class="fas fa-bus text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">Nenhum ônibus cadastrado</p>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($stats['onibus_status'] as $onibus): ?>
                                <div class="d-flex align-items-center justify-content-between p-2">
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator status-online me-3"></div>
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($onibus['nome']) ?></div>
                                            <small class="text-muted">Capacidade: <?= $onibus['capacidade'] ?></small>
                                        </div>
                                    </div>
                                    <span class="badge bg-success">Operacional</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Initialize Chart
        const ctx = document.getElementById('eventsChart').getContext('2d');
        const eventsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Eventos Criados',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        function atualizarDados() {
            // Simular carregamento
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Atualizando...';
            btn.disabled = true;
            
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        function verDetalhes(eventoId) {
            window.location.href = `eventos.php?view=${eventoId}`;
        }
    </script>
</body>
</html>
