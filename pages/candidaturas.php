<?php
$current_page = "candidaturas";
include '../includes/page-layout.php';

// Incluir configuração do banco
include '../config/config.php';

// Configuração do breadcrumb
$breadcrumb = [
    ['label' => 'Candidaturas']
];

// Ações do header
$actions = [
    [
        'url' => '#', 
        'icon' => 'fas fa-plus', 
        'label' => 'Adicionar Teste'
    ],
    [
        'url' => 'eventos.php', 
        'icon' => 'fas fa-calendar', 
        'label' => 'Ver Eventos'
    ]
];

// Renderizar header simplificado
renderHeader("Candidaturas");
?>

<!-- Container Principal -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-xl p-8 mb-8 text-white">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-6 md:mb-0">
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-user-graduate mr-3"></i>Candidaturas
                </h1>
                <p class="text-lg opacity-90">Gerencie inscrições e candidatos dos eventos</p>
            </div>
            <div class="text-center">
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                    <i class="fas fa-clipboard-list text-4xl mb-2"></i>
                    <p class="text-sm opacity-80">Real Time</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loading" class="hidden text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
        <span class="ml-2 text-gray-600">Carregando candidaturas...</span>
    </div>

    <!-- Main Content -->
    <div id="main-content">
        <div class="card">
            <div class="card-header bg-primary">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-users mr-2 text-amarelo-onibus"></i>
                        <h2 class="text-xl font-semibold text-white">Candidaturas de Eventos</h2>
                    </div>
                    <button type="button" class="btn-warning text-sm" onclick="adicionarCandidaturaTeste()">
                        <i class="fas fa-plus mr-1"></i>Adicionar Teste
                    </button>
                </div>
            </div>
            <div class="card-body bg-cinza-claro">
                <div id="candidaturas-container">
                    <!-- As candidaturas serão carregadas aqui -->
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard-list text-6xl text-cinza-medio mb-4"></i>
                        <p class="text-cinza-medio">Carregando candidaturas...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Avaliação de Candidatura -->
