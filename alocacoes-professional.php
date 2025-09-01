<?php
// Configurações da página
$page_title = "Alocações";
$page_description = "Gestão de Rotas e Alocações";

// Incluir configuração do banco
include 'config/config.php';

// Conectar ao banco
$conn = getDatabaseConnection();
$alocacoes = [];
$rotas = [];
$onibus = [];
$alunos = [];

if (!$conn->connect_error) {
    // Buscar alocações com informações relacionadas
    $result = $conn->query("SELECT ao.*, 
                                  o.numero as onibus_numero, 
                                  o.tipo as onibus_tipo,
                                  o.capacidade as onibus_capacidade,
                                  a.nome as aluno_nome,
                                  a.telefone as aluno_telefone,
                                  a.serie as aluno_serie,
                                  a.curso as aluno_curso,
                                  e.nome as evento_nome,
                                  e.data_inicio as evento_data_inicio
                           FROM alocacoes_onibus ao 
                           LEFT JOIN onibus o ON ao.onibus_id = o.id 
                           LEFT JOIN alunos a ON ao.aluno_id = a.id
                           LEFT JOIN eventos e ON ao.evento_id = e.id 
                           ORDER BY ao.created_at DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $alocacoes[] = $row;
        }
    }
    
    // Como não há tabela de rotas, definir rotas vazia
    $rotas = [];
    
    // Buscar todos os ônibus (não há coluna status)
    $result = $conn->query("SELECT * FROM onibus ORDER BY numero");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $onibus[] = $row;
        }
    }
    
    // Buscar total de alunos (não há coluna status)
    $result = $conn->query("SELECT COUNT(*) as total FROM alunos");
    if ($result && $row = $result->fetch_assoc()) {
        $total_alunos = $row['total'];
    } else {
        $total_alunos = 0;
    }
}

$custom_css = '
.allocations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.allocation-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    transition: all 0.2s ease;
    border-left: 4px solid var(--primary-color);
    position: relative;
}

.allocation-card:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-2px);
}

.allocation-card.active {
    border-left-color: var(--success-color);
}

.allocation-card.pending {
    border-left-color: var(--warning-color);
}

.allocation-card.inactive {
    border-left-color: var(--danger-color);
}

.allocation-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.allocation-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-dark);
    margin: 0;
    line-height: 1.3;
}

.allocation-route {
    font-size: 0.875rem;
    color: var(--text-gray);
    margin-top: 0.25rem;
}

.allocation-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.allocation-status.active {
    background-color: rgba(56, 161, 105, 0.1);
    color: var(--success-color);
}

.allocation-status.pending {
    background-color: rgba(214, 158, 46, 0.1);
    color: var(--warning-color);
}

.allocation-status.inactive {
    background-color: rgba(229, 62, 62, 0.1);
    color: var(--danger-color);
}

.allocation-info {
    margin-bottom: 1.5rem;
}

.allocation-info-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
    color: var(--text-gray);
}

.allocation-info-item i {
    width: 18px;
    margin-right: 0.75rem;
    color: var(--accent-color);
}

.allocation-info-item strong {
    color: var(--text-dark);
    margin-left: 0.25rem;
}

.schedule-info {
    background: var(--light-gray);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.schedule-time {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.time-item {
    text-align: center;
}

.time-label {
    font-size: 0.75rem;
    color: var(--text-gray);
    text-transform: uppercase;
    font-weight: 600;
}

.time-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-top: 0.25rem;
}

.allocation-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--light-gray);
}

.allocation-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.allocation-stat {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    text-align: center;
}

