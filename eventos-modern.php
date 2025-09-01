<?php
// Configurações da página
$page_title = "Gerenciar Eventos";
$page_description = "Crie, edite e gerencie eventos do sistema de transporte";
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'index.php', 'icon' => 'fas fa-home'],
    ['title' => 'Eventos', 'icon' => 'fas fa-calendar-alt']
];

$page_actions = '
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-modern" onclick="atualizarEventos()">
            <i class="fas fa-sync-alt me-2"></i>Atualizar
        </button>
        <button class="btn btn-gradient-primary btn-modern" data-bs-toggle="modal" data-bs-target="#novoEventoModal">
            <i class="fas fa-plus me-2"></i>Novo Evento
        </button>
    </div>
';

$custom_css = '
    .event-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .status-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 10;
    }
    
    .qr-icon {
        transition: all 0.3s ease;
    }
    
    .qr-icon:hover {
        transform: scale(1.1);
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
';

ob_start();
?>

<!-- Filtros e Busca -->
<div class="content-card p-4 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" id="searchEvents" 
                       placeholder="Buscar eventos..." onkeyup="filtrarEventos()">
            </div>
        </div>
        <div class="col-md-6">
            <ul class="nav nav-pills filter-tabs justify-content-end" id="eventFilter">
                <li class="nav-item">
                    <a class="nav-link active" data-filter="all" href="#" onclick="filtrarPorStatus('all')">
                        <i class="fas fa-list me-1"></i>Todos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-filter="active" href="#" onclick="filtrarPorStatus('active')">
                        <i class="fas fa-play-circle me-1"></i>Ativos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-filter="upcoming" href="#" onclick="filtrarPorStatus('upcoming')">
                        <i class="fas fa-clock me-1"></i>Próximos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-filter="finished" href="#" onclick="filtrarPorStatus('finished')">
                        <i class="fas fa-check-circle me-1"></i>Finalizados
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Lista de Eventos -->
<div class="row g-4" id="eventosContainer">
    <div class="col-12 text-center py-5">
        <div class="loading-spinner"></div>
        <p class="text-muted mt-3">Carregando eventos...</p>
    </div>
</div>

<!-- Modal Novo Evento -->
<div class="modal fade" id="novoEventoModal" tabindex="-1" aria-labelledby="novoEventoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0" style="background: var(--gradient-primary); border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center text-white">
                    <div class="me-3" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-calendar-plus" style="font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h4 class="modal-title mb-0 fw-bold" id="novoEventoModalLabel">Novo Evento</h4>
                        <small class="text-white-50">Preencha as informações do evento</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formNovoEvento" class="form-modern">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="nomeEvento" class="form-label">
                                <i class="fas fa-tag me-2 text-primary"></i>Nome do Evento
                            </label>
                            <input type="text" class="form-control" id="nomeEvento" name="nome" required 
                                   placeholder="Ex: Viagem para o Museu Nacional">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="dataInicio" class="form-label">
                                <i class="fas fa-calendar me-2 text-success"></i>Data de Início
                            </label>
                            <input type="date" class="form-control" id="dataInicio" name="data_inicio" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="dataFim" class="form-label">
                                <i class="fas fa-calendar-check me-2 text-success"></i>Data de Fim
                            </label>
                            <input type="date" class="form-control" id="dataFim" name="data_fim" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="localEvento" class="form-label">
                                <i class="fas fa-map-marker-alt me-2 text-danger"></i>Local
                            </label>
                            <input type="text" class="form-control" id="localEvento" name="local" required 
                                   placeholder="Ex: Museu Nacional - Rio de Janeiro">
                        </div>
                        
                        <div class="col-md-12">
                            <label for="descricaoEvento" class="form-label">
                                <i class="fas fa-align-left me-2 text-info"></i>Descrição
                            </label>
                            <textarea class="form-control" id="descricaoEvento" name="descricao" rows="3" 
                                      placeholder="Descreva os detalhes do evento..."></textarea>
                        </div>
                        
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fas fa-info-circle me-3"></i>
                                <div>
                                    <strong>Dica:</strong> Após criar o evento, você poderá adicionar alunos e gerar QR codes para inscrições.
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
                <button type="button" class="btn btn-gradient-primary btn-modern" onclick="salvarNovoEvento()">
                    <i class="fas fa-save me-2"></i>Criar Evento
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Evento -->
<div class="modal fade" id="editarEventoModal" tabindex="-1" aria-labelledby="editarEventoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0" style="background: var(--gradient-warning); border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center text-white">
                    <div class="me-3" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-edit" style="font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h4 class="modal-title mb-0 fw-bold" id="editarEventoModalLabel">Editar Evento</h4>
                        <small class="text-white-50">Modifique as informações do evento</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formEditarEvento" class="form-modern">
                    <input type="hidden" id="edit_evento_id" name="evento_id">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="edit_nome" class="form-label">
                                <i class="fas fa-tag me-2 text-primary"></i>Nome do Evento
                            </label>
                            <input type="text" class="form-control" id="edit_nome" name="nome" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edit_data_inicio" class="form-label">
                                <i class="fas fa-calendar me-2 text-success"></i>Data de Início
                            </label>
                            <input type="date" class="form-control" id="edit_data_inicio" name="data_inicio" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edit_data_fim" class="form-label">
                                <i class="fas fa-calendar-check me-2 text-success"></i>Data de Fim
                            </label>
                            <input type="date" class="form-control" id="edit_data_fim" name="data_fim" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="edit_local" class="form-label">
                                <i class="fas fa-map-marker-alt me-2 text-danger"></i>Local
                            </label>
                            <input type="text" class="form-control" id="edit_local" name="local" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="edit_descricao" class="form-label">
                                <i class="fas fa-align-left me-2 text-info"></i>Descrição
                            </label>
                            <textarea class="form-control" id="edit_descricao" name="descricao" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light btn-modern" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-gradient-warning btn-modern" onclick="salvarEdicaoEvento()">
                    <i class="fas fa-save me-2"></i>Salvar Alterações
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variáveis globais
let eventosData = [];
let filtroAtual = 'all';

// Carregar eventos ao inicializar
document.addEventListener('DOMContentLoaded', function() {
    carregarEventos();
});

// Função para carregar eventos
function carregarEventos() {
    showLoading();
    
    fetch('api/ajax_eventos.php?action=get_events')
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                eventosData = data.events;
                renderizarEventos(eventosData);
            } else {
                mostrarErro('Erro ao carregar eventos: ' + data.message);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Erro:', error);
            mostrarErro('Erro ao conectar com o servidor');
        });
}

