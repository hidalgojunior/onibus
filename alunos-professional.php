<?php
// Configurações da página
$page_title = "Alunos";
$page_description = "Gestão de Estudantes";

// Incluir configuração do banco
include 'config/config.php';

// Conectar ao banco
$conn = getDatabaseConnection();
$alunos = [];
$message = '';
$message_type = '';

// Processar ações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $nome = trim($_POST['nome']);
                $rg = trim($_POST['rg']);
                $rm = trim($_POST['rm']);
                $serie = trim($_POST['serie']);
                $curso = trim($_POST['curso']);
                $telefone = trim($_POST['telefone']);
                $data_aniversario = !empty($_POST['data_aniversario']) ? $_POST['data_aniversario'] : NULL;
                $responsavel_nome = trim($_POST['responsavel_nome']);
                $responsavel_telefone = trim($_POST['responsavel_telefone']);
                $responsavel_whatsapp = trim($_POST['responsavel_whatsapp']);
                $telefone_emergencia = trim($_POST['telefone_emergencia']);
                $endereco_completo = trim($_POST['endereco_completo']);
                $ponto_embarque = trim($_POST['ponto_embarque']);
                $observacoes_medicas = trim($_POST['observacoes_medicas']);
                $whatsapp_permissao = isset($_POST['whatsapp_permissao']) ? 1 : 0;
                $autorizacao_transporte = isset($_POST['autorizacao_transporte']) ? 1 : 0;
                
                if (!empty($nome) && !empty($serie) && !empty($curso)) {
                    $stmt = $conn->prepare("INSERT INTO alunos (nome, rg, rm, serie, curso, telefone, data_aniversario, responsavel_nome, responsavel_telefone, responsavel_whatsapp, telefone_emergencia, endereco_completo, ponto_embarque, observacoes_medicas, whatsapp_permissao, autorizacao_transporte, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->bind_param("ssssssssssssssii", $nome, $rg, $rm, $serie, $curso, $telefone, $data_aniversario, $responsavel_nome, $responsavel_telefone, $responsavel_whatsapp, $telefone_emergencia, $endereco_completo, $ponto_embarque, $observacoes_medicas, $whatsapp_permissao, $autorizacao_transporte);
                    
                    if ($stmt->execute()) {
                        $message = "Aluno cadastrado com sucesso!";
                        $message_type = "success";
                    } else {
                        $message = "Erro ao cadastrar aluno: " . $stmt->error;
                        $message_type = "danger";
                    }
                    $stmt->close();
                } else {
                    $message = "Por favor, preencha os campos obrigatórios (Nome, Série e Curso).";
                    $message_type = "warning";
                }
                break;
                
            case 'update':
                $id = (int)$_POST['id'];
                $nome = trim($_POST['nome']);
                $rg = trim($_POST['rg']);
                $rm = trim($_POST['rm']);
                $serie = trim($_POST['serie']);
                $curso = trim($_POST['curso']);
                $telefone = trim($_POST['telefone']);
                $data_aniversario = !empty($_POST['data_aniversario']) ? $_POST['data_aniversario'] : NULL;
                $responsavel_nome = trim($_POST['responsavel_nome']);
                $responsavel_telefone = trim($_POST['responsavel_telefone']);
                $responsavel_whatsapp = trim($_POST['responsavel_whatsapp']);
                $telefone_emergencia = trim($_POST['telefone_emergencia']);
                $endereco_completo = trim($_POST['endereco_completo']);
                $ponto_embarque = trim($_POST['ponto_embarque']);
                $observacoes_medicas = trim($_POST['observacoes_medicas']);
                $whatsapp_permissao = isset($_POST['whatsapp_permissao']) ? 1 : 0;
                $autorizacao_transporte = isset($_POST['autorizacao_transporte']) ? 1 : 0;
                
                if (!empty($nome) && !empty($serie) && !empty($curso) && $id > 0) {
                    $stmt = $conn->prepare("UPDATE alunos SET nome=?, rg=?, rm=?, serie=?, curso=?, telefone=?, data_aniversario=?, responsavel_nome=?, responsavel_telefone=?, responsavel_whatsapp=?, telefone_emergencia=?, endereco_completo=?, ponto_embarque=?, observacoes_medicas=?, whatsapp_permissao=?, autorizacao_transporte=? WHERE id=?");
                    $stmt->bind_param("sssssssssssssssii", $nome, $rg, $rm, $serie, $curso, $telefone, $data_aniversario, $responsavel_nome, $responsavel_telefone, $responsavel_whatsapp, $telefone_emergencia, $endereco_completo, $ponto_embarque, $observacoes_medicas, $whatsapp_permissao, $autorizacao_transporte, $id);
                    
                    if ($stmt->execute()) {
                        $message = "Aluno atualizado com sucesso!";
                        $message_type = "success";
                    } else {
                        $message = "Erro ao atualizar aluno: " . $stmt->error;
                        $message_type = "danger";
                    }
                    $stmt->close();
                } else {
                    $message = "Dados inválidos para atualização.";
                    $message_type = "warning";
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                if ($id > 0) {
                    // Verificar se o aluno tem alocações ou presenças
                    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM alocacoes_onibus WHERE aluno_id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $tem_alocacoes = $row['total'] > 0;
                    $stmt->close();
                    
                    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM presencas WHERE aluno_id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $tem_presencas = $row['total'] > 0;
                    $stmt->close();
                    
                    if ($tem_alocacoes || $tem_presencas) {
                        $message = "Não é possível excluir este aluno pois ele possui alocações ou presenças registradas.";
                        $message_type = "warning";
                    } else {
                        $stmt = $conn->prepare("DELETE FROM alunos WHERE id = ?");
                        $stmt->bind_param("i", $id);
                        
                        if ($stmt->execute()) {
                            $message = "Aluno excluído com sucesso!";
                            $message_type = "success";
                        } else {
                            $message = "Erro ao excluir aluno: " . $stmt->error;
                            $message_type = "danger";
                        }
                        $stmt->close();
                    }
                } else {
                    $message = "ID inválido para exclusão.";
                    $message_type = "warning";
                }
                break;
        }
    }
}

