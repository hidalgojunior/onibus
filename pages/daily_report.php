<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Diário de Presença</title>
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
                            <i class="fas fa-chart-bar me-2"></i>Relatório Diário de Presença
                        </h1>
                        <small>Veja todos os alunos alocados por ônibus, com status de embarque e retorno</small>
                    </div>
                    <div class="card-body">
                        <?php
                        // Incluir arquivo de configuração
                        include 'config.php';

                        // Obter conexão com o banco
                        $conn = getDatabaseConnection();

                        // Buscar eventos disponíveis
                        $evento_query = "SELECT id, nome FROM eventos ORDER BY data_inicio DESC";
                        $evento_result = $conn->query($evento_query);

                        if ($evento_result->num_rows == 0) {
                            echo '<div class="alert alert-warning">';
                            echo '<i class="fas fa-exclamation-triangle me-2"></i>';
                            echo '<strong>Nenhum evento encontrado.</strong><br>';
                            echo 'Configure um evento primeiro no <a href="eventos.php" class="alert-link">Gerenciamento de Eventos</a>.';
                            echo '</div>';
                        } else {
                            // Selecionar evento (usar o primeiro como padrão ou o selecionado via POST)
                            $evento_selecionado = isset($_POST['evento_id']) ? $_POST['evento_id'] : '';
                            if (empty($evento_selecionado)) {
                                $evento_result->data_seek(0);
                                $first_evento = $evento_result->fetch_assoc();
                                $evento_selecionado = $first_evento['id'];
                            }

                            $evento_query_single = "SELECT id, nome FROM eventos WHERE id = $evento_selecionado";
                            $evento_single_result = $conn->query($evento_query_single);
                            $evento = $evento_single_result->fetch_assoc();
                            $evento_id = $evento['id'];

                            $onibus_query = "SELECT id, numero FROM onibus WHERE evento_id = $evento_id";
                            $onibus_result = $conn->query($onibus_query);

                            if ($onibus_result->num_rows == 0) {
                                echo '<div class="alert alert-warning">';
                                echo '<i class="fas fa-exclamation-triangle me-2"></i>';
                                echo '<strong>Nenhum ônibus encontrado para o evento "' . htmlspecialchars($evento['nome']) . '".</strong><br>';
                                echo 'Cadastre ônibus para este evento no <a href="onibus.php" class="alert-link">Gerenciamento de Ônibus</a>.';
                                echo '</div>';
                            } else {
                                // Formulário para seleção de data e ônibus
                                $data_selecionada = isset($_POST['data']) ? $_POST['data'] : date('Y-m-d');
                                $onibus_selecionado = isset($_POST['onibus_id']) ? $_POST['onibus_id'] : '';

                                echo '<form method="POST" class="mb-4">';
                                echo '<div class="row">';
                                echo '<div class="col-md-4">';
                                echo '<label for="evento_id" class="form-label">Evento</label>';
                                echo '<select class="form-control" id="evento_id" name="evento_id" required>';
                                echo '<option value="">Selecione o evento</option>';

                                $evento_result->data_seek(0); // Reset pointer
                                while ($evento_option = $evento_result->fetch_assoc()) {
                                    $selected = ($evento_selecionado == $evento_option['id']) ? 'selected' : '';
                                    echo '<option value="' . $evento_option['id'] . '" ' . $selected . '>' . htmlspecialchars($evento_option['nome']) . '</option>';
                                }
                                echo '</select>';
                                echo '</div>';
                                echo '<div class="col-md-4">';
                                echo '<label for="data" class="form-label">Data</label>';
                                echo '<input type="date" class="form-control" id="data" name="data" value="' . $data_selecionada . '" required>';
                                echo '</div>';
                                echo '<div class="col-md-4">';
                                echo '<label for="onibus_id" class="form-label">Ônibus</label>';
                                echo '<select class="form-control" id="onibus_id" name="onibus_id" required>';
                                echo '<option value="">Selecione o ônibus</option>';

                                $onibus_result->data_seek(0); // Reset pointer
                                while ($onibus = $onibus_result->fetch_assoc()) {
                                    $selected = ($onibus_selecionado == $onibus['id']) ? 'selected' : '';
                                    echo '<option value="' . $onibus['id'] . '" ' . $selected . '>Ônibus ' . $onibus['numero'] . '</option>';
                                }
                                echo '</select>';
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="row mt-3">';
                                echo '<div class="col-12 text-center">';
                                echo '<button type="submit" class="btn btn-info">Gerar Relatório</button>';
                                echo '</div>';
                                echo '</div>';
                                echo '</form>';

                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($onibus_selecionado) && !empty($evento_selecionado)) {
                                    // Buscar TODOS os alunos: alocados no ônibus E não alocados no evento
                                    $alunos_query = "
                                        SELECT a.id, a.nome, a.serie, a.curso, a.telefone,
                                               ao.onibus_id,
                                               p.presenca_embarque, p.presenca_retorno, p.data,
                                               CASE
                                                   WHEN ao.onibus_id IS NULL THEN 'nao_alocado'
                                                   WHEN ao.onibus_id = $onibus_selecionado THEN 'alocado_este_onibus'
                                                   ELSE 'alocado_outro_onibus'
                                               END as status_alocacao
                                        FROM alunos a
                                        LEFT JOIN alocacoes_onibus ao ON a.id = ao.aluno_id AND ao.evento_id = $evento_selecionado
                                        LEFT JOIN presencas p ON a.id = p.aluno_id AND p.data = '$data_selecionada' AND p.evento_id = $evento_selecionado
                                        WHERE ao.onibus_id IS NULL OR ao.onibus_id = $onibus_selecionado
                                        ORDER BY
                                            CASE WHEN ao.onibus_id IS NULL THEN 1 ELSE 0 END,
                                            a.nome
                                    ";

                                    $alunos_result = $conn->query($alunos_query);

                                    if ($alunos_result->num_rows > 0) {
                                        $total_alunos = $alunos_result->num_rows;
                                        $alunos_alocados = 0;
                                        $alunos_nao_alocados = 0;
                                        $embarcaram = 0;
                                        $nao_embarcaram = 0;
                                        $retornaram = 0;

                                        echo '<div class="row mb-3">';
                                        echo '<div class="col-md-12">';
                                        echo '<h4>';
                                        echo '<i class="fas fa-bus me-2"></i>';
                                        echo 'Relatório do dia ' . formatarDataBR($data_selecionada, 'curta') . ' - Ônibus ' . $onibus_selecionado;
                                        echo '<br><small class="text-muted">Evento: ' . htmlspecialchars($evento['nome']) . '</small>';
                                        echo '</h4>';
                                        echo '<small class="text-muted">Mostrando alunos alocados neste ônibus e alunos não alocados no evento</small>';
                                        echo '</div>';
                                        echo '</div>';

                                        echo '<div class="table-responsive">';
                                        echo '<table class="table table-striped table-hover">';
                                        echo '<thead class="table-dark">';
                                        echo '<tr>';
                                        echo '<th>Nome</th>';
                                        echo '<th>Série</th>';
                                        echo '<th>Curso</th>';
                                        echo '<th>Telefone</th>';
                                        echo '<th>Status Alocação</th>';
                                        echo '<th>Status Embarque</th>';
                                        echo '<th>Status Retorno</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($aluno = $alunos_result->fetch_assoc()) {
                                            // Determinar status de alocação
                                            $status_alocacao = '';
                                            $class_alocacao = '';

                                            if ($aluno['status_alocacao'] == 'nao_alocado') {
                                                $status_alocacao = 'Não Alocado';
                                                $class_alocacao = 'secondary';
                                            } elseif ($aluno['status_alocacao'] == 'alocado_este_onibus') {
                                                $status_alocacao = 'Neste Ônibus';
                                                $class_alocacao = 'primary';
                                            } else {
                                                $status_alocacao = 'Outro Ônibus';
                                                $class_alocacao = 'info';
                                            }

                                            // Verificar presença de embarque
                                            $status_embarque = 'Não embarcou';
                                            $status_retorno = 'Não retornou';
                                            $class_embarque = 'danger';
                                            $class_retorno = 'warning';

                                            // Contar alunos alocados vs não alocados
                                            if ($aluno['status_alocacao'] == 'nao_alocado') {
                                                $alunos_nao_alocados++;
                                            } else {
                                                $alunos_alocados++;
                                                // Se há registro de presença para esta data específica
                                                if ($aluno['presenca_embarque'] !== null) {
                                                    if ($aluno['presenca_embarque'] == 1) {
                                                        $status_embarque = 'Embarcou';
                                                        $class_embarque = 'success';
                                                        $embarcaram++;
                                                    } else {
                                                        $nao_embarcaram++;
                                                    }
                                                } else {
                                                    // Não há registro de presença para esta data
                                                    $nao_embarcaram++;
                                                }

                                                // Verificar presença de retorno
                                                if ($aluno['presenca_retorno'] !== null && $aluno['presenca_retorno'] == 1) {
                                                    $status_retorno = 'Retornou';
                                                    $class_retorno = 'success';
                                                    $retornaram++;
                                                }
                                            }

                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($aluno['nome']) . '</td>';
                                            echo '<td>' . htmlspecialchars($aluno['serie']) . '</td>';
                                            echo '<td>' . htmlspecialchars($aluno['curso']) . '</td>';
                                            echo '<td>' . htmlspecialchars($aluno['telefone']) . '</td>';
                                            echo '<td><span class="badge bg-' . $class_alocacao . '">' . $status_alocacao . '</span></td>';
                                            echo '<td><span class="badge bg-' . $class_embarque . '">' . $status_embarque . '</span></td>';
                                            echo '<td><span class="badge bg-' . $class_retorno . '">' . $status_retorno . '</span></td>';
                                            echo '</tr>';
                                        }

                                        echo '</tbody>';
                                        echo '</table>';
                                        echo '</div>';

                                        // Estatísticas melhoradas
                                        echo '<div class="row mt-4">';
                                        echo '<div class="col-md-3">';
                                        echo '<div class="card bg-primary text-white">';
                                        echo '<div class="card-body text-center">';
                                        echo '<h5 class="card-title"><i class="fas fa-users"></i> Total Alunos</h5>';
                                        echo '<h2>' . $total_alunos . '</h2>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '<div class="col-md-3">';
                                        echo '<div class="card bg-info text-white">';
                                        echo '<div class="card-body text-center">';
                                        echo '<h5 class="card-title"><i class="fas fa-bus"></i> Alocados</h5>';
                                        echo '<h2>' . $alunos_alocados . '</h2>';
                                        echo '<small>(' . round(($total_alunos > 0 ? ($alunos_alocados/$total_alunos)*100 : 0), 1) . '%)</small>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '<div class="col-md-3">';
                                        echo '<div class="card bg-secondary text-white">';
                                        echo '<div class="card-body text-center">';
                                        echo '<h5 class="card-title"><i class="fas fa-user-times"></i> Não Alocados</h5>';
                                        echo '<h2>' . $alunos_nao_alocados . '</h2>';
                                        echo '<small>(' . round(($total_alunos > 0 ? ($alunos_nao_alocados/$total_alunos)*100 : 0), 1) . '%)</small>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '<div class="col-md-3">';
                                        echo '<div class="card bg-success text-white">';
                                        echo '<div class="card-body text-center">';
                                        echo '<h5 class="card-title"><i class="fas fa-arrow-up"></i> Embarcaram</h5>';
                                        echo '<h2>' . $embarcaram . '</h2>';
                                        echo '<small>(' . round(($alunos_alocados > 0 ? ($embarcaram/$alunos_alocados)*100 : 0), 1) . '% dos alocados)</small>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';

                                        // Segunda linha de estatísticas
                                        echo '<div class="row mt-3">';
                                        echo '<div class="col-md-4">';
                                        echo '<div class="card bg-danger text-white">';
                                        echo '<div class="card-body text-center">';
                                        echo '<h5 class="card-title"><i class="fas fa-times"></i> Faltaram</h5>';
                                        echo '<h2>' . $nao_embarcaram . '</h2>';
                                        echo '<small>(' . round(($total_alunos > 0 ? ($nao_embarcaram/$total_alunos)*100 : 0), 1) . '%)</small>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '<div class="col-md-4">';
                                        echo '<div class="card bg-info text-white">';
                                        echo '<div class="card-body text-center">';
                                        echo '<h5 class="card-title"><i class="fas fa-arrow-down"></i> Retornaram</h5>';
                                        echo '<h2>' . $retornaram . '</h2>';
                                        echo '<small>(' . round(($embarcaram > 0 ? ($retornaram/$embarcaram)*100 : 0), 1) . '% dos que embarcaram)</small>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '<div class="col-md-4">';
                                        echo '<div class="card bg-warning text-white">';
                                        echo '<div class="card-body text-center">';
                                        echo '<h5 class="card-title"><i class="fas fa-exclamation-triangle"></i> Alocados neste Ônibus</h5>';
                                        echo '<h2>' . $alunos_alocados . '</h2>';
                                        echo '<small>Alocados no ônibus selecionado</small>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';

                                        // Botões de ação
                                        echo '<div class="text-center mt-4">';
                                        echo '<button onclick="window.print()" class="btn btn-secondary me-2">';
                                        echo '<i class="fas fa-print"></i> Imprimir Relatório';
                                        echo '</button>';
                                        echo '<button onclick="exportToPDF()" class="btn btn-danger me-2">';
                                        echo '<i class="fas fa-file-pdf"></i> Exportar PDF';
                                        echo '</button>';
                                        echo '<a href="index.php" class="btn btn-primary">Voltar ao Início</a>';
                                        echo '</div>';

                                    } else {
                                        echo '<div class="alert alert-info">';
                                        echo '<i class="fas fa-info-circle me-2"></i>';
                                        echo 'Nenhum aluno encontrado para os critérios selecionados.';
                                        echo '<br><small>Nota: Este relatório mostra alunos alocados no ônibus selecionado E alunos não alocados no evento.</small>';
                                        echo '</div>';
                                    }
                                }
                            }
                        }

                        $conn->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportToPDF() {
            // Criar uma nova janela com o conteúdo otimizado para impressão
            const printWindow = window.open('', '_blank');
            const currentDate = new Date().toLocaleDateString('pt-BR');

            printWindow.document.write(`
                <!DOCTYPE html>
                <html lang="pt-BR">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Relatório Diário - ${currentDate}</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            color: #333;
                        }
                        .header {
                            text-align: center;
                            border-bottom: 2px solid #007bff;
                            padding-bottom: 10px;
                            margin-bottom: 20px;
                        }
                        .stats {
                            display: flex;
                            justify-content: space-around;
                            margin: 20px 0;
                            flex-wrap: wrap;
                        }
                        .stat-card {
                            border: 1px solid #ddd;
                            padding: 10px;
                            margin: 5px;
                            border-radius: 5px;
                            text-align: center;
                            min-width: 120px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 20px;
                        }
                        th, td {
                            border: 1px solid #ddd;
                            padding: 8px;
                            text-align: left;
                        }
                        th {
                            background-color: #f8f9fa;
                            font-weight: bold;
                        }
                        .badge {
                            padding: 4px 8px;
                            border-radius: 4px;
                            color: white;
                            font-size: 12px;
                        }
                        .bg-success { background-color: #28a745; }
                        .bg-danger { background-color: #dc3545; }
                        .bg-warning { background-color: #ffc107; color: #000; }
                        .footer {
                            margin-top: 30px;
                            text-align: center;
                            font-size: 12px;
                            color: #666;
                        }
                        @media print {
                            body { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h2>Relatório Diário do Sistema de Ônibus</h2>
                        <p>Data: ${currentDate}</p>
                    </div>
            `);

            // Capturar o conteúdo atual da página
            const reportContent = document.querySelector('.container');
            if (reportContent) {
                const tables = reportContent.querySelectorAll('table');
                const headers = reportContent.querySelectorAll('h4');
                const stats = reportContent.querySelectorAll('.row.mb-4, .row.mt-4');

                // Adicionar cabeçalho
                if (headers.length > 0) {
                    printWindow.document.write('<h3>' + headers[0].innerText + '</h3>');
                }

                // Adicionar estatísticas
                if (stats.length > 0) {
                    printWindow.document.write('<div class="stats">');
                    const statCards = stats[0].querySelectorAll('.card');
                    statCards.forEach(card => {
                        const title = card.querySelector('.card-title').innerText;
                        const value = card.querySelector('h2, h3').innerText;
                        const subtitle = card.querySelector('small') ? card.querySelector('small').innerText : '';
                        printWindow.document.write(`
                            <div class="stat-card">
                                <strong>${title}</strong><br>
                                <span style="font-size: 24px;">${value}</span><br>
                                <small>${subtitle}</small>
                            </div>
                        `);
                    });
                    printWindow.document.write('</div>');
                }

                // Adicionar tabela
                if (tables.length > 0) {
                    printWindow.document.write('<h4>Lista de Alunos</h4>');
                    printWindow.document.write(tables[0].outerHTML);
                }
            }

            printWindow.document.write(`
                    <div class="footer">
                        <p>Relatório gerado automaticamente pelo Sistema de Gestão de Ônibus</p>
                        <p>Data de geração: ${new Date().toLocaleString('pt-BR')}</p>
                    </div>
                </body>
                </html>
            `);

            printWindow.document.close();

            // Aguardar um pouco e então abrir o diálogo de impressão
            setTimeout(() => {
                printWindow.print();
            }, 500);
        }
    </script>
</body>
</html>