// Função para renderizar eventos
function renderizarEventos(eventos) {
    const container = document.getElementById('eventosContainer');
    
    if (eventos.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="content-card p-5 text-center">
                    <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">Nenhum evento encontrado</h4>
                    <p class="text-muted">Crie seu primeiro evento para começar</p>
                    <button class="btn btn-gradient-primary btn-modern" data-bs-toggle="modal" data-bs-target="#novoEventoModal">
                        <i class="fas fa-plus me-2"></i>Criar Primeiro Evento
                    </button>
                </div>
            </div>
        `;
        return;
    }
    
    let html = '';
    eventos.forEach(evento => {
        const dataInicio = new Date(evento.data_inicio);
        const dataFim = new Date(evento.data_fim);
        const hoje = new Date();
        
        let status = 'finished';
        let statusText = 'Finalizado';
        let statusClass = 'bg-secondary';
        
        if (dataInicio > hoje) {
            status = 'upcoming';
            statusText = 'Próximo';
            statusClass = 'bg-warning';
        } else if (dataFim >= hoje) {
            status = 'active';
            statusText = 'Ativo';
            statusClass = 'bg-success';
        }
        
        html += `
            <div class="col-xl-4 col-lg-6 evento-item" data-status="${status}" data-nome="${evento.nome.toLowerCase()}">
                <div class="content-card event-card h-100 position-relative overflow-hidden">
                    <div class="status-badge">
                        <span class="badge ${statusClass} badge-modern">${statusText}</span>
                    </div>
                    
                    <div class="p-4">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h5 class="fw-bold text-dark mb-2">${evento.nome}</h5>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>${evento.local}
                                </p>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded-3">
                                    <div class="fw-bold text-primary">${evento.total_alunos || 0}</div>
                                    <small class="text-muted">Alunos</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded-3">
                                    <div class="fw-bold text-success">${evento.total_onibus || 0}</div>
                                    <small class="text-muted">Ônibus</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center text-muted mb-3">
                            <i class="fas fa-calendar me-2"></i>
                            <small>${dataInicio.toLocaleDateString('pt-BR')} - ${dataFim.toLocaleDateString('pt-BR')}</small>
                        </div>
                        
                        <div class="action-buttons d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm flex-fill" onclick="editarEvento(${evento.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            ${evento.short_code ? 
                                `<button class="btn btn-outline-success btn-sm qr-icon" onclick="verQRCode(${evento.id}, '${evento.short_code}', '${evento.public_url}')" title="Ver QR Code">
                                    <i class="fas fa-qrcode"></i>
                                </button>` :
                                `<button class="btn btn-outline-warning btn-sm" onclick="gerarQRCode(${evento.id}, '${evento.nome}')" title="Gerar QR Code">
                                    <i class="fas fa-plus"></i>
                                </button>`
                            }
                            
                            <button class="btn btn-outline-info btn-sm" onclick="verDetalhes(${evento.id})" title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </button>
                            
                            <button class="btn btn-outline-danger btn-sm" onclick="confirmarExclusao(${evento.id}, '${evento.nome}')" title="Excluir">
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
    const cards = container.querySelectorAll('.evento-item');
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
    
    filtrarEventos();
}

