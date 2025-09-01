<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Banco de Dados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h1 class="card-title mb-0">Atualizar Banco de Dados</h1>
                    </div>
                    <div class="card-body">
                        <p>Esta página corrige o tamanho do campo telefone nas tabelas alunos e responsaveis.</p>

                        <?php
                        // Incluir arquivo de configuração
                        include 'config.php';

                        // Obter configuração do banco
                        $config = getDatabaseConfig();
                        $conn = new mysqli($config['host'], $config['usuario'], $config['senha'], $config['banco']);

                        if ($conn->connect_error) {
                            echo '<div class="alert alert-danger">Erro de conexão: ' . $conn->connect_error . '</div>';
                        } else {
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                // Alterar tamanho do campo telefone
                                $queries = [
                                    "ALTER TABLE alunos MODIFY COLUMN telefone VARCHAR(50)",
                                    "ALTER TABLE responsaveis MODIFY COLUMN telefone VARCHAR(50)"
                                ];

                                $success = true;
                                $errors = [];

                                foreach ($queries as $query) {
                                    if ($conn->query($query) === FALSE) {
                                        $success = false;
                                        $errors[] = $conn->error;
                                    }
                                }

                                if ($success) {
                                    echo '<div class="alert alert-success alert-dismissible fade show">Banco de dados atualizado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                } else {
                                    echo '<div class="alert alert-danger alert-dismissible fade show">Erros durante a atualização:<ul class="mb-0 mt-2">';
                                    foreach ($errors as $error) {
                                        echo '<li>' . $error . '</li>';
                                    }
                                    echo '</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                }
                            } else {
                                echo '<form method="POST">';
                                echo '<button type="submit" class="btn btn-warning">Atualizar Campo Telefone</button>';
                                echo '</form>';
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