// Buscar todos os alunos
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

$custom_js = '';

ob_start();
?>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Header com botão de adicionar -->
<div class="page-header">
    <div class="header-content">
        <div>
            <h2>Gestão de Alunos</h2>
            <p class="text-muted">Gerencie os estudantes do sistema de transporte</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="fas fa-plus"></i> Novo Aluno
        </button>
    </div>
</div>

<!-- Students Statistics -->
<div class="students-stats">
    <div class="student-stat">
        <div class="student-stat-icon" style="background-color: var(--success-color);">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="student-stat-value"><?php echo count($alunos); ?></div>
        <div class="student-stat-label">Total de Alunos</div>
    </div>
    
    <div class="student-stat">
        <div class="student-stat-icon" style="background-color: var(--accent-color);">
            <i class="fas fa-bus"></i>
        </div>
        <div class="student-stat-value"><?php echo count(array_filter($alunos, function($a) { return $a['autorizacao_transporte']; })); ?></div>
        <div class="student-stat-label">Com Autorização</div>
    </div>
    
    <div class="student-stat">
        <div class="student-stat-icon" style="background-color: var(--warning-color);">
            <i class="fas fa-mobile-alt"></i>
        </div>
        <div class="student-stat-value"><?php echo count(array_filter($alunos, function($a) { return $a['whatsapp_permissao']; })); ?></div>
        <div class="student-stat-label">Permitem WhatsApp</div>
    </div>
    
    <div class="student-stat">
        <div class="student-stat-icon" style="background-color: var(--danger-color);">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="student-stat-value"><?php echo count(array_filter($alunos, function($a) { return !empty($a['observacoes_medicas']); })); ?></div>
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
                    <button class="btn-icon view" onclick="viewStudent(<?php echo $aluno['id']; ?>)" title="Ver detalhes">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon edit" onclick="editStudent(<?php echo $aluno['id']; ?>)" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon delete" onclick="deleteStudent(<?php echo $aluno['id']; ?>)" title="Excluir">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Modal para adicionar aluno -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">RG</label>
                            <input type="text" class="form-control" name="rg">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">RM</label>
                            <input type="text" class="form-control" name="rm">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Série *</label>
                            <input type="text" class="form-control" name="serie" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Curso *</label>
                            <input type="text" class="form-control" name="curso" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" name="data_aniversario">
                            <small class="form-text text-muted">Formato: dia/mês/ano</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" class="form-control" name="telefone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telefone de Emergência</label>
                            <input type="text" class="form-control" name="telefone_emergencia">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nome do Responsável</label>
                            <input type="text" class="form-control" name="responsavel_nome">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Telefone do Responsável</label>
                            <input type="text" class="form-control" name="responsavel_telefone">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">WhatsApp do Responsável</label>
                            <input type="text" class="form-control" name="responsavel_whatsapp">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Endereço Completo</label>
                            <textarea class="form-control" name="endereco_completo" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ponto de Embarque</label>
                            <input type="text" class="form-control" name="ponto_embarque">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observações Médicas</label>
                        <textarea class="form-control" name="observacoes_medicas" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="whatsapp_permissao" id="whatsapp_permissao">
                                <label class="form-check-label" for="whatsapp_permissao">
                                    Permite contato via WhatsApp
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="autorizacao_transporte" id="autorizacao_transporte">
                                <label class="form-check-label" for="autorizacao_transporte">
                                    Autorização para transporte
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cadastrar Aluno</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para visualizar aluno -->
<div class="modal fade" id="viewStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewStudentContent">
                <!-- Conteúdo será carregado via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="editCurrentStudent()">Editar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar aluno -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editStudentForm">
                <div class="modal-body" id="editStudentContent">
                    <!-- Conteúdo será carregado via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para confirmar exclusão -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este aluno?</p>
                <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteStudentId">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Continuar o JavaScript já iniciado
