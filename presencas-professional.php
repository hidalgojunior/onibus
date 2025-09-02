<?php
// Configurações da página
$page_title = "Controle de Presenças";
$page_description = "Scanner QR e Registro de Embarque/Desembarque";

// Incluir configuração do banco
include 'config/config.php';

// Conectar ao banco
$conn = getDatabaseConnection();

// Inicializar estatísticas
$stats = [
    'presencas_hoje' => 0,
    'alunos_embarcados' => 0,
    'onibus_ativos' => 0,
    'qr_codes_gerados' => 0
];

if (!$conn->connect_error) {
    $hoje = date('Y-m-d');
    
    // Presenças registradas hoje
    $result = $conn->query("SELECT COUNT(*) as total FROM presencas WHERE data = '$hoje'");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['presencas_hoje'] = $row['total'];
    }
    
    // Alunos embarcados hoje
    $result = $conn->query("SELECT COUNT(DISTINCT aluno_id) as total FROM presencas WHERE data = '$hoje' AND tipo_registro = 'embarque'");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['alunos_embarcados'] = $row['total'];
    }
    
    // Ônibus ativos
    $result = $conn->query("SELECT COUNT(*) as total FROM onibus WHERE ativo = 1");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['onibus_ativos'] = $row['total'];
    }
    
    // QR Codes gerados (usando a estrutura antiga da tabela)
    $result = $conn->query("SELECT COUNT(*) as total FROM qr_codes");
    if ($result && $row = $result->fetch_assoc()) {
        $stats['qr_codes_gerados'] = $row['total'];
    }
}

$custom_css = '
.scanner-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 300px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.scanner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.qr-frame {
    width: 200px;
    height: 200px;
    border: 3px solid #fff;
    border-radius: 10px;
    position: relative;
    margin-bottom: 20px;
}

.qr-frame::before {
    content: "";
    position: absolute;
    top: -3px;
    left: -3px;
    right: -3px;
    bottom: -3px;
    border: 2px solid #00ff88;
    border-radius: 13px;
    animation: scan 2s ease-in-out infinite;
}

@keyframes scan {
    0%, 100% { opacity: 0; }
    50% { opacity: 1; }
}

.presence-card {
    transition: all 0.3s ease;
    border-left: 4px solid #007bff;
}

.presence-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.presence-card.embarque {
    border-left-color: #28a745;
}

.presence-card.desembarque {
    border-left-color: #ffc107;
}

.bus-status {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 600;
}

.bus-status.ativo {
    background-color: #d4edda;
    color: #155724;
}

.bus-status.inativo {
    background-color: #f8d7da;
    color: #721c24;
}
';

// Incluir layout
$content = '
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-qrcode text-primary me-2"></i>
                        Controle de Presenças
                    </h1>
                    <p class="text-muted mb-0">Scanner QR Code e registro de embarque/desembarque</p>
                </div>
                <div class="text-end">
                    <a href="lista-manual-onibus.php" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-list me-2"></i>
                        Lista Manual
                    </a>
                    <div class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fas fa-calendar-day me-1"></i>
                        ' . date('d/m/Y') . '
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle bg-success p-3">
                            <i class="fas fa-check-circle text-white fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="card-title text-success mb-1">' . number_format($stats['presencas_hoje']) . '</h3>
                    <p class="card-text text-muted mb-0">Presenças Hoje</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle bg-primary p-3">
                            <i class="fas fa-users text-white fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="card-title text-primary mb-1">' . number_format($stats['alunos_embarcados']) . '</h3>
                    <p class="card-text text-muted mb-0">Alunos Embarcados</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle bg-warning p-3">
                            <i class="fas fa-bus text-white fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="card-title text-warning mb-1">' . number_format($stats['onibus_ativos']) . '</h3>
                    <p class="card-text text-muted mb-0">Ônibus Ativos</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle bg-info p-3">
                            <i class="fas fa-qrcode text-white fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="card-title text-info mb-1">' . number_format($stats['qr_codes_gerados']) . '</h3>
                    <p class="card-text text-muted mb-0">QR Codes Ativos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Scanner QR Code -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-camera text-primary me-2"></i>
                        Scanner QR Code
                    </h5>
                </div>
                <div class="card-body">
                    <div class="scanner-container">
                        <div class="scanner-overlay">
                            <div class="qr-frame"></div>
                            <h4 class="mb-3">Scanner QR Code</h4>
                            <p class="text-center mb-4">Posicione o QR Code do aluno na área acima</p>
                            <button class="btn btn-light btn-lg" onclick="iniciarScanner()">
                                <i class="fas fa-camera me-2"></i>
                                Iniciar Scanner
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-select" id="onibus_scanner">
                                    <option value="">Selecionar Ônibus</option>';

                                    $result = $conn->query("SELECT * FROM onibus WHERE ativo = 1 ORDER BY numero");
                                    if ($result) {
                                        while ($onibus = $result->fetch_assoc()) {
                                            $content .= "<option value='{$onibus['id']}'>Ônibus {$onibus['numero']} - {$onibus['rota_descricao']}</option>";
                                        }
                                    }
                                    