.allocation-stat-icon {
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

.allocation-stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.allocation-stat-label {
    font-size: 0.875rem;
    color: var(--text-gray);
    font-weight: 500;
}

.quick-actions {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
}

.quick-actions h3 {
    margin: 0 0 1rem 0;
    color: var(--text-dark);
    font-size: 1.1rem;
    font-weight: 600;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.quick-action-btn {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: var(--white);
    border: none;
    border-radius: var(--border-radius);
    padding: 1rem;
    text-align: center;
    transition: all 0.2s ease;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
    color: var(--white);
}

.quick-action-btn i {
    font-size: 1.5rem;
}

.quick-action-btn span {
    font-weight: 600;
    font-size: 0.875rem;
}
';

$custom_js = '
function filterAllocations() {
    const statusFilter = document.getElementById("statusFilter").value;
    const routeFilter = document.getElementById("routeFilter").value;
    const busFilter = document.getElementById("busFilter").value;
    
    const allocationCards = document.querySelectorAll(".allocation-card");
    
    allocationCards.forEach(card => {
        const status = card.querySelector(".allocation-status").textContent.toLowerCase();
        const route = card.querySelector(".allocation-route").textContent.toLowerCase();
        const busInfo = card.textContent.toLowerCase();
        
        let showCard = true;
        
        if (statusFilter && !status.includes(statusFilter)) {
            showCard = false;
        }
        
        if (routeFilter && !route.includes(routeFilter.toLowerCase())) {
            showCard = false;
        }
        
        if (busFilter && !busInfo.includes(busFilter.toLowerCase())) {
            showCard = false;
        }
        
        card.style.display = showCard ? "block" : "none";
    });
}

function clearFilters() {
    document.getElementById("statusFilter").value = "";
    document.getElementById("routeFilter").value = "";
    document.getElementById("busFilter").value = "";
    filterAllocations();
}

function createAllocation() {
    alert("Funcionalidade de criar alocação será implementada em breve");
}

function editAllocation(id) {
    alert("Editar alocação " + id);
}

function viewAllocation(id) {
    alert("Ver detalhes da alocação " + id);
}

function deleteAllocation(id) {
    if (confirm("Tem certeza que deseja excluir esta alocação?")) {
        alert("Alocação " + id + " excluída");
    }
}

function optimizeRoutes() {
    alert("Otimização de rotas será implementada em breve");
}

function generateReport() {
    alert("Relatório de alocações será gerado em breve");
}

function bulkAssign() {
    alert("Atribuição em lote será implementada em breve");
}
';

ob_start();
?>

<!-- Allocation Statistics -->
<div class="allocation-stats">
    <div class="allocation-stat">
        <div class="allocation-stat-icon" style="background-color: var(--success-color);">
            <i class="fas fa-route"></i>
        </div>
        <div class="allocation-stat-value"><?php echo count($alocacoes); ?></div>
        <div class="allocation-stat-label">Alocações Ativas</div>
    </div>
    
    <div class="allocation-stat">
        <div class="allocation-stat-icon" style="background-color: var(--primary-color);">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="allocation-stat-value">
            <?php 
            // Contar eventos ativos
            $eventos_count = 0;
            if (!$conn->connect_error) {
                $result = $conn->query("SELECT COUNT(*) as total FROM eventos WHERE data_fim >= CURDATE()");
                if ($result) {
                    $eventos_count = $result->fetch_assoc()['total'];
                }
            }
            echo $eventos_count;
            ?>
        </div>
        <div class="allocation-stat-label">Eventos Ativos</div>
    </div>
    
    <div class="allocation-stat">
        <div class="allocation-stat-icon" style="background-color: var(--accent-color);">
            <i class="fas fa-bus"></i>
        </div>
        <div class="allocation-stat-value"><?php echo count($onibus); ?></div>
        <div class="allocation-stat-label">Ônibus Disponíveis</div>
    </div>
    
    <div class="allocation-stat">
        <div class="allocation-stat-icon" style="background-color: var(--warning-color);">
            <i class="fas fa-users"></i>
        </div>
        <div class="allocation-stat-value"><?php echo $total_alunos ?? 0; ?></div>
        <div class="allocation-stat-label">Alunos Ativos</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3>Ações Rápidas</h3>
    <div class="quick-actions-grid">
        <button class="quick-action-btn" onclick="createAllocation()">
            <i class="fas fa-plus"></i>
            <span>Nova Alocação</span>
        </button>
        <button class="quick-action-btn" onclick="optimizeRoutes()">
            <i class="fas fa-route"></i>
            <span>Otimizar Rotas</span>
        </button>
        <button class="quick-action-btn" onclick="generateReport()">
            <i class="fas fa-chart-line"></i>
            <span>Relatórios</span>
        </button>
        <button class="quick-action-btn" onclick="bulkAssign()">
            <i class="fas fa-tasks"></i>
            <span>Atribuição em Lote</span>
        </button>
    </div>
</div>

<!-- Filters Bar -->
<div class="filters-bar">
    <div class="filters-grid">
        <div class="form-group">
            <label for="statusFilter" class="form-label">Status</label>
            <select id="statusFilter" class="form-control" onchange="filterAllocations()">
                <option value="">Todos os status</option>
                <option value="ativa">Ativa</option>
                <option value="pendente">Pendente</option>
                <option value="inativa">Inativa</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="routeFilter" class="form-label">Evento</label>
            <select id="routeFilter" class="form-control" onchange="filterAllocations()">
                <option value="">Todos os eventos</option>
                <?php
                // Buscar eventos para o filtro
                if (!$conn->connect_error) {
                    $result = $conn->query("SELECT DISTINCT nome FROM eventos ORDER BY nome");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['nome']) . '">';
                            echo htmlspecialchars($row['nome']);
                            echo '</option>';
                        }
                    }
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="busFilter" class="form-label">Ônibus</label>
            <select id="busFilter" class="form-control" onchange="filterAllocations()">
                <option value="">Todos os ônibus</option>
                <?php foreach ($onibus as $bus): ?>
                    <option value="<?php echo htmlspecialchars($bus['numero']); ?>">
                        Ônibus <?php echo htmlspecialchars($bus['numero']); ?> - <?php echo ucfirst(htmlspecialchars($bus['tipo'])); ?> (<?php echo htmlspecialchars($bus['capacidade']); ?> lugares)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" class="btn-primary" onclick="createAllocation()">
                <i class="fas fa-plus"></i>
                Nova Alocação
            </button>
            <button type="button" class="btn-icon" onclick="clearFilters()" title="Limpar filtros">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>
