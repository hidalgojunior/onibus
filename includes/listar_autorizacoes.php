<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Autorizações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h1 class="card-title mb-0">Listar Autorizações</h1>
                        <small>Visualize todas as autorizações geradas</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <a href="autorizacoes.php" class="btn btn-primary">Gerar Nova Autorização</a>
                            <a href="index.php" class="btn btn-secondary">Voltar ao Início</a>
                        </div>

                        <?php
                        include 'config.php';
                        $conn = getDatabaseConnection();

                        // Buscar filtros
                        $aluno_filtro = isset($_GET['aluno']) ? $_GET['aluno'] : '';
                        $tipo_filtro = isset($_GET['tipo']) ? $_GET['tipo'] : '';

                        // Query para buscar autorizações
                        $sql = "SELECT aut.*, a.nome as aluno_nome, a.serie, a.curso,
                                       r.nome as responsavel_nome
                                FROM autorizacoes aut
                                JOIN alunos a ON aut.aluno_id = a.id
                                LEFT JOIN responsaveis r ON aut.responsavel_id = r.id
                                WHERE 1=1";

                        $params = [];
                        $types = '';

                        if (!empty($aluno_filtro)) {
                            $sql .= " AND a.nome LIKE ?";
                            $params[] = "%$aluno_filtro%";
                            $types .= 's';
                        }

                        if (!empty($tipo_filtro)) {
                            $sql .= " AND aut.tipo = ?";
                            $params[] = $tipo_filtro;
                            $types .= 's';
                        }

                        $sql .= " ORDER BY aut.data_geracao DESC";

                        $stmt = $conn->prepare($sql);
                        if (!empty($params)) {
                            $stmt->bind_param($types, ...$params);
                        }
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-striped table-hover">';
                            echo '<thead class="table-dark">';
                            echo '<tr>';
                            echo '<th>Data Geração</th>';
                            echo '<th>Aluno</th>';
                            echo '<th>Série/Curso</th>';
                            echo '<th>Responsável</th>';
                            echo '<th>Tipo</th>';
                            echo '<th>Ações</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            while($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . date('d/m/Y H:i', strtotime($row['data_geracao'])) . '</td>';
                                echo '<td>' . htmlspecialchars($row['aluno_nome']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['serie']) . ' ' . htmlspecialchars($row['curso']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['responsavel_nome'] ?: 'N/A') . '</td>';
                                echo '<td>';
                                if ($row['tipo'] == 'saida') {
                                    echo '<span class="badge bg-success">Saída</span>';
                                } else {
                                    echo '<span class="badge bg-info">Uso de Imagem</span>';
                                }
                                echo '</td>';
                                echo '<td>';
                                echo '<button class="btn btn-sm btn-outline-primary" onclick="visualizarAutorizacao(' . $row['id'] . ')">Visualizar</button>';
                                echo '</td>';
                                echo '</tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-info">Nenhuma autorização encontrada.</div>';
                        }
                        ?>

                        <!-- Modal para visualizar autorização -->
                        <div class="modal fade" id="modalAutorizacao" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Visualizar Autorização</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body" id="modalContent">
                                        <!-- Conteúdo será carregado aqui -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                        <button type="button" class="btn btn-primary" onclick="window.print()">Imprimir</button>
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
        function visualizarAutorizacao(id) {
            // Buscar conteúdo da autorização via AJAX
            fetch('get_autorizacao.php?id=' + id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('modalContent').innerHTML = data;
                    const modal = new bootstrap.Modal(document.getElementById('modalAutorizacao'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao carregar autorização');
                });
        }
    </script>
</body>
</html>
