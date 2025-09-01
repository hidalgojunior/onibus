<?php
// Configurações da página
$page_title = "Eventos";
$page_description = "Gestão de Frota";

// Incluir configuração do banco
include 'config/config.php';

// Conectar ao banco
$conn = getDatabaseConnection();
$eventos = [];

if (!$conn->connect_error) {
    $result = $conn->query("SELECT * FROM eventos ORDER BY data_inicio DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $eventos[] = $row;
        }
    }
}

$custom_css = '
.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.event-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    transition: all 0.2s ease;
    border-left: 4px solid var(--accent-color);
    position: relative;
}

.event-card:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-2px);
}

.event-status {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.event-status.active {
    background-color: rgba(56, 161, 105, 0.1);
    color: var(--success-color);
}

.event-status.upcoming {
    background-color: rgba(66, 153, 225, 0.1);
    color: var(--accent-color);
}

.event-status.ended {
    background-color: rgba(113, 128, 150, 0.1);
    color: var(--text-gray);
}

.event-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.event-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: var(--text-gray);
}

.event-meta i {
    width: 16px;
    text-align: center;
}

.event-description {
    color: var(--text-gray);
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.event-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.event-stat {
    text-align: center;
    padding: 0.75rem;
    background-color: var(--light-gray);
    border-radius: 8px;
}

.event-stat-value {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.event-stat-label {
    font-size: 0.75rem;
    color: var(--text-gray);
    text-transform: uppercase;
    font-weight: 500;
}

.event-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid var(--medium-gray);
    background: var(--white);
    color: var(--text-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-icon:hover {
    background-color: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

.filters-bar {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
}

.filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
}

.form-group {
    margin: 0;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
    display: block;
}

.form-control {
    border: 1px solid var(--medium-gray);
    border-radius: 8px;
    padding: 0.75rem;
    font-size: 0.875rem;
    transition: border-color 0.2s ease;
}

.form-control:focus {
    border-color: var(--accent-color);
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    outline: none;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--text-gray);
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    color: var(--medium-gray);
}

.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-dark);
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .events-grid {
        grid-template-columns: 1fr;
    }
}
';

$custom_js = '
function filterEvents() {
    const searchTerm = document.getElementById("searchInput").value.toLowerCase();
    const statusFilter = document.getElementById("statusFilter").value;
    const dateFilter = document.getElementById("dateFilter").value;
    
    const eventCards = document.querySelectorAll(".event-card");
    
    eventCards.forEach(card => {
        const title = card.querySelector(".event-title").textContent.toLowerCase();
        const status = card.querySelector(".event-status").textContent.toLowerCase();
        
        let showCard = true;
        
        // Filter by search term
        if (searchTerm && !title.includes(searchTerm)) {
            showCard = false;
        }
        
        // Filter by status
        if (statusFilter && !status.includes(statusFilter)) {
            showCard = false;
        }
        
        card.style.display = showCard ? "block" : "none";
    });
}

function clearFilters() {
    document.getElementById("searchInput").value = "";
    document.getElementById("statusFilter").value = "";
    document.getElementById("dateFilter").value = "";
    filterEvents();
}

function createEvent() {
    // Implementar modal de criação de evento
    alert("Funcionalidade de criação de evento será implementada em breve");
}

function editEvent(id) {
    // Implementar edição de evento
    alert("Editar evento " + id);
}

function viewEvent(id) {
    // Implementar visualização de evento
    alert("Ver detalhes do evento " + id);
}

function deleteEvent(id) {
    if (confirm("Tem certeza que deseja excluir este evento?")) {
        // Implementar exclusão
        alert("Evento " + id + " excluído");
    }
}
';

ob_start();
?>

<!-- Filters Bar -->
<div class="filters-bar">
    <div class="filters-grid">
        <div class="form-group">
            <label for="searchInput" class="form-label">Buscar eventos</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Digite o nome do evento..." onkeyup="filterEvents()">
        </div>
        
        <div class="form-group">
            <label for="statusFilter" class="form-label">Status</label>
            <select id="statusFilter" class="form-control" onchange="filterEvents()">
                <option value="">Todos os status</option>
                <option value="ativo">Ativo</option>
                <option value="próximo">Próximo</option>
                <option value="finalizado">Finalizado</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="dateFilter" class="form-label">Período</label>
            <select id="dateFilter" class="form-control" onchange="filterEvents()">
                <option value="">Todos os períodos</option>
                <option value="hoje">Hoje</option>
                <option value="semana">Esta semana</option>
                <option value="mes">Este mês</option>
            </select>
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" class="btn-primary" onclick="createEvent()">
                <i class="fas fa-plus"></i>
                Novo Evento
            </button>
            <button type="button" class="btn-icon" onclick="clearFilters()" title="Limpar filtros">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>
</div>

<!-- Events Grid -->
<?php if (empty($eventos)): ?>
    <div class="chart-container">
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Nenhum evento encontrado</h3>
            <p>Comece criando seu primeiro evento no sistema.</p>
            <button type="button" class="btn-primary" onclick="createEvent()">
                <i class="fas fa-plus"></i>
                Criar Primeiro Evento
            </button>
        </div>
    </div>
<?php else: ?>
    <div class="events-grid">
        <?php foreach ($eventos as $evento): ?>
            <?php
            $hoje = new DateTime();
            $dataInicio = new DateTime($evento['data_inicio']);
            $dataFim = new DateTime($evento['data_fim']);
            
            // Determinar status
            $status = 'ended';
            $statusText = 'Finalizado';
            if ($dataInicio > $hoje) {
                $status = 'upcoming';
                $statusText = 'Próximo';
            } elseif ($dataFim >= $hoje) {
                $status = 'active';
                $statusText = 'Ativo';
            }
            ?>
            
            <div class="event-card">
                <div class="event-status <?php echo $status; ?>">
                    <?php echo $statusText; ?>
                </div>
                
                <h3 class="event-title"><?php echo htmlspecialchars($evento['nome']); ?></h3>
                
                <div class="event-meta">
                    <div>
                        <i class="fas fa-calendar"></i>
                        <?php echo date('d/m/Y', strtotime($evento['data_inicio'])); ?>
                    </div>
                    <div>
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo htmlspecialchars($evento['local'] ?? 'Local não definido'); ?>
                    </div>
                </div>
                
                <?php if (!empty($evento['descricao'])): ?>
                    <div class="event-description">
                        <?php echo htmlspecialchars(substr($evento['descricao'], 0, 120)); ?>
                        <?php if (strlen($evento['descricao']) > 120) echo '...'; ?>
                    </div>
                <?php endif; ?>
                
                <div class="event-stats">
                    <div class="event-stat">
                        <div class="event-stat-value">0</div>
                        <div class="event-stat-label">Participantes</div>
                    </div>
                    <div class="event-stat">
                        <div class="event-stat-value">0</div>
                        <div class="event-stat-label">Ônibus</div>
                    </div>
                </div>
                
                <div class="event-actions">
                    <button class="btn-icon" onclick="viewEvent(<?php echo $evento['id']; ?>)" title="Ver detalhes">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon" onclick="editEvent(<?php echo $evento['id']; ?>)" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon" onclick="deleteEvent(<?php echo $evento['id']; ?>)" title="Excluir">
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
