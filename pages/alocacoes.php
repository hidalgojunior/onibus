<?php
$current_page = "alocacoes";
include '../includes/page-layout.php';

// Incluir configuração do banco
include '../config/config.php';

// Configuração do breadcrumb
$breadcrumb = [
    ['label' => 'Alocações']
];

// Ações do header
$actions = [
    [
        'url' => '#', 
        'icon' => 'fas fa-plus', 
        'label' => 'Nova Alocação'
    ]
];

// Renderizar header simplificado
renderHeader("Alocações");

// Obter conexão com banco
$conn = getDatabaseConnection();
?>

<!-- Container Principal -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-xl p-8 mb-8 text-white">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-6 md:mb-0">
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-map-marked-alt mr-3"></i>Gestão de Alocações
                </h1>
                <p class="text-lg opacity-90">Visualize, edite e gerencie alocações manuais</p>
            </div>
            <div class="text-center">
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                    <i class="fas fa-edit text-4xl mb-2"></i>
                    <p class="text-sm opacity-80">Manual</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Spinner -->
    <div id="loading" class="hidden text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
        <div class="mt-2 text-gray-600">Processando...</div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content">
        <?php
        if ($conn->connect_error) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>Erro de conexão:</strong> ' . htmlspecialchars($conn->connect_error ?? '') . '
                  </div>';
        } else {
            // Verificar se o evento existe
            $evento_nome = 'Bootcamp Jovem Programador';
            $evento_query = "SELECT id FROM eventos WHERE nome = ?";
            $stmt = $conn->prepare($evento_query);
            $stmt->bind_param("s", $evento_nome);
            $stmt->execute();
            $evento_result = $stmt->get_result();

            if ($evento_result->num_rows == 0) {
                $conn->query("INSERT INTO eventos (nome, data_inicio, data_fim) VALUES ('$evento_nome', '2025-08-27', '2025-09-05')");
                $evento_id = $conn->insert_id;
            } else {
                $evento_row = $evento_result->fetch_assoc();
                $evento_id = $evento_row['id'];
            }

            // Buscar ônibus do evento
            $onibus_query = "SELECT * FROM onibus WHERE evento_id = $evento_id ORDER BY numero";
            $onibus_result = $conn->query($onibus_query);

            if ($onibus_result->num_rows == 0) {
                echo '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Nenhum ônibus cadastrado para este evento.
                      </div>';
            } else {
                echo '<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">';
                
                // Seção de Alocação
                echo '<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">';
                echo '<h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-plus-circle text-blue-600 mr-3"></i>Alocar Alunos
                      </h2>';
                
                echo '<form id="alocacao-form" class="space-y-6">';
                echo '<div>';
                echo '<label for="onibus_select" class="block text-sm font-medium text-gray-700 mb-2">
                        Selecionar Ônibus:
                      </label>';
                echo '<select 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                        id="onibus_select" 
                        name="onibus_id" 
                        required
                      >';
                echo '<option value="">Escolha um ônibus...</option>';

                while ($onibus = $onibus_result->fetch_assoc()) {
                    echo '<option value="' . $onibus['id'] . '">
                            Ônibus ' . htmlspecialchars($onibus['numero'] ?? '') . ' 
                            (Capacidade: ' . htmlspecialchars($onibus['capacidade'] ?? '') . ')
                          </option>';
                }

                echo '</select>';
                echo '</div>';

                echo '<div id="alunos-nao-alocados-container" class="border border-gray-200 rounded-lg p-4 bg-gray-50">';
                echo '<div class="text-center text-gray-600">
                        <div class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-2"></div>
                        Carregando alunos...
                      </div>';
                echo '</div>';

                echo '<button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200"
                      >
                        <i class="fas fa-check mr-2"></i>Alocar Alunos Selecionados
                      </button>';
                echo '</form>';
                echo '</div>';

                // Seção de Alocações Atuais
                echo '<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">';
                                    echo '<h5>Alocações Atuais</h5>';
                                    echo '<div id="alocacoes-atuais-container">';
                                    // Esta seção será carregada via AJAX
                                    echo '<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Carregando alocações...</div>';
                                    echo '</div>';
                                    echo '</div>';

                                    echo '</div>';
                                }

                                $conn->close();
                            }
                            ?>
                        </div>

                        <!-- Modal de Confirmação de Remoção -->
                        <div class="modal fade" id="confirmRemoveModal" tabindex="-1" aria-labelledby="confirmRemoveModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmRemoveModalLabel">Confirmar Remoção</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Tem certeza que deseja remover a alocação de <strong id="aluno-nome-remover"></strong>?</p>
                                        <small class="text-muted">Esta ação não pode ser desfeita.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-danger" id="confirm-remove-btn">Remover</button>
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
        let eventoId = <?php echo $evento_id ?? 0; ?>;
        let alocacaoIdToRemove = null;

        // Carregar dados iniciais
        document.addEventListener('DOMContentLoaded', function() {
            carregarAlunosNaoAlocados();
            carregarAlocacoesAtuais();
        });

        // Carregar alunos não alocados
        function carregarAlunosNaoAlocados() {
            fetch('ajax_alocacoes.php?action=get_unallocated_students&evento_id=' + eventoId)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('alunos-nao-alocados-container');

                    if (data.success) {
                        if (data.students.length > 0) {
                            let html = '<label class="form-label">Alunos Não Alocados (' + data.students.length + '):</label>';
                            html += '<div class="border p-3" style="max-height: 400px; overflow-y: auto;">';

                            data.students.forEach(student => {
                                html += '<div class="form-check">';
                                html += '<input class="form-check-input" type="checkbox" name="alunos[]" value="' + student.id + '" id="aluno_' + student.id + '">';
                                html += '<label class="form-check-label" for="aluno_' + student.id + '">';
                                html += student.nome + ' - ' + student.curso;
                                html += '</label>';
                                html += '</div>';
                            });

                            html += '</div>';
                            container.innerHTML = html;
                        } else {
                            container.innerHTML = '<div class="alert alert-success">Todos os alunos já estão alocados!</div>';
                        }
                    } else {
                        container.innerHTML = '<div class="alert alert-danger">Erro ao carregar alunos: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('alunos-nao-alocados-container').innerHTML = '<div class="alert alert-danger">Erro ao conectar com o servidor.</div>';
                });
        }

        // Carregar alocações atuais
        function carregarAlocacoesAtuais() {
            fetch('ajax_alocacoes.php?action=get_current_allocations&evento_id=' + eventoId)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('alocacoes-atuais-container');

                    if (data.success) {
                        if (data.allocations.length > 0) {
                            let html = '';
                            let currentBus = null;

                            data.allocations.forEach(allocation => {
                                if (currentBus != allocation.onibus_numero) {
                                    if (currentBus !== null) {
                                        html += '</ul>';
                                    }
                                    currentBus = allocation.onibus_numero;
                                    html += '<h6>Ônibus ' + currentBus + ':</h6>';
                                    html += '<ul class="list-group mb-3">';
                                }

                                html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                html += allocation.nome + ' - ' + allocation.curso;
                                html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmarRemocao(' + allocation.alocacao_id + ', \'' + allocation.nome + '\')">';
                                html += '<i class="fas fa-trash"></i>';
                                html += '</button>';
                                html += '</li>';
                            });

                            html += '</ul>';
                            container.innerHTML = html;
                        } else {
                            container.innerHTML = '<div class="alert alert-info">Nenhuma alocação encontrada.</div>';
                        }
                    } else {
                        container.innerHTML = '<div class="alert alert-danger">Erro ao carregar alocações: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('alocacoes-atuais-container').innerHTML = '<div class="alert alert-danger">Erro ao conectar com o servidor.</div>';
                });
        }

        // Manipular formulário de alocação
        document.getElementById('alocacao-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const selectedStudents = formData.getAll('alunos[]');

            if (selectedStudents.length === 0) {
                alert('Selecione pelo menos um aluno para alocar.');
                return;
            }

            if (!formData.get('onibus_id')) {
                alert('Selecione um ônibus.');
                return;
            }

            // Mostrar loading
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('main-content').classList.add('d-none');

            // Enviar dados via AJAX
            fetch('ajax_alocacoes.php?action=allocate_students', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Esconder loading
                document.getElementById('loading').classList.add('d-none');
                document.getElementById('main-content').classList.remove('d-none');

                if (data.success) {
                    // Mostrar mensagem de sucesso
                    mostrarMensagem('success', data.message);

                    // Recarregar listas
                    carregarAlunosNaoAlocados();
                    carregarAlocacoesAtuais();

                    // Limpar seleção
                    document.querySelectorAll('input[name="alunos[]"]:checked').forEach(checkbox => {
                        checkbox.checked = false;
                    });
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
        function confirmarRemocao(alocacaoId, alunoNome) {
            alocacaoIdToRemove = alocacaoId;
            document.getElementById('aluno-nome-remover').textContent = alunoNome;

            const modal = new bootstrap.Modal(document.getElementById('confirmRemoveModal'));
            modal.show();
        }

        // Confirmar remoção no modal
        document.getElementById('confirm-remove-btn').addEventListener('click', function() {
            if (alocacaoIdToRemove) {
                // Fechar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmRemoveModal'));
                modal.hide();

                // Mostrar loading
                document.getElementById('loading').classList.remove('d-none');
                document.getElementById('main-content').classList.add('d-none');

                // Enviar requisição de remoção
                const formData = new FormData();
                formData.append('alocacao_id', alocacaoIdToRemove);

                fetch('ajax_alocacoes.php?action=remove_allocation', {
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
                        carregarAlunosNaoAlocados();
                        carregarAlocacoesAtuais();
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

            // Inserir no topo do card-body
            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alertContainer, cardBody.firstChild);

            // Auto-remover após 5 segundos
            setTimeout(() => {
                if (alertContainer && alertContainer.parentNode) {
                    alertContainer.remove();
                }
            }, 5000);
        }
    </script>
</div>

<?php renderFooter(); ?></content>
<parameter name="filePath">c:\laragon\www\onibus\alocacoes.php
