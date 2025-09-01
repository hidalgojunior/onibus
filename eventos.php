<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Estilos modernos customizados -->
    <style>
        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(102, 126, 234, 0); }
            100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
        }
        
        .btn-modern {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
        }
        
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-modern:hover::before {
            left: 100%;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .qr-code-container:hover {
            animation: pulseGlow 2s infinite;
        }
        
        .floating-badge {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .tooltip-modern {
            --bs-tooltip-bg: rgba(0, 0, 0, 0.8);
            --bs-tooltip-border-radius: 0.5rem;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h1 class="card-title mb-0">Gerenciar Eventos</h1>
                        <small>Cadastrar e gerenciar eventos do sistema</small>
                    </div>
                    <div class="card-body">
                        <div id="loading" class="d-none">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <div class="mt-2">Processando...</div>
                            </div>
                        </div>

                        <div id="main-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5>Cadastrar Novo Evento</h5>
                                    <form id="evento-form">
                                        <div class="mb-3">
                                            <label for="nome_evento" class="form-label">Nome do Evento</label>
                                            <input type="text" class="form-control" id="nome_evento" name="nome" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="data_inicio" class="form-label">Data de Início</label>
                                            <input type="date" class="form-control" id="data_inicio" name="data_inicio" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="data_fim" class="form-label">Data de Fim</label>
                                            <input type="date" class="form-control" id="data_fim" name="data_fim" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="local_evento" class="form-label">Local</label>
                                            <input type="text" class="form-control" id="local_evento" name="local" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="descricao_evento" class="form-label">Descrição</label>
                                            <textarea class="form-control" id="descricao_evento" name="descricao" rows="3"></textarea>
                                        </div>

                                        <!-- Seção de Cadastro de Alunos -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="form-label mb-0">Alunos do Evento</label>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="add-aluno-btn">
                                                    <i class="fas fa-plus me-1"></i>Adicionar Aluno
                                                </button>
                                            </div>
                                            <small class="text-muted">Cadastre os alunos que participarão deste evento</small>
                                        </div>

                                        <div id="alunos-container">
                                            <!-- Alunos serão adicionados dinamicamente aqui -->
                                        </div>

                                        <button type="submit" class="btn btn-success">Cadastrar Evento e Alunos</button>
                                    </form>
                                </div>

                                <div class="col-md-8">
                                    <h5>Eventos Cadastrados</h5>
                                    <div id="eventos-list-container">
                                        <!-- Esta seção será carregada via AJAX -->
                                        <div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Carregando eventos...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de Edição de Evento -->
                        <div class="modal fade" id="editarEventoModal" tabindex="-1" aria-labelledby="editarEventoModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editarEventoModalLabel">Editar Evento</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form id="edit-evento-form">
                                        <div class="modal-body">
                                            <input type="hidden" name="evento_id" id="edit_evento_id">

                                            <div class="mb-3">
                                                <label for="edit_nome" class="form-label">Nome do Evento</label>
                                                <input type="text" class="form-control" id="edit_nome" name="nome" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="edit_data_inicio" class="form-label">Data de Início</label>
                                                <input type="date" class="form-control" id="edit_data_inicio" name="data_inicio" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="edit_data_fim" class="form-label">Data de Fim</label>
                                                <input type="date" class="form-control" id="edit_data_fim" name="data_fim" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="edit_local" class="form-label">Local</label>
                                                <input type="text" class="form-control" id="edit_local" name="local" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="edit_descricao" class="form-label">Descrição</label>
                                                <textarea class="form-control" id="edit_descricao" name="descricao" rows="3"></textarea>
                                            </div>

                                            <!-- Seção de Cadastro de Alunos na Edição -->
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <label class="form-label mb-0">Alunos do Evento</label>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="edit-add-aluno-btn">
                                                        <i class="fas fa-plus me-1"></i>Adicionar Aluno
                                                    </button>
                                                </div>
                                                <small class="text-muted">Cadastre os alunos que participarão deste evento</small>
                                            </div>

                                            <div id="edit-alunos-container">
                                                <!-- Alunos serão adicionados dinamicamente aqui -->
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de Confirmação de Remoção -->
                        <div class="modal fade" id="confirmRemoveEventModal" tabindex="-1" aria-labelledby="confirmRemoveEventModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmRemoveEventModalLabel">Confirmar Remoção</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Tem certeza que deseja remover o evento <strong id="event-info-remover"></strong>?</p>
                                        <small class="text-muted">Esta ação também removerá todos os ônibus e alocações associados a este evento.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-danger" id="confirm-remove-event-btn">Remover</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let eventoIdToRemove = null;

        // Carregar dados iniciais
        document.addEventListener('DOMContentLoaded', function() {
            carregarEventos();
        });

        // Carregar lista de eventos
        function carregarEventos() {
            fetch('api/ajax_eventos.php?action=get_events')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('eventos-list-container');

                    if (data.success) {
                        if (data.events.length > 0) {
                            let html = '<div class="table-responsive">';
                            html += '<table class="table table-striped">';
                            html += '<thead><tr><th>Nome</th><th>Período</th><th>Local</th><th>Alunos</th><th>Ônibus</th><th>QR Code</th><th>Ações</th></tr></thead>';
                            html += '<tbody>';

                            data.events.forEach(event => {
                                const dataInicio = new Date(event.data_inicio).toLocaleDateString('pt-BR');
                                const dataFim = new Date(event.data_fim).toLocaleDateString('pt-BR');
                                const periodo = dataInicio + ' - ' + dataFim;

                                html += '<tr>';
                                html += '<td>' + event.nome + '</td>';
                                html += '<td>' + periodo + '</td>';
                                html += '<td>' + event.local + '</td>';
                                html += '<td><span class="badge bg-primary">' + event.total_alunos + '</span></td>';
                                html += '<td><span class="badge bg-info">' + event.total_onibus + '</span></td>';
                                html += '<td>';
                                if (event.short_code) {
                                    html += '<button type="button" class="btn btn-sm btn-outline-success me-1" onclick="verQRCode(' + event.id + ', \'' + event.short_code + '\', \'' + event.public_url + '\')" title="Ver QR Code">';
                                    html += '<i class="fas fa-qrcode"></i>';
                                    html += '</button>';
                                    html += '<button type="button" class="btn btn-sm btn-outline-info me-1" onclick="copiarLink(' + event.id + ', \'' + event.public_url + '\')" title="Copiar Link">';
                                    html += '<i class="fas fa-link"></i>';
                                    html += '</button>';
                                } else {
                                    html += '<button type="button" class="btn btn-sm btn-outline-warning" onclick="gerarQRCode(' + event.id + ', \'' + event.nome + '\')" title="Gerar QR Code">';
                                    html += '<i class="fas fa-plus"></i> Gerar';
                                    html += '</button>';
                                }
                                html += '</td>';
                                html += '<td>';
                                html += '<button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="editarEvento(' + event.id + ')">';
                                html += '<i class="fas fa-edit"></i>';
                                html += '</button>';
                                html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmarRemocaoEvento(' + event.id + ', \'' + event.nome + '\')">';
                                html += '<i class="fas fa-trash"></i>';
                                html += '</button>';
                                html += '</td>';
                                html += '</tr>';
                            });

                            html += '</tbody></table></div>';
                            container.innerHTML = html;
                        } else {
                            container.innerHTML = '<div class="alert alert-info">Nenhum evento cadastrado.</div>';
                        }
                    } else {
                        container.innerHTML = '<div class="alert alert-danger">Erro ao carregar eventos: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('eventos-list-container').innerHTML = '<div class="alert alert-danger">Erro ao conectar com o servidor.</div>';
                });
        }

        // Manipular formulário de cadastro
        document.getElementById('evento-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Mostrar loading
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('main-content').classList.add('d-none');

            fetch('api/ajax_eventos.php?action=create_event', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Esconder loading
                document.getElementById('loading').classList.add('d-none');
                document.getElementById('main-content').classList.remove('d-none');

                if (data.success) {
                    mostrarMensagem('success', data.message);
                    carregarEventos();
                    // Limpar formulário
                    this.reset();
                    // Limpar alunos e adicionar um novo vazio
                    document.getElementById('alunos-container').innerHTML = '';
                    alunoCounter = 0;
                    adicionarAluno();
                } else {
                    mostrarMensagem('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('loading').classList.add('d-none');
                document.getElementById('main-content').classList.remove('d-none');
                mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
            });
        });

        // Editar evento
        function editarEvento(id) {
            // Buscar dados do evento via AJAX
            fetch(`api/ajax_eventos.php?action=get_event&evento_id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const event = data.event;
                        
                        document.getElementById('edit_evento_id').value = event.id;
                        document.getElementById('edit_nome').value = event.nome;
                        document.getElementById('edit_data_inicio').value = event.data_inicio;
                        document.getElementById('edit_data_fim').value = event.data_fim;
                        document.getElementById('edit_local').value = event.local;
                        document.getElementById('edit_descricao').value = event.descricao || '';

                        // Carregar alunos do evento
                        carregarAlunosEdicao(id);

                        const modal = new bootstrap.Modal(document.getElementById('editarEventoModal'));
                        modal.show();
                    } else {
                        alert('Erro ao carregar dados do evento: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao conectar com o servidor');
                });
        }

        // Manipular formulário de edição
        document.getElementById('edit-evento-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editarEventoModal'));
            modal.hide();

            // Mostrar loading
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('main-content').classList.add('d-none');

            fetch('api/ajax_eventos.php?action=update_event', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Esconder loading
                document.getElementById('loading').classList.add('d-none');
                document.getElementById('main-content').classList.remove('d-none');

                if (data.success) {
                    mostrarMensagem('success', data.message);
                    carregarEventos();
                } else {
                    mostrarMensagem('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('loading').classList.add('d-none');
                document.getElementById('main-content').classList.remove('d-none');
                mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
            });
        });

        // Confirmar remoção
        function confirmarRemocaoEvento(eventoId, eventInfo) {
            eventoIdToRemove = eventoId;
            document.getElementById('event-info-remover').textContent = eventInfo;

            const modal = new bootstrap.Modal(document.getElementById('confirmRemoveEventModal'));
            modal.show();
        }

        // Confirmar remoção no modal
        document.getElementById('confirm-remove-event-btn').addEventListener('click', function() {
            if (eventoIdToRemove) {
                // Fechar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmRemoveEventModal'));
                modal.hide();

                // Mostrar loading
                document.getElementById('loading').classList.remove('d-none');
                document.getElementById('main-content').classList.add('d-none');

                const formData = new FormData();
                formData.append('evento_id', eventoIdToRemove);

                fetch('api/ajax_eventos.php?action=remove_event', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Esconder loading
                    document.getElementById('loading').classList.add('d-none');
                    document.getElementById('main-content').classList.remove('d-none');

                    if (data.success) {
                        mostrarMensagem('success', data.message);
                        carregarEventos();
                    } else {
                        mostrarMensagem('danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('loading').classList.add('d-none');
                    document.getElementById('main-content').classList.remove('d-none');
                    mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
                });
            }
        });

        // Função para mostrar mensagens
        function mostrarMensagem(tipo, mensagem) {
            const alertContainer = document.createElement('div');
            alertContainer.className = `alert alert-${tipo} alert-dismissible fade show`;
            alertContainer.innerHTML = `
                ${mensagem}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alertContainer, cardBody.firstChild);

            setTimeout(() => {
                if (alertContainer && alertContainer.parentNode) {
                    alertContainer.remove();
                }
            }, 5000);
        }

        // ===== FUNÇÕES PARA CADASTRO DE ALUNOS =====

        let alunoCounter = 0;

        // Função para adicionar um novo aluno ao formulário
        function adicionarAluno(nome = '', telefone = '', serie = '', curso = '') {
            alunoCounter++;
            const alunoHtml = `
                <div class="aluno-item border rounded p-3 mb-3 bg-light" data-aluno-id="${alunoCounter}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Aluno ${alunoCounter}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerAluno(${alunoCounter})">
                            <i class="fas fa-trash me-1"></i>Remover
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" name="alunos[${alunoCounter}][nome]" value="${nome}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="tel" class="form-control" name="alunos[${alunoCounter}][telefone]" value="${telefone}" required>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Série</label>
                            <select class="form-select" name="alunos[${alunoCounter}][serie]" required>
                                <option value="">Selecione a série</option>
                                <option value="1" ${serie === '1' ? 'selected' : ''}>1ª Série</option>
                                <option value="2" ${serie === '2' ? 'selected' : ''}>2ª Série</option>
                                <option value="3" ${serie === '3' ? 'selected' : ''}>3ª Série</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Curso</label>
                            <select class="form-select" name="alunos[${alunoCounter}][curso]" required>
                                <option value="">Selecione o curso</option>
                                <option value="MTEC PI Desenvolvimento de Sistemas" ${curso === 'MTEC PI Desenvolvimento de Sistemas' ? 'selected' : ''}>MTEC PI Desenvolvimento de Sistemas</option>
                                <option value="Administração" ${curso === 'Administração' ? 'selected' : ''}>Administração</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('alunos-container').insertAdjacentHTML('beforeend', alunoHtml);
        }

        // Função para remover um aluno do formulário
        function removerAluno(alunoId) {
            const alunoElement = document.querySelector(`[data-aluno-id="${alunoId}"]`);
            if (alunoElement) {
                alunoElement.remove();
                renumerarAlunos();
            }
        }

        // Função para renumerar os alunos após remoção
        function renumerarAlunos() {
            const alunos = document.querySelectorAll('.aluno-item');
            alunos.forEach((aluno, index) => {
                const numero = index + 1;
                aluno.setAttribute('data-aluno-id', numero);
                aluno.querySelector('h6').textContent = `Aluno ${numero}`;

                // Atualizar names dos inputs
                const inputs = aluno.querySelectorAll('input, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, `[${numero}]`);
                        input.setAttribute('name', newName);
                    }
                });

                // Atualizar botão de remover
                const btnRemover = aluno.querySelector('button[onclick*="removerAluno"]');
                if (btnRemover) {
                    btnRemover.setAttribute('onclick', `removerAluno(${numero})`);
                }
            });
            alunoCounter = alunos.length;
        }

        // Event listener para o botão de adicionar aluno
        document.getElementById('add-aluno-btn').addEventListener('click', function() {
            adicionarAluno();
        });

        // Adicionar um aluno por padrão quando a página carrega
        document.addEventListener('DOMContentLoaded', function() {
            adicionarAluno();
        });

        // ===== FUNÇÕES PARA MODAL DE EDIÇÃO =====

        let editAlunoCounter = 0;

        // Função para adicionar um novo aluno ao formulário de edição
        function adicionarAlunoEdicao(nome = '', telefone = '', serie = '', curso = '') {
            editAlunoCounter++;
            const alunoHtml = `
                <div class="aluno-item border rounded p-3 mb-3 bg-light" data-aluno-id="${editAlunoCounter}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Aluno ${editAlunoCounter}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerAlunoEdicao(${editAlunoCounter})">
                            <i class="fas fa-trash me-1"></i>Remover
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" name="alunos[${editAlunoCounter}][nome]" value="${nome}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="tel" class="form-control" name="alunos[${editAlunoCounter}][telefone]" value="${telefone}" required>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Série</label>
                            <select class="form-select" name="alunos[${editAlunoCounter}][serie]" required>
                                <option value="">Selecione a série</option>
                                <option value="1" ${serie === '1' ? 'selected' : ''}>1ª Série</option>
                                <option value="2" ${serie === '2' ? 'selected' : ''}>2ª Série</option>
                                <option value="3" ${serie === '3' ? 'selected' : ''}>3ª Série</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Curso</label>
                            <select class="form-select" name="alunos[${editAlunoCounter}][curso]" required>
                                <option value="">Selecione o curso</option>
                                <option value="MTEC PI Desenvolvimento de Sistemas" ${curso === 'MTEC PI Desenvolvimento de Sistemas' ? 'selected' : ''}>MTEC PI Desenvolvimento de Sistemas</option>
                                <option value="Administração" ${curso === 'Administração' ? 'selected' : ''}>Administração</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('edit-alunos-container').insertAdjacentHTML('beforeend', alunoHtml);
        }

        // Função para remover um aluno do formulário de edição
        function removerAlunoEdicao(alunoId) {
            const alunoElement = document.querySelector(`#edit-alunos-container [data-aluno-id="${alunoId}"]`);
            if (alunoElement) {
                alunoElement.remove();
                renumerarAlunosEdicao();
            }
        }

        // Função para renumerar os alunos após remoção na edição
        function renumerarAlunosEdicao() {
            const alunos = document.querySelectorAll('#edit-alunos-container .aluno-item');
            alunos.forEach((aluno, index) => {
                const numero = index + 1;
                aluno.setAttribute('data-aluno-id', numero);
                aluno.querySelector('h6').textContent = `Aluno ${numero}`;

                // Atualizar names dos inputs
                const inputs = aluno.querySelectorAll('input, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, `[${numero}]`);
                        input.setAttribute('name', newName);
                    }
                });

                // Atualizar botão de remover
                const btnRemover = aluno.querySelector('button[onclick*="removerAlunoEdicao"]');
                if (btnRemover) {
                    btnRemover.setAttribute('onclick', `removerAlunoEdicao(${numero})`);
                }
            });
            editAlunoCounter = alunos.length;
        }

        // Event listener para o botão de adicionar aluno na edição
        document.getElementById('edit-add-aluno-btn').addEventListener('click', function() {
            adicionarAlunoEdicao();
        });

        // Função para carregar alunos do evento na edição
        function carregarAlunosEdicao(eventoId) {
            fetch(`api/ajax_eventos.php?action=get_event_students&evento_id=${eventoId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('edit-alunos-container');
                    container.innerHTML = '';
                    editAlunoCounter = 0;

                    if (data.success && data.students.length > 0) {
                        data.students.forEach(student => {
                            adicionarAlunoEdicao(student.nome, student.telefone, student.serie, student.curso);
                        });
                    } else {
                        // Adicionar um aluno vazio se não houver alunos
                        adicionarAlunoEdicao();
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar alunos:', error);
                    adicionarAlunoEdicao(); // Adicionar um aluno vazio em caso de erro
                });
        }

        // ===== FUNÇÕES PARA GERENCIAMENTO DE QR CODE =====

        function gerarQRCode(eventoId, eventoNome) {
            if (confirm('Deseja gerar um QR Code para o evento "' + eventoNome + '"?\n\nIsso permitirá que alunos se candidatem ao evento através de um link público.')) {
                // Mostrar loading
                document.getElementById('loading').classList.remove('d-none');
                document.getElementById('main-content').classList.add('d-none');

                fetch('api/ajax_eventos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=generate_qr&evento_id=' + eventoId
                })
                    .then(response => response.json())
                    .then(data => {
                        // Esconder loading
                        document.getElementById('loading').classList.add('d-none');
                        document.getElementById('main-content').classList.remove('d-none');

                        if (data.success) {
                            mostrarMensagem('success', 'QR Code gerado com sucesso! Link público: ' + data.public_url);
                            carregarEventos(); // Recarregar lista
                        } else {
                            mostrarMensagem('danger', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        document.getElementById('loading').classList.add('d-none');
                        document.getElementById('main-content').classList.remove('d-none');
                        mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
                    });
            }
        }

        function verQRCode(eventoId, shortCode, publicUrl) {
            console.log('verQRCode chamado:', {eventoId, shortCode, publicUrl});
            
            // Modal Bootstrap com design moderno e tendências atuais
            const modalHtml = `
                <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true" data-bs-backdrop="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg" style="
                            background: rgba(255, 255, 255, 0.95);
                            backdrop-filter: blur(20px);
                            border-radius: 20px;
                            overflow: hidden;
                        ">
                            <!-- Header com gradiente e glassmorphism -->
                            <div class="modal-header border-0 position-relative" style="
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                padding: 1.5rem 2rem;
                            ">
                                <div class="d-flex align-items-center">
                                    <div class="me-3" style="
                                        width: 50px;
                                        height: 50px;
                                        background: rgba(255, 255, 255, 0.2);
                                        border-radius: 15px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        backdrop-filter: blur(10px);
                                    ">
                                        <i class="fas fa-qrcode text-white" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="modal-title text-white mb-0 fw-bold" id="qrCodeModalLabel">QR Code do Evento</h4>
                                        <small class="text-white-50">Compartilhe com facilidade</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="
                                    background: rgba(255, 255, 255, 0.2);
                                    border-radius: 50%;
                                    padding: 0.5rem;
                                    backdrop-filter: blur(10px);
                                "></button>
                            </div>

                            <!-- Body com layout moderno -->
                            <div class="modal-body p-4">
                                <div class="row g-4">
                                    <!-- QR Code Section -->
                                    <div class="col-md-6">
                                        <div class="text-center">
                                            <div class="position-relative d-inline-block">
                                                <!-- QR Code Container com efeito glassmorphism -->
                                                <div style="
                                                    background: rgba(255, 255, 255, 0.8);
                                                    backdrop-filter: blur(15px);
                                                    border: 2px solid rgba(255, 255, 255, 0.3);
                                                    border-radius: 20px;
                                                    padding: 1.5rem;
                                                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                                                ">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(publicUrl)}&margin=10&color=333333&bgcolor=FFFFFF" 
                                                         alt="QR Code" 
                                                         class="img-fluid rounded-3"
                                                         style="width: 220px; height: 220px; transition: transform 0.3s ease;"
                                                         onmouseover="this.style.transform='scale(1.05)'"
                                                         onmouseout="this.style.transform='scale(1)'"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    
                                                    <!-- Fallback QR Code -->
                                                    <div style="display: none; width: 220px; height: 220px; background: linear-gradient(45deg, #f8f9fa, #e9ecef); border: 2px dashed #dee2e6; border-radius: 15px;" class="d-flex align-items-center justify-content-center flex-column">
                                                        <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                                                        <span class="text-muted small">QR Code indisponível</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Badge flutuante com código -->
                                                <div class="position-absolute top-0 start-50 translate-middle">
                                                    <span class="badge rounded-pill" style="
                                                        background: linear-gradient(135deg, #667eea, #764ba2);
                                                        padding: 0.5rem 1rem;
                                                        font-size: 0.8rem;
                                                        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                                                    ">${shortCode}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info Section -->
                                    <div class="col-md-6">
                                        <div class="h-100 d-flex flex-column">
                                            <!-- Link Section -->
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold text-dark mb-2">
                                                    <i class="fas fa-link me-2 text-primary"></i>Link Público
                                                </label>
                                                <div class="input-group input-group-lg">
                                                    <input type="text" 
                                                           class="form-control border-0 bg-light" 
                                                           value="${publicUrl}" 
                                                           readonly 
                                                           id="linkInput${eventoId}"
                                                           style="
                                                               background: rgba(248, 249, 250, 0.8) !important;
                                                               backdrop-filter: blur(10px);
                                                               border-radius: 15px 0 0 15px !important;
                                                               font-size: 0.9rem;
                                                           ">
                                                    <button class="btn btn-success border-0" 
                                                            type="button" 
                                                            onclick="copiarLinkModerno(${eventoId}, '${publicUrl}', this)"
                                                            style="
                                                                background: linear-gradient(135deg, #28a745, #20c997);
                                                                border-radius: 0 15px 15px 0 !important;
                                                                padding: 0.75rem 1.25rem;
                                                                transition: all 0.3s ease;
                                                            "
                                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(40, 167, 69, 0.4)'"
                                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fas fa-copy me-2"></i>Copiar
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Instructions Card -->
                                            <div class="flex-grow-1">
                                                <div class="card border-0 h-100" style="
                                                    background: linear-gradient(135deg, rgba(13, 202, 240, 0.1), rgba(13, 110, 253, 0.1));
                                                    backdrop-filter: blur(10px);
                                                    border-radius: 15px;
                                                ">
                                                    <div class="card-body p-3">
                                                        <h6 class="card-title text-primary mb-3">
                                                            <i class="fas fa-lightbulb me-2"></i>Como usar
                                                        </h6>
                                                        <div class="d-flex flex-column gap-2">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3" style="
                                                                    width: 30px;
                                                                    height: 30px;
                                                                    background: linear-gradient(135deg, #667eea, #764ba2);
                                                                    border-radius: 50%;
                                                                    display: flex;
                                                                    align-items: center;
                                                                    justify-content: center;
                                                                ">
                                                                    <span class="text-white fw-bold small">1</span>
                                                                </div>
                                                                <small class="text-dark">Compartilhe o QR Code ou link</small>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3" style="
                                                                    width: 30px;
                                                                    height: 30px;
                                                                    background: linear-gradient(135deg, #667eea, #764ba2);
                                                                    border-radius: 50%;
                                                                    display: flex;
                                                                    align-items: center;
                                                                    justify-content: center;
                                                                ">
                                                                    <span class="text-white fw-bold small">2</span>
                                                                </div>
                                                                <small class="text-dark">Alunos escaneiam para se inscrever</small>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3" style="
                                                                    width: 30px;
                                                                    height: 30px;
                                                                    background: linear-gradient(135deg, #667eea, #764ba2);
                                                                    border-radius: 50%;
                                                                    display: flex;
                                                                    align-items: center;
                                                                    justify-content: center;
                                                                ">
                                                                    <span class="text-white fw-bold small">3</span>
                                                                </div>
                                                                <small class="text-dark">Acesso direto ao formulário</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer com botões modernos -->
                            <div class="modal-footer border-0 p-4 pt-0">
                                <div class="d-flex gap-2 w-100 justify-content-end">
                                    <button type="button" 
                                            class="btn btn-light border-0 px-4 py-2" 
                                            data-bs-dismiss="modal"
                                            style="
                                                background: rgba(248, 249, 250, 0.8);
                                                backdrop-filter: blur(10px);
                                                border-radius: 12px;
                                                transition: all 0.3s ease;
                                            "
                                            onmouseover="this.style.transform='translateY(-2px)'"
                                            onmouseout="this.style.transform='translateY(0)'">
                                        <i class="fas fa-times me-2"></i>Fechar
                                    </button>
                                    <button type="button" 
                                            class="btn btn-primary border-0 px-4 py-2"
                                            onclick="baixarQRCodeModerno(${eventoId}, '${shortCode}')"
                                            style="
                                                background: linear-gradient(135deg, #667eea, #764ba2);
                                                border-radius: 12px;
                                                transition: all 0.3s ease;
                                            "
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.4)'"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                        <i class="fas fa-download me-2"></i>Baixar QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remover modal anterior se existir
            const existingModal = document.getElementById('qrCodeModal');
            if (existingModal) {
                const bootstrapModal = bootstrap.Modal.getInstance(existingModal);
                if (bootstrapModal) {
                    bootstrapModal.dispose();
                }
                existingModal.remove();
            }

            // Adicionar novo modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Inicializar e mostrar modal Bootstrap
            const modalElement = document.getElementById('qrCodeModal');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
            
            modal.show();
            
            // Adicionar animação de entrada
            modalElement.addEventListener('shown.bs.modal', function() {
                modalElement.querySelector('.modal-content').style.animation = 'modalSlideIn 0.3s ease-out';
            });
        }

        // Função moderna para copiar link com feedback visual
        function copiarLinkModerno(eventoId, publicUrl, buttonElement) {
            // Selecionar o input
            const input = document.getElementById(`linkInput${eventoId}`);
            if (input) {
                input.select();
                input.setSelectionRange(0, 99999);
            }
            
            // Animação do botão
            const originalHTML = buttonElement.innerHTML;
            const originalStyle = buttonElement.style.background;
            
            navigator.clipboard.writeText(publicUrl).then(function() {
                // Feedback visual de sucesso
                buttonElement.innerHTML = '<i class="fas fa-check me-2"></i>Copiado!';
                buttonElement.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
                buttonElement.style.transform = 'scale(1.05)';
                
                // Toast notification
                mostrarToastModerno('success', 'Link copiado!', 'Link público copiado para a área de transferência');
                
                setTimeout(() => {
                    buttonElement.innerHTML = originalHTML;
                    buttonElement.style.background = originalStyle;
                    buttonElement.style.transform = 'scale(1)';
                }, 2000);
                
            }).catch(function(err) {
                console.error('Erro ao copiar:', err);
                // Fallback
                try {
                    const textArea = document.createElement('textarea');
                    textArea.value = publicUrl;
                    textArea.style.position = 'fixed';
                    textArea.style.opacity = '0';
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    buttonElement.innerHTML = '<i class="fas fa-check me-2"></i>Copiado!';
                    mostrarToastModerno('success', 'Link copiado!', 'Link copiado para a área de transferência');
                    
                    setTimeout(() => {
                        buttonElement.innerHTML = originalHTML;
                    }, 2000);
                    
                } catch (fallbackErr) {
                    buttonElement.innerHTML = '<i class="fas fa-exclamation me-2"></i>Erro';
                    buttonElement.style.background = 'linear-gradient(135deg, #dc3545, #c82333)';
                    mostrarToastModerno('error', 'Erro ao copiar', 'Selecione e copie o link manualmente');
                    
                    setTimeout(() => {
                        buttonElement.innerHTML = originalHTML;
                        buttonElement.style.background = originalStyle;
                    }, 2000);
                }
            });
        }

        // Função para baixar QR Code moderno
        function baixarQRCodeModerno(eventoId, shortCode) {
            const button = event.target;
            const originalHTML = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Baixando...';
            button.disabled = true;
            
            // Simular download
            setTimeout(() => {
                const link = document.createElement('a');
                link.href = `https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=${encodeURIComponent(document.getElementById('linkInput' + eventoId).value)}&format=png&margin=20`;
                link.download = `qr_code_evento_${shortCode}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                button.innerHTML = '<i class="fas fa-check me-2"></i>Baixado!';
                mostrarToastModerno('success', 'QR Code baixado!', 'Arquivo salvo na pasta de downloads');
                
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                }, 2000);
            }, 1000);
        }

        // Sistema de toast moderno
        function mostrarToastModerno(tipo, titulo, mensagem) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            const toastId = 'toast_' + Date.now();
            
            const iconMap = {
                'success': 'fas fa-check-circle text-success',
                'error': 'fas fa-exclamation-circle text-danger',
                'warning': 'fas fa-exclamation-triangle text-warning',
                'info': 'fas fa-info-circle text-info'
            };
            
            const toastHTML = `
                <div id="${toastId}" class="toast align-items-center border-0 shadow-lg" role="alert" style="
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(20px);
                    border-radius: 12px;
                    margin-bottom: 0.5rem;
                ">
                    <div class="d-flex">
                        <div class="toast-body d-flex align-items-center">
                            <i class="${iconMap[tipo]} me-3" style="font-size: 1.2rem;"></i>
                            <div>
                                <div class="fw-semibold text-dark">${titulo}</div>
                                <small class="text-muted">${mensagem}</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }

        // Função legada para compatibilidade (mantida para outros usos)
        function copiarLink(eventoId, publicUrl) {
            navigator.clipboard.writeText(publicUrl).then(function() {
                mostrarMensagem('success', 'Link copiado para a área de transferência!');
            }).catch(function(err) {
                console.error('Erro ao copiar:', err);
                try {
                    const textArea = document.createElement('textarea');
                    textArea.value = publicUrl;
                    textArea.style.position = 'fixed';
                    textArea.style.opacity = '0';
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    mostrarMensagem('success', 'Link copiado para a área de transferência!');
                } catch (fallbackErr) {
                    mostrarMensagem('warning', 'Não foi possível copiar automaticamente. Selecione e copie manualmente.');
                }
            });
        }

        function baixarQRCode(eventoId) {
            // Criar link temporário para download
            const link = document.createElement('a');
            link.href = 'qr_manager.php?action=generate_qr&evento_id=' + eventoId;
            link.download = 'qr_code_evento_' + eventoId + '.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
