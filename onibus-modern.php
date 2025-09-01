<?php
// Configurações da página
$page_title = "Gerenciar Ônibus";
$page_description = "Administre a frota de ônibus e suas informações";
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'icon' => 'fas fa-home'],
    ['title' => 'Ônibus', 'icon' => 'fas fa-bus']
];

$page_actions = '
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-modern" onclick="atualizarOnibus()">
            <i class="fas fa-sync-alt me-2"></i>Atualizar
        </button>
        <button class="btn btn-gradient-primary btn-modern" data-bs-toggle="modal" data-bs-target="#novoOnibusModal">
            <i class="fas fa-plus me-2"></i>Novo Ônibus
        </button>
    </div>
';

$custom_css = '
    .bus-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
    }
    
    .bus-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .bus-status {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 10;
    }
    
    .bus-icon {
        font-size: 3rem;
        opacity: 0.1;
        position: absolute;
        top: 50%;
        right: 1rem;
        transform: translateY(-50%);
    }
    
    .capacity-progress {
        height: 8px;
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.1);
        overflow: hidden;
    }
    
    .capacity-progress .progress-bar {
        border-radius: 50px;
        transition: width 0.3s ease;
    }
    
    .action-buttons .btn {
        transition: all 0.3s ease;
    }
    
    .action-buttons .btn:hover {
        transform: translateY(-2px);
    }
    
    .filter-tabs .nav-link {
        border-radius: 50px !important;
        padding: 0.5rem 1.5rem;
        margin: 0 0.25rem;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .filter-tabs .nav-link.active {
        background: var(--gradient-primary) !important;
        color: white !important;
        border-color: var(--primary-color);
    }
    
    .filter-tabs .nav-link:hover:not(.active) {
        background: rgba(102, 126, 234, 0.1);
        color: var(--primary-color);
    }
    
    .maintenance-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-left: 8px;
    }
    
    .maintenance-indicator.good { background-color: #28a745; }
    .maintenance-indicator.warning { background-color: #ffc107; }
    .maintenance-indicator.danger { background-color: #dc3545; }
    
    .stats-card {
        background: var(--gradient-primary);
        color: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .stats-item {
        text-align: center;
        padding: 1rem;
    }
    
    .stats-value {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    
    .stats-label {
        opacity: 0.8;
        font-size: 0.9rem;
    }
';

ob_start();
?>

<!-- Estatísticas da Frota -->
<div class="content-card stats-card mb-4">
    <div class="row g-0" id="estatisticasFrota">
        <div class="col-md-3">
            <div class="stats-item">
                <div class="stats-value" id="totalOnibus">-</div>
                <div class="stats-label">Total de Ônibus</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-item">
                <div class="stats-value text-success" id="onibusAtivos">-</div>
                <div class="stats-label">Ativos</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-item">
                <div class="stats-value text-warning" id="onibusManutencao">-</div>
                <div class="stats-label">Em Manutenção</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-item">
                <div class="stats-value text-light" id="capacidadeTotal">-</div>
                <div class="stats-label">Capacidade Total</div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Busca -->
<div class="content-card p-4 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" id="searchOnibus" 
                       placeholder="Buscar ônibus..." onkeyup="filtrarOnibus()">
            </div>
        </div>
        <div class="col-md-6">
            <ul class="nav nav-pills filter-tabs justify-content-end" id="onibusFilter">
                <li class="nav-item">
                    <a class="nav-link active" data-filter="all" href="#" onclick="filtrarPorStatus('all')">
                        <i class="fas fa-list me-1"></i>Todos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-filter="ativo" href="#" onclick="filtrarPorStatus('ativo')">
                        <i class="fas fa-check-circle me-1"></i>Ativos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-filter="manutencao" href="#" onclick="filtrarPorStatus('manutencao')">
                        <i class="fas fa-tools me-1"></i>Manutenção
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-filter="inativo" href="#" onclick="filtrarPorStatus('inativo')">
                        <i class="fas fa-ban me-1"></i>Inativos
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Lista de Ônibus -->
<div class="row g-4" id="onibusContainer">
    <div class="col-12 text-center py-5">
        <div class="loading-spinner"></div>
        <p class="text-muted mt-3">Carregando ônibus...</p>
    </div>
</div>

<!-- Modal Novo Ônibus -->
<div class="modal fade" id="novoOnibusModal" tabindex="-1" aria-labelledby="novoOnibusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0" style="background: var(--gradient-primary); border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center text-white">
                    <div class="me-3" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-bus" style="font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h4 class="modal-title mb-0 fw-bold" id="novoOnibusModalLabel">Novo Ônibus</h4>
                        <small class="text-white-50">Adicione um novo ônibus à frota</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formNovoOnibus" class="form-modern">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="placa" class="form-label">
                                <i class="fas fa-id-card me-2 text-primary"></i>Placa
                            </label>
                            <input type="text" class="form-control" id="placa" name="placa" required 
                                   placeholder="Ex: ABC-1234" maxlength="8">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="numero" class="form-label">
                                <i class="fas fa-hashtag me-2 text-info"></i>Número
                            </label>
                            <input type="text" class="form-control" id="numero" name="numero" required 
                                   placeholder="Ex: 001">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="modelo" class="form-label">
                                <i class="fas fa-bus me-2 text-success"></i>Modelo
                            </label>
                            <input type="text" class="form-control" id="modelo" name="modelo" required 
                                   placeholder="Ex: Mercedes-Benz O500">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="ano" class="form-label">
                                <i class="fas fa-calendar me-2 text-warning"></i>Ano
                            </label>
                            <input type="number" class="form-control" id="ano" name="ano" required 
                                   min="1990" max="2030" placeholder="Ex: 2020">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="capacidade" class="form-label">
                                <i class="fas fa-users me-2 text-purple"></i>Capacidade
                            </label>
                            <input type="number" class="form-control" id="capacidade" name="capacidade" required 
                                   min="1" max="100" placeholder="Ex: 45">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="status" class="form-label">
                                <i class="fas fa-info-circle me-2 text-danger"></i>Status
                            </label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="ativo">Ativo</option>
                                <option value="manutencao">Em Manutenção</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="observacoes" class="form-label">
                                <i class="fas fa-sticky-note me-2 text-secondary"></i>Observações
                            </label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3" 
                                      placeholder="Informações adicionais sobre o ônibus..."></textarea>
                        </div>
                        
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fas fa-info-circle me-3"></i>
                                <div>
                                    <strong>Dica:</strong> Certifique-se de que a placa e número são únicos no sistema.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light btn-modern" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-gradient-primary btn-modern" onclick="salvarNovoOnibus()">
                    <i class="fas fa-save me-2"></i>Adicionar Ônibus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Ônibus -->
<div class="modal fade" id="editarOnibusModal" tabindex="-1" aria-labelledby="editarOnibusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0" style="background: var(--gradient-warning); border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center text-white">
                    <div class="me-3" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-edit" style="font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h4 class="modal-title mb-0 fw-bold" id="editarOnibusModalLabel">Editar Ônibus</h4>
                        <small class="text-white-50">Modifique as informações do ônibus</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formEditarOnibus" class="form-modern">
                    <input type="hidden" id="edit_onibus_id" name="onibus_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_placa" class="form-label">
                                <i class="fas fa-id-card me-2 text-primary"></i>Placa
                            </label>
                            <input type="text" class="form-control" id="edit_placa" name="placa" required maxlength="8">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edit_numero" class="form-label">
                                <i class="fas fa-hashtag me-2 text-info"></i>Número
                            </label>
                            <input type="text" class="form-control" id="edit_numero" name="numero" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edit_modelo" class="form-label">
                                <i class="fas fa-bus me-2 text-success"></i>Modelo
                            </label>
                            <input type="text" class="form-control" id="edit_modelo" name="modelo" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edit_ano" class="form-label">
                                <i class="fas fa-calendar me-2 text-warning"></i>Ano
                            </label>
                            <input type="number" class="form-control" id="edit_ano" name="ano" required min="1990" max="2030">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edit_capacidade" class="form-label">
                                <i class="fas fa-users me-2 text-purple"></i>Capacidade
                            </label>
                            <input type="number" class="form-control" id="edit_capacidade" name="capacidade" required min="1" max="100">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edit_status" class="form-label">
                                <i class="fas fa-info-circle me-2 text-danger"></i>Status
                            </label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="ativo">Ativo</option>
                                <option value="manutencao">Em Manutenção</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="edit_observacoes" class="form-label">
                                <i class="fas fa-sticky-note me-2 text-secondary"></i>Observações
                            </label>
                            <textarea class="form-control" id="edit_observacoes" name="observacoes" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light btn-modern" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-gradient-warning btn-modern" onclick="salvarEdicaoOnibus()">
                    <i class="fas fa-save me-2"></i>Salvar Alterações
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variáveis globais
let onibusData = [];
let filtroAtual = 'all';

// Carregar ônibus ao inicializar
document.addEventListener('DOMContentLoaded', function() {
    carregarOnibus();
});

// Função para carregar ônibus
function carregarOnibus() {
    showLoading();
    
    fetch('api/ajax_onibus.php?action=get_buses')
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                onibusData = data.buses;
                renderizarOnibus(onibusData);
                atualizarEstatisticas(onibusData);
            } else {
                mostrarErro('Erro ao carregar ônibus: ' + data.message);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Erro:', error);
            mostrarErro('Erro ao conectar com o servidor');
        });
}

// Função para atualizar estatísticas
function atualizarEstatisticas(buses) {
    const total = buses.length;
    const ativos = buses.filter(bus => bus.status === 'ativo').length;
    const manutencao = buses.filter(bus => bus.status === 'manutencao').length;
    const capacidadeTotal = buses.reduce((sum, bus) => sum + parseInt(bus.capacidade || 0), 0);
    
    document.getElementById('totalOnibus').textContent = total;
    document.getElementById('onibusAtivos').textContent = ativos;
    document.getElementById('onibusManutencao').textContent = manutencao;
    document.getElementById('capacidadeTotal').textContent = capacidadeTotal;
}

// Função para renderizar ônibus
function renderizarOnibus(buses) {
    const container = document.getElementById('onibusContainer');
    
    if (buses.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="content-card p-5 text-center">
                    <i class="fas fa-bus text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">Nenhum ônibus encontrado</h4>
                    <p class="text-muted">Adicione o primeiro ônibus à sua frota</p>
                    <button class="btn btn-gradient-primary btn-modern" data-bs-toggle="modal" data-bs-target="#novoOnibusModal">
                        <i class="fas fa-plus me-2"></i>Adicionar Primeiro Ônibus
                    </button>
                </div>
            </div>
        `;
        return;
    }
    
    let html = '';
    buses.forEach(onibus => {
        let statusBadge = '';
        let statusColor = '';
        let maintenanceClass = '';
        
        switch (onibus.status) {
            case 'ativo':
                statusBadge = '<span class="badge bg-success badge-modern">Ativo</span>';
                statusColor = 'success';
                maintenanceClass = 'good';
                break;
            case 'manutencao':
                statusBadge = '<span class="badge bg-warning badge-modern">Manutenção</span>';
                statusColor = 'warning';
                maintenanceClass = 'warning';
                break;
            case 'inativo':
                statusBadge = '<span class="badge bg-secondary badge-modern">Inativo</span>';
                statusColor = 'secondary';
                maintenanceClass = 'danger';
                break;
        }
        
        const ocupacao = onibus.ocupacao_atual || 0;
        const capacidade = parseInt(onibus.capacidade || 0);
        const percentualOcupacao = capacidade > 0 ? (ocupacao / capacidade) * 100 : 0;
        
        html += `
            <div class="col-xl-4 col-lg-6 onibus-item" data-status="${onibus.status}" data-placa="${onibus.placa.toLowerCase()}" data-numero="${onibus.numero.toLowerCase()}">
                <div class="content-card bus-card h-100 position-relative">
                    <div class="bus-status">
                        ${statusBadge}
                    </div>
                    
                    <i class="fas fa-bus bus-icon text-${statusColor}"></i>
                    
                    <div class="p-4">
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="fw-bold text-dark mb-0">Ônibus ${onibus.numero}</h5>
                                <span class="maintenance-indicator ${maintenanceClass}"></span>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="fas fa-id-card me-2"></i>${onibus.placa}
                            </p>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded-3">
                                    <div class="fw-bold text-primary">${onibus.modelo}</div>
                                    <small class="text-muted">Modelo</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded-3">
                                    <div class="fw-bold text-info">${onibus.ano}</div>
                                    <small class="text-muted">Ano</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <small class="text-muted">Ocupação: ${ocupacao}/${capacidade} lugares</small>
                                <small class="text-muted">${Math.round(percentualOcupacao)}%</small>
                            </div>
                            <div class="capacity-progress">
                                <div class="progress-bar bg-${statusColor}" style="width: ${percentualOcupacao}%"></div>
                            </div>
                        </div>
                        
                        ${onibus.observacoes ? `
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    ${onibus.observacoes}
                                </small>
                            </div>
                        ` : ''}
                        
                        <div class="action-buttons d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm flex-fill" onclick="editarOnibus(${onibus.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <button class="btn btn-outline-info btn-sm" onclick="verDetalhes(${onibus.id})" title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </button>
                            
                            <button class="btn btn-outline-success btn-sm" onclick="verAlocacoes(${onibus.id})" title="Ver Alocações">
                                <i class="fas fa-users"></i>
                            </button>
                            
                            <button class="btn btn-outline-danger btn-sm" onclick="confirmarExclusao(${onibus.id}, '${onibus.placa}')" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // Animar entrada dos cards
    const cards = container.querySelectorAll('.onibus-item');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in-up');
        }, index * 100);
    });
}

