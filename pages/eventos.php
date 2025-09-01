<?php
$current_page = "eventos";
include '../includes/page-layout.php';

// Incluir configuração do banco
include '../config/config.php';

// Configuração do breadcrumb
$breadcrumb = [
    ['label' => 'Eventos']
];

// Ações do header
$actions = [
    [
        'url' => '#', 
        'icon' => 'fas fa-plus', 
        'label' => 'Novo Evento'
    ],
    [
        'url' => '#', 
        'icon' => 'fas fa-qrcode', 
        'label' => 'Gerar QR Codes'
    ]
];

// Renderizar header simplificado
renderHeader("Eventos");
?>

<!-- Container Principal -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-8 mb-8 text-white">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-6 md:mb-0">
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-calendar-alt mr-3"></i>Gestão de Eventos
                </h1>
                <p class="text-lg opacity-90">Crie eventos, gere QR Codes e gerencie inscrições</p>
            </div>
            <div class="text-center">
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                    <i class="fas fa-qrcode text-4xl mb-2"></i>
                    <p class="text-sm opacity-80">QR Code</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loading" class="hidden">
        <div class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <div class="mt-2 text-gray-600">Processando...</div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulário de Cadastro -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">`
                    <div class="card-header bg-success text-white">
                        <h2 class="text-xl font-semibold">Cadastrar Novo Evento</h2>
                    </div>
                    <div class="card-body">
                        <form id="evento-form">
                            <div class="mb-4">
                                <label for="nome_evento" class="form-label">Nome do Evento</label>
                                <input type="text" class="form-control" id="nome_evento" name="nome" required>
                            </div>

                            <div class="mb-4">
                                <label for="data_inicio" class="form-label">Data de Início</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" required>
                            </div>

                            <div class="mb-4">
                                <label for="data_fim" class="form-label">Data de Fim</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim" required>
                            </div>

                            <div class="mb-4">
                                <label for="local_evento" class="form-label">Local</label>
                                <input type="text" class="form-control" id="local_evento" name="local" required>
                            </div>

                            <div class="mb-4">
                                <label for="descricao_evento" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao_evento" name="descricao" rows="3"></textarea>
                            </div>

                            <!-- Seção de Cadastro de Alunos -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="form-label mb-0">Alunos do Evento</label>
                                    <button type="button" class="btn-primary text-sm px-3 py-1" id="add-aluno-btn">
                                        <i class="fas fa-plus mr-1"></i>Adicionar Aluno
                                    </button>
                                </div>
                                <small class="text-gray-600">Cadastre os alunos que participarão deste evento</small>
                            </div>

                            <div id="alunos-container" class="mb-6">
                                <!-- Alunos serão adicionados dinamicamente aqui -->
                            </div>

                            <button type="submit" class="btn-success w-full">Cadastrar Evento e Alunos</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Lista de Eventos -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold">Eventos Cadastrados</h2>
                    </div>
                    <div class="card-body">
                        <div id="eventos-list-container">
                            <!-- Esta seção será carregada via AJAX -->
                            <div class="text-center">
                                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                                <span class="ml-2 text-gray-600">Carregando eventos...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição de Evento -->
