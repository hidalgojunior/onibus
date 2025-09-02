<?php
// Configurações da página
$page_title = "Ônibus";
$page_description = "Gestão de Frota";

// Incluir configuração do banco
include 'config/config.php';

// Conectar ao banco
$conn = getDatabaseConnection();
$onibus = [];

// Processar cadastro de novo ônibus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_onibus'])) {
    $numero = $_POST['numero'] ?? '';
    $placa = $_POST['placa'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $capacidade = intval($_POST['capacidade'] ?? 0);
    $motorista_nome = $_POST['motorista_nome'] ?? '';
    $monitor_nome = $_POST['monitor_nome'] ?? '';
    $responsavel_emergencia_nome = $_POST['responsavel_emergencia_nome'] ?? '';
    $responsavel_emergencia_whatsapp = $_POST['responsavel_emergencia_whatsapp'] ?? '';
    $rota_descricao = $_POST['rota_descricao'] ?? '';
    $turno = $_POST['turno'] ?? 'ambos';
    $dias_reservados = intval($_POST['dias_reservados'] ?? 10);
    
    if (!empty($numero) && !empty($tipo) && $capacidade > 0 && !empty($responsavel_emergencia_nome) && !empty($responsavel_emergencia_whatsapp)) {
        $query = "INSERT INTO onibus (numero, placa, tipo, capacidade, motorista_nome, monitor_nome, responsavel_emergencia_nome, responsavel_emergencia_whatsapp, rota_descricao, turno, dias_reservados, ativo, evento_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssissssssi", $numero, $placa, $tipo, $capacidade, $motorista_nome, $monitor_nome, $responsavel_emergencia_nome, $responsavel_emergencia_whatsapp, $rota_descricao, $turno, $dias_reservados);
        
        if ($stmt->execute()) {
            $success_message = "Ônibus cadastrado com sucesso!";
            // Recarregar a página para mostrar o novo ônibus
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit;
        } else {
            $error_message = "Erro ao cadastrar ônibus: " . $conn->error;
        }
    } else {
        $error_message = "Por favor, preencha todos os campos obrigatórios.";
    }
}

// Processar edição de ônibus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_onibus'])) {
    $id = intval($_POST['id']);
    $numero = $_POST['numero'] ?? '';
    $placa = $_POST['placa'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $capacidade = intval($_POST['capacidade'] ?? 0);
    $motorista_nome = $_POST['motorista_nome'] ?? '';
    $monitor_nome = $_POST['monitor_nome'] ?? '';
    $responsavel_emergencia_nome = $_POST['responsavel_emergencia_nome'] ?? '';
    $responsavel_emergencia_whatsapp = $_POST['responsavel_emergencia_whatsapp'] ?? '';
    $rota_descricao = $_POST['rota_descricao'] ?? '';
    $turno = $_POST['turno'] ?? 'ambos';
    $dias_reservados = intval($_POST['dias_reservados'] ?? 10);
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    if (!empty($numero) && !empty($tipo) && $capacidade > 0 && !empty($responsavel_emergencia_nome) && !empty($responsavel_emergencia_whatsapp)) {
        $query = "UPDATE onibus SET numero = ?, placa = ?, tipo = ?, capacidade = ?, motorista_nome = ?, monitor_nome = ?, responsavel_emergencia_nome = ?, responsavel_emergencia_whatsapp = ?, rota_descricao = ?, turno = ?, dias_reservados = ?, ativo = ? WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssissssssiii", $numero, $placa, $tipo, $capacidade, $motorista_nome, $monitor_nome, $responsavel_emergencia_nome, $responsavel_emergencia_whatsapp, $rota_descricao, $turno, $dias_reservados, $ativo, $id);
        
        if ($stmt->execute()) {
            $success_message = "Ônibus atualizado com sucesso!";
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=2");
            exit;
        } else {
            $error_message = "Erro ao atualizar ônibus: " . $conn->error;
        }
    } else {
        $error_message = "Por favor, preencha todos os campos obrigatórios.";
    }
}

// Processar exclusão de ônibus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_onibus'])) {
    $id = intval($_POST['id']);
    
    // Verificar se há alunos alocados neste ônibus
    $check_alocacoes = $conn->query("SELECT COUNT(*) as total FROM alocacoes_onibus WHERE onibus_id = $id AND ativo = 1");
    $alocacoes = $check_alocacoes->fetch_assoc();
    
    if ($alocacoes['total'] > 0) {
        $error_message = "Não é possível excluir este ônibus pois há {$alocacoes['total']} aluno(s) alocado(s). Remova as alocações primeiro.";
    } else {
        $query = "DELETE FROM onibus WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $success_message = "Ônibus excluído com sucesso!";
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=3");
            exit;
        } else {
            $error_message = "Erro ao excluir ônibus: " . $conn->error;
        }
    }
}