// Função para filtrar por status
function filtrarPorStatus(status) {
    filtroAtual = status;
    
    // Atualizar abas ativas
    document.querySelectorAll('.filter-tabs .nav-link').forEach(link => {
        link.classList.remove('active');
    });
    document.querySelector(`[data-filter="${status}"]`).classList.add('active');
    
    filtrarOnibus();
}

// Função para filtrar ônibus
function filtrarOnibus() {
    const searchTerm = document.getElementById('searchOnibus').value.toLowerCase();
    let onibusFiltrados = onibusData;
    
    // Filtrar por status
    if (filtroAtual !== 'all') {
        onibusFiltrados = onibusFiltrados.filter(onibus => onibus.status === filtroAtual);
    }
    
    // Filtrar por termo de busca
    if (searchTerm) {
        onibusFiltrados = onibusFiltrados.filter(onibus => 
            onibus.placa.toLowerCase().includes(searchTerm) ||
            onibus.numero.toLowerCase().includes(searchTerm) ||
            onibus.modelo.toLowerCase().includes(searchTerm)
        );
    }
    
    renderizarOnibus(onibusFiltrados);
}

// Função para atualizar ônibus
function atualizarOnibus() {
    carregarOnibus();
    showToast('info', 'Atualizando', 'Carregando dados mais recentes...');
}