$custom_js .= '

// Debug inicial
console.log("=== INICIANDO CARREGAMENTO DAS FUNCOES ===");

// Dados dos alunos para JavaScript
window.alunosData = ' . json_encode($alunos, JSON_UNESCAPED_UNICODE) . ';
console.log("Dados carregados:", window.alunosData ? window.alunosData.length : 0, "alunos");

// Funcao de teste simples
window.testeClick = function() {
    alert("Teste de clique funcionando!");
    console.log("Teste de clique executado com sucesso!");
};

// Definir funcoes no escopo global imediatamente
window.viewStudent = function(id) {
    console.log("=== VIEWSTUDENT CHAMADO ===");
    console.log("ID recebido:", id);
    console.log("Tipo do ID:", typeof id);
    
    if (!window.alunosData) {
        console.error("alunosData nao esta definido!");
        alert("Erro: Dados dos alunos nao carregados!");
        return;
    }
    
    const aluno = window.alunosData.find(a => a.id == id);
    console.log("Aluno encontrado:", aluno);
    
    if (!aluno) {
        console.error("Aluno nao encontrado para ID:", id);
        alert("Aluno nao encontrado!");
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
            openViewModal(id, aluno);
        });
    } else {
        console.log("DOM pronto, abrindo modal...");
        openViewModal(id, aluno);
    }
};