// Mostrar mensagens de sucesso se redirecionado
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case '1':
            $success_message = "Ônibus cadastrado com sucesso!";
            break;
        case '2':
            $success_message = "Ônibus atualizado com sucesso!";
            break;
        case '3':
            $success_message = "Ônibus excluído com sucesso!";
            break;
    }
}

if (!$conn->connect_error) {
    $result = $conn->query("SELECT * FROM onibus ORDER BY numero");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $onibus[] = $row;
        }
    }
}

$custom_css = '
.buses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.bus-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    transition: all 0.2s ease;
    border-left: 4px solid var(--success-color);
    position: relative;
}

.bus-card:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-2px);
}

.bus-card.maintenance {
    border-left-color: var(--warning-color);
}

.bus-card.inactive {
    border-left-color: var(--danger-color);
}

.bus-header {
    display: flex;
    align-items: center;
    justify-content: between;
    margin-bottom: 1rem;
}

.bus-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
}

.bus-plate {
    font-size: 0.875rem;
    color: var(--text-gray);
    font-weight: 500;
    margin-top: 0.25rem;
}

.bus-status {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.bus-status.active {
    background-color: rgba(56, 161, 105, 0.1);
    color: var(--success-color);
}

.bus-status.maintenance {
    background-color: rgba(214, 158, 46, 0.1);
    color: var(--warning-color);
}

.bus-status.inactive {
    background-color: rgba(229, 62, 62, 0.1);
    color: var(--danger-color);
}

.bus-info {
    margin-bottom: 1rem;
}

.bus-info-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-gray);
}

.bus-info-item i {
    width: 16px;
    margin-right: 0.5rem;
    color: var(--accent-color);
}

.bus-capacity {
    margin-bottom: 1rem;
}

.capacity-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
    color: var(--text-gray);
    margin-bottom: 0.5rem;
}

.capacity-bar {
    height: 8px;
    background-color: var(--medium-gray);
    border-radius: 4px;
    overflow: hidden;
}

.capacity-fill {
    height: 100%;
    background-color: var(--success-color);
    transition: width 0.3s ease;
}

.bus-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--light-gray);
}

.btn-icon {
    background: none;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-gray);
    transition: all 0.2s ease;
    cursor: pointer;
    font-size: 14px;
}

.btn-icon:hover {
    background-color: var(--light-gray);
    color: var(--primary-color);
    transform: translateY(-1px);
}

.btn-icon:active {
    transform: translateY(0);
}

.btn-icon.view {
    color: var(--info-color);
}

.btn-icon.edit {
    color: var(--warning-color);
}

.btn-icon.delete {
    color: var(--danger-color);
}

.btn-icon.view:hover {
    background-color: rgba(54, 162, 235, 0.1);
    color: var(--info-color);
}

.btn-icon.edit:hover {
    background-color: rgba(255, 193, 7, 0.1);
    color: var(--warning-color);
}

.btn-icon.delete:hover {
    background-color: rgba(220, 53, 69, 0.1);
    color: var(--danger-color);
}

.fleet-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.fleet-stat {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    text-align: center;
}

.fleet-stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--white);
}

.fleet-stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.fleet-stat-label {
    font-size: 0.875rem;
    color: var(--text-gray);
    font-weight: 500;
}
';

$custom_js = '
function filterBuses() {
    const searchTerm = document.getElementById("searchInput").value.toLowerCase();
    const statusFilter = document.getElementById("statusFilter").value;
    
    const busCards = document.querySelectorAll(".bus-card");
    
    busCards.forEach(card => {
        const number = card.querySelector(".bus-number").textContent.toLowerCase();
        const tipo = card.querySelector(".bus-plate").textContent.toLowerCase(); // Agora contém o tipo
        const status = card.querySelector(".bus-status").textContent.toLowerCase();
        
        let showCard = true;
        
        // Filter by search term
        if (searchTerm && !number.includes(searchTerm) && !tipo.includes(searchTerm)) {
            showCard = false;
        }
        
        // Filter by tipo (usando o campo de status para filtrar por tipo)
        if (statusFilter && !tipo.includes(statusFilter)) {
            showCard = false;
        }
        
        card.style.display = showCard ? "block" : "none";
    });
}