// Função para salvar novo ônibus
function salvarNovoOnibus() {
    const form = document.getElementById('formNovoOnibus');
    const formData = new FormData(form);
    
    // Validação básica
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    showLoading();
    
    fetch('api/ajax_onibus.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'create_bus',
            ...Object.fromEntries(formData)
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('novoOnibusModal')).hide();
            form.reset();
            carregarOnibus();
            showToast('success', 'Sucesso!', 'Ônibus adicionado com sucesso');
        } else {
            showToast('error', 'Erro', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Erro:', error);
        showToast('error', 'Erro', 'Erro ao conectar com o servidor');
    });
}

// Função para editar ônibus
function editarOnibus(id) {
    showLoading();
    
    fetch(`api/ajax_onibus.php?action=get_bus&onibus_id=${id}`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                const onibus = data.bus;
                
                document.getElementById('edit_onibus_id').value = onibus.id;
                document.getElementById('edit_placa').value = onibus.placa;
                document.getElementById('edit_numero').value = onibus.numero;
                document.getElementById('edit_modelo').value = onibus.modelo;
                document.getElementById('edit_ano').value = onibus.ano;
                document.getElementById('edit_capacidade').value = onibus.capacidade;
                document.getElementById('edit_status').value = onibus.status;
                document.getElementById('edit_observacoes').value = onibus.observacoes || '';
                
                const modal = new bootstrap.Modal(document.getElementById('editarOnibusModal'));
                modal.show();
            } else {
                showToast('error', 'Erro', 'Erro ao carregar dados do ônibus');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Erro:', error);
            showToast('error', 'Erro', 'Erro ao conectar com o servidor');
        });
}