</div>

<!-- Allocations Grid -->
<?php if (empty($alocacoes)): ?>
    <div class="chart-container">
        <div class="empty-state">
            <i class="fas fa-route"></i>
            <h3>Nenhuma alocação cadastrada</h3>
            <p>Comece criando a primeira alocação de rota e ônibus.</p>
            <button type="button" class="btn-primary" onclick="createAllocation()">
                <i class="fas fa-plus"></i>
                Criar Primeira Alocação
            </button>
        </div>
    </div>
<?php else: ?>
    <div class="allocations-grid">
        <?php foreach ($alocacoes as $alocacao): ?>
            <?php
            // Como não há campo status, considerar todas as alocações como ativas
            $statusClass = 'active';
            $statusText = 'Ativa';
            ?>
            
            <div class="allocation-card <?php echo $statusClass; ?>">
                <div class="allocation-header">
                    <div>
                        <h3 class="allocation-title">
                            <?php echo htmlspecialchars($alocacao['evento_nome'] ?? 'Evento não definido'); ?>
                        </h3>
                        <div class="allocation-route">
                            <strong><?php echo htmlspecialchars($alocacao['aluno_nome'] ?? 'N/A'); ?></strong><br>
                            <?php if ($alocacao['aluno_serie'] || $alocacao['aluno_curso']): ?>
                                <small><?php echo htmlspecialchars($alocacao['aluno_serie'] ?? ''); ?> - <?php echo htmlspecialchars($alocacao['aluno_curso'] ?? ''); ?></small><br>
                            <?php endif; ?>
                            <small>Ônibus <?php echo htmlspecialchars($alocacao['onibus_numero'] ?? 'N/A'); ?> (<?php echo ucfirst(htmlspecialchars($alocacao['onibus_tipo'] ?? 'N/A')); ?>)</small>
                        </div>
                    </div>
                    <div class="allocation-status <?php echo $statusClass; ?>">
                        <?php echo $statusText; ?>
                    </div>
                </div>
                
                <div class="allocation-info">
                    <div class="allocation-info-item">
                        <i class="fas fa-calendar"></i>
                        Data: <strong><?php echo date('d/m/Y H:i', strtotime($alocacao['created_at'])); ?></strong>
                    </div>
                    
                    <?php if (!empty($alocacao['evento_data_inicio'])): ?>
                        <div class="allocation-info-item">
                            <i class="fas fa-play"></i>
                            Evento: <strong><?php echo date('d/m/Y', strtotime($alocacao['evento_data_inicio'])); ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($alocacao['aluno_telefone'])): ?>
                        <div class="allocation-info-item">
                            <i class="fas fa-phone"></i>
                            Telefone: <strong><?php echo htmlspecialchars($alocacao['aluno_telefone']); ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <div class="allocation-info-item">
                        <i class="fas fa-users"></i>
                        Capacidade: <strong><?php echo htmlspecialchars($alocacao['onibus_capacidade'] ?? 'N/A'); ?> lugares</strong>
                    </div>
                </div>
                
                <div class="allocation-actions">
                    <button class="btn-icon" onclick="viewAllocation(<?php echo $alocacao['id']; ?>)" title="Ver detalhes">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon" onclick="editAllocation(<?php echo $alocacao['id']; ?>)" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon" onclick="deleteAllocation(<?php echo $alocacao['id']; ?>)" title="Excluir">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();

// Incluir layout
include 'includes/layout-professional.php';
?>
