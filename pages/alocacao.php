<?php
$current_page = "alocacao";
include '../includes/page-layout.php';

// Incluir configuração do banco
include '../config/config.php';

// Configuração do breadcrumb
$breadcrumb = [
    ['label' => 'Alocação Automática']
];

// Ações do header
$actions = [
    [
        'url' => '#', 
        'icon' => 'fas fa-magic', 
        'label' => 'Iniciar Alocação'
    ]
];

// Renderizar header simplificado
renderHeader("Alocação");

// Obter conexão com banco
$conn = getDatabaseConnection();
?>

<!-- Container Principal -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-amarelo-onibus to-yellow-500 rounded-xl p-8 mb-8 text-white">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-6 md:mb-0">
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-magic mr-3"></i>Alocação Inteligente
                </h1>
                <p class="text-lg opacity-90">Sistema automatizado baseado em algoritmos otimizados</p>
            </div>
            <div class="text-center">
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                    <i class="fas fa-robot text-4xl mb-2"></i>
                    <p class="text-sm opacity-80">IA</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loading" class="hidden text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
        <div class="mt-2 text-gray-600">Processando alocações...</div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6">

                        <div id="main-content">
                            <?php
                            include 'config.php';
                            $conn = getDatabaseConnection();

                            if ($conn->connect_error) {
                                echo '<div class="alert alert-danger">Erro de conexão: ' . $conn->connect_error . '</div>';
                            } else {
                                // Buscar eventos disponíveis
                                $eventos_query = "SELECT * FROM eventos ORDER BY data_inicio DESC";
                                $eventos_result = $conn->query($eventos_query);

                                if ($eventos_result->num_rows == 0) {
                                    echo '<div class="alert alert-warning">Nenhum evento cadastrado. <a href="eventos.php">Cadastrar evento primeiro</a></div>';
                                } else {
                                    echo '<div class="row">';
                                    echo '<div class="col-md-4">';

                                    // Formulário de seleção de evento
                                    echo '<h5>Selecionar Evento</h5>';
                                    echo '<form id="evento-selection-form">';
                                    echo '<div class="mb-3">';
                                    echo '<label for="evento_select" class="form-label">Evento</label>';
                                    echo '<select class="form-select" id="evento_select" name="evento_id" required>';
                                    echo '<option value="">Selecione um evento...</option>';

                                    while ($evento = $eventos_result->fetch_assoc()) {
                                        $data_formatada = date('d/m/Y', strtotime($evento['data_inicio'])) . ' - ' . date('d/m/Y', strtotime($evento['data_fim']));
                                        echo '<option value="' . $evento['id'] . '">' . htmlspecialchars($evento['nome']) . ' (' . $data_formatada . ')</option>';
                                    }

                                    echo '</select>';
                                    echo '</div>';

                                    echo '<div class="alert alert-info mt-3">';
                                    echo '<h6><i class="fas fa-info-circle me-2"></i>Como Funciona o Sistema de Alocações</h6>';
                                    echo '<ul class="mb-0">';
                                    echo '<li><strong>Alocações Persistentes:</strong> Uma vez alocado, o aluno permanece no mesmo ônibus todos os dias</li>';
                                    echo '<li><strong>Presença Diária:</strong> A presença é registrada separadamente a cada dia</li>';
                                    echo '<li><strong>Alocação Automática:</strong> Substitui TODAS as alocações existentes</li>';
                                    echo '<li><strong>Alocar Não Alocados:</strong> Adiciona apenas alunos novos sem alterar alocações existentes</li>';
                                    echo '</ul>';
                                    echo '</div>';

                                    echo '<div id="evento-info" class="mt-4 d-none">';
                                    echo '<h6>Informações do Evento</h6>';
                                    echo '<div id="evento-details"></div>';
                                    echo '<button type="button" class="btn btn-success mt-3" id="btn-alocar-automaticamente">Alocar Automaticamente</button>';
                                    echo '<button type="button" class="btn btn-info mt-3 ms-2" id="btn-alocar-nao-alocados">Alocar Apenas Não Alocados</button>';
                                    echo '<button type="button" class="btn btn-danger mt-3 ms-2" id="btn-limpar-alocacoes">Limpar Todas as Alocações</button>';
                                    echo '</div>';

                                    echo '</div>';

                                    echo '<div class="col-md-8">';
                                    echo '<div id="alocacao-results">';
                                    echo '<div class="alert alert-info">Selecione um evento para visualizar as informações de alocação.</div>';
                                    echo '</div>';
                                    echo '</div>';

                                    echo '</div>';
                                }

                                $conn->close();
                            }
                            ?>
                        </div>

                        <!-- Modal de Confirmação de Alocação -->
                        <div class="modal fade" id="confirmAllocationModal" tabindex="-1" aria-labelledby="confirmAllocationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmAllocationModalLabel">Confirmar Alocação Automática</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Esta ação irá:</p>
                                        <ul>
                                            <li><strong class="text-danger">LIMPAR todas as alocações atuais do evento</strong></li>
                                            <li>Alocar alunos automaticamente baseado na ordem de inscrição</li>
                                            <li>Distribuir alunos igualmente entre os ônibus disponíveis</li>
                                        </ul>
                                        <div class="alert alert-warning">
                                            <strong>⚠️ Importante:</strong> As alocações são <strong>PERSISTENTES</strong> e só devem ser alteradas quando necessário.
                                            <br>Use "Alocar Apenas Não Alocados" para adicionar novos alunos sem afetar alocações existentes.
                                        </div>
                                        <div id="allocation-preview" class="mt-3"></div>
                                        <p class="text-danger mt-3"><strong>Esta ação não pode ser desfeita. Deseja continuar?</strong></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-success" id="confirm-allocation-btn">Confirmar Alocação</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de Confirmação de Limpeza -->
                        <div class="modal fade" id="confirmClearModal" tabindex="-1" aria-labelledby="confirmClearModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmClearModalLabel">Confirmar Limpeza</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Tem certeza que deseja limpar todas as alocações deste evento?</p>
                                        <small class="text-muted">Esta ação removerá todas as alocações atuais dos alunos neste evento.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-danger" id="confirm-clear-btn">Limpar Alocações</button>
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
        let currentEventoId = null;

        // Manipular seleção de evento
        document.getElementById('evento-selection-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const eventoId = document.getElementById('evento_select').value;
            if (!eventoId) {
                mostrarMensagem('warning', 'Selecione um evento primeiro');
                return;
            }

            currentEventoId = eventoId;
            carregarDadosEvento(eventoId);
        });

        // Carregar dados do evento
        function carregarDadosEvento(eventoId) {
            // Mostrar loading
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('main-content').classList.add('d-none');

            fetch('ajax_alocacao.php?action=get_event_data&evento_id=' + eventoId)
                .then(response => response.json())
                .then(data => {
                    // Esconder loading
                    document.getElementById('loading').classList.add('d-none');
                    document.getElementById('main-content').classList.remove('d-none');

                    if (data.success) {
                        exibirDadosEvento(data);
                        document.getElementById('evento-info').classList.remove('d-none');
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

        // Exibir dados do evento
        function exibirDadosEvento(data) {
            const details = document.getElementById('evento-details');
            const results = document.getElementById('alocacao-results');

            let html = '<div class="card bg-light">';
            html += '<div class="card-body">';
            html += '<h6>' + data.evento.nome + '</h6>';
            html += '<p class="mb-1"><strong>Período:</strong> ' + new Date(data.evento.data_inicio).toLocaleDateString('pt-BR') + ' - ' + new Date(data.evento.data_fim).toLocaleDateString('pt-BR') + '</p>';
            html += '<p class="mb-1"><strong>Local:</strong> ' + data.evento.local + '</p>';
            html += '<p class="mb-1"><strong>Total de Alunos:</strong> <span class="badge bg-primary">' + data.total_alunos + '</span></p>';
            html += '<p class="mb-1"><strong>Total de Ônibus:</strong> <span class="badge bg-info">' + data.total_onibus + '</span></p>';
            html += '<p class="mb-1"><strong>Capacidade Total:</strong> <span class="badge bg-success">' + data.capacidade_total + '</span></p>';
            html += '<p class="mb-1"><strong>Alunos Alocados:</strong> <span class="badge bg-warning">' + data.alunos_alocados + '</span></p>';

            if (data.total_alunos > data.capacidade_total) {
                html += '<div class="alert alert-danger mt-2">Atenção: Não há capacidade suficiente para todos os alunos!</div>';
            } else if (data.total_alunos > 0 && data.total_onibus > 0) {
                html += '<div class="alert alert-success mt-2">Capacidade suficiente disponível.</div>';
            }

            html += '</div></div>';
            details.innerHTML = html;

            // Exibir resultados de alocação
            exibirResultadosAlocacao(data);
        }

        // Exibir resultados de alocação
        function exibirResultadosAlocacao(data) {
            const results = document.getElementById('alocacao-results');

            if (data.alocacoes && data.alocacoes.length > 0) {
                let html = '<h5>Alocações Atuais</h5>';
                html += '<div class="table-responsive">';
                html += '<table class="table table-striped">';
                html += '<thead><tr><th>Ônibus</th><th>Capacidade</th><th>Alunos Alocados</th><th>Status</th></tr></thead>';
                html += '<tbody>';

                data.alocacoes.forEach(alocacao => {
                    const status = alocacao.alunos_count >= alocacao.capacidade ? 'Cheio' : 'Disponível';
                    const statusClass = alocacao.alunos_count >= alocacao.capacidade ? 'danger' : 'success';

                    html += '<tr>';
                    html += '<td>' + alocacao.tipo + ' ' + alocacao.numero + '</td>';
                    html += '<td>' + alocacao.capacidade + '</td>';
                    html += '<td>' + alocacao.alunos_count + '</td>';
                    html += '<td><span class="badge bg-' + statusClass + '">' + status + '</span></td>';
                    html += '</tr>';
                });

                html += '</tbody></table></div>';
                results.innerHTML = html;
            } else {
                results.innerHTML = '<div class="alert alert-info">Nenhuma alocação realizada ainda.</div>';
            }
        }

        // Botão de alocação automática
        document.getElementById('btn-alocar-automaticamente').addEventListener('click', function() {
            if (!currentEventoId) {
                mostrarMensagem('warning', 'Selecione um evento primeiro');
                return;
            }

            // Carregar preview da alocação
            fetch('ajax_alocacao.php?action=get_allocation_preview&evento_id=' + currentEventoId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        exibirPreviewAlocacao(data);
                        const modal = new bootstrap.Modal(document.getElementById('confirmAllocationModal'));
                        modal.show();
                    } else {
                        mostrarMensagem('danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    mostrarMensagem('danger', 'Erro ao carregar preview da alocação.');
                });
        });

        // Exibir preview da alocação
        function exibirPreviewAlocacao(data) {
            const preview = document.getElementById('allocation-preview');

            if (data.preview && data.preview.length > 0) {
                let html = '<h6>Preview da Alocação:</h6>';
                html += '<div class="table-responsive">';
                html += '<table class="table table-sm">';
                html += '<thead><tr><th>Ônibus</th><th>Capacidade</th><th>Alunos a Serem Alocados</th></tr></thead>';
                html += '<tbody>';

                data.preview.forEach(item => {
                    html += '<tr>';
                    html += '<td>' + item.onibus_tipo + ' ' + item.onibus_numero + '</td>';
                    html += '<td>' + item.capacidade + '</td>';
                    html += '<td>' + item.alunos_count + '</td>';
                    html += '</tr>';
                });

                html += '</tbody></table></div>';
                preview.innerHTML = html;
            } else {
                preview.innerHTML = '<div class="alert alert-warning">Não há alunos ou ônibus suficientes para alocação.</div>';
            }
        }

        // Confirmar alocação
        document.getElementById('confirm-allocation-btn').addEventListener('click', function() {
            if (!currentEventoId) return;

            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmAllocationModal'));
            modal.hide();

            // Mostrar loading
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('main-content').classList.add('d-none');

            const formData = new FormData();
            formData.append('evento_id', currentEventoId);

            fetch('ajax_alocacao.php?action=auto_allocate', {
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
                    carregarDadosEvento(currentEventoId);
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

        // Botão de alocar apenas alunos não alocados
        document.getElementById('btn-alocar-nao-alocados').addEventListener('click', function() {
            if (!currentEventoId) {
                mostrarMensagem('warning', 'Selecione um evento primeiro');
                return;
            }

            // Mostrar loading
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('main-content').classList.add('d-none');

            const formData = new FormData();
            formData.append('evento_id', currentEventoId);

            fetch('ajax_alocacao.php?action=allocate_unallocated', {
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
                    carregarDadosEvento(currentEventoId);
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

        // Botão de limpar alocações
        document.getElementById('btn-limpar-alocacoes').addEventListener('click', function() {
            if (!currentEventoId) {
                mostrarMensagem('warning', 'Selecione um evento primeiro');
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById('confirmClearModal'));
            modal.show();
        });

        // Confirmar limpeza
        document.getElementById('confirm-clear-btn').addEventListener('click', function() {
            if (!currentEventoId) return;

            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmClearModal'));
            modal.hide();

            // Mostrar loading
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('main-content').classList.add('d-none');

            const formData = new FormData();
            formData.append('evento_id', currentEventoId);

            fetch('ajax_alocacao.php?action=clear_allocations', {
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
                    carregarDadosEvento(currentEventoId);
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
    </script>
</div>

<?php renderFooter(); ?>