<div class="modal-backdrop" id="avaliarCandidaturaModal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-lg font-semibold text-white">Avaliar Candidatura</h3>
                <button type="button" class="modal-close" onclick="closeModal('avaliarCandidaturaModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="avaliar-candidatura-form">
                <div class="modal-body">
                    <input type="hidden" name="candidatura_id" id="candidatura_id">
                    
                    <div class="mb-4">
                        <h4 class="text-lg font-medium text-preto-suave mb-3">Dados do Candidato</h4>
                        <div class="bg-cinza-claro p-4 rounded-lg">
                            <div id="candidato-info" class="space-y-2">
                                <!-- Informações do candidato serão inseridas aqui -->
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="status_avaliacao" class="form-label">Status da Avaliação</label>
                        <select class="form-control" id="status_avaliacao" name="status" required>
                            <option value="">Selecione o status</option>
                            <option value="aprovado" class="text-verde-medio">✓ Aprovado</option>
                            <option value="rejeitado" class="text-red-600">✗ Rejeitado</option>
                            <option value="pendente" class="text-amarelo-onibus">⏳ Pendente</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="observacoes_avaliacao" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes_avaliacao" name="observacoes" rows="3" 
                                  placeholder="Adicione comentários sobre a avaliação..."></textarea>
                    </div>

                    <div class="mb-4" id="motivo-rejeicao-container" style="display: none;">
                        <label for="motivo_rejeicao" class="form-label">Motivo da Rejeição</label>
                        <select class="form-control" id="motivo_rejeicao" name="motivo_rejeicao">
                            <option value="">Selecione o motivo</option>
                            <option value="documentos_incompletos">Documentos incompletos</option>
                            <option value="idade_inadequada">Idade inadequada</option>
                            <option value="local_incompativel">Local incompatível com rota</option>
                            <option value="vagas_esgotadas">Vagas esgotadas</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('avaliarCandidaturaModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Salvar Avaliação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Detalhes da Candidatura -->
<div class="modal-backdrop" id="detalhesCandidaturaModal" style="display: none;">
    <div class="modal-dialog max-w-2xl">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-lg font-semibold text-white">Detalhes da Candidatura</h3>
                <button type="button" class="modal-close" onclick="closeModal('detalhesCandidaturaModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="detalhes-candidatura-content">
                    <!-- Conteúdo será carregado dinamicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('detalhesCandidaturaModal')">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Funções auxiliares para modais
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

    // Carregar candidaturas ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        carregarCandidaturas();
    });

    // Função para carregar candidaturas
    function carregarCandidaturas() {
        document.getElementById('loading').classList.remove('hidden');
        
        fetch('../api/ajax_candidaturas.php?action=get_candidaturas')
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading').classList.add('hidden');
                const container = document.getElementById('candidaturas-container');
                
                if (data.success && data.candidaturas.length > 0) {
                    let html = '';
                    
                    // Agrupar por evento
                    const candidaturasPorEvento = {};
                    data.candidaturas.forEach(candidatura => {
                        const eventoId = candidatura.evento_id;
                        if (!candidaturasPorEvento[eventoId]) {
                            candidaturasPorEvento[eventoId] = {
                                evento: candidatura.evento_nome,
                                candidaturas: []
                            };
                        }
                        candidaturasPorEvento[eventoId].candidaturas.push(candidatura);
                    });
                    
                    // Renderizar candidaturas agrupadas
                    Object.values(candidaturasPorEvento).forEach(grupo => {
                        html += `
                            <div class="mb-6">
                                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                                    <div class="px-6 py-4 border-b border-gray-200 bg-azul-escuro">
                                        <h3 class="text-lg font-semibold text-white flex items-center">
                                            <i class="fas fa-calendar mr-2 text-amarelo-onibus"></i>
                                            ${grupo.evento}
                                            <span class="ml-auto badge-warning">${grupo.candidaturas.length} candidaturas</span>
                                        </h3>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="table-header">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-preto-suave uppercase tracking-wider">Candidato</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-preto-suave uppercase tracking-wider">Contato</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-preto-suave uppercase tracking-wider">Série/Curso</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-preto-suave uppercase tracking-wider">Data</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-preto-suave uppercase tracking-wider">Status</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-preto-suave uppercase tracking-wider">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                        `;
                        
                        grupo.candidaturas.forEach(candidatura => {
                            const statusClass = getStatusClass(candidatura.status);
                            const statusText = getStatusText(candidatura.status);
                            const dataFormatada = new Date(candidatura.data_candidatura).toLocaleDateString('pt-BR');
                            
                            html += `
                                <tr class="table-row">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-preto-suave">${candidatura.nome}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-cinza-medio">${candidatura.telefone}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-preto-suave">${candidatura.serie}ª Série</div>
                                        <div class="text-sm text-cinza-medio">${candidatura.curso}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-cinza-medio">
                                        ${dataFormatada}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="${statusClass}">${statusText}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="verDetalhes(${candidatura.id})" class="text-azul-claro hover:text-azul-escuro">
                                                <i class="fas fa-eye mr-1"></i>Ver
                                            </button>
                                            ${candidatura.status === 'pendente' ? `
                                                <button onclick="avaliarCandidatura(${candidatura.id})" class="text-verde-medio hover:text-green-700">
                                                    <i class="fas fa-check-circle mr-1"></i>Avaliar
                                                </button>
                                            ` : ''}
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        html += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-12">
                            <i class="fas fa-inbox text-6xl text-cinza-medio mb-4"></i>
                            <h3 class="text-lg font-medium text-preto-suave mb-2">Nenhuma candidatura encontrada</h3>
                            <p class="text-cinza-medio">Quando os alunos se inscreverem via QR Code, as candidaturas aparecerão aqui.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                document.getElementById('loading').classList.add('hidden');
                console.error('Erro:', error);
                document.getElementById('candidaturas-container').innerHTML = `
                    <div class="alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Erro ao carregar candidaturas. Tente novamente.
                    </div>
                `;
            });
    }

    // Função para obter classe CSS do status
    function getStatusClass(status) {
        switch(status) {
            case 'aprovado': return 'status-presente';
            case 'rejeitado': return 'status-ausente';
            case 'pendente': return 'status-onibus';
            default: return 'badge-primary';
        }
    }

    // Função para obter texto do status
    function getStatusText(status) {
        switch(status) {
            case 'aprovado': return '✓ Aprovado';
            case 'rejeitado': return '✗ Rejeitado';
            case 'pendente': return '⏳ Pendente';
            default: return status;
        }
    }

    // Função para avaliar candidatura
    function avaliarCandidatura(candidaturaId) {
        fetch(`../api/ajax_candidaturas.php?action=get_candidatura&id=${candidaturaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const candidatura = data.candidatura;
                    
                    // Preencher dados do modal
                    document.getElementById('candidatura_id').value = candidatura.id;
                    document.getElementById('candidato-info').innerHTML = `
                        <div class="grid grid-cols-2 gap-4">
                            <div><strong>Nome:</strong> ${candidatura.nome}</div>
                            <div><strong>Telefone:</strong> ${candidatura.telefone}</div>
                            <div><strong>Série:</strong> ${candidatura.serie}ª Série</div>
                            <div><strong>Curso:</strong> ${candidatura.curso}</div>
                            <div class="col-span-2"><strong>Evento:</strong> ${candidatura.evento_nome}</div>
                        </div>
                    `;
                    
                    // Resetar formulário
                    document.getElementById('status_avaliacao').value = candidatura.status;
                    document.getElementById('observacoes_avaliacao').value = candidatura.observacoes || '';
                    document.getElementById('motivo_rejeicao').value = candidatura.motivo_rejeicao || '';
                    
                    openModal('avaliarCandidaturaModal');
                } else {
                    mostrarMensagem('danger', 'Erro ao carregar dados da candidatura.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
            });
    }

    // Função para ver detalhes
    function verDetalhes(candidaturaId) {
        fetch(`../api/ajax_candidaturas.php?action=get_candidatura_detalhes&id=${candidaturaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const candidatura = data.candidatura;
                    const dataFormatada = new Date(candidatura.data_candidatura).toLocaleString('pt-BR');
                    
                    document.getElementById('detalhes-candidatura-content').innerHTML = `
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-cinza-claro p-4 rounded-lg">
                                    <h4 class="font-semibold text-preto-suave mb-2">Dados Pessoais</h4>
                                    <p><strong>Nome:</strong> ${candidatura.nome}</p>
                                    <p><strong>Telefone:</strong> ${candidatura.telefone}</p>
                                    <p><strong>Série:</strong> ${candidatura.serie}ª Série</p>
                                    <p><strong>Curso:</strong> ${candidatura.curso}</p>
                                </div>
                                <div class="bg-cinza-claro p-4 rounded-lg">
                                    <h4 class="font-semibold text-preto-suave mb-2">Evento</h4>
                                    <p><strong>Nome:</strong> ${candidatura.evento_nome}</p>
                                    <p><strong>Data da Candidatura:</strong> ${dataFormatada}</p>
                                    <p><strong>Status:</strong> <span class="${getStatusClass(candidatura.status)}">${getStatusText(candidatura.status)}</span></p>
                                </div>
                            </div>
                            ${candidatura.observacoes ? `
                                <div class="bg-cinza-claro p-4 rounded-lg">
                                    <h4 class="font-semibold text-preto-suave mb-2">Observações</h4>
                                    <p>${candidatura.observacoes}</p>
                                </div>
                            ` : ''}
                            ${candidatura.motivo_rejeicao ? `
                                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                    <h4 class="font-semibold text-red-800 mb-2">Motivo da Rejeição</h4>
                                    <p class="text-red-700">${candidatura.motivo_rejeicao}</p>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    
                    openModal('detalhesCandidaturaModal');
                } else {
                    mostrarMensagem('danger', 'Erro ao carregar detalhes da candidatura.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
            });
    }

    // Função para mostrar/ocultar motivo de rejeição
    document.getElementById('status_avaliacao').addEventListener('change', function() {
        const motivoContainer = document.getElementById('motivo-rejeicao-container');
        if (this.value === 'rejeitado') {
            motivoContainer.style.display = 'block';
            document.getElementById('motivo_rejeicao').required = true;
        } else {
            motivoContainer.style.display = 'none';
            document.getElementById('motivo_rejeicao').required = false;
        }
    });

    // Formulário de avaliação
    document.getElementById('avaliar-candidatura-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'avaliar_candidatura');
        
        fetch('../api/ajax_candidaturas.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarMensagem('success', 'Candidatura avaliada com sucesso!');
                closeModal('avaliarCandidaturaModal');
                carregarCandidaturas();
            } else {
                mostrarMensagem('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
        });
    });

    // Função para adicionar candidatura de teste
    function adicionarCandidaturaTeste() {
        if (confirm('Deseja adicionar uma candidatura de teste para demonstração?')) {
            fetch('../api/ajax_candidaturas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=add_candidatura_teste'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensagem('success', 'Candidatura de teste adicionada com sucesso!');
                    carregarCandidaturas();
                } else {
                    mostrarMensagem('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarMensagem('danger', 'Erro ao conectar com o servidor.');
            });
        }
    }

    // Função para mostrar mensagens
    function mostrarMensagem(tipo, mensagem) {
        if (typeof Utils !== 'undefined' && Utils.showToast) {
            Utils.showToast(mensagem, tipo);
        } else {
            alert(mensagem);
        }
    }
</script>

<?php renderFooter(); ?>
