<?php
// Configurações da página
$page_title = "Alunos";
$page_description = "Gestão de Estudantes";

// Incluir configuração do banco
include 'config/config.php';

// Conectar ao banco
$conn = getDatabaseConnection();
$alunos = [];

if (!$conn->connect_error) {
    $result = $conn->query("SELECT * FROM alunos ORDER BY nome");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $alunos[] = $row;
        }
    }
}

$custom_css = '
.students-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.student-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    transition: all 0.2s ease;
    border-left: 4px solid var(--success-color);
    position: relative;
}

.student-card:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-2px);
}

.student-card.inactive {
    border-left-color: var(--danger-color);
}

.student-card.pending {
    border-left-color: var(--warning-color);
}

.student-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.student-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-weight: 600;
    font-size: 1.25rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.student-info {
    flex-grow: 1;
}

.student-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-dark);
    margin: 0 0 0.25rem 0;
    line-height: 1.3;
}

.student-school {
    font-size: 0.875rem;
    color: var(--text-gray);
    margin-bottom: 0.5rem;
}

.student-status {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.student-status.active {
    background-color: rgba(56, 161, 105, 0.1);
    color: var(--success-color);
}

.student-status.pending {
    background-color: rgba(214, 158, 46, 0.1);
    color: var(--warning-color);
}

.student-status.inactive {
    background-color: rgba(229, 62, 62, 0.1);
    color: var(--danger-color);
}

.student-details {
    margin-top: 1rem;
}

.student-detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-gray);
}

.student-detail-item i {
    width: 16px;
    margin-right: 0.75rem;
    color: var(--accent-color);
}

.student-detail-item strong {
    color: var(--text-dark);
    margin-left: 0.25rem;
}

.student-route {
    background: var(--light-gray);
    border-radius: 8px;
    padding: 0.75rem;
    margin: 1rem 0;
    font-size: 0.875rem;
}

.route-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.route-name {
    font-weight: 600;
    color: var(--text-dark);
}

.route-bus {
    color: var(--text-gray);
    font-size: 0.8rem;
}

.pickup-time {
    display: flex;
    align-items: center;
    color: var(--text-gray);
    font-size: 0.8rem;
}

.pickup-time i {
    margin-right: 0.5rem;
    color: var(--accent-color);
}

.student-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--light-gray);
}

.students-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.student-stat {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    text-align: center;
}

.student-stat-icon {
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

.student-stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.student-stat-label {
    font-size: 0.875rem;
    color: var(--text-gray);
    font-weight: 500;
}

.bulk-actions {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1rem 1.5rem;
    box-shadow: var(--shadow);
    margin-bottom: 1.5rem;
    display: none;
}

.bulk-actions.show {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.bulk-info {
    color: var(--text-gray);
    font-size: 0.875rem;
}

.bulk-buttons {
    display: flex;
    gap: 0.5rem;
}
';

$custom_js = '
let selectedStudents = new Set();

function filterStudents() {
    const searchTerm = document.getElementById("searchInput").value.toLowerCase();
    const statusFilter = document.getElementById("statusFilter").value;
    const schoolFilter = document.getElementById("schoolFilter").value;
    const routeFilter = document.getElementById("routeFilter").value;
    
    const studentCards = document.querySelectorAll(".student-card");
    
    studentCards.forEach(card => {
        const name = card.querySelector(".student-name").textContent.toLowerCase();
        const school = card.querySelector(".student-school").textContent.toLowerCase();
        const curso = card.textContent.toLowerCase(); // Usar todo o texto do card para buscar curso
        const cardText = card.textContent.toLowerCase();
        
        let showCard = true;
        
        if (searchTerm && !name.includes(searchTerm) && !cardText.includes(searchTerm)) {
            showCard = false;
        }
        
        if (statusFilter && !curso.includes(statusFilter.toLowerCase())) {
            showCard = false;
        }
        
        if (schoolFilter && !school.includes(schoolFilter)) {
            showCard = false;
        }
        
        if (routeFilter && !cardText.includes(routeFilter.toLowerCase())) {
            showCard = false;
        }
        
        card.style.display = showCard ? "block" : "none";
    });
}

function clearFilters() {
    document.getElementById("searchInput").value = "";
    document.getElementById("statusFilter").value = "";
    document.getElementById("schoolFilter").value = "";
    document.getElementById("routeFilter").value = "";
    filterStudents();
}

function addStudent() {
    alert("Funcionalidade de adicionar aluno será implementada em breve");
}

function editStudent(id) {
    alert("Editar aluno " + id);
}

function viewStudent(id) {
    alert("Ver detalhes do aluno " + id);
}

function deleteStudent(id) {
    if (confirm("Tem certeza que deseja excluir este aluno?")) {
        alert("Aluno " + id + " excluído");
    }
}

function toggleStudentSelection(id, checkbox) {
    if (checkbox.checked) {
        selectedStudents.add(id);
    } else {
        selectedStudents.delete(id);
    }
    
    updateBulkActions();
}

function updateBulkActions() {
    const bulkActions = document.querySelector(".bulk-actions");
    const bulkInfo = document.querySelector(".bulk-info");
    
    if (selectedStudents.size > 0) {
        bulkActions.classList.add("show");
        bulkInfo.textContent = `${selectedStudents.size} aluno(s) selecionado(s)`;
    } else {
        bulkActions.classList.remove("show");
    }
}

function selectAllStudents() {
    const checkboxes = document.querySelectorAll(".student-checkbox");
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        const id = checkbox.getAttribute("data-id");
        selectedStudents.add(id);
    });
    updateBulkActions();
}

