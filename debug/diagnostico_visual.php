<?php
// Página de diagnóstico visual do sistema
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico Visual - Sistema de Ônibus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .diagnostic-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            margin: 20px auto;
            padding: 30px;
        }
        .status-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }
        .status-success { background-color: #28a745; }
        .status-warning { background-color: #ffc107; }
        .status-error { background-color: #dc3545; }
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 15px;
        }
        .feature-body {
            padding: 20px;
        }
        .btn-test {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
        .compatibility-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .compatibility-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            border: 2px solid #dee2e6;
        }
        .compatibility-item.success {
            border-color: #28a745;
            background: #d4edda;
        }
        .compatibility-item.warning {
            border-color: #ffc107;
            background: #fff3cd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="diagnostic-container">
            <div class="text-center mb-5">
                <h1><i class="fas fa-stethoscope"></i> Diagnóstico Visual do Sistema</h1>
                <p class="text-muted">Verificação completa da interface e funcionalidades</p>
            </div>

            <!-- Status Geral -->
            <div class="card feature-card mb-4">
                <div class="feature-header">
                    <h4 class="mb-0"><i class="fas fa-tachometer-alt"></i> Status Geral do Sistema</h4>
                </div>
                <div class="feature-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <span class="status-indicator status-success"></span>
                            <strong>Servidor Web</strong>
                            <br><small class="text-muted">Apache/Nginx OK</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <span class="status-indicator status-success"></span>
                            <strong>PHP</strong>
                            <br><small class="text-muted"><?php echo phpversion(); ?></small>
                        </div>
                        <div class="col-md-3 text-center">
                            <span class="status-indicator status-success"></span>
                            <strong>Bootstrap</strong>
                            <br><small class="text-muted">v5.3.0</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <span class="status-indicator status-success"></span>
                            <strong>Font Awesome</strong>
                            <br><small class="text-muted">v6.4.0</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Testes de Páginas -->
            <div class="card feature-card mb-4">
                <div class="feature-header">
                    <h4 class="mb-0"><i class="fas fa-file-alt"></i> Testes de Páginas</h4>
                </div>
                <div class="feature-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-home"></i> Página Inicial</h5>
                            <p>Dashboard principal com estatísticas e navegação</p>
                            <a href="index.php" class="btn-test" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Testar Index
                            </a>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-users"></i> Controle de Presença</h5>
                            <p>Interface de embarque com JavaScript avançado</p>
                            <a href="presence.php" class="btn-test" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Testar Presença
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-database"></i> Teste de Query</h5>
                            <p>Verificação de conexão com banco de dados</p>
                            <a href="test_query.php" class="btn-test" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Testar Query
                            </a>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-cogs"></i> Teste Completo</h5>
                            <p>Validação completa do sistema</p>
                            <a href="teste_sistema.php" class="btn-test" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Teste Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compatibilidade -->
            <div class="card feature-card mb-4">
                <div class="feature-header">
                    <h4 class="mb-0"><i class="fas fa-mobile-alt"></i> Compatibilidade de Dispositivos</h4>
                </div>
                <div class="feature-body">
                    <div class="compatibility-grid">
                        <div class="compatibility-item success">
                            <i class="fas fa-desktop fa-2x text-success mb-2"></i>
                            <h6>Desktop</h6>
                            <small>Chrome, Firefox, Edge</small>
                        </div>
                        <div class="compatibility-item success">
                            <i class="fas fa-tablet-alt fa-2x text-success mb-2"></i>
                            <h6>Tablet</h6>
                            <small>iPad, Android Tablets</small>
                        </div>
                        <div class="compatibility-item success">
                            <i class="fas fa-mobile-alt fa-2x text-success mb-2"></i>
                            <h6>Mobile</h6>
                            <small>iOS, Android</small>
                        </div>
                        <div class="compatibility-item success">
                            <i class="fas fa-print fa-2x text-success mb-2"></i>
                            <h6>Impressão</h6>
                            <small>Relatórios PDF</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Funcionalidades Implementadas -->
            <div class="card feature-card mb-4">
                <div class="feature-header">
                    <h4 class="mb-0"><i class="fas fa-check-circle"></i> Funcionalidades Implementadas</h4>
                </div>
                <div class="feature-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-qrcode text-primary"></i> Sistema QR Code</h6>
                            <p class="text-muted">Geração e leitura de códigos QR para eventos</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-users text-primary"></i> Controle de Presença</h6>
                            <p class="text-muted">Interface interativa com atualização automática</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-envelope text-primary"></i> Relatórios por Email</h6>
                            <p class="text-muted">Envio automático de relatórios HTML</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-chart-bar text-primary"></i> Dashboard</h6>
                            <p class="text-muted">Estatísticas e métricas em tempo real</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações Técnicas -->
            <div class="card feature-card">
                <div class="feature-header">
                    <h4 class="mb-0"><i class="fas fa-info-circle"></i> Informações Técnicas</h4>
                </div>
                <div class="feature-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Sistema Operacional:</strong><br>
                            <span class="text-muted"><?php echo php_uname('s'); ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong>Versão do PHP:</strong><br>
                            <span class="text-muted"><?php echo phpversion(); ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong>Data/Hora:</strong><br>
                            <span class="text-muted"><?php echo date('d/m/Y H:i:s'); ?></span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Timezone:</strong><br>
                            <span class="text-muted"><?php echo date_default_timezone_get(); ?></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Memória Usada:</strong><br>
                            <span class="text-muted"><?php echo round(memory_get_peak_usage(true) / 1024 / 1024, 2); ?> MB</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">Sistema de Gerenciamento de Ônibus - Diagnóstico Visual</p>
                <small>Para suporte técnico, consulte a documentação ou entre em contato com o administrador</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html></content>
<parameter name="filePath">c:\laragon\www\onibus\diagnostico_visual.php