function clearFilters() {
    document.getElementById("searchInput").value = "";
    document.getElementById("statusFilter").value = "";
    filterBuses();
}

function addBus() {
    alert("Funcionalidade de adicionar ônibus será implementada em breve");
}

function editBus(id) {
    alert("Editar ônibus " + id);
}

function viewBus(id) {
    alert("Ver detalhes do ônibus " + id);
}

function deleteBus(id) {
    if (confirm("Tem certeza que deseja excluir este ônibus?")) {
        alert("Ônibus " + id + " excluído");
    }
}
';

ob_start();
?>

<!-- Header da página -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-bus text-primary me-2"></i>
                    Gestão de Ônibus
                </h1>
                <p class="text-muted mb-0">Controle da frota e alocações</p>
            </div>
            <div>
                <a href="lista-manual-onibus.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-list me-2"></i>
                    Lista Manual
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoOnibusModal">
                    <i class="fas fa-plus me-2"></i>
                    Novo Ônibus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alerts -->
<?php if (isset($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= htmlspecialchars($success_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= htmlspecialchars($error_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Fleet Statistics -->
<div class="fleet-stats">
    <div class="fleet-stat">
        <div class="fleet-stat-icon" style="background-color: var(--success-color);">
            <i class="fas fa-bus"></i>
        </div>
        <div class="fleet-stat-value"><?php echo count($onibus); ?></div>
        <div class="fleet-stat-label">Ônibus Ativos</div>
    </div>
    
    <div class="fleet-stat">
        <div class="fleet-stat-icon" style="background-color: var(--warning-color);">
            <i class="fas fa-tools"></i>
        </div>
        <div class="fleet-stat-value">0</div>
        <div class="fleet-stat-label">Em Manutenção</div>
    </div>
    
    <div class="fleet-stat">
        <div class="fleet-stat-icon" style="background-color: var(--accent-color);">
            <i class="fas fa-users"></i>
        </div>
        <div class="fleet-stat-value"><?php echo array_sum(array_column($onibus, 'capacidade')); ?></div>
        <div class="fleet-stat-label">Capacidade Total</div>
    </div>
    
    <div class="fleet-stat">
        <div class="fleet-stat-icon" style="background-color: var(--primary-color);">
            <i class="fas fa-percentage"></i>
        </div>
        <div class="fleet-stat-value">87%</div>
        <div class="fleet-stat-label">Taxa de Utilização</div>
    </div>
</div>

<!-- Filters Bar -->
<div class="filters-bar">
    <div class="filters-grid">
        <div class="form-group">
            <label for="searchInput" class="form-label">Buscar ônibus</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Número do ônibus ou tipo..." onkeyup="filterBuses()">
        </div>
        
        <div class="form-group">
            <label for="statusFilter" class="form-label">Tipo</label>
            <select id="statusFilter" class="form-control" onchange="filterBuses()">
                <option value="">Todos os tipos</option>
                <option value="ônibus">Ônibus</option>
                <option value="van">Van</option>
                <option value="carro">Carro</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="capacityFilter" class="form-label">Capacidade</label>
            <select id="capacityFilter" class="form-control">
                <option value="">Todas as capacidades</option>
                <option value="pequeno">Até 30 lugares</option>
                <option value="medio">31-50 lugares</option>
                <option value="grande">Mais de 50 lugares</option>
            </select>
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" class="btn-primary" onclick="addBus()">
                <i class="fas fa-plus"></i>
                Novo Ônibus
            </button>
            <button type="button" class="btn-icon" onclick="clearFilters()" title="Limpar filtros">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>
</div>

<!-- Buses Grid -->
<?php if (empty($onibus)): ?>
    <div class="chart-container">
        <div class="empty-state">
            <i class="fas fa-bus"></i>
            <h3>Nenhum ônibus cadastrado</h3>
            <p>Comece adicionando o primeiro ônibus à sua frota.</p>
            <button type="button" class="btn-primary" onclick="addBus()">
                <i class="fas fa-plus"></i>
                Adicionar Primeiro Ônibus
            </button>
        </div>
    </div>
<?php else: ?>
    <div class="buses-grid">
        <?php foreach ($onibus as $bus): ?>
            <?php
            // Como não há coluna status, todos os ônibus são considerados ativos
            $statusClass = 'active';
            $statusText = 'Ativo';
            
            // Simular ocupação (em uma implementação real, isso viria do banco)
            $ocupacao = rand(0, intval($bus['capacidade']));
            $percentualOcupacao = $bus['capacidade'] > 0 ? ($ocupacao / $bus['capacidade']) * 100 : 0;
            ?>
            
            <div class="bus-card <?php echo $statusClass; ?>">
                <div class="bus-status <?php echo $statusClass; ?>">
                    <?php echo $statusText; ?>
                </div>
                
                <div class="bus-header">
                    <div>
                        <h3 class="bus-number">Ônibus <?php echo htmlspecialchars($bus['numero']); ?></h3>
                        <div class="bus-plate">Tipo: <?php echo htmlspecialchars($bus['tipo']); ?></div>
                    </div>
                </div>
                
                <div class="bus-info">
                    <div class="bus-info-item">
                        <i class="fas fa-bus"></i>
                        <?php echo ucfirst(htmlspecialchars($bus['tipo'])); ?>
                    </div>
                    <div class="bus-info-item">
                        <i class="fas fa-calendar"></i>
                        Cadastrado: <?php echo date('d/m/Y', strtotime($bus['created_at'])); ?>
                    </div>
                    <div class="bus-info-item">
                        <i class="fas fa-users"></i>
                        Capacidade: <?php echo htmlspecialchars($bus['capacidade']); ?> lugares
                    </div>
                </div>
                
                <div class="bus-capacity">
                    <div class="capacity-label">
                        <span>Ocupação</span>
                        <span><?php echo $ocupacao; ?>/<?php echo $bus['capacidade']; ?> (<?php echo round($percentualOcupacao); ?>%)</span>
                    </div>
                    <div class="capacity-bar">
                        <div class="capacity-fill" style="width: <?php echo $percentualOcupacao; ?>%"></div>
                    </div>
                </div>
                
                <?php if (!empty($bus['observacoes'])): ?>
                    <div class="bus-info-item">
                        <i class="fas fa-sticky-note"></i>
                        <?php echo htmlspecialchars(substr($bus['observacoes'], 0, 60)); ?>
                        <?php if (strlen($bus['observacoes']) > 60) echo '...'; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($bus['responsavel_emergencia_nome'])): ?>
                    <div class="bus-info-item">
                        <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
                        <strong>Emergência:</strong> <?php echo htmlspecialchars($bus['responsavel_emergencia_nome']); ?>
                        <?php if (!empty($bus['responsavel_emergencia_whatsapp'])): ?>
                            <br><small class="text-muted ms-3"><?php echo htmlspecialchars($bus['responsavel_emergencia_whatsapp']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="bus-actions">
                    <button class="btn-icon view" onclick="viewBus(<?php echo $bus['id']; ?>)" title="Ver detalhes">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon edit" onclick="editBus(<?php echo $bus['id']; ?>)" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon delete" onclick="deleteBus(<?php echo $bus['id']; ?>)" title="Excluir">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Modal para Novo Ônibus -->
<div class="modal fade" id="novoOnibusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bus text-primary me-2"></i>
                    Cadastrar Novo Ônibus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formNovoOnibus">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="numero" class="form-label">Número do Ônibus *</label>
                            <input type="text" class="form-control" id="numero" name="numero" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="placa" class="form-label">Placa</label>
                            <input type="text" class="form-control" id="placa" name="placa" placeholder="ABC-1234">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo de Veículo *</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Selecione...</option>
                                <option value="ônibus">Ônibus</option>
                                <option value="van">Van</option>
                                <option value="carro">Carro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="capacidade" class="form-label">Capacidade *</label>
                            <input type="number" class="form-control" id="capacidade" name="capacidade" min="1" max="100" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="motorista_nome" class="form-label">Nome do Motorista</label>
                            <input type="text" class="form-control" id="motorista_nome" name="motorista_nome">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="monitor_nome" class="form-label">Nome do Monitor</label>
                            <input type="text" class="form-control" id="monitor_nome" name="monitor_nome">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="turno" class="form-label">Turno de Operação</label>
                            <select class="form-select" id="turno" name="turno">
                                <option value="ambos">Ambos os turnos</option>
                                <option value="matutino">Matutino</option>
                                <option value="vespertino">Vespertino</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dias_reservados" class="form-label">Dias Reservados</label>
                            <input type="number" class="form-control" id="dias_reservados" name="dias_reservados" value="10" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="rota_descricao" class="form-label">Descrição da Rota</label>
                        <textarea class="form-control" id="rota_descricao" name="rota_descricao" rows="2" placeholder="Descreva a rota do ônibus..."></textarea>
                    </div>
                    
                    <hr>
                    <h6 class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Responsável de Emergência
                    </h6>
                    <p class="text-muted small mb-3">Pessoa que deve ser notificada em caso de emergência durante o transporte.</p>
                    
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="responsavel_emergencia_nome" class="form-label">Nome do Responsável *</label>
                            <input type="text" class="form-control" id="responsavel_emergencia_nome" name="responsavel_emergencia_nome" required placeholder="Nome completo">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="responsavel_emergencia_whatsapp" class="form-label">WhatsApp *</label>
                            <input type="text" class="form-control" id="responsavel_emergencia_whatsapp" name="responsavel_emergencia_whatsapp" required placeholder="(11) 99999-9999">
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Importante:</strong> Este contato será usado para notificações automáticas em caso de emergência, atrasos ou problemas durante o transporte.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="cadastrar_onibus" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Cadastrar Ônibus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Visualizar Ônibus -->
<div class="modal fade" id="visualizarOnibusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye text-info me-2"></i>
                    Detalhes do Ônibus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="visualizarOnibusConteudo">
                <!-- Conteúdo será carregado via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Ônibus -->
<div class="modal fade" id="editarOnibusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit text-warning me-2"></i>
                    Editar Ônibus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formEditarOnibus">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_numero" class="form-label">Número do Ônibus *</label>
                            <input type="text" class="form-control" id="edit_numero" name="numero" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_placa" class="form-label">Placa</label>
                            <input type="text" class="form-control" id="edit_placa" name="placa" placeholder="ABC-1234">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_tipo" class="form-label">Tipo de Veículo *</label>
                            <select class="form-select" id="edit_tipo" name="tipo" required>
                                <option value="">Selecione...</option>
                                <option value="ônibus">Ônibus</option>
                                <option value="van">Van</option>
                                <option value="carro">Carro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_capacidade" class="form-label">Capacidade *</label>
                            <input type="number" class="form-control" id="edit_capacidade" name="capacidade" min="1" max="100" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_motorista_nome" class="form-label">Nome do Motorista</label>
                            <input type="text" class="form-control" id="edit_motorista_nome" name="motorista_nome">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_monitor_nome" class="form-label">Nome do Monitor</label>
                            <input type="text" class="form-control" id="edit_monitor_nome" name="monitor_nome">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_turno" class="form-label">Turno de Operação</label>
                            <select class="form-select" id="edit_turno" name="turno">
                                <option value="ambos">Ambos os turnos</option>
                                <option value="matutino">Matutino</option>
                                <option value="vespertino">Vespertino</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_dias_reservados" class="form-label">Dias Reservados</label>
                            <input type="number" class="form-control" id="edit_dias_reservados" name="dias_reservados" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_rota_descricao" class="form-label">Descrição da Rota</label>
                        <textarea class="form-control" id="edit_rota_descricao" name="rota_descricao" rows="2"></textarea>
                    </div>
                    
                    <hr>
                    <h6 class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Responsável de Emergência
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="edit_responsavel_emergencia_nome" class="form-label">Nome do Responsável *</label>
                            <input type="text" class="form-control" id="edit_responsavel_emergencia_nome" name="responsavel_emergencia_nome" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="edit_responsavel_emergencia_whatsapp" class="form-label">WhatsApp *</label>
                            <input type="text" class="form-control" id="edit_responsavel_emergencia_whatsapp" name="responsavel_emergencia_whatsapp" required>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_ativo" name="ativo" checked>
                        <label class="form-check-label" for="edit_ativo">
                            Ônibus ativo (desmarque para desativar)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="editar_onibus" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Excluir Ônibus -->
<div class="modal fade" id="excluirOnibusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção!</strong> Esta ação não pode ser desfeita.
                </div>
                <p>Tem certeza que deseja excluir este ônibus?</p>
                <div id="excluirOnibusDetalhes">
                    <!-- Detalhes do ônibus serão carregados via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <form method="POST" id="formExcluirOnibus">
                    <input type="hidden" id="delete_id" name="id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="excluir_onibus" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Excluir Ônibus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Adicionar JavaScript
$content .= '
<script>
// Debug inicial
console.log("=== INICIANDO CARREGAMENTO DAS FUNCOES DE ONIBUS ===");

// Dados dos ônibus para JavaScript
window.onibusData = ' . json_encode($onibus, JSON_UNESCAPED_UNICODE) . ';
console.log("Dados carregados:", window.onibusData ? window.onibusData.length : 0, "onibus");

// Funcao de teste simples
window.testeClickBus = function() {
    alert("Teste de clique de onibus funcionando!");
    console.log("Teste de clique de onibus executado com sucesso!");
};

// Definir funcoes no escopo global imediatamente
window.viewBus = function(id) {
    console.log("=== VIEWBUS CHAMADO ===");
    console.log("ID recebido:", id);
    console.log("Tipo do ID:", typeof id);
    
    if (!window.onibusData) {
        console.error("onibusData nao esta definido!");
        alert("Erro: Dados dos onibus nao carregados!");
        return;
    }
    
    const bus = window.onibusData.find(b => b.id == id);
    console.log("Onibus encontrado:", bus);
    
    if (!bus) {
        console.error("Onibus nao encontrado para ID:", id);
        alert("Onibus nao encontrado!");
        return;
    }
    
    console.log("Tentando abrir modal...");
    
    // Verificar se Bootstrap esta carregado
    if (typeof bootstrap === "undefined") {
        console.error("Bootstrap nao esta carregado!");
        alert("Erro: Bootstrap nao carregado!");
        return;
    }
    
    // Esperar o DOM estar pronto se necessario
    if (document.readyState === "loading") {
        console.log("DOM ainda carregando, aguardando...");
        document.addEventListener("DOMContentLoaded", function() {
            openViewBusModal(id, bus);
        });
    } else {
        console.log("DOM pronto, abrindo modal...");
        openViewBusModal(id, bus);
    }
};

window.editBus = function(id) {
    console.log("=== EDITBUS CHAMADO ===");
    console.log("ID recebido:", id);
    
    if (!window.onibusData) {
        console.error("onibusData nao esta definido!");
        alert("Erro: Dados dos onibus nao carregados!");
        return;
    }
    
    const bus = window.onibusData.find(b => b.id == id);
    if (!bus) {
        console.error("Onibus nao encontrado para ID:", id);
        alert("Onibus nao encontrado!");
        return;
    }
    
    console.log("Onibus encontrado para edicao:", bus.numero);
    
    // Verificar se Bootstrap esta carregado
    if (typeof bootstrap === "undefined") {
        console.error("Bootstrap nao esta carregado!");
        alert("Erro: Bootstrap nao carregado!");
        return;
    }
    
    // Esperar o DOM estar pronto se necessario
    if (document.readyState === "loading") {
        console.log("DOM ainda carregando, aguardando...");
        document.addEventListener("DOMContentLoaded", function() {
            openEditBusModal(id, bus);
        });
    } else {
        console.log("DOM pronto, abrindo modal de edicao...");
        openEditBusModal(id, bus);
    }
};

window.deleteBus = function(id) {
    console.log("=== DELETEBUS CHAMADO ===");
    console.log("ID recebido:", id);
    
    if (!window.onibusData) {
        console.error("onibusData nao esta definido!");
        alert("Erro: Dados dos onibus nao carregados!");
        return;
    }
    
    const bus = window.onibusData.find(b => b.id == id);
    if (!bus) {
        console.error("Onibus nao encontrado para ID:", id);
        alert("Onibus nao encontrado!");
        return;
    }
    
    console.log("Onibus encontrado para exclusao:", bus.numero);
    
    // Verificar se Bootstrap esta carregado
    if (typeof bootstrap === "undefined") {
        console.error("Bootstrap nao esta carregado!");
        alert("Erro: Bootstrap nao carregado!");
        return;
    }
    
    // Esperar o DOM estar pronto se necessario
    if (document.readyState === "loading") {
        console.log("DOM ainda carregando, aguardando...");
        document.addEventListener("DOMContentLoaded", function() {
            openDeleteBusModal(id, bus);
        });
    } else {
        console.log("DOM pronto, abrindo modal de exclusao...");
        openDeleteBusModal(id, bus);
    }
};

window.addBus = function() {
    console.log("=== ADDBUS CHAMADO ===");
    
    // Verificar se Bootstrap esta carregado
    if (typeof bootstrap === "undefined") {
        console.error("Bootstrap nao esta carregado!");
        alert("Erro: Bootstrap nao carregado!");
        return;
    }
    
    // Esperar o DOM estar pronto se necessario
    if (document.readyState === "loading") {
        console.log("DOM ainda carregando, aguardando...");
        document.addEventListener("DOMContentLoaded", function() {
            openAddBusModal();
        });
    } else {
        console.log("DOM pronto, abrindo modal de adicao...");
        openAddBusModal();
    }
};

function openViewBusModal(id, bus) {
    console.log("=== OPENVIEWBUSMODAL ===");
    console.log("Procurando modal visualizarOnibusModal...");
    
    const modalElement = document.getElementById("visualizarOnibusModal");
    if (!modalElement) {
        console.error("Modal visualizarOnibusModal nao encontrado!");
        alert("Modal de visualizacao nao encontrado!");
        return;
    }
    
    console.log("Modal encontrado, preenchendo conteudo...");
    
    const statusBadge = bus.ativo == 1 ? 
        `<span class="badge bg-success">Ativo</span>` : 
        `<span class="badge bg-secondary">Inativo</span>`;
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th>Numero:</th><td>${bus.numero}</td></tr>
                    <tr><th>Placa:</th><td>${bus.placa || "Nao informada"}</td></tr>
                    <tr><th>Tipo:</th><td>${bus.tipo}</td></tr>
                    <tr><th>Capacidade:</th><td>${bus.capacidade} passageiros</td></tr>
                    <tr><th>Status:</th><td>${statusBadge}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th>Motorista:</th><td>${bus.motorista_nome || "Nao informado"}</td></tr>
                    <tr><th>Monitor:</th><td>${bus.monitor_nome || "Nao informado"}</td></tr>
                    <tr><th>Turno:</th><td>${bus.turno}</td></tr>
                    <tr><th>Dias Reservados:</th><td>${bus.dias_reservados}</td></tr>
                    <tr><th>Cadastrado em:</th><td>${new Date(bus.created_at).toLocaleDateString("pt-BR")}</td></tr>
                </table>
            </div>
        </div>
        
        ${bus.rota_descricao ? `
        <div class="mb-3">
            <h6>Descricao da Rota:</h6>
            <p class="text-muted">${bus.rota_descricao}</p>
        </div>
        ` : ""}
        
        <div class="alert alert-danger">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Contato de Emergencia
            </h6>
            <p class="mb-1"><strong>Nome:</strong> ${bus.responsavel_emergencia_nome || "Nao informado"}</p>
            <p class="mb-0"><strong>WhatsApp:</strong> ${bus.responsavel_emergencia_whatsapp || "Nao informado"}</p>
        </div>
    `;
    
    const contentElement = document.getElementById("visualizarOnibusConteudo");
    if (contentElement) {
        contentElement.innerHTML = html;
        console.log("Conteudo do modal preenchido");
    } else {
        console.error("Elemento visualizarOnibusConteudo nao encontrado!");
    }
    
    console.log("Abrindo modal com Bootstrap...");
    try {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log("Modal aberto com sucesso!");
    } catch (error) {
        console.error("Erro ao abrir modal:", error);
        alert("Erro ao abrir modal: " + error.message);
    }
}

function openEditBusModal(id, bus) {
    console.log("=== OPENEDITBUSMODAL ===");
    console.log("Procurando modal editarOnibusModal...");
    
    const modalElement = document.getElementById("editarOnibusModal");
    if (!modalElement) {
        console.error("Modal editarOnibusModal nao encontrado!");
        alert("Modal de edicao nao encontrado!");
        return;
    }
    
    console.log("Modal encontrado, preenchendo formulario...");
    
    // Preencher formulario
    const setFieldValue = (fieldId, value) => {
        const field = document.getElementById(fieldId);
        if (field) {
            if (field.type === "checkbox") {
                field.checked = value == 1;
            } else {
                field.value = value || "";
            }
            console.log(`Campo ${fieldId} preenchido com:`, value);
        } else {
            console.warn(`Campo ${fieldId} nao encontrado`);
        }
    };
    
    setFieldValue("edit_id", bus.id);
    setFieldValue("edit_numero", bus.numero);
    setFieldValue("edit_placa", bus.placa);
    setFieldValue("edit_tipo", bus.tipo);
    setFieldValue("edit_capacidade", bus.capacidade);
    setFieldValue("edit_motorista_nome", bus.motorista_nome);
    setFieldValue("edit_monitor_nome", bus.monitor_nome);
    setFieldValue("edit_responsavel_emergencia_nome", bus.responsavel_emergencia_nome);
    setFieldValue("edit_responsavel_emergencia_whatsapp", bus.responsavel_emergencia_whatsapp);
    setFieldValue("edit_rota_descricao", bus.rota_descricao);
    setFieldValue("edit_turno", bus.turno);
    setFieldValue("edit_dias_reservados", bus.dias_reservados);
    setFieldValue("edit_ativo", bus.ativo);
    
    console.log("Abrindo modal de edicao...");
    try {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log("Modal de edicao aberto com sucesso!");
    } catch (error) {
        console.error("Erro ao abrir modal de edicao:", error);
        alert("Erro ao abrir modal de edicao: " + error.message);
    }
}

function openDeleteBusModal(id, bus) {
    console.log("=== OPENDELETEBUSMODAL ===");
    console.log("Procurando modal excluirOnibusModal...");
    
    const modalElement = document.getElementById("excluirOnibusModal");
    if (!modalElement) {
        console.error("Modal excluirOnibusModal nao encontrado!");
        alert("Modal de exclusao nao encontrado!");
        return;
    }
    
    console.log("Modal encontrado, preenchendo detalhes...");
    
    const html = `
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Onibus ${bus.numero}</h6>
                <p class="card-text">
                    <strong>Tipo:</strong> ${bus.tipo}<br>
                    <strong>Capacidade:</strong> ${bus.capacidade} passageiros<br>
                    <strong>Motorista:</strong> ${bus.motorista_nome || "Nao informado"}<br>
                    <strong>Monitor:</strong> ${bus.monitor_nome || "Nao informado"}
                </p>
            </div>
        </div>
    `;
    
    const detailsElement = document.getElementById("excluirOnibusDetalhes");
    if (detailsElement) {
        detailsElement.innerHTML = html;
        console.log("Detalhes do onibus preenchidos");
    } else {
        console.warn("Elemento excluirOnibusDetalhes nao encontrado");
    }
    
    const hiddenInput = document.getElementById("delete_id");
    if (hiddenInput) {
        hiddenInput.value = id;
        console.log("ID definido no input hidden:", id);
    } else {
        console.warn("Input delete_id nao encontrado");
    }
    
    console.log("Abrindo modal de exclusao...");
    try {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log("Modal de exclusao aberto com sucesso!");
    } catch (error) {
        console.error("Erro ao abrir modal de exclusao:", error);
        alert("Erro ao abrir modal de exclusao: " + error.message);
    }
}

function openAddBusModal() {
    console.log("=== OPENADDBUSMODAL ===");
    console.log("Procurando modal cadastrarOnibusModal...");
    
    const modalElement = document.getElementById("cadastrarOnibusModal");
    if (!modalElement) {
        console.error("Modal cadastrarOnibusModal nao encontrado!");
        alert("Modal de adicao nao encontrado!");
        return;
    }
    
    console.log("Abrindo modal de adicao...");
    try {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log("Modal de adicao aberto com sucesso!");
    } catch (error) {
        console.error("Erro ao abrir modal de adicao:", error);
        alert("Erro ao abrir modal de adicao: " + error.message);
    }
}

// Funcoes de filtro
window.filterBuses = function() {
    console.log("Filtrando onibus...");
    const searchTerm = document.getElementById("searchInput").value.toLowerCase();
    const statusFilter = document.getElementById("statusFilter").value;
    const typeFilter = document.getElementById("typeFilter").value;
    
    const busCards = document.querySelectorAll(".bus-card");
    
    busCards.forEach(card => {
        const cardText = card.textContent.toLowerCase();
        let showCard = true;
        
        if (searchTerm && !cardText.includes(searchTerm)) {
            showCard = false;
        }
        
        if (statusFilter && !cardText.includes(statusFilter.toLowerCase())) {
            showCard = false;
        }
        
        if (typeFilter && !cardText.includes(typeFilter.toLowerCase())) {
            showCard = false;
        }
        
        card.style.display = showCard ? "block" : "none";
    });
};

window.clearFilters = function() {
    console.log("Limpando filtros...");
    const searchInput = document.getElementById("searchInput");
    const statusFilter = document.getElementById("statusFilter");
    const typeFilter = document.getElementById("typeFilter");
    
    if (searchInput) searchInput.value = "";
    if (statusFilter) statusFilter.value = "";
    if (typeFilter) typeFilter.value = "";
    
    if (typeof window.filterBuses === "function") {
        window.filterBuses();
    }
};

console.log("=== FUNCOES JAVASCRIPT DE ONIBUS CARREGADAS COM SUCESSO ===");
console.log("Bootstrap disponivel:", typeof bootstrap !== "undefined");
console.log("Dados dos onibus:", window.onibusData ? "OK" : "ERRO");
console.log("viewBus:", typeof window.viewBus);
console.log("editBus:", typeof window.editBus);
console.log("deleteBus:", typeof window.deleteBus);
console.log("addBus:", typeof window.addBus);
</script>
';

// Incluir layout
include 'includes/layout-professional.php';
?>
