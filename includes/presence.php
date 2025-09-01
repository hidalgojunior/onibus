<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Presença - Embarque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            margin: 20px auto;
            padding: 30px;
            max-width: 1200px;
        }

        .page-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        }

        .page-header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 2.2rem;
        }

        .page-header .subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
            margin-top: 5px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.success {
            border-left: 5px solid #28a745;
        }

        .stat-card.danger {
            border-left: 5px solid #dc3545;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stat-card.success .stat-number {
            color: #28a745;
        }

        .stat-card.danger .stat-number {
            color: #dc3545;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .info-banner {
            background: linear-gradient(135deg, #007bff, #6610f2);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }

        .student-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            position: relative;
        }

        .student-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
            background: white;
        }

        .student-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .student-info h5 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .student-details {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-present {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .status-absent {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
        }

        .status-unknown {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn-presence {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-presence:hover {
            transform: scale(1.05);
        }

        .btn-action {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 600;
        }

        .save-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .btn-save {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-save-outline {
            background: transparent;
            border: 2px solid #28a745;
            color: #28a745;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-save-outline:hover {
            background: #28a745;
            color: white;
            transform: translateY(-2px);
        }

        .alert-custom {
            border-radius: 12px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .feedback-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            font-weight: 600;
        }

        .feedback-message.feedback-show {
            transform: translateX(0);
        }

        /* Modal de Substituição */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
            padding: 20px;
        }

        .modal-title {
            font-weight: 600;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .btn-substituir {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-substituir:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                padding: 20px;
            }

            .page-header h1 {
                font-size: 1.8rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .student-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .action-buttons {
                flex-direction: column;
                width: 100%;
            }

            .btn-presence {
                width: 100%;
            }

            .save-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <?php
    // Função para enviar relatório de presença por email
    function enviarRelatorioPresenca($conn, $evento_id, $onibus_id, $data_hoje, $alunos_embarcaram, $todos_alunos_result) {
        // Incluir configuração de email
        include 'config_email.php';

        // Obter configurações de email
        $email_config = getEmailConfig();
        $destinatarios = getEmailDestinatarios();

        // Assunto do email
        $assunto_base = getEmailAssuntoBase();
        $assunto = $assunto_base . " 1 - " . date('d/m/Y', strtotime($data_hoje));

        // Corpo do email em HTML
        $mensagem = "
        <html>
        <head>
            <title>Relatório de Presença</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .stats { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .student-list { margin: 20px 0; }
                .student-item { padding: 10px; border-bottom: 1px solid #dee2e6; }
                .present { color: #28a745; font-weight: bold; }
                .absent { color: #dc3545; font-weight: bold; }
                .footer { background: #6c757d; color: white; padding: 15px; text-align: center; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Relatório de Presença - Ônibus 1</h1>
                <p>Evento: Bootcamp Jovem Programador</p>
                <p>Data: " . date('d/m/Y', strtotime($data_hoje)) . "</p>
            </div>

            <div class='content'>
                <div class='stats'>
                    <h3>Estatísticas do Dia</h3>
                    <p><strong>Total de Alunos:</strong> " . $todos_alunos_result->num_rows . "</p>
                    <p><strong>Alunos que Embarcaram:</strong> " . count($alunos_embarcaram) . "</p>
                    <p><strong>Taxa de Presença:</strong> " . round((count($alunos_embarcaram) / $todos_alunos_result->num_rows) * 100, 1) . "%</p>
                </div>

                <div class='student-list'>
                    <h3>Lista de Presença</h3>";

        // Reset pointer para o início
        $todos_alunos_result->data_seek(0);

        while ($aluno = $todos_alunos_result->fetch_assoc()) {
            $status = in_array($aluno['id'], $alunos_embarcaram) ? 'present' : 'absent';
            $status_text = in_array($aluno['id'], $alunos_embarcaram) ? 'Embarcou' : 'Não embarcou';

            $mensagem .= "
                    <div class='student-item'>
                        <strong>" . htmlspecialchars($aluno['nome']) . "</strong> -
                        <span class='$status'>$status_text</span>
                        " . (!empty($aluno['serie']) ? " | Série: " . htmlspecialchars($aluno['serie']) : "") . "
                        " . (!empty($aluno['curso']) ? " | Curso: " . htmlspecialchars($aluno['curso']) : "") . "
                        " . (!empty($aluno['telefone']) ? " | Tel: " . htmlspecialchars($aluno['telefone']) : "") . "
                    </div>";
        }

        $mensagem .= "
                </div>

                <div class='footer'>
                    <p>Este relatório foi gerado automaticamente pelo Sistema de Controle de Presença</p>
                    <p>Para mais detalhes, acesse o sistema administrativo</p>
                </div>
            </div>
        </body>
        </html>
        ";

        // Headers do email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$email_config['from_name']} <{$email_config['from_email']}>" . "\r\n";
        $headers .= "Reply-To: {$email_config['reply_to']}" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        // Enviar email para cada destinatário
        $emails_enviados = 0;
        $emails_falharam = 0;

        foreach ($destinatarios as $email) {
            if (mail($email, $assunto, $mensagem, $headers)) {
                $emails_enviados++;
                echo '<div class="alert alert-success alert-dismissible fade show">✅ Relatório enviado para ' . $email . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            } else {
                $emails_falharam++;
                echo '<div class="alert alert-danger alert-dismissible fade show">❌ Erro ao enviar relatório para ' . $email . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            }
        }

        // Resumo final
        if ($emails_enviados > 0) {
            echo '<div class="alert alert-info alert-dismissible fade show">📧 Relatório enviado para ' . $emails_enviados . ' destinatário(s)';
            if ($emails_falharam > 0) {
                echo ' (' . $emails_falharam . ' falha(s))';
            }
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
    }
    ?>

    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <div class="main-container">
            <div class="page-header">
                <h1><i class="fas fa-users"></i> Controle de Presença</h1>
                <div class="subtitle">Sistema de embarque e gerenciamento de alunos</div>
            </div>

            <?php
            // Incluir arquivo de configuração
            include 'config.php';

            // Obter conexão com o banco
            $conn = getDatabaseConnection();

            if ($conn->connect_error) {
                echo '<div class="alert alert-danger alert-custom">Erro de conexão: ' . $conn->connect_error . '</div>';
            } else {
                // Verificar se o evento e ônibus existem, senão criar
                $evento_nome = 'Bootcamp Jovem Programador';
                $evento_query = "SELECT id FROM eventos WHERE nome = '$evento_nome'";
                $evento_result = $conn->query($evento_query);

                if ($evento_result->num_rows == 0) {
                    $conn->query("INSERT INTO eventos (nome, data_inicio, data_fim) VALUES ('$evento_nome', '2025-08-27', '2025-09-05')");
                    $evento_id = $conn->insert_id;
                } else {
                    $evento_row = $evento_result->fetch_assoc();
                    $evento_id = $evento_row['id'];
                }

                $onibus_numero = '1';
                $onibus_query = "SELECT id FROM onibus WHERE numero = '$onibus_numero' AND evento_id = $evento_id";
                $onibus_result = $conn->query($onibus_query);

                if ($onibus_result->num_rows == 0) {
                    $conn->query("INSERT INTO onibus (numero, tipo, capacidade, evento_id, dias_reservados) VALUES ('$onibus_numero', 'ônibus', 50, $evento_id, 10)");
                    $onibus_id = $conn->insert_id;
                } else {
                    $onibus_row = $onibus_result->fetch_assoc();
                    $onibus_id = $onibus_row['id'];
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Processar presença
                    if (isset($_POST['alunos'])) {
                        $data_hoje = date('Y-m-d');
                        $alunos_embarcaram = [];

                        // Primeiro, marcar todos como não embarcaram
                        foreach ($_POST['alunos'] as $aluno_id => $status) {
                            if ($status == 'nao_embarcou') {
                                // Remover presença se existir
                                $conn->query("DELETE FROM presencas WHERE aluno_id = $aluno_id AND data = '$data_hoje'");
                            } elseif ($status == 'embarcou') {
                                // Registrar presença
                                $conn->query("INSERT INTO presencas (aluno_id, data, presenca_embarque) VALUES ($aluno_id, '$data_hoje', 1) ON DUPLICATE KEY UPDATE presenca_embarque = 1");
                                $alunos_embarcaram[] = $aluno_id;
                            }
                        }

                        echo '<div class="alert alert-success alert-custom alert-dismissible fade show">Status atualizado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';

                        // Enviar email com relatório de presença apenas se solicitado
                        if (isset($_POST['enviar_email'])) {
                            enviarRelatorioPresenca($conn, $evento_id, $onibus_id, $data_hoje, $alunos_embarcaram, $todos_alunos_result);
                        }
                    }

                    // Processar substituição de aluno
                    if (isset($_POST['substituir_aluno'])) {
                        $aluno_antigo_id = $_POST['aluno_antigo_id'];
                        $novo_nome = trim($_POST['sub_novo_nome']);
                        $nova_serie = trim($_POST['sub_nova_serie']);
                        $novo_curso = trim($_POST['sub_novo_curso']);
                        $novo_telefone = trim($_POST['sub_novo_telefone']);

                        // Validar campos obrigatórios
                        if (!empty($novo_nome) && !empty($nova_serie) && !empty($novo_curso)) {
                            // Inserir novo aluno
                            $insert_stmt = $conn->prepare("INSERT INTO alunos (nome, serie, curso, telefone) VALUES (?, ?, ?, ?)");
                            $insert_stmt->bind_param("ssss", $novo_nome, $nova_serie, $novo_curso, $novo_telefone);

                            if ($insert_stmt->execute()) {
                                $novo_aluno_id = $conn->insert_id;

                                // Remover alocação antiga primeiro (se existir)
                                $delete_alocacao = $conn->prepare("DELETE FROM alocacoes_onibus WHERE aluno_id = ? AND onibus_id = ? AND evento_id = ?");
                                $delete_alocacao->bind_param("iii", $aluno_antigo_id, $onibus_id, $evento_id);
                                $delete_alocacao->execute();
                                $delete_alocacao->close();

                                // Criar nova alocação para o aluno substituto
                                $insert_alocacao = $conn->prepare("INSERT INTO alocacoes_onibus (aluno_id, onibus_id, evento_id) VALUES (?, ?, ?)");
                                $insert_alocacao->bind_param("iii", $novo_aluno_id, $onibus_id, $evento_id);
                                $insert_alocacao->execute();
                                $insert_alocacao->close();

                                // Transferir presenças do aluno antigo para o novo (se existirem)
                                $update_presencas = $conn->prepare("UPDATE presencas SET aluno_id = ? WHERE aluno_id = ? AND evento_id = ?");
                                $update_presencas->bind_param("iii", $novo_aluno_id, $aluno_antigo_id, $evento_id);
                                $update_presencas->execute();
                                $update_presencas->close();

                                echo '<div class="alert alert-success alert-custom alert-dismissible fade show">✅ Aluno substituído com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                            } else {
                                echo '<div class="alert alert-danger alert-custom alert-dismissible fade show">❌ Erro ao substituir aluno: ' . $conn->error . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                            }
                            $insert_stmt->close();
                        } else {
                            echo '<div class="alert alert-warning alert-custom alert-dismissible fade show">⚠️ Nome, série e curso são obrigatórios!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                        }
                    }
                }

                // Definir datas
                $data_hoje = date('Y-m-d');
                $data_ontem = date('Y-m-d', strtotime('-1 day'));

                // Listar alunos alocados no ônibus atual
                $alunos_query = "
                    SELECT a.*,
                           MAX(p_ontem.presenca_embarque) as embarcou_ontem,
                           MAX(p_hoje.presenca_embarque) as presenca_embarque,
                           CASE WHEN ao.id IS NOT NULL THEN 1 ELSE 0 END as alocado_neste_onibus,
                           ao.id as alocacao_id
                    FROM alunos a
                    LEFT JOIN alocacoes_onibus ao ON a.id = ao.aluno_id AND ao.onibus_id = $onibus_id AND ao.evento_id = $evento_id
                    LEFT JOIN presencas p_hoje ON a.id = p_hoje.aluno_id AND p_hoje.data = '$data_hoje'
                    LEFT JOIN presencas p_ontem ON a.id = p_ontem.aluno_id AND p_ontem.data = '$data_ontem'
                    INNER JOIN alocacoes_onibus ao_evento ON a.id = ao_evento.aluno_id AND ao_evento.evento_id = $evento_id
                    GROUP BY a.id, a.nome, a.serie, a.curso, a.telefone, ao.id
                    ORDER BY
                        -- Primeiro: alunos alocados neste ônibus
                        CASE WHEN ao.id IS NOT NULL THEN 0 ELSE 1 END ASC,
                        -- Depois: ordem alfabética por nome
                        a.nome ASC
                ";

                $todos_alunos_result = $conn->query($alunos_query);

                if ($todos_alunos_result && $todos_alunos_result->num_rows > 0) {
                    $total_alocados = 0;
                    $total_nao_alocados = 0;
                    $total_embarcados = 0;
                    $total_nao_embarcados = 0;

                    // Contar estatísticas
                    $todos_alunos_result->data_seek(0);
                    while ($aluno_temp = $todos_alunos_result->fetch_assoc()) {
                        if ($aluno_temp['alocado_neste_onibus']) {
                            $total_alocados++;
                            if ($aluno_temp['presenca_embarque'] == 1) {
                                $total_embarcados++;
                            } else {
                                $total_nao_embarcados++;
                            }
                        } else {
                            $total_nao_alocados++;
                        }
                    }

                    // Estatísticas em tempo real
                    echo '<div class="stats-container">';
                    echo '<div class="stat-card success">';
                    echo '<div class="stat-number" id="stat-total">' . $total_alocados . '</div>';
                    echo '<div class="stat-label">Total de Alunos</div>';
                    echo '</div>';
                    echo '<div class="stat-card success">';
                    echo '<div class="stat-number" id="stat-embarcados">' . $total_embarcados . '</div>';
                    echo '<div class="stat-label">Embarcaram</div>';
                    echo '</div>';
                    echo '<div class="stat-card danger">';
                    echo '<div class="stat-number" id="stat-nao-embarcados">' . $total_nao_embarcados . '</div>';
                    echo '<div class="stat-label">Não Embarcaram</div>';
                    echo '</div>';
                    echo '</div>';

                    // Informações do evento
                    echo '<div class="info-banner">';
                    echo '<i class="fas fa-bus"></i> <strong>Ônibus 1</strong> - Evento: Bootcamp Jovem Programador (27/08 a 05/09)';
                    if ($total_nao_alocados > 0) {
                        echo ' | <i class="fas fa-users"></i> ' . $total_nao_alocados . ' alunos disponíveis para realocação';
                    }
                    echo '</div>';

                    // Formulário de presença
                    echo '<form method="POST" id="presencaForm">';

                    // Reset pointer para o início
                    $todos_alunos_result->data_seek(0);

                    while ($aluno = $todos_alunos_result->fetch_assoc()) {
                        $aluno_id = $aluno['id'];
                        $status_atual = $aluno['presenca_embarque'];
                        $alocado_neste_onibus = $aluno['alocado_neste_onibus'];

                        echo '<div class="student-card">';
                        echo '<div class="student-header">';
                        echo '<div class="student-info">';
                        echo '<h5>' . htmlspecialchars($aluno['nome']) . '</h5>';
                        echo '<div class="student-details">';
                        if (!empty($aluno['serie'])) echo 'Série: ' . htmlspecialchars($aluno['serie']) . ' | ';
                        if (!empty($aluno['curso'])) echo 'Curso: ' . htmlspecialchars($aluno['curso']) . ' | ';
                        if (!empty($aluno['telefone'])) echo 'Tel: ' . htmlspecialchars($aluno['telefone']);
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="status-badge ' . ($status_atual === null ? 'status-unknown' : ($status_atual == 1 ? 'status-present' : 'status-absent')) . '">';
                        echo $status_atual === null ? 'Não registrado' : ($status_atual == 1 ? 'Embarcou' : 'Não embarcou');
                        echo '</div>';
                        echo '</div>';

                        if ($alocado_neste_onibus) {
                            echo '<div class="action-buttons">';
                            echo '<input type="hidden" name="alunos[' . $aluno_id . ']" id="status_' . $aluno_id . '" value="' . ($status_atual == 1 ? 'embarcou' : 'nao_embarcou') . '">';

                            if ($status_atual == 1) {
                                echo '<button type="button" class="btn btn-success btn-presence" id="btn_' . $aluno_id . '" onclick="toggleStatus(' . $aluno_id . ')">Embarcou</button>';
                                echo '<button type="button" class="btn btn-outline-danger btn-presence" onclick="toggleStatus(' . $aluno_id . ')">Não Embarcou</button>';
                            } else {
                                echo '<button type="button" class="btn btn-outline-success btn-presence" onclick="toggleStatus(' . $aluno_id . ')">Embarcou</button>';
                                echo '<button type="button" class="btn btn-danger btn-presence" id="btn_' . $aluno_id . '" onclick="toggleStatus(' . $aluno_id . ')">Não Embarcou</button>';
                            }

                            // Botão para substituir aluno
                            echo '<button type="button" class="btn btn-warning btn-sm ms-2" onclick="abrirModalSubstituicao(' . $aluno_id . ', \'' . htmlspecialchars($aluno['nome']) . '\')" title="Substituir este aluno">';
                            echo '<i class="fas fa-exchange-alt"></i> <small>Substituir</small>';
                            echo '</button>';

                            echo '</div>';
                        } else {
                            echo '<div class="action-buttons">';
                            echo '<button type="submit" name="mover_para_onibus" value="1" class="btn btn-outline-info btn-action" onclick="document.getElementById(\'aluno_mover_id\').value = ' . $aluno_id . '">Mover para este ônibus</button>';
                            echo '<input type="hidden" name="aluno_mover_id" id="aluno_mover_id" value="">';
                            echo '</div>';
                        }

                        echo '</div>';
                    }

                    // Botões de ação
                    echo '<div class="save-buttons">';
                    echo '<button type="submit" name="salvar" class="btn btn-save-outline">';
                    echo '<i class="fas fa-save"></i> Salvar Apenas';
                    echo '</button>';
                    echo '<button type="submit" name="salvar" value="1" class="btn btn-save" onclick="document.getElementById(\'enviar_email\').checked = true;">';
                    echo '<i class="fas fa-envelope"></i> Salvar Presença & Enviar Relatório';
                    echo '</button>';
                    echo '<input type="checkbox" name="enviar_email" id="enviar_email" style="display: none;">';
                    echo '</div>';

                    echo '</form>';
                } else {
                    echo '<div class="alert alert-warning alert-custom">Nenhum aluno encontrado para este evento.</div>';
                }

                $conn->close();
            }
            ?>
        </div>
    </div>

    <!-- Modal de Substituição de Aluno -->
    <div class="modal fade" id="modalSubstituicao" tabindex="-1" aria-labelledby="modalSubstituicaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSubstituicaoLabel">
                        <i class="fas fa-exchange-alt"></i> Substituir Aluno
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="formSubstituicao">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Substituindo:</strong> <span id="aluno_antigo_nome"></span>
                        </div>

                        <input type="hidden" name="aluno_antigo_id" id="aluno_antigo_id">

                        <div class="mb-3">
                            <label for="sub_novo_nome" class="form-label required-field">Nome do Novo Aluno</label>
                            <input type="text" class="form-control" id="sub_novo_nome" name="sub_novo_nome" required placeholder="Digite o nome completo">
                        </div>

                        <div class="mb-3">
                            <label for="sub_nova_serie" class="form-label required-field">Série</label>
                            <input type="text" class="form-control" id="sub_nova_serie" name="sub_nova_serie" required placeholder="Ex: 8º ano, 1º ano EM">
                        </div>

                        <div class="mb-3">
                            <label for="sub_novo_curso" class="form-label required-field">Curso</label>
                            <input type="text" class="form-control" id="sub_novo_curso" name="sub_novo_curso" required placeholder="Ex: Ensino Médio, Fundamental II">
                        </div>

                        <div class="mb-3">
                            <label for="sub_novo_telefone" class="form-label">Telefone (Opcional)</label>
                            <input type="text" class="form-control" id="sub_novo_telefone" name="sub_novo_telefone" placeholder="Ex: (11) 99999-9999">
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Atenção:</strong> Esta ação irá substituir completamente o aluno atual.
                            Todas as presenças serão transferidas para o novo aluno.
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Como funciona:</strong><br>
                            • O novo aluno será cadastrado no sistema<br>
                            • Todas as presenças do aluno antigo serão transferidas<br>
                            • O aluno antigo será removido desta rota<br>
                            • O novo aluno ocupará a vaga no ônibus
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" name="substituir_aluno" class="btn btn-substituir">
                            <i class="fas fa-exchange-alt"></i> Substituir Aluno
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Função para atualizar estatísticas em tempo real
        function atualizarEstatisticas() {
            const botoes = document.querySelectorAll('[id^="btn_"]');
            let embarcados = 0;
            let naoEmbarcados = 0;

            botoes.forEach(botao => {
                if (botao.textContent === 'Embarcou') {
                    embarcados++;
                } else if (botao.textContent === 'Não Embarcou') {
                    naoEmbarcados++;
                }
            });

            // Atualizar elementos de estatísticas se existirem
            const statEmbarcados = document.getElementById('stat-embarcados');
            const statNaoEmbarcados = document.getElementById('stat-nao-embarcados');
            const statTotal = document.getElementById('stat-total');

            if (statEmbarcados) statEmbarcados.textContent = embarcados;
            if (statNaoEmbarcados) statNaoEmbarcados.textContent = naoEmbarcados;
            if (statTotal && statEmbarcados && statNaoEmbarcados) {
                const total = parseInt(statTotal.textContent);
                statNaoEmbarcados.textContent = total - embarcados;
            }
        }

        // Inicializar estatísticas quando a página carrega
        document.addEventListener('DOMContentLoaded', function() {
            atualizarEstatisticas();
        });

        function toggleStatus(alunoId) {
            const button = document.getElementById('btn_' + alunoId);
            const hiddenInput = document.getElementById('status_' + alunoId);
            const currentStatus = hiddenInput.value;

            if (currentStatus === 'nao_embarcou') {
                // Mudar para embarcou
                button.className = 'btn btn-success btn-presenca';
                button.textContent = 'Embarcou';
                hiddenInput.value = 'embarcou';

                // Adicionar efeito visual de confirmação
                button.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    button.style.transform = 'scale(1)';
                }, 200);
            } else {
                // Mudar para não embarcou
                button.className = 'btn btn-danger btn-presenca';
                button.textContent = 'Não Embarcou';
                hiddenInput.value = 'nao_embarcou';

                // Adicionar efeito visual de confirmação
                button.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    button.style.transform = 'scale(1)';
                }, 200);
            }

            // Atualizar estatísticas em tempo real
            atualizarEstatisticas();

            // Mostrar feedback visual temporário
            mostrarFeedback(button, currentStatus === 'nao_embarcou' ? 'Embarcou!' : 'Não embarcou!');
        }

        function mostrarFeedback(elemento, mensagem) {
            // Criar elemento de feedback
            const feedback = document.createElement('div');
            feedback.className = 'feedback-message';
            feedback.textContent = mensagem;
            feedback.style.background = elemento.classList.contains('btn-success') ? '#28a745' : '#dc3545';

            document.body.appendChild(feedback);

            // Mostrar com animação
            setTimeout(() => {
                feedback.classList.add('feedback-show');
            }, 100);

            // Remover após 2 segundos
            setTimeout(() => {
                feedback.classList.remove('feedback-show');
                setTimeout(() => {
                    document.body.removeChild(feedback);
                }, 300);
            }, 2000);
        }

        // Função para abrir modal de substituição
        function abrirModalSubstituicao(alunoId, alunoNome) {
            document.getElementById('aluno_antigo_id').value = alunoId;
            document.getElementById('aluno_antigo_nome').textContent = alunoNome;

            // Limpar formulário
            document.getElementById('sub_novo_nome').value = '';
            document.getElementById('sub_nova_serie').value = '';
            document.getElementById('sub_novo_curso').value = '';
            document.getElementById('sub_novo_telefone').value = '';

            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('modalSubstituicao'));
            modal.show();
        }

        // Validação do formulário de substituição
        document.getElementById('formSubstituicao').addEventListener('submit', function(e) {
            const nome = document.getElementById('sub_novo_nome').value.trim();
            const serie = document.getElementById('sub_nova_serie').value.trim();
            const curso = document.getElementById('sub_novo_curso').value.trim();

            if (!nome || !serie || !curso) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios (Nome, Série e Curso).');
                return false;
            }

            // Mostrar confirmação
            if (!confirm('Tem certeza que deseja substituir este aluno? Todas as presenças serão transferidas para o novo aluno.')) {
                e.preventDefault();
                return false;
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
