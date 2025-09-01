<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar e Alocar Alunos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h1 class="card-title mb-0">Importar e Alocar Alunos</h1>
                        <small>Importa alunos e os aloca automaticamente nos ônibus disponíveis</small>
                    </div>
                    <div class="card-body">
                        <?php
                        include 'config.php';
                        $conn = getDatabaseConnection();

                        if ($conn->connect_error) {
                            echo '<div class="alert alert-danger">Erro de conexão: ' . $conn->connect_error . '</div>';
                        } else {
                            // Verificar se o evento existe, senão criar
                            $evento_nome = 'Bootcamp Jovem Programador';
                            $evento_query = "SELECT id FROM eventos WHERE nome = '$evento_nome'";
                            $evento_result = $conn->query($evento_query);

                            if ($evento_result->num_rows == 0) {
                                $conn->query("INSERT INTO eventos (nome, data_inicio, data_fim) VALUES ('$evento_nome', '2025-08-27', '2025-09-05')");
                                $evento_id = $conn->insert_id;
                                echo '<div class="alert alert-info">Evento criado: ' . $evento_nome . '</div>';
                            } else {
                                $evento_row = $evento_result->fetch_assoc();
                                $evento_id = $evento_row['id'];
                            }

                            // Verificar ônibus disponíveis
                            $onibus_query = "SELECT id, numero, capacidade FROM onibus WHERE evento_id = $evento_id ORDER BY numero";
                            $onibus_result = $conn->query($onibus_query);

                            if ($onibus_result->num_rows == 0) {
                                // Criar ônibus padrão se não existir
                                $conn->query("INSERT INTO onibus (numero, tipo, capacidade, evento_id, dias_reservados) VALUES (1, 'ônibus', 50, $evento_id, 10)");
                                $onibus_id = $conn->insert_id;
                                echo '<div class="alert alert-info">Ônibus 1 criado com capacidade para 50 alunos.</div>';

                                // Recarregar ônibus
                                $onibus_result = $conn->query($onibus_query);
                            }

                            $onibus = [];
                            while ($row = $onibus_result->fetch_assoc()) {
                                $onibus[] = $row;
                            }

                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $alunos_texto = $_POST['alunos_texto'];
                                $linhas = explode("\n", trim($alunos_texto));
                                $importados = 0;
                                $alocados = 0;
                                $erros = [];

                                foreach ($linhas as $linha) {
                                    $linha = trim($linha);
                                    if (!empty($linha)) {
                                        // Formato esperado: Nome - Série - Curso - Telefone
                                        $partes = explode(' - ', $linha);
                                        if (count($partes) >= 4) {
                                            // Se tem mais de 4 partes, juntar as primeiras como nome
                                            if (count($partes) > 4) {
                                                $nome = trim(implode(' - ', array_slice($partes, 0, -3)));
                                                $serie = trim($partes[count($partes)-3]);
                                                $curso = trim($partes[count($partes)-2]);
                                                $telefone = trim($partes[count($partes)-1]);
                                            } else {
                                                $nome = trim($partes[0]);
                                                $serie = trim($partes[1]);
                                                $curso = trim($partes[2]);
                                                $telefone = trim($partes[3]);
                                            }

                                            // Inserir aluno
                                            $stmt = $conn->prepare("INSERT INTO alunos (nome, serie, curso, telefone) VALUES (?, ?, ?, ?)");
                                            $stmt->bind_param("ssss", $nome, $serie, $curso, $telefone);

                                            if ($stmt->execute()) {
                                                $aluno_id = $conn->insert_id;
                                                $importados++;

                                                // Tentar alocar no primeiro ônibus disponível
                                                $alocado = false;
                                                foreach ($onibus as $bus) {
                                                    // Verificar capacidade atual do ônibus
                                                    $count_query = "SELECT COUNT(*) as ocupados FROM alocacoes_onibus WHERE onibus_id = ? AND evento_id = ?";
                                                    $count_stmt = $conn->prepare($count_query);
                                                    $count_stmt->bind_param("ii", $bus['id'], $evento_id);
                                                    $count_stmt->execute();
                                                    $count_result = $count_stmt->get_result();
                                                    $count_row = $count_result->fetch_assoc();

                                                    if ($count_row['ocupados'] < $bus['capacidade']) {
                                                        // Alocar aluno neste ônibus
                                                        $alloc_stmt = $conn->prepare("INSERT INTO alocacoes_onibus (aluno_id, onibus_id, evento_id) VALUES (?, ?, ?)");
                                                        $alloc_stmt->bind_param("iii", $aluno_id, $bus['id'], $evento_id);

                                                        if ($alloc_stmt->execute()) {
                                                            $alocados++;
                                                            $alocado = true;
                                                            echo "<small class='text-success'>✓ $nome → Ônibus {$bus['numero']}</small><br>";
                                                        }
                                                        $alloc_stmt->close();
                                                        break;
                                                    }
                                                    $count_stmt->close();
                                                }

                                                if (!$alocado) {
                                                    $erros[] = "Não foi possível alocar $nome - todos os ônibus estão lotados";
                                                }
                                            } else {
                                                $erros[] = "Erro ao importar: $nome - " . $stmt->error;
                                            }
                                            $stmt->close();
                                        } elseif (count($partes) == 3) {
                                            // Formato: Nome - Nome - Telefone (caso especial)
                                            $nome = trim($partes[0] . ' - ' . $partes[1]);
                                            $telefone = trim($partes[2]);
                                            $serie = 'Não informado';
                                            $curso = 'Não informado';

                                            $stmt = $conn->prepare("INSERT INTO alunos (nome, serie, curso, telefone) VALUES (?, ?, ?, ?)");
                                            $stmt->bind_param("ssss", $nome, $serie, $curso, $telefone);

                                            if ($stmt->execute()) {
                                                $aluno_id = $conn->insert_id;
                                                $importados++;

                                                // Tentar alocar no primeiro ônibus disponível
                                                $alocado = false;
                                                foreach ($onibus as $bus) {
                                                    $count_query = "SELECT COUNT(*) as ocupados FROM alocacoes_onibus WHERE onibus_id = ? AND evento_id = ?";
                                                    $count_stmt = $conn->prepare($count_query);
                                                    $count_stmt->bind_param("ii", $bus['id'], $evento_id);
                                                    $count_stmt->execute();
                                                    $count_result = $count_stmt->get_result();
                                                    $count_row = $count_result->fetch_assoc();

                                                    if ($count_row['ocupados'] < $bus['capacidade']) {
                                                        $alloc_stmt = $conn->prepare("INSERT INTO alocacoes_onibus (aluno_id, onibus_id, evento_id) VALUES (?, ?, ?)");
                                                        $alloc_stmt->bind_param("iii", $aluno_id, $bus['id'], $evento_id);

                                                        if ($alloc_stmt->execute()) {
                                                            $alocados++;
                                                            $alocado = true;
                                                            echo "<small class='text-success'>✓ $nome → Ônibus {$bus['numero']}</small><br>";
                                                        }
                                                        $alloc_stmt->close();
                                                        break;
                                                    }
                                                    $count_stmt->close();
                                                }

                                                if (!$alocado) {
                                                    $erros[] = "Não foi possível alocar $nome - todos os ônibus estão lotados";
                                                }
                                            } else {
                                                $erros[] = "Erro ao importar: $nome - " . $stmt->error;
                                            }
                                            $stmt->close();
                                        } else {
                                            $erros[] = "Formato inválido (menos de 3 partes): $linha";
                                        }
                                    }
                                }

                                echo '<hr>';
                                if ($importados > 0) {
                                    echo '<div class="alert alert-success">' . $importados . ' alunos importados com sucesso!</div>';
                                }
                                if ($alocados > 0) {
                                    echo '<div class="alert alert-success">' . $alocados . ' alunos alocados em ônibus!</div>';
                                }
                                if (!empty($erros)) {
                                    echo '<div class="alert alert-warning"><strong>Erros:</strong><ul>';
                                    foreach ($erros as $erro) {
                                        echo '<li>' . $erro . '</li>';
                                    }
                                    echo '</ul></div>';
                                }
                            }

                            // Mostrar status atual
                            echo '<hr>';
                            echo '<h5>Status Atual:</h5>';

                            $total_alunos = $conn->query("SELECT COUNT(*) as total FROM alunos")->fetch_assoc()['total'];
                            $total_alocacoes = $conn->query("SELECT COUNT(*) as total FROM alocacoes_onibus WHERE evento_id = $evento_id")->fetch_assoc()['total'];

                            echo '<div class="row">';
                            echo '<div class="col-md-4">';
                            echo '<div class="card text-center">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . $total_alunos . '</h5>';
                            echo '<p class="card-text">Alunos Cadastrados</p>';
                            echo '</div></div></div>';

                            echo '<div class="col-md-4">';
                            echo '<div class="card text-center">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . $total_alocacoes . '</h5>';
                            echo '<p class="card-text">Alunos Alocados</p>';
                            echo '</div></div></div>';

                            echo '<div class="col-md-4">';
                            echo '<div class="card text-center">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . count($onibus) . '</h5>';
                            echo '<p class="card-text">Ônibus Disponíveis</p>';
                            echo '</div></div></div>';
                            echo '</div>';
                        }
                        ?>

                        <hr>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="alunos_texto" class="form-label">Cole aqui a lista de alunos (um por linha):</label>
                                <textarea class="form-control" id="alunos_texto" name="alunos_texto" rows="15" placeholder="Nome - Série - Curso - Telefone
Exemplo:
João Silva - 1ª Série - Informática - (11) 99999-9999
Maria Santos - 2ª Série - Administração - (11) 88888-8888"></textarea>
                                <div class="form-text">
                                    Formato esperado: <strong>Nome - Série - Curso - Telefone</strong><br>
                                    Cada aluno em uma linha separada.
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload"></i> Importar e Alocar Alunos
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html></content>
<parameter name="filePath">c:\laragon\www\onibus\import_allocate_students.php
