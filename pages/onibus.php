<?php
$current_page = "onibus";
include '../includes/page-layout.php';

// Incluir configuração do banco
include '../config/config.php';

// Configuração do breadcrumb
$breadcrumb = [
    ['label' => 'Ônibus']
];

// Ações do header
$actions = [
    [
        'url' => '#', 
        'icon' => 'fas fa-plus', 
        'label' => 'Novo Ônibus'
    ]
];

// Renderizar header simplificado
renderHeader("Ônibus");

// Obter conexão com banco
$conn = getDatabaseConnection();
?>

<!-- Container Principal -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-verde-medio to-green-600 rounded-xl p-8 mb-8 text-white">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-6 md:mb-0">
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-bus mr-3"></i>Gestão da Frota
                </h1>
                <p class="text-lg opacity-90">Cadastre e gerencie toda a frota de ônibus</p>
            </div>
            <div class="text-center">
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                    <i class="fas fa-cogs text-4xl mb-2"></i>
                    <p class="text-sm opacity-80">Operacional</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loading" class="hidden text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
        <div class="mt-2 text-gray-600">Processando...</div>
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

                                    // Formulário de cadastro de ônibus
                                    echo '<h5>Cadastrar Novo Ônibus</h5>';
                                    echo '<form id="onibus-form">';
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

                                    echo '<div class="mb-3">';
                                    echo '<label for="numero_onibus" class="form-label">Número do Ônibus</label>';
                                    echo '<input type="number" class="form-control" id="numero_onibus" name="numero" min="1" required>';
                                    echo '</div>';

                                    echo '<div class="mb-3">';
                                    echo '<label for="tipo_onibus" class="form-label">Tipo</label>';
                                    echo '<select class="form-select" id="tipo_onibus" name="tipo" required>';
                                    echo '<option value="ônibus">Ônibus</option>';
                                    echo '<option value="van">Van</option>';
                                    echo '<option value="micro-ônibus">Micro-ônibus</option>';
                                    echo '</select>';
                                    echo '</div>';

                                    echo '<div class="mb-3">';
                                    echo '<label for="capacidade_onibus" class="form-label">Capacidade</label>';
                                    echo '<input type="number" class="form-control" id="capacidade_onibus" name="capacidade" min="1" max="100" required>';
                                    echo '</div>';

                                    echo '<div class="mb-3">';
                                    echo '<label for="dias_onibus" class="form-label">Dias de Reserva</label>';
                                    echo '<input type="number" class="form-control" id="dias_onibus" name="dias_reservados" min="1" max="30" value="10" required>';
                                    echo '</div>';

                                    echo '<button type="submit" class="btn btn-success">Cadastrar Ônibus</button>';
                                    echo '</form>';

                                    echo '</div>';

                                    echo '<div class="col-md-8">';
                                    echo '<h5>Ônibus Cadastrados</h5>';
                                    echo '<div id="onibus-list-container">';
                                    // Esta seção será carregada via AJAX
                                    echo '<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Carregando ônibus...</div>';
                                    echo '</div>';
                                    echo '</div>';

                                    echo '</div>';
                                }

                                $conn->close();
                            }
                            ?>
                        </div>

                        <!-- Modal de Edição de Ônibus -->
                        <div class="modal fade" id="editarOnibusModal" tabindex="-1" aria-labelledby="editarOnibusModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editarOnibusModalLabel">Editar Ônibus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form id="edit-onibus-form">
                                        <div class="modal-body">
                                            <input type="hidden" name="onibus_id" id="edit_onibus_id">

                                            <div class="mb-3">
                                                <label for="edit_numero" class="form-label">Número do Ônibus</label>
                                                <input type="number" class="form-control" id="edit_numero" name="numero" min="1" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="edit_tipo" class="form-label">Tipo</label>
                                                <select class="form-select" id="edit_tipo" name="tipo" required>
                                                    <option value="ônibus">Ônibus</option>
                                                    <option value="van">Van</option>
                                                    <option value="micro-ônibus">Micro-ônibus</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="edit_capacidade" class="form-label">Capacidade</label>
                                                <input type="number" class="form-control" id="edit_capacidade" name="capacidade" min="1" max="100" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="edit_dias" class="form-label">Dias de Reserva</label>
                                                <input type="number" class="form-control" id="edit_dias" name="dias_reservados" min="1" max="30" required>
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
                        <div class="modal fade" id="confirmRemoveBusModal" tabindex="-1" aria-labelledby="confirmRemoveBusModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmRemoveBusModalLabel">Confirmar Remoção</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Tem certeza que deseja remover o <strong id="bus-info-remover"></strong>?</p>
                                        <small class="text-muted">Esta ação também removerá todas as alocações deste ônibus.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-danger" id="confirm-remove-bus-btn">Remover</button>
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
        let onibusIdToRemove = null;

        // Carregar dados iniciais
        document.addEventListener('DOMContentLoaded', function() {
            carregarOnibus();
        });

        // Carregar lista de ônibus
        function carregarOnibus() {
            fetch('ajax_onibus.php?action=get_buses')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('onibus-list-container');

                    if (data.success) {
                        if (data.buses.length > 0) {
                            let html = '<div class="table-responsive">';
                            html += '<table class="table table-striped">';
                            html += '<thead><tr><th>Número</th><th>Tipo</th><th>Capacidade</th><th>Evento</th><th>Dias</th><th>Ações</th></tr></thead>';
                            html += '<tbody>';

                            data.buses.forEach(bus => {
                                html += '<tr>';
                                html += '<td>' + bus.numero + '</td>';
                                html += '<td>' + bus.tipo + '</td>';
                                html += '<td>' + bus.capacidade + '</td>';
                                html += '<td>' + bus.evento_nome + '</td>';
                                html += '<td>' + bus.dias_reservados + '</td>';
                                html += '<td>';
                                html += '<button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="editarOnibus(' + bus.id + ', ' + bus.numero + ', \'' + bus.tipo + '\', ' + bus.capacidade + ', ' + bus.dias_reservados + ')">';
                                html += '<i class="fas fa-edit"></i>';
                                html += '</button>';
                                html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmarRemocaoOnibus(' + bus.id + ', \'' + bus.tipo + ' ' + bus.numero + '\')">';
                                html += '<i class="fas fa-trash"></i>';
                                html += '</button>';
                                html += '</td>';
                                html += '</tr>';
                            });

                            html += '</tbody></table></div>';
                            container.innerHTML = html;
                        } else {
                            container.innerHTML = '<div class="alert alert-info">Nenhum ônibus cadastrado.</div>';
                        }
                    } else {
                        container.innerHTML = '<div class="alert alert-danger">Erro ao carregar ônibus: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('onibus-list-container').innerHTML = '<div class="alert alert-danger">Erro ao conectar com o servidor.</div>';
                });
        }

        // Manipular formulário de cadastro
        document.getElementById('onibus-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Mostrar loading
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('main-content').classList.add('d-none');

            fetch('ajax_onibus.php?action=create_bus', {
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
                    carregarOnibus();
                    // Limpar formulário
                    this.reset();
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

        // Editar ônibus
        function editarOnibus(id, numero, tipo, capacidade, dias) {
            document.getElementById('edit_onibus_id').value = id;
            document.getElementById('edit_numero').value = numero;
            document.getElementById('edit_tipo').value = tipo;
            document.getElementById('edit_capacidade').value = capacidade;
            document.getElementById('edit_dias').value = dias;

            const modal = new bootstrap.Modal(document.getElementById('editarOnibusModal'));
            modal.show();
        }

        // Manipular formulário de edição
        document.getElementById('edit-onibus-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editarOnibusModal'));
            modal.hide();

            // Mostrar loading
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('main-content').classList.add('d-none');

            fetch('ajax_onibus.php?action=update_bus', {
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
                    carregarOnibus();
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
        function confirmarRemocaoOnibus(onibusId, busInfo) {
            onibusIdToRemove = onibusId;
            document.getElementById('bus-info-remover').textContent = busInfo;

            const modal = new bootstrap.Modal(document.getElementById('confirmRemoveBusModal'));
            modal.show();
        }

        // Confirmar remoção no modal
        document.getElementById('confirm-remove-bus-btn').addEventListener('click', function() {
            if (onibusIdToRemove) {
                // Fechar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmRemoveBusModal'));
                modal.hide();

                // Mostrar loading
                document.getElementById('loading').classList.remove('d-none');
                document.getElementById('main-content').classList.add('d-none');

                const formData = new FormData();
                formData.append('onibus_id', onibusIdToRemove);

                fetch('ajax_onibus.php?action=remove_bus', {
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
                        carregarOnibus();
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
    </script>
</div>

<?php renderFooter(); ?>