function deselectAllStudents() {
    const checkboxes = document.querySelectorAll(".student-checkbox");
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectedStudents.clear();
    updateBulkActions();
}

function bulkAssignRoute() {
    if (selectedStudents.size === 0) return;
    alert(`Atribuir rota para ${selectedStudents.size} aluno(s)`);
}

function bulkChangeStatus() {
    if (selectedStudents.size === 0) return;
    alert(`Alterar status de ${selectedStudents.size} aluno(s)`);
}

function exportStudents() {
    alert("Exportar dados dos alunos para Excel");
}

function importStudents() {
    alert("Importar alunos via planilha");
}
';

ob_start();
?>

<!-- Students Statistics -->
<div class="students-stats">
    <div class="student-stat">
        <div class="student-stat-icon" style="background-color: var(--success-color);">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="student-stat-value"><?php echo count($alunos); ?></div>
        <div class="student-stat-label">Alunos Ativos</div>
    </div>
    
    <div class="student-stat">
        <div class="student-stat-icon" style="background-color: var(--warning-color);">
            <i class="fas fa-clock"></i>
        </div>
        <div class="student-stat-value">0</div>
        <div class="student-stat-label">Aguardando</div>
    </div>
    
    <div class="student-stat">
        <div class="student-stat-icon" style="background-color: var(--primary-color);">
            <i class="fas fa-school"></i>
        </div>
        <div class="student-stat-value"><?php echo count(array_unique(array_column($alunos, 'escola'))); ?></div>
        <div class="student-stat-label">Escolas</div>
    </div>
    
    <div class="student-stat">
        <div class="student-stat-icon" style="background-color: var(--accent-color);">
            <i class="fas fa-route"></i>
        </div>
        <div class="student-stat-value"><?php echo count(array_unique(array_filter(array_column($alunos, 'rota_id')))); ?></div>
        <div class="student-stat-label">Rotas em Uso</div>
    </div>
</div>

<!-- Bulk Actions -->
<div class="bulk-actions">
    <div class="bulk-info">0 aluno(s) selecionado(s)</div>
    <div class="bulk-buttons">
        <button class="btn-secondary" onclick="bulkAssignRoute()">
            <i class="fas fa-route"></i> Atribuir Rota
        </button>
        <button class="btn-secondary" onclick="bulkChangeStatus()">
            <i class="fas fa-toggle-on"></i> Alterar Status
        </button>
        <button class="btn-secondary" onclick="deselectAllStudents()">
            <i class="fas fa-times"></i> Limpar Seleção
        </button>
    </div>
</div>