window.editStudent = function(id) {
    console.log("=== EDITSTUDENT CHAMADO ===");
    console.log("ID recebido:", id);
    console.log("Tipo do ID:", typeof id);
    
    if (!window.alunosData) {
        console.error("alunosData nao esta definido!");
        alert("Erro: Dados dos alunos nao carregados!");
        return;
    }
    
    const aluno = window.alunosData.find(a => a.id == id);
    console.log("Aluno encontrado:", aluno);
    
    if (!aluno) {
        console.error("Aluno nao encontrado para ID:", id);
        alert("Aluno nao encontrado!");
        return;
    }
    
    console.log("Tentando abrir modal de edicao...");
    
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
            openEditModal(id, aluno);
        });
    } else {
        console.log("DOM pronto, abrindo modal de edicao...");
        openEditModal(id, aluno);
    }
};

window.deleteStudent = function(id) {
    console.log("=== DELETESTUDENT CHAMADO ===");
    console.log("ID recebido:", id);
    
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
            openDeleteModal(id);
        });
    } else {
        console.log("DOM pronto, abrindo modal de exclusao...");
        openDeleteModal(id);
    }
};

window.addStudent = function() {
    console.log("=== ADDSTUDENT CHAMADO ===");
    
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
            openAddModal();
        });
    } else {
        console.log("DOM pronto, abrindo modal de adicao...");
        openAddModal();
    }
};

