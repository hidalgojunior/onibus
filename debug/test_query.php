<?php
include 'config.php';

// Configurar headers para HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Query - Sistema de Ônibus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            margin: 20px auto;
            padding: 30px;
        }
        .header-card {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .status-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .status-card:hover {
            transform: translateY(-5px);
        }
        .success-icon {
            color: #28a745;
            font-size: 2rem;
        }
        .error-icon {
            color: #dc3545;
            font-size: 2rem;
        }
        .info-card {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #007bff;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .code-display {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
        }
        .btn-custom {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            border-radius: 25px;
            padding: 10px 30px;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-card">
            <h1><i class="fas fa-bus"></i> Teste de Query do Sistema</h1>
            <p class="mb-0">Verificação da conexão e dados do banco</p>
        </div>

        <?php
        try {
            $conn = getDatabaseConnection();

            $query = "SELECT e.*,
                     COUNT(DISTINCT a.id) as total_alunos,
                     COUNT(DISTINCT o.id) as total_onibus,
                     MAX(q.short_code) as short_code,
                     MAX(q.public_url) as public_url
                     FROM eventos e
                     LEFT JOIN alunos a ON a.evento_id = e.id
                     LEFT JOIN onibus o ON o.evento_id = e.id
                     LEFT JOIN qr_codes q ON q.evento_id = e.id
                     GROUP BY e.id
                     ORDER BY e.data_inicio DESC";

            $result = $conn->query($query);

            if ($result) {
                $totalEventos = $result->num_rows;
                ?>
                <div class="card status-card border-success mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle success-icon mb-3"></i>
                        <h4 class="card-title text-success">Query Executada com Sucesso!</h4>
                        <p class="card-text">A conexão com o banco de dados está funcionando perfeitamente.</p>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $totalEventos; ?></div>
                        <div class="stat-label">Total de Eventos</div>
                    </div>
                </div>

                <?php if ($totalEventos > 0) { ?>
                    <div class="card status-card mb-4">
                        <div class="card-header info-card">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Detalhes do Primeiro Evento</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $row = $result->fetch_assoc();
                            ?>
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="stat-number"><?php echo htmlspecialchars($row['nome']); ?></div>
                                    <div class="stat-label">Nome do Evento</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number"><?php echo $row['total_alunos']; ?></div>
                                    <div class="stat-label">Total de Alunos</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number"><?php echo $row['total_onibus']; ?></div>
                                    <div class="stat-label">Total de Ônibus</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number"><?php echo $row['short_code'] ?? 'N/A'; ?></div>
                                    <div class="stat-label">Código QR</div>
                                </div>
                            </div>

                            <?php if ($row['public_url']) { ?>
                                <div class="mt-3">
                                    <strong>URL Pública:</strong>
                                    <div class="code-display"><?php echo htmlspecialchars($row['public_url']); ?></div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="card status-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-code"></i> Query Executada</h5>
                    </div>
                    <div class="card-body">
                        <div class="code-display"><?php echo htmlspecialchars($query); ?></div>
                    </div>
                </div>

            <?php } else { ?>
                <div class="card status-card border-danger">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle error-icon mb-3"></i>
                        <h4 class="card-title text-danger">Erro na Query</h4>
                        <p class="card-text"><?php echo htmlspecialchars($conn->error); ?></p>
                    </div>
                </div>
            <?php }

            $conn->close();

        } catch (Exception $e) { ?>
            <div class="card status-card border-danger">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle error-icon mb-3"></i>
                    <h4 class="card-title text-danger">Erro de Conexão</h4>
                    <p class="card-text"><?php echo htmlspecialchars($e->getMessage()); ?></p>
                </div>
            </div>
        <?php } ?>

        <div class="text-center mt-4">
            <a href="index.php" class="btn-custom">
                <i class="fas fa-home"></i> Voltar ao Início
            </a>
            <a href="presence.php" class="btn-custom ms-2">
                <i class="fas fa-users"></i> Controle de Presença
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html></content>
<parameter name="filePath">c:\laragon\www\onibus\test_query.php