// Função para salvar edição do ônibus
function salvarEdicaoOnibus() {
    const form = document.getElementById('formEditarOnibus');
    const formData = new FormData(form);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    showLoading();
    
    fetch('api/ajax_onibus.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'update_bus',
            ...Object.fromEntries(formData)
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editarOnibusModal')).hide();
            carregarOnibus();
            showToast('success', 'Sucesso!', 'Ônibus atualizado com sucesso');
        } else {
            showToast('error', 'Erro', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Erro:', error);
        showToast('error', 'Erro', 'Erro ao conectar com o servidor');
    });
}

// Função para ver detalhes
function verDetalhes(onibusId) {
    window.location.href = `onibus-detalhes.php?id=${onibusId}`;
}

// Função para ver alocações
function verAlocacoes(onibusId) {
    window.location.href = `alocacoes.php?onibus=${onibusId}`;
}

// Função para confirmar exclusão
function confirmarExclusao(onibusId, placa) {
    if (confirm(`Tem certeza que deseja excluir o ônibus ${placa}?\n\nEsta ação não pode ser desfeita.`)) {
        showLoading();
        
        fetch('api/ajax_onibus.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove_bus&onibus_id=${onibusId}`
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                carregarOnibus();
                showToast('success', 'Excluído!', 'Ônibus excluído com sucesso');
            } else {
                showToast('error', 'Erro', data.message);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Erro:', error);
            showToast('error', 'Erro', 'Erro ao conectar com o servidor');
        });
    }
}

function mostrarErro(mensagem) {
    const container = document.getElementById('onibusContainer');
    container.innerHTML = `
        <div class="col-12">
            <div class="content-card p-4">
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-3"></i>
                    <div>
                        <strong>Erro!</strong> ${mensagem}
                    </div>
                </div>
                <button class="btn btn-outline-primary" onclick="carregarOnibus()">
                    <i class="fas fa-redo me-2"></i>Tentar Novamente
                </button>
            </div>
        </div>
    `;
}
</script>

<?php
$content = ob_get_clean();

// Incluir layout
include 'includes/layout-modern.php';
?>