function openViewModal(id, aluno) {
    console.log("=== OPENVIEWMODAL ===");
    console.log("Procurando modal viewStudentModal...");
    
    const modalElement = document.getElementById("viewStudentModal");
    if (!modalElement) {
        console.error("Modal viewStudentModal nao encontrado!");
        alert("Modal de visualizacao nao encontrado!");
        return;
    }
    
    console.log("Modal encontrado, preenchendo conteudo...");
    
    const formatDate = (dateStr) => {
        if (!dateStr) return "Não informado";
        const date = new Date(dateStr + "T00:00:00"); // Adiciona hora para evitar problemas de timezone
        if (isNaN(date.getTime())) return "Data inválida";
        return date.toLocaleDateString("pt-BR", {
            day: "2-digit",
            month: "2-digit", 
            year: "numeric"
        });
    };
    
    const contentElement = document.getElementById("viewStudentContent");
    if (contentElement) {
        contentElement.innerHTML = `
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="d-flex align-items-center">
                        <div style="width: 80px; height: 80px; font-size: 2rem; background: linear-gradient(135deg, #1e3a5f, #4299e1); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                            ${aluno.nome.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <h4 class="mb-1">${aluno.nome}</h4>
                            <p class="text-muted mb-0">${aluno.serie} - ${aluno.curso}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Dados Pessoais</h6>
                    <p><strong>RG:</strong> ${aluno.rg || "Nao informado"}</p>
                    <p><strong>RM:</strong> ${aluno.rm || "Nao informado"}</p>
                    <p><strong>Data de Nascimento:</strong> ${formatDate(aluno.data_aniversario)}</p>
                    <p><strong>Telefone:</strong> ${aluno.telefone || "Nao informado"}</p>
                </div>
                <div class="col-md-6">
                    <h6>Responsavel</h6>
                    <p><strong>Nome:</strong> ${aluno.responsavel_nome || "Nao informado"}</p>
                    <p><strong>Telefone:</strong> ${aluno.responsavel_telefone || "Nao informado"}</p>
                    <p><strong>WhatsApp:</strong> ${aluno.responsavel_whatsapp || "Nao informado"}</p>
                </div>
            </div>
        `;
        console.log("Conteudo do modal preenchido");
    } else {
        console.error("Elemento viewStudentContent nao encontrado!");
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

function openDeleteModal(id) {
    console.log("=== OPENDELETEMODAL ===");
    console.log("Procurando modal deleteStudentModal...");
    
    const modalElement = document.getElementById("deleteStudentModal");
    if (!modalElement) {
        console.error("Modal deleteStudentModal nao encontrado!");
        alert("Modal de exclusao nao encontrado!");
        return;
    }
    
    console.log("Modal encontrado, definindo ID...");
    
    const hiddenInput = document.getElementById("deleteStudentId");
    if (hiddenInput) {
        hiddenInput.value = id;
        console.log("ID definido no input hidden:", id);
    } else {
        console.warn("Input deleteStudentId nao encontrado");
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

function openAddModal() {
    console.log("=== OPENADDMODAL ===");
    console.log("Procurando modal addStudentModal...");
    
    const modalElement = document.getElementById("addStudentModal");
    if (!modalElement) {
        console.error("Modal addStudentModal nao encontrado!");
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

function openEditModal(id, aluno) {
    console.log("=== OPENEDITMODAL ===");
    console.log("Procurando modal editStudentModal...");
    
    const modalElement = document.getElementById("editStudentModal");
    if (!modalElement) {
        console.error("Modal editStudentModal nao encontrado!");
        alert("Modal de edicao nao encontrado!");
        return;
    }
    
    console.log("Modal encontrado, preenchendo formulario...");
    
    // Função para formatar data para input date
    const formatDateForInput = (dateStr) => {
        if (!dateStr) return "";
        if (dateStr.match(/^\\\d{4}-\\\d{2}-\\\d{2}$/)) {
            return dateStr;
        }
        const date = new Date(dateStr);
        if (isNaN(date.getTime())) return "";
        return date.toISOString().split("T")[0];
    };
    
    const contentElement = document.getElementById("editStudentContent");
    if (contentElement) {
        // Construir o HTML usando concatenação de strings para evitar problemas de aspas
        let html = "";
        html += "<input type=\\"hidden\\" name=\\"action\\" value=\\"update\\">";
        html += "<input type=\\"hidden\\" name=\\"id\\" value=\\"" + aluno.id + "\\">";
        
        html += "<div class=\\"row\\">";
        html += "<div class=\\"col-md-8 mb-3\\">";
        html += "<label class=\\"form-label\\">Nome Completo *</label>";
        html += "<input type=\\"text\\" class=\\"form-control\\" name=\\"nome\\" value=\\"" + (aluno.nome || "") + "\\" required>";
        html += "</div>";
        html += "<div class=\\"col-md-4 mb-3\\">";
        html += "<label class=\\"form-label\\">RG</label>";
        html += "<input type=\\"text\\" class=\\"form-control\\" name=\\"rg\\" value=\\"" + (aluno.rg || "") + "\\">";
        html += "</div>";
        html += "</div>";
        
        html += "<div class=\\"row\\">";
        html += "<div class=\\"col-md-4 mb-3\\">";
        html += "<label class=\\"form-label\\">RM</label>";
        html += "<input type=\\"text\\" class=\\"form-control\\" name=\\"rm\\" value=\\"" + (aluno.rm || "") + "\\">";
        html += "</div>";
        html += "<div class=\\"col-md-4 mb-3\\">";
        html += "<label class=\\"form-label\\">Série *</label>";
        html += "<input type=\\"text\\" class=\\"form-control\\" name=\\"serie\\" value=\\"" + (aluno.serie || "") + "\\" required>";
        html += "</div>";
        html += "<div class=\\"col-md-4 mb-3\\">";
        html += "<label class=\\"form-label\\">Curso *</label>";
        html += "<input type=\\"text\\" class=\\"form-control\\" name=\\"curso\\" value=\\"" + (aluno.curso || "") + "\\" required>";
        html += "</div>";
        html += "</div>";
        
        html += "<div class=\\"row\\">";
        html += "<div class=\\"col-md-6 mb-3\\">";
        html += "<label class=\\"form-label\\">Data de Nascimento</label>";
        html += "<input type=\\"date\\" class=\\"form-control\\" name=\\"data_aniversario\\" value=\\"" + formatDateForInput(aluno.data_aniversario) + "\\">";
        html += "<small class=\\"form-text text-muted\\">Formato: dia/mês/ano</small>";
        html += "</div>";
        html += "<div class=\\"col-md-6 mb-3\\">";
        html += "<label class=\\"form-label\\">Telefone</label>";
        html += "<input type=\\"text\\" class=\\"form-control\\" name=\\"telefone\\" value=\\"" + (aluno.telefone || "") + "\\">";
        html += "</div>";
        html += "</div>";
        
        html += "<div class=\\"row\\">";
        html += "<div class=\\"col-md-12 mb-3\\">";
        html += "<label class=\\"form-label\\">Nome do Responsável</label>";
        html += "<input type=\\"text\\" class=\\"form-control\\" name=\\"responsavel_nome\\" value=\\"" + (aluno.responsavel_nome || "") + "\\">";
        html += "</div>";
        html += "</div>";
        
        html += "<div class=\\"row\\">";
        html += "<div class=\\"col-md-6 mb-3\\">";
        html += "<label class=\\"form-label\\">Telefone do Responsável</label>";
        html += "<input type=\\"text\\" class=\\"form-control\\" name=\\"responsavel_telefone\\" value=\\"" + (aluno.responsavel_telefone || "") + "\\">";
        html += "</div>";
        html += "<div class=\\"col-md-6 mb-3\\">";
        html += "<label class=\\"form-label\\">WhatsApp do Responsável</label>";
        html += "<input type=\\"text\\" class=\\"form-control\\" name=\\"responsavel_whatsapp\\" value=\\"" + (aluno.responsavel_whatsapp || "") + "\\">";
        html += "</div>";
        html += "</div>";
        
        html += "<div class=\\"row\\">";
        html += "<div class=\\"col-md-12 mb-3\\">";
        html += "<label class=\\"form-label\\">Observações Médicas</label>";
        html += "<textarea class=\\"form-control\\" name=\\"observacoes_medicas\\" rows=\\"3\\">" + (aluno.observacoes_medicas || "") + "</textarea>";
        html += "</div>";
        html += "</div>";
        
        html += "<div class=\\"row\\">";
        html += "<div class=\\"col-md-6 mb-3\\">";
        html += "<div class=\\"form-check\\">";
        html += "<input class=\\"form-check-input\\" type=\\"checkbox\\" name=\\"autorizacao_transporte\\" value=\\"1\\"" + (aluno.autorizacao_transporte ? " checked" : "") + ">";
        html += "<label class=\\"form-check-label\\">Autorização para Transporte</label>";
        html += "</div>";
        html += "</div>";
        html += "<div class=\\"col-md-6 mb-3\\">";
        html += "<div class=\\"form-check\\">";
        html += "<input class=\\"form-check-input\\" type=\\"checkbox\\" name=\\"whatsapp_permissao\\" value=\\"1\\"" + (aluno.whatsapp_permissao ? " checked" : "") + ">";
        html += "<label class=\\"form-check-label\\">Permissão WhatsApp</label>";
        html += "</div>";
        html += "</div>";
        html += "</div>";
        
        contentElement.innerHTML = html;
        console.log("Formulario de edicao preenchido");
    } else {
        console.error("Elemento editStudentContent nao encontrado!");
    }

    console.log("Abrindo modal de edicao com Bootstrap...");
    try {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log("Modal de edicao aberto com sucesso!");
    } catch (error) {
        console.error("Erro ao abrir modal de edicao:", error);
        alert("Erro ao abrir modal de edicao: " + error.message);
    }
}

console.log("=== FUNCOES JAVASCRIPT CARREGADAS COM SUCESSO ===");
console.log("Bootstrap disponivel:", typeof bootstrap !== "undefined");
console.log("Dados dos alunos:", window.alunosData ? "OK" : "ERRO");
console.log("viewStudent:", typeof window.viewStudent);
console.log("editStudent:", typeof window.editStudent);
console.log("deleteStudent:", typeof window.deleteStudent);
console.log("addStudent:", typeof window.addStudent);
';

include 'includes/layout-professional.php';
?>