// Função para filtrar eventos
function filtrarEventos() {
    const searchTerm = document.getElementById('searchEvents').value.toLowerCase();
    let eventosFiltrados = eventosData;
    
    // Filtrar por status
    if (filtroAtual !== 'all') {
        const hoje = new Date();
        eventosFiltrados = eventosFiltrados.filter(evento => {
            const dataInicio = new Date(evento.data_inicio);
            const dataFim = new Date(evento.data_fim);
            
            switch (filtroAtual) {
                case 'active':
                    return dataInicio <= hoje && dataFim >= hoje;
                case 'upcoming':
                    return dataInicio > hoje;
                case 'finished':
                    return dataFim < hoje;
                default:
                    return true;
            }
        });
    }
    
    // Filtrar por termo de busca
    if (searchTerm) {
        eventosFiltrados = eventosFiltrados.filter(evento => 
            evento.nome.toLowerCase().includes(searchTerm) ||
            evento.local.toLowerCase().includes(searchTerm)
        );
    }
    
    renderizarEventos(eventosFiltrados);
}

// Função para atualizar eventos
function atualizarEventos() {
    carregarEventos();
    showToast('info', 'Atualizando', 'Carregando eventos mais recentes...');
}

// Função para salvar novo evento
function salvarNovoEvento() {
    const form = document.getElementById('formNovoEvento');
    const formData = new FormData(form);
    
    // Validação básica
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    showLoading();
    
    fetch('api/ajax_eventos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'create_event',
            ...Object.fromEntries(formData)
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('novoEventoModal')).hide();
            form.reset();
            carregarEventos();
            showToast('success', 'Sucesso!', 'Evento criado com sucesso');
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

// Função para editar evento
function editarEvento(id) {
    showLoading();
    
    fetch(`api/ajax_eventos.php?action=get_event&evento_id=${id}`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                const evento = data.event;
                
                document.getElementById('edit_evento_id').value = evento.id;
                document.getElementById('edit_nome').value = evento.nome;
                document.getElementById('edit_data_inicio').value = evento.data_inicio;
                document.getElementById('edit_data_fim').value = evento.data_fim;
                document.getElementById('edit_local').value = evento.local;
                document.getElementById('edit_descricao').value = evento.descricao || '';
                
                const modal = new bootstrap.Modal(document.getElementById('editarEventoModal'));
                modal.show();
            } else {
                showToast('error', 'Erro', 'Erro ao carregar dados do evento');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Erro:', error);
            showToast('error', 'Erro', 'Erro ao conectar com o servidor');
        });
}

// Função para salvar edição do evento
function salvarEdicaoEvento() {
    const form = document.getElementById('formEditarEvento');
    const formData = new FormData(form);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    showLoading();
    
    fetch('api/ajax_eventos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'update_event',
            ...Object.fromEntries(formData)
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editarEventoModal')).hide();
            carregarEventos();
            showToast('success', 'Sucesso!', 'Evento atualizado com sucesso');
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

// Função para gerar QR Code
function gerarQRCode(eventoId, eventoNome) {
    if (confirm(`Deseja gerar um QR Code para o evento "${eventoNome}"?`)) {
        showLoading();
        
        fetch('api/ajax_eventos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=generate_qr&evento_id=${eventoId}`
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                carregarEventos();
                showToast('success', 'QR Code gerado!', 'QR Code criado com sucesso');
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

// Função para ver QR Code (usar a função existente do modal QR)
function verQRCode(eventoId, shortCode, publicUrl) {
    // Esta função já existe no arquivo original eventos.php
    // Mantém a implementação do modal QR Code moderno
}

// Função para ver detalhes
function verDetalhes(eventoId) {
    window.location.href = `evento-detalhes.php?id=${eventoId}`;
}

// Função para confirmar exclusão
function confirmarExclusao(eventoId, eventoNome) {
    if (confirm(`Tem certeza que deseja excluir o evento "${eventoNome}"?\n\nEsta ação não pode ser desfeita.`)) {
        showLoading();
        
        fetch('api/ajax_eventos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove_event&evento_id=${eventoId}`
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                carregarEventos();
                showToast('success', 'Excluído!', 'Evento excluído com sucesso');
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
    const container = document.getElementById('eventosContainer');
    container.innerHTML = `
        <div class="col-12">
            <div class="content-card p-4">
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-3"></i>
                    <div>
                        <strong>Erro!</strong> ${mensagem}
                    </div>
                </div>
                <button class="btn btn-outline-primary" onclick="carregarEventos()">
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
