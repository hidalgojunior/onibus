<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Retorno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h1 class="card-title mb-0">Controle de Retorno</h1>
                        <small>Marque os alunos que retornaram no ônibus</small>
                    </div>
                    <div class="card-body">
                        <?php
                        // Incluir arquivo de configuração
                        include 'config.php';

                        // Obter conexão com o banco
                        $conn = getDatabaseConnection();

                        // Verificar se há evento e ônibus
                        $evento_query = "SELECT id, nome FROM eventos WHERE nome = 'Bootcamp Jovem Programador'";
                        $evento_result = $conn->query($evento_query);

                        if ($evento_result->num_rows == 0) {
                            echo '<div class="alert alert-warning">Nenhum evento encontrado. Configure o evento primeiro.</div>';
                        } else {
                            $evento = $evento_result->fetch_assoc();
                            $evento_id = $evento['id'];

                            $onibus_query = "SELECT id, numero FROM onibus WHERE evento_id = $evento_id";
                            $onibus_result = $conn->query($onibus_query);

                            if ($onibus_result->num_rows == 0) {
                                echo '<div class="alert alert-warning">Nenhum ônibus encontrado para o evento.</div>';
                            } else {
                                // Formulário para seleção de data e ônibus
                                $data_selecionada = isset($_POST['data']) ? $_POST['data'] : date('Y-m-d');
                                $onibus_selecionado = isset($_POST['onibus_id']) ? $_POST['onibus_id'] : '';

                                echo '<form method="POST" class="mb-4">';
                                echo '<div class="row">';
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
                                echo '<div class="col-md-4 d-flex align-items-end">';
                                echo '<button type="submit" class="btn btn-warning">Carregar Alunos</button>';
                                echo '</div>';
                                echo '</div>';
                                echo '</form>';

                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($onibus_selecionado)) {
                                    if (isset($_POST['salvar_retorno'])) {
                                        // Salvar retorno dos alunos
                                        if (isset($_POST['alunos_retorno'])) {
                                            foreach ($_POST['alunos_retorno'] as $aluno_id => $status) {
                                                if ($status == 'retornou') {
                                                    // Verificar se já existe presença
                                                    $check_query = "SELECT id FROM presencas WHERE aluno_id = $aluno_id AND data = '$data_selecionada'";
                                                    $check_result = $conn->query($check_query);

                                                    if ($check_result->num_rows == 0) {
                                                        $conn->query("INSERT INTO presencas (aluno_id, evento_id, data, presenca_retorno) VALUES ($aluno_id, $evento_id, '$data_selecionada', 1)");
                                                    } else {
                                                        $conn->query("UPDATE presencas SET presenca_retorno = 1 WHERE aluno_id = $aluno_id AND data = '$data_selecionada'");
                                                    }
                                                }
                                            }
                                        }
                                        echo '<div class="alert alert-success alert-dismissible fade show">Retorno registrado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                    }

                                    // Buscar alunos que embarcaram no dia e ônibus selecionados
                                    $alunos_query = "
                                        SELECT a.id, a.nome, a.serie, a.curso, a.telefone,
                                               p.presenca_embarque, p.presenca_retorno
                                        FROM alunos a
                                        INNER JOIN alocacoes_onibus ao ON a.id = ao.aluno_id
                                        LEFT JOIN presencas p ON a.id = p.aluno_id AND p.data = '$data_selecionada'
                                        WHERE ao.onibus_id = $onibus_selecionado AND ao.evento_id = $evento_id
                                        AND (p.presenca_embarque = 1 OR p.presenca_embarque IS NULL)
                                        ORDER BY a.nome
                                    ";

                                    $alunos_result = $conn->query($alunos_query);

                                    if ($alunos_result->num_rows > 0) {
                                        echo '<form method="POST">';
                                        echo '<input type="hidden" name="data" value="' . $data_selecionada . '">';
                                        echo '<input type="hidden" name="onibus_id" value="' . $onibus_selecionado . '">';
                                        echo '<input type="hidden" name="salvar_retorno" value="1">';

                                        echo '<div class="row mb-3">';
                                        echo '<div class="col-md-12">';
                                        echo '<h4>Alunos que embarcaram em ' . formatarDataBR($data_selecionada, 'curta') . ' - Ônibus ' . $onibus_selecionado . '</h4>';
                                        echo '<p class="text-muted">Marque apenas os alunos que retornaram no ônibus</p>';
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
                                        echo '<th>Embarcou</th>';
                                        echo '<th>Retornou?</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($aluno = $alunos_result->fetch_assoc()) {
                                            $embarcou = $aluno['presenca_embarque'] ? 'Sim' : 'Não registrado';
                                            $retornou_checked = $aluno['presenca_retorno'] ? 'checked' : '';

                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($aluno['nome']) . '</td>';
                                            echo '<td>' . htmlspecialchars($aluno['serie']) . '</td>';
                                            echo '<td>' . htmlspecialchars($aluno['curso']) . '</td>';
                                            echo '<td>' . htmlspecialchars($aluno['telefone']) . '</td>';
                                            echo '<td><span class="badge bg-' . ($aluno['presenca_embarque'] ? 'success' : 'secondary') . '">' . $embarcou . '</span></td>';
                                            echo '<td>';
                                            if ($aluno['presenca_embarque']) {
                                                echo '<div class="form-check">';
                                                echo '<input class="form-check-input" type="checkbox" name="alunos_retorno[' . $aluno['id'] . ']" value="retornou" id="retorno' . $aluno['id'] . '" ' . $retornou_checked . '>';
                                                echo '<label class="form-check-label" for="retorno' . $aluno['id'] . '">Retornou</label>';
                                                echo '</div>';
                                            } else {
                                                echo '<span class="text-muted">Não embarcou</span>';
                                            }
                                            echo '</td>';
                                            echo '</tr>';
                                        }

                                        echo '</tbody>';
                                        echo '</table>';
                                        echo '</div>';

                                        echo '<div class="text-center mt-4">';
                                        echo '<button type="submit" class="btn btn-warning">Salvar Retorno</button>';
                                        echo '<a href="daily_report.php" class="btn btn-info ms-2">Ver Relatório</a>';
                                        echo '<a href="index.php" class="btn btn-primary ms-2">Voltar ao Início</a>';
                                        echo '</div>';
                                        echo '</form>';

                                    } else {
                                        echo '<div class="alert alert-info">Nenhum aluno embarcou neste ônibus na data selecionada.</div>';
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
</body>
</html>