$content .= '
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" id="tipo_registro">
                                    <option value="embarque">Embarque</option>
                                    <option value="desembarque">Desembarque</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Presenças Recentes -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock text-primary me-2"></i>
                            Presenças Recentes
                        </h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="atualizarPresencas()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="presencas-recentes" style="max-height: 400px; overflow-y: auto;">';
                    
                        $hoje = date('Y-m-d');
                        $query = "
                            SELECT p.*, a.nome as aluno_nome, o.numero as onibus_numero, 
                                   p.horario_registro, p.tipo_registro
                            FROM presencas p
                            LEFT JOIN alunos a ON p.aluno_id = a.id
                            LEFT JOIN onibus o ON p.onibus_id = o.id
                            WHERE p.data = '$hoje'
                            ORDER BY p.created_at DESC
                            LIMIT 10
                        ";
                        
                        $result = $conn->query($query);
                        if ($result && $result->num_rows > 0) {
                            while ($presenca = $result->fetch_assoc()) {
                                $tipo_class = $presenca['tipo_registro'] == 'embarque' ? 'embarque' : 'desembarque';
                                $icon = $presenca['tipo_registro'] == 'embarque' ? 'arrow-up' : 'arrow-down';
                                $badge_color = $presenca['tipo_registro'] == 'embarque' ? 'success' : 'warning';
                                
                                $content .= "<div class='presence-card card mb-2 {$tipo_class}'>";
                                $content .= "<div class='card-body py-2'>";
                                $content .= "<div class='d-flex justify-content-between align-items-center'>";
                                $content .= "<div>";
                                $content .= "<h6 class='mb-1'>{$presenca['aluno_nome']}</h6>";
                                $content .= "<small class='text-muted'>Ônibus {$presenca['onibus_numero']}</small>";
                                $content .= "</div>";
                                $content .= "<div class='text-end'>";
                                $content .= "<span class='badge bg-{$badge_color} mb-1'>";
                                $content .= "<i class='fas fa-{$icon} me-1'></i>";
                                $content .= ucfirst($presenca['tipo_registro']);
                                $content .= "</span><br>";
                                $content .= "<small class='text-muted'>" . date('H:i', strtotime($presenca['created_at'])) . "</small>";
                                $content .= "</div>";
                                $content .= "</div>";
                                $content .= "</div>";
                                $content .= "</div>";
                            }
                        } else {
                            $content .= "<div class='text-center py-4'>";
                            $content .= "<i class='fas fa-clock fa-3x text-muted mb-3'></i>";
                            $content .= "<p class='text-muted'>Nenhuma presença registrada hoje</p>";
                            $content .= "</div>";
                        }
                        