<!-- Filters Bar -->
<div class="filters-bar">
    <div class="filters-grid">
        <div class="form-group">
            <label for="searchInput" class="form-label">Buscar aluno</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Nome do aluno..." onkeyup="filterStudents()">
        </div>
        
        <div class="form-group">
            <label for="statusFilter" class="form-label">Curso</label>
            <select id="statusFilter" class="form-control" onchange="filterStudents()">
                <option value="">Todos os cursos</option>
                <?php
                // Buscar cursos únicos dos alunos
                $cursos = array_unique(array_filter(array_column($alunos, 'curso')));
                foreach ($cursos as $curso) {
                    echo '<option value="' . htmlspecialchars($curso) . '">' . htmlspecialchars($curso) . '</option>';
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="schoolFilter" class="form-label">Escola</label>
            <select id="schoolFilter" class="form-control" onchange="filterStudents()">
                <option value="">Todas as escolas</option>
                <?php 
                $escolas = array_unique(array_column($alunos, 'escola'));
                foreach ($escolas as $escola): 
                    if (!empty($escola)):
                ?>
                    <option value="<?php echo htmlspecialchars($escola); ?>">
                        <?php echo htmlspecialchars($escola); ?>
                    </option>
                <?php 
                    endif;
                endforeach; 
                ?>
            </select>
        </div>
        
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button type="button" class="btn-primary" onclick="addStudent()">
                <i class="fas fa-plus"></i>
                Novo Aluno
            </button>
            <button type="button" class="btn-secondary" onclick="selectAllStudents()">
                <i class="fas fa-check-square"></i>
                Selecionar Todos
            </button>
            <button type="button" class="btn-secondary" onclick="exportStudents()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
            <button type="button" class="btn-secondary" onclick="importStudents()">
                <i class="fas fa-upload"></i>
                Importar
            </button>
            <button type="button" class="btn-icon" onclick="clearFilters()" title="Limpar filtros">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>
</div>

<!-- Students Grid -->
<?php if (empty($alunos)): ?>
    <div class="chart-container">
        <div class="empty-state">
            <i class="fas fa-user-graduate"></i>
            <h3>Nenhum aluno cadastrado</h3>
            <p>Comece adicionando o primeiro aluno ao sistema.</p>
            <button type="button" class="btn-primary" onclick="addStudent()">
                <i class="fas fa-plus"></i>
                Adicionar Primeiro Aluno
            </button>
        </div>
    </div>
<?php else: ?>
    <div class="students-grid">
        <?php foreach ($alunos as $aluno): ?>
            <?php
            // Como não há coluna status, considerar todos os alunos como ativos
            $statusClass = 'active';
            $statusText = 'Ativo';
            $initials = '';
            $nameParts = explode(' ', $aluno['nome']);
            foreach ($nameParts as $part) {
                if (!empty($part)) {
                    $initials .= strtoupper(substr($part, 0, 1));
                    if (strlen($initials) >= 2) break;
                }
            }
            ?>
            
            <div class="student-card <?php echo $statusClass; ?>">
                <div class="student-status <?php echo $statusClass; ?>">
                    <?php echo $statusText; ?>
                </div>
                
                <div class="student-header">
                    <div style="display: flex; align-items: center; width: 100%;">
                        <input type="checkbox" class="student-checkbox" data-id="<?php echo $aluno['id']; ?>" 
                               onchange="toggleStudentSelection(<?php echo $aluno['id']; ?>, this)" 
                               style="margin-right: 1rem;">
                        
                        <div class="student-avatar">
                            <?php echo $initials; ?>
                        </div>
                        
                        <div class="student-info">
                            <h3 class="student-name"><?php echo htmlspecialchars($aluno['nome']); ?></h3>
                            <div class="student-school"><?php echo htmlspecialchars($aluno['escola'] ?? 'Escola não definida'); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="student-details">
                    <?php if (!empty($aluno['idade'])): ?>
                        <div class="student-detail-item">
                            <i class="fas fa-birthday-cake"></i>
                            Idade: <strong><?php echo htmlspecialchars($aluno['idade']); ?> anos</strong>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($aluno['telefone'])): ?>
                        <div class="student-detail-item">
                            <i class="fas fa-phone"></i>
                            <strong><?php echo htmlspecialchars($aluno['telefone']); ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($aluno['endereco'])): ?>
                        <div class="student-detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars(substr($aluno['endereco'], 0, 40)); ?>
                            <?php if (strlen($aluno['endereco']) > 40) echo '...'; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($aluno['responsavel'])): ?>
                        <div class="student-detail-item">
                            <i class="fas fa-user"></i>
                            Responsável: <strong><?php echo htmlspecialchars($aluno['responsavel']); ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($aluno['rota_id'])): ?>
                    <div class="student-route">
                        <div class="route-info">
                            <span class="route-name">Rota Principal</span>
                            <span class="route-bus">Ônibus 001</span>
                        </div>
                        <div class="pickup-time">
                            <i class="fas fa-clock"></i>
                            Embarque às 07:00
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="student-actions">
                    <button class="btn-icon" onclick="viewStudent(<?php echo $aluno['id']; ?>)" title="Ver detalhes">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon" onclick="editStudent(<?php echo $aluno['id']; ?>)" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon" onclick="deleteStudent(<?php echo $aluno['id']; ?>)" title="Excluir">
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