<div class="modal-backdrop" id="editarEventoModal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Editar Evento</h3>
                <button type="button" class="modal-close" onclick="closeModal('editarEventoModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="edit-evento-form">
                <div class="modal-body">
                    <input type="hidden" name="evento_id" id="edit_evento_id">

                    <div class="mb-4">
                        <label for="edit_nome" class="form-label">Nome do Evento</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>

                    <div class="mb-4">
                        <label for="edit_data_inicio" class="form-label">Data de Início</label>
                        <input type="date" class="form-control" id="edit_data_inicio" name="data_inicio" required>
                    </div>

                    <div class="mb-4">
                        <label for="edit_data_fim" class="form-label">Data de Fim</label>
                        <input type="date" class="form-control" id="edit_data_fim" name="data_fim" required>
                    </div>

                    <div class="mb-4">
                        <label for="edit_local" class="form-label">Local</label>
                        <input type="text" class="form-control" id="edit_local" name="local" required>
                    </div>

                    <div class="mb-4">
                        <label for="edit_descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="edit_descricao" name="descricao" rows="3"></textarea>
                    </div>

                    <!-- Seção de Cadastro de Alunos na Edição -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="form-label mb-0">Alunos do Evento</label>
                            <button type="button" class="btn-primary text-sm px-3 py-1" id="edit-add-aluno-btn">
                                <i class="fas fa-plus mr-1"></i>Adicionar Aluno
                            </button>
                        </div>
                        <small class="text-gray-600">Cadastre os alunos que participarão deste evento</small>
                    </div>

                    <div id="edit-alunos-container">
                        <!-- Alunos serão adicionados dinamicamente aqui -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('editarEventoModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Remoção -->
<div class="modal-backdrop" id="confirmRemoveEventModal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Confirmar Remoção</h3>
                <button type="button" class="modal-close" onclick="closeModal('confirmRemoveEventModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover o evento <strong id="event-info-remover"></strong>?</p>
                <small class="text-gray-600">Esta ação também removerá todos os ônibus e alocações associados a este evento.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('confirmRemoveEventModal')">Cancelar</button>
                <button type="button" class="btn-danger" id="confirm-remove-event-btn">Remover</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Funções auxiliares para modais TailwindCSS
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Fechar modal clicando no backdrop
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            e.target.style.display = 'none';
        }
    });

    let eventoIdToRemove = null;

    // Carregar dados iniciais
    document.addEventListener('DOMContentLoaded', function() {
        carregarEventos();
        adicionarAluno(); // Adicionar um aluno por padrão
    });

    // Carregar lista de eventos
    function carregarEventos() {
        fetch('../api/ajax_eventos.php?action=get_events')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('eventos-list-container');

                if (data.success) {
                    if (data.events.length > 0) {
                        let html = '<div class="overflow-x-auto">';
                        html += '<table class="min-w-full divide-y divide-gray-200">';
                        html += '<thead class="bg-gray-50">';
                        html += '<tr>';
                        html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>';
                        html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>';
                        html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Local</th>';
                        html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alunos</th>';
                        html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ônibus</th>';
                        html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>';
                        html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>';
                        html += '</tr>';
                        html += '</thead>';
                        html += '<tbody class="bg-white divide-y divide-gray-200">';

                        data.events.forEach(evento => {
                            const dataInicio = new Date(evento.data_inicio).toLocaleDateString('pt-BR');
                            const dataFim = new Date(evento.data_fim).toLocaleDateString('pt-BR');
                            const periodo = dataInicio === dataFim ? dataInicio : `${dataInicio} - ${dataFim}`;

                            html += '<tr class="hover:bg-gray-50">';
                            html += `<td class="px-6 py-4 whitespace-nowrap">`;
                            html += `<div class="text-sm font-medium text-gray-900">${evento.nome}</div>`;
                            if (evento.descricao) {
                                html += `<div class="text-sm text-gray-500">${evento.descricao}</div>`;
                            }
                            html += `</td>`;
                            html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${periodo}</td>`;
                            html += `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${evento.local}</td>`;
                            html += `<td class="px-6 py-4 whitespace-nowrap">`;
                            html += `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">`;
                            html += `${evento.total_alunos || 0} alunos`;
                            html += `</span>`;
                            html += `</td>`;
                            html += `<td class="px-6 py-4 whitespace-nowrap">`;
                            html += `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">`;
                            html += `${evento.total_onibus || 0} ônibus`;
                            html += `</span>`;
                            html += `</td>`;
                            html += `<td class="px-6 py-4 whitespace-nowrap text-sm">`;
                            
                            if (evento.short_code) {
                                html += `<div class="flex space-x-2">`;
                                html += `<button onclick="verQRCode(${evento.id}, '${evento.short_code}', '${evento.public_url}')" class="text-blue-600 hover:text-blue-900">`;
                                html += `<i class="fas fa-qrcode mr-1"></i>Ver`;
                                html += `</button>`;
                                html += `</div>`;
                            } else {
                                html += `<button onclick="gerarQRCode(${evento.id}, '${evento.nome}')" class="text-green-600 hover:text-green-900">`;
                                html += `<i class="fas fa-plus mr-1"></i>Gerar`;
                                html += `</button>`;
                            }
                            
                            html += `</td>`;
                            html += `<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">`;
                            html += `<div class="flex space-x-2">`;
                            html += `<button onclick="editarEvento(${evento.id})" class="text-indigo-600 hover:text-indigo-900">`;
                            html += `<i class="fas fa-edit mr-1"></i>Editar`;
                            html += `</button>`;
                            html += `<button onclick="confirmarRemocao(${evento.id}, '${evento.nome}')" class="text-red-600 hover:text-red-900">`;
                            html += `<i class="fas fa-trash mr-1"></i>Remover`;
                            html += `</button>`;
                            html += `</div>`;
                            html += `</td>`;
                            html += '</tr>';
                        });

                        html += '</tbody>';
                        html += '</table>';
                        html += '</div>';
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<div class="text-center text-gray-500 py-8">Nenhum evento cadastrado ainda.</div>';
                    }
                } else {
                    container.innerHTML = '<div class="text-center text-red-500 py-8">Erro ao carregar eventos.</div>';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('eventos-list-container').innerHTML = '<div class="text-center text-red-500 py-8">Erro ao conectar com o servidor.</div>';
            });
    }

    // Função para mostrar mensagens
    function mostrarMensagem(tipo, mensagem) {
        if (typeof Utils !== 'undefined' && Utils.showToast) {
            Utils.showToast(mensagem, tipo);
        } else {
            alert(mensagem);
        }
    }

    // Função para confirmar remoção
    function confirmarRemocao(eventoId, eventoNome) {
        eventoIdToRemove = eventoId;
        document.getElementById('event-info-remover').textContent = eventoNome;
        openModal('confirmRemoveEventModal');
    }

    // Event listener para confirmar remoção
    document.getElementById('confirm-remove-event-btn').addEventListener('click', function() {
        if (eventoIdToRemove) {
            removerEvento(eventoIdToRemove);
            closeModal('confirmRemoveEventModal');
        }
    });

    // Função para remover evento
    function removerEvento(eventoId) {
        fetch('../api/ajax_eventos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_event&evento_id=${eventoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarMensagem('success', 'Evento removido com sucesso!');
                carregarEventos();
            } else {
                mostrarMensagem('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
        });
    }

    // Função para editar evento
    function editarEvento(eventoId) {
        fetch(`../api/ajax_eventos.php?action=get_event&evento_id=${eventoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const evento = data.event;
                    document.getElementById('edit_evento_id').value = evento.id;
                    document.getElementById('edit_nome').value = evento.nome;
                    document.getElementById('edit_data_inicio').value = evento.data_inicio;
                    document.getElementById('edit_data_fim').value = evento.data_fim;
                    document.getElementById('edit_local').value = evento.local;
                    document.getElementById('edit_descricao').value = evento.descricao || '';
                    
                    carregarAlunosEdicao(eventoId);
                    openModal('editarEventoModal');
                } else {
                    mostrarMensagem('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarMensagem('danger', 'Erro ao carregar dados do evento.');
            });
    }

    // ===== FUNÇÕES PARA GERENCIAMENTO DE ALUNOS =====

    let alunoCounter = 0;

    // Função para adicionar um novo aluno ao formulário
    function adicionarAluno(nome = '', telefone = '', serie = '', curso = '') {
        alunoCounter++;
        const alunoHtml = `
            <div class="aluno-item border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50" data-aluno-id="${alunoCounter}">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-medium text-gray-900">Aluno ${alunoCounter}</h4>
                    <button type="button" class="text-red-600 hover:text-red-800 text-sm" onclick="removerAluno(${alunoCounter})">
                        <i class="fas fa-trash mr-1"></i>Remover
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" name="alunos[${alunoCounter}][nome]" value="${nome}" required>
                    </div>
                    <div>
                        <label class="form-label">Telefone</label>
                        <input type="tel" class="form-control" name="alunos[${alunoCounter}][telefone]" value="${telefone}" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="form-label">Série</label>
                        <select class="form-control" name="alunos[${alunoCounter}][serie]" required>
                            <option value="">Selecione a série</option>
                            <option value="1" ${serie === '1' ? 'selected' : ''}>1ª Série</option>
                            <option value="2" ${serie === '2' ? 'selected' : ''}>2ª Série</option>
                            <option value="3" ${serie === '3' ? 'selected' : ''}>3ª Série</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Curso</label>
                        <select class="form-control" name="alunos[${alunoCounter}][curso]" required>
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
        const alunos = document.querySelectorAll('#alunos-container .aluno-item');
        alunos.forEach((aluno, index) => {
            const numero = index + 1;
            aluno.setAttribute('data-aluno-id', numero);
            aluno.querySelector('h4').textContent = `Aluno ${numero}`;

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

    // ===== FUNÇÕES PARA MODAL DE EDIÇÃO =====

    let editAlunoCounter = 0;

    // Função para adicionar um novo aluno ao formulário de edição
    function adicionarAlunoEdicao(nome = '', telefone = '', serie = '', curso = '') {
        editAlunoCounter++;
        const alunoHtml = `
            <div class="aluno-item border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50" data-aluno-id="${editAlunoCounter}">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-medium text-gray-900">Aluno ${editAlunoCounter}</h4>
                    <button type="button" class="text-red-600 hover:text-red-800 text-sm" onclick="removerAlunoEdicao(${editAlunoCounter})">
                        <i class="fas fa-trash mr-1"></i>Remover
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" name="alunos[${editAlunoCounter}][nome]" value="${nome}" required>
                    </div>
                    <div>
                        <label class="form-label">Telefone</label>
                        <input type="tel" class="form-control" name="alunos[${editAlunoCounter}][telefone]" value="${telefone}" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="form-label">Série</label>
                        <select class="form-control" name="alunos[${editAlunoCounter}][serie]" required>
                            <option value="">Selecione a série</option>
                            <option value="1" ${serie === '1' ? 'selected' : ''}>1ª Série</option>
                            <option value="2" ${serie === '2' ? 'selected' : ''}>2ª Série</option>
                            <option value="3" ${serie === '3' ? 'selected' : ''}>3ª Série</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Curso</label>
                        <select class="form-control" name="alunos[${editAlunoCounter}][curso]" required>
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
            aluno.querySelector('h4').textContent = `Aluno ${numero}`;

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
        fetch(`../api/ajax_eventos.php?action=get_event_students&evento_id=${eventoId}`)
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
                    adicionarAlunoEdicao();
                }
            })
            .catch(error => {
                console.error('Erro ao carregar alunos:', error);
                adicionarAlunoEdicao();
            });
    }

    // ===== FORMULÁRIOS =====

    // Formulário de criação de evento
    document.getElementById('evento-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'create_event');

        document.getElementById('loading').classList.remove('hidden');
        document.getElementById('main-content').classList.add('hidden');

        fetch('../api/ajax_eventos.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('main-content').classList.remove('hidden');

            if (data.success) {
                mostrarMensagem('success', 'Evento criado com sucesso!');
                this.reset();
                document.getElementById('alunos-container').innerHTML = '';
                alunoCounter = 0;
                adicionarAluno();
                carregarEventos();
            } else {
                mostrarMensagem('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('main-content').classList.remove('hidden');
            mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
        });
    });

    // Formulário de edição de evento
    document.getElementById('edit-evento-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'update_event');

        fetch('../api/ajax_eventos.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarMensagem('success', 'Evento atualizado com sucesso!');
                closeModal('editarEventoModal');
                carregarEventos();
            } else {
                mostrarMensagem('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
        });
    });

    // ===== FUNÇÕES PARA GERENCIAMENTO DE QR CODE =====

    function gerarQRCode(eventoId, eventoNome) {
        if (confirm(`Deseja gerar um QR Code para o evento "${eventoNome}"?\n\nIsso permitirá que alunos se candidatem ao evento através de um link público.`)) {
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('main-content').classList.add('hidden');

            fetch('../api/qr_manager.php?action=generate_qr&evento_id=' + eventoId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loading').classList.add('hidden');
                    document.getElementById('main-content').classList.remove('hidden');

                    if (data.success) {
                        mostrarMensagem('success', 'QR Code gerado com sucesso! Link público: ' + data.public_url);
                        carregarEventos();
                    } else {
                        mostrarMensagem('danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('loading').classList.add('hidden');
                    document.getElementById('main-content').classList.remove('hidden');
                    mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
                });
        }
    }

    function verQRCode(eventoId, shortCode, publicUrl) {
        // Criar modal para mostrar QR Code
        const modalHtml = `
            <div class="modal-backdrop" id="qrCodeModal" style="display: flex;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="text-lg font-semibold">QR Code do Evento</h3>
                            <button type="button" class="modal-close" onclick="closeModal('qrCodeModal')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-4">
                                <img src="../api/qr_manager.php?action=generate_qr&evento_id=${eventoId}" alt="QR Code" class="mx-auto max-w-xs rounded-lg shadow-lg">
                            </div>
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Link Público:</h4>
                                <input type="text" class="form-control" value="${publicUrl}" readonly>
                            </div>
                            <div class="flex justify-center space-x-3">
                                <button type="button" class="btn-primary" onclick="copiarLink(${eventoId}, '${publicUrl}')">
                                    <i class="fas fa-copy mr-1"></i>Copiar Link
                                </button>
                                <button type="button" class="btn-success" onclick="baixarQRCode(${eventoId})">
                                    <i class="fas fa-download mr-1"></i>Baixar QR Code
                                </button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" onclick="closeModal('qrCodeModal')">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remover modal anterior se existir
        const existingModal = document.getElementById('qrCodeModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Adicionar novo modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    function copiarLink(eventoId, publicUrl) {
        navigator.clipboard.writeText(publicUrl).then(function() {
            mostrarMensagem('success', 'Link copiado para a área de transferência!');
        }).catch(function(err) {
            // Fallback para navegadores antigos
            const textArea = document.createElement('textarea');
            textArea.value = publicUrl;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            mostrarMensagem('success', 'Link copiado para a área de transferência!');
        });
    }

    function baixarQRCode(eventoId) {
        const link = document.createElement('a');
        link.href = '../api/qr_manager.php?action=generate_qr&evento_id=' + eventoId;
        link.download = 'qr_code_evento_' + eventoId + '.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<?php renderFooter(); ?>