$content .= '
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status dos Ônibus -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bus text-primary me-2"></i>
                        Status da Frota
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">';
                    
                        $query = "
                            SELECT o.*, 
                                   COUNT(DISTINCT ao.aluno_id) as alunos_alocados,
                                   COUNT(DISTINCT p.aluno_id) as alunos_embarcados_hoje
                            FROM onibus o
                            LEFT JOIN alocacoes_onibus ao ON o.id = ao.onibus_id AND ao.ativo = 1
                            LEFT JOIN presencas p ON o.id = p.onibus_id AND p.data = '$hoje' AND p.tipo_registro = 'embarque'
                            GROUP BY o.id
                            ORDER BY o.numero
                        ";
                        
                        $result = $conn->query($query);
                        if ($result && $result->num_rows > 0) {
                            while ($onibus = $result->fetch_assoc()) {
                                $status_class = $onibus['ativo'] ? 'ativo' : 'inativo';
                                $ocupacao_percent = $onibus['capacidade'] > 0 ? ($onibus['alunos_embarcados_hoje'] / $onibus['capacidade']) * 100 : 0;
                                
                                $content .= "<div class='col-lg-4 col-md-6 mb-3'>";
                                $content .= "<div class='card border-0 bg-light h-100'>";
                                $content .= "<div class='card-body'>";
                                $content .= "<div class='d-flex justify-content-between align-items-start mb-3'>";
                                $content .= "<h6 class='card-title mb-0'>Ônibus {$onibus['numero']}</h6>";
                                $content .= "<span class='bus-status {$status_class}'>" . ucfirst($onibus['ativo'] ? 'Ativo' : 'Inativo') . "</span>";
                                $content .= "</div>";
                                
                                $content .= "<p class='text-muted small mb-2'>{$onibus['rota_descricao']}</p>";
                                $content .= "<p class='text-muted small mb-2'>Motorista: {$onibus['motorista_nome']}</p>";
                                $content .= "<p class='text-muted small mb-3'>Monitor: {$onibus['monitor_nome']}</p>";
                                
                                $content .= "<div class='row text-center'>";
                                $content .= "<div class='col-4'>";
                                $content .= "<small class='text-muted d-block'>Capacidade</small>";
                                $content .= "<strong>{$onibus['capacidade']}</strong>";
                                $content .= "</div>";
                                $content .= "<div class='col-4'>";
                                $content .= "<small class='text-muted d-block'>Alocados</small>";
                                $content .= "<strong>{$onibus['alunos_alocados']}</strong>";
                                $content .= "</div>";
                                $content .= "<div class='col-4'>";
                                $content .= "<small class='text-muted d-block'>Embarcaram</small>";
                                $content .= "<strong class='text-success'>{$onibus['alunos_embarcados_hoje']}</strong>";
                                $content .= "</div>";
                                $content .= "</div>";
                                
                                if ($onibus['capacidade'] > 0) {
                                    $content .= "<div class='mt-3'>";
                                    $content .= "<div class='progress' style='height: 6px;'>";
                                    $content .= "<div class='progress-bar bg-success' style='width: {$ocupacao_percent}%'></div>";
                                    $content .= "</div>";
                                    $content .= "<small class='text-muted'>" . number_format($ocupacao_percent, 1) . "% ocupação hoje</small>";
                                    $content .= "</div>";
                                }
                                
                                $content .= "</div>";
                                $content .= "</div>";
                                $content .= "</div>";
                            }
                        }
                        
$content .= '
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';

$custom_js = '
function iniciarScanner() {
    alert("Scanner QR Code será implementado com câmera do dispositivo. Funcionalidade em desenvolvimento.");
}

function atualizarPresencas() {
    location.reload();
}

// Auto-refresh a cada 30 segundos
setInterval(function() {
    atualizarPresencas();
}, 30000);
';

include 'includes/layout-professional.php';
$conn->close();
?>
