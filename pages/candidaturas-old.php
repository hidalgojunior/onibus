<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Candidaturas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h1 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>Gerenciar Candidaturas de Eventos
                        </h1>
                        <small>Visualize e gerencie inscrições recebidas via QR Code</small>
                    </div>
                    <div class="card-body">
                        <div id="loading" class="d-none text-center">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Carregando candidaturas...
                        </div>

                        <div id="candidaturas-container">
                            <!-- As candidaturas serão carregadas aqui -->
                        </div>

                        <!-- Botão para adicionar candidatura de teste -->
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="adicionarCandidaturaTeste()">
                                <i class="fas fa-plus"></i> Adicionar Candidatura de Teste
                            </button>
                            <small class="text-muted ms-2">Para testar o sistema</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Avaliação de Candidatura -->
    <div class="modal fade" id="avaliarCandidaturaModal" tabindex="-1" aria-labelledby="avaliarCandidaturaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="avaliarCandidaturaModalLabel">Avaliar Candidatura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="avaliar-candidatura-form">
                    <div class="modal-body">
                        <input type="hidden" name="candidatura_id" id="avaliar_candidatura_id">

                        <div class="mb-3">
                            <h6>Informações do Candidato</h6>
                            <div id="candidato-info" class="bg-light p-3 rounded">
                                <!-- Informações serão carregadas aqui -->
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Decisão *</label>
                            <select class="form-select" name="status" id="avaliar_status" required>
                                <option value="">Selecione uma decisão</option>
                                <option value="aprovada">✅ Aprovar Candidatura</option>
                                <option value="reprovada">❌ Reprovar Candidatura</option>
                                <option value="cancelada">🚫 Cancelar Candidatura</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observações da Avaliação</label>
                            <textarea class="form-control" name="observacao_admin" id="avaliar_observacao" rows="3" placeholder="Adicione observações sobre sua decisão..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Avaliação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let candidaturaIdToEvaluate = null;

        // Carregar dados iniciais
        document.addEventListener('DOMContentLoaded', function() {
            carregarCandidaturas();
        });

        // Carregar lista de candidaturas
        function carregarCandidaturas() {
            const loading = document.getElementById('loading');
            const container = document.getElementById('candidaturas-container');

            loading.classList.remove('d-none');

            // Adicionar timestamp para evitar cache
            const timestamp = new Date().getTime();

            fetch(`ajax_candidaturas.php?action=get_candidaturas&t=${timestamp}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    console.log('Content-Type:', response.headers.get('content-type'));

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    // Primeiro obter o texto da resposta
                    return response.text().then(text => {
                        console.log('Raw response length:', text.length);
                        console.log('First 200 chars:', text.substring(0, 200));
                        console.log('Last 200 chars:', text.substring(text.length - 200));

                        // Verificar se é JSON válido
                        try {
                            return JSON.parse(text);
                        } catch (jsonError) {
                            console.error('JSON parse error:', jsonError);
                            console.error('Response text:', text);
                            throw new Error(`JSON parse error: ${jsonError.message}`);
                        }
                    });
                })
                .then(data => {
                    loading.classList.add('d-none');

                    if (data.success) {
                        if (data.candidaturas.length > 0) {
                            let html = '<div class="table-responsive">';
                            html += '<table class="table table-striped table-hover">';
                            html += '<thead class="table-dark"><tr><th>Data</th><th>Nome</th><th>Telefone</th><th>Série</th><th>Curso</th><th>Evento</th><th>Status</th><th>Ações</th></tr></thead>';
                            html += '<tbody>';

                            data.candidaturas.forEach(candidatura => {
                                const dataFormatada = new Date(candidatura.data_candidatura).toLocaleDateString('pt-BR');
                                const statusClass = getStatusClass(candidatura.status);
                                const statusText = getStatusText(candidatura.status);

                                html += '<tr>';
                                html += '<td>' + dataFormatada + '</td>';
                                html += '<td><strong>' + candidatura.nome + '</strong></td>';
                                html += '<td>' + (candidatura.telefone || 'N/A') + '</td>';
                                html += '<td>' + candidatura.serie + 'ª Série</td>';
                                html += '<td>' + candidatura.curso + '</td>';
                                html += '<td>' + (candidatura.evento_nome || 'N/A') + '</td>';
                                html += '<td><span class="badge ' + statusClass + '">' + statusText + '</span></td>';
                                html += '<td>';
                                if (candidatura.status === 'pendente') {
                                    html += '<button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="avaliarCandidatura(' + candidatura.id + ', \'' + candidatura.nome + '\', \'' + (candidatura.telefone || '') + '\', \'' + candidatura.serie + '\', \'' + candidatura.curso + '\', \'' + (candidatura.email || '') + '\', \'' + (candidatura.observacoes || '') + '\')">';
                                    html += '<i class="fas fa-check"></i> Avaliar';
                                    html += '</button>';
                                } else {
                                    html += '<button type="button" class="btn btn-sm btn-outline-info me-1" onclick="verDetalhes(' + candidatura.id + ')">';
                                    html += '<i class="fas fa-eye"></i> Ver';
                                    html += '</button>';
                                }
                                html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirCandidatura(' + candidatura.id + ', \'' + candidatura.nome + '\')">';
                                html += '<i class="fas fa-trash"></i> Excluir';
                                html += '</button>';
                                html += '</td>';
                                html += '</tr>';
                            });

                            html += '</tbody></table></div>';
                            container.innerHTML = html;
                        } else {
                            container.innerHTML = `
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhuma candidatura recebida ainda</h5>
                                    <p class="text-muted">As candidaturas aparecerão aqui quando forem enviadas via QR Code.</p>
                                    <button type="button" class="btn btn-outline-primary" onclick="adicionarCandidaturaTeste()">
                                        <i class="fas fa-plus"></i> Adicionar Candidatura de Teste
                                    </button>
                                </div>
                            `;
                        }
                    } else {
                        container.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <strong>Erro:</strong> ' + (data.message || 'Erro desconhecido') + '</div>';
                    }
                })
                .catch(error => {
                    loading.classList.add('d-none');
                    console.error('Erro de conexão:', error);
                    container.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Erro de Conexão:</strong> ${error.message}<br>
                            <small class="text-muted">Verifique se o servidor está rodando e se não há problemas de rede.</small>
                        </div>
                    `;
                });
        }

        function getStatusClass(status) {
            switch(status) {
                case 'pendente': return 'bg-warning';
                case 'aprovada': return 'bg-success';
                case 'reprovada': return 'bg-danger';
                case 'cancelada': return 'bg-secondary';
                default: return 'bg-light';
            }
        }

        function getStatusText(status) {
            switch(status) {
                case 'pendente': return 'Pendente';
                case 'aprovada': return 'Aprovada';
                case 'reprovada': return 'Reprovada';
                case 'cancelada': return 'Cancelada';
                default: return status;
            }
        }

        function avaliarCandidatura(id, nome, telefone, serie, curso, email, observacoes) {
            document.getElementById('avaliar_candidatura_id').value = id;

            const infoHtml = `
                <strong>Nome:</strong> ${nome}<br>
                <strong>Telefone:</strong> ${telefone}<br>
                <strong>Série:</strong> ${serie}ª Série<br>
                <strong>Curso:</strong> ${curso}<br>
                ${email ? '<strong>Email:</strong> ' + email + '<br>' : ''}
                ${observacoes ? '<strong>Observações:</strong> ' + observacoes : ''}
            `;

            document.getElementById('candidato-info').innerHTML = infoHtml;
            document.getElementById('avaliar_observacao').value = '';

            const modal = new bootstrap.Modal(document.getElementById('avaliarCandidaturaModal'));
            modal.show();
        }

        function verDetalhes(id) {
            // Implementar visualização de detalhes da candidatura
            alert('Funcionalidade de visualização de detalhes será implementada em breve.');
        }

        // Função para adicionar candidatura de teste
        function adicionarCandidaturaTeste() {
            if (!confirm('Deseja adicionar uma candidatura de teste para verificar o funcionamento do sistema?')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'teste');

            document.getElementById('loading').classList.remove('d-none');

            fetch('ajax_candidaturas.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading').classList.add('d-none');

                if (data.success) {
                    mostrarMensagem('success', data.message);
                    carregarCandidaturas();
                } else {
                    mostrarMensagem('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('loading').classList.add('d-none');
                mostrarMensagem('danger', 'Erro ao adicionar candidatura de teste.');
            });
        }

        // Função para excluir candidatura
        function excluirCandidatura(id, nome) {
            if (!confirm(`Tem certeza que deseja excluir a candidatura de ${nome}?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'excluir_candidatura');
            formData.append('candidatura_id', id);

            document.getElementById('loading').classList.remove('d-none');

            fetch('ajax_candidaturas.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading').classList.add('d-none');

                if (data.success) {
                    mostrarMensagem('success', data.message);
                    carregarCandidaturas();
                } else {
                    mostrarMensagem('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('loading').classList.add('d-none');
                mostrarMensagem('danger', 'Erro ao excluir candidatura.');
            });
        }

        // Manipular formulário de avaliação
        document.getElementById('avaliar-candidatura-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('avaliarCandidaturaModal'));
            modal.hide();

            // Mostrar loading
            document.getElementById('loading').classList.remove('d-none');

            fetch('ajax_candidaturas.php?action=avaliar_candidatura', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Esconder loading
                document.getElementById('loading').classList.add('d-none');

                if (data.success) {
                    mostrarMensagem('success', data.message);
                    carregarCandidaturas();
                } else {
                    mostrarMensagem('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('loading').classList.add('d-none');
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
</body>
</html></content>
<parameter name="filePath">c:\laragon\www\onibus\candidaturas.php
