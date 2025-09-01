<?php
// Configurações da página
$page_title = "Ônibus";
$page_description = "Gestão de Frota";

// Incluir configuração do banco
include 'config/config.php';

// Conectar ao banco
$conn = getDatabaseConnection();
$onibus = [];

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
                
                <div class="bus-actions">
                    <button class="btn-icon" onclick="viewBus(<?php echo $bus['id']; ?>)" title="Ver detalhes">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon" onclick="editBus(<?php echo $bus['id']; ?>)" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon" onclick="deleteBus(<?php echo $bus['id']; ?>)" title="Excluir">
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
