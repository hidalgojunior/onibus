<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Presença - Embarque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php
    // Incluir configurações de email
    include 'config_email.php';

    // Função para enviar relatório de presença por email
    function enviarRelatorioPresenca($conn, $evento_id, $onibus_id, $data_hoje, $alunos_embarcaram, $todos_alunos_result) {
        // Buscar informações do evento e ônibus
        $evento_query = "SELECT nome FROM eventos WHERE id = $evento_id";
        $evento_result = $conn->query($evento_query);
        $evento = $evento_result->fetch_assoc();

        $onibus_query = "SELECT numero FROM onibus WHERE id = $onibus_id";
        $onibus_result = $conn->query($onibus_query);
        $onibus = $onibus_result->fetch_assoc();

        // Calcular estatísticas
        $total_alunos = $todos_alunos_result->num_rows;
        $total_embarcados = count($alunos_embarcaram);
        $total_nao_embarcados = $total_alunos - $total_embarcados;

        // Formatar data
        $data_formatada = date('d/m/Y', strtotime($data_hoje));

        // Obter configurações de email
        $email_config = getEmailConfig();
        $destinatarios = getEmailDestinatarios();
        $assunto_base = getEmailAssuntoBase();

        // Criar assunto do email
        $assunto = "{$assunto_base} {$onibus['numero']} - {$data_formatada}";

        // Criar corpo do email em HTML
        $mensagem = "
        <html>
        <head>
            <title>{$assunto}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
                .header h2 { margin: 0; font-size: 24px; }
                .content { padding: 20px 0; }
                .stats { display: flex; justify-content: space-around; margin: 30px 0; }
                .stat-box { text-align: center; padding: 20px; border-radius: 8px; min-width: 120px; }
                .stat-box h3 { margin: 0 0 10px 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
                .stat-box .number { font-size: 32px; font-weight: bold; margin: 0; }
                .embarcados { background: linear-gradient(135deg, #28a745, #20c997); color: white; }
                .nao-embarcados { background: linear-gradient(135deg, #dc3545, #fd7e14); color: white; }
                .total { background: linear-gradient(135deg, #007bff, #6610f2); color: white; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
                .evento-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🚍 Relatório de Presença</h2>
                    <p>Ônibus {$onibus['numero']} - {$data_formatada}</p>
                </div>

                <div class='content'>
                    <div class='evento-info'>
                        <strong>Evento:</strong> {$evento['nome']}<br>
                        <strong>Data do Relatório:</strong> {$data_formatada}<br>
                        <strong>Hora do Envio:</strong> " . date('H:i:s') . "
                    </div>

                    <div class='stats'>
                        <div class='stat-box total'>
                            <h3>Total</h3>
                            <div class='number'>{$total_alunos}</div>
                        </div>

                        <div class='stat-box embarcados'>
                            <h3>Embarcaram</h3>
                            <div class='number'>{$total_embarcados}</div>
                        </div>

                        <div class='stat-box nao-embarcados'>
                            <h3>Não Embarcaram</h3>
                            <div class='number'>{$total_nao_embarcados}</div>
                        </div>
                    </div>

                    <div style='background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <strong>Percentual de Presença:</strong> " . ($total_alunos > 0 ? round(($total_embarcados / $total_alunos) * 100, 1) : 0) . "%<br>
                        <strong>Status:</strong> " . ($total_nao_embarcados == 0 ? '<span style="color: #28a745;">✅ Todos embarcaram</span>' : '<span style="color: #fd7e14;">⚠️ Alguns alunos não embarcaram</span>') . "
                    </div>
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

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h1 class="card-title mb-0">Controle de Presença - Embarque</h1>
                        <small>Ônibus 1 - Evento: Bootcamp Jovem Programador (27/08 a 05/09)</small>
                    </div>
                    <div class="card-body">
                        <?php
                        // Incluir arquivo de configuração
                        include 'config.php';

                        // Obter conexão com o banco
                        $conn = getDatabaseConnection();

                        if ($conn->connect_error) {
                            echo '<div class="alert alert-danger">Erro de conexão: ' . $conn->connect_error . '</div>';
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
                                // Processar cadastro de novo aluno
                                if (isset($_POST['cadastrar_aluno'])) {
                                    $novo_nome = trim($_POST['novo_nome']);
                                    $nova_serie = trim($_POST['nova_serie']);
                                    $novo_curso = trim($_POST['novo_curso']);
                                    $novo_telefone = trim($_POST['novo_telefone']);

                                    if (!empty($novo_nome)) {
                                        $insert_stmt = $conn->prepare("INSERT INTO alunos (nome, serie, curso, telefone) VALUES (?, ?, ?, ?)");
                                        $insert_stmt->bind_param("ssss", $novo_nome, $nova_serie, $novo_curso, $novo_telefone);

                                        if ($insert_stmt->execute()) {
                                            $novo_aluno_id = $conn->insert_id;

                                            // Alocar o novo aluno no ônibus atual
                                            $alocacao_stmt = $conn->prepare("INSERT INTO alocacoes_onibus (aluno_id, onibus_id, evento_id) VALUES (?, ?, ?)");
                                            $alocacao_stmt->bind_param("iii", $novo_aluno_id, $onibus_id, $evento_id);
                                            $alocacao_stmt->execute();
                                            $alocacao_stmt->close();

                                            echo '<div class="alert alert-success alert-dismissible fade show">Novo aluno cadastrado e alocado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                        } else {
                                            echo '<div class="alert alert-danger alert-dismissible fade show">Erro ao cadastrar aluno: ' . $conn->error . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                        }
                                        $insert_stmt->close();
                                    }
                                }

                                // Processar substituição de aluno
                                if (isset($_POST['substituir_aluno'])) {
                                    $aluno_antigo_id = $_POST['aluno_antigo_id'];
                                    $novo_nome = trim($_POST['sub_novo_nome']);
                                    $nova_serie = trim($_POST['sub_nova_serie']);
                                    $novo_curso = trim($_POST['sub_novo_curso']);
                                    $novo_telefone = trim($_POST['sub_novo_telefone']);

                                    if (!empty($novo_nome)) {
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

                                            echo '<div class="alert alert-success alert-dismissible fade show">Aluno substituído com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                        } else {
                                            echo '<div class="alert alert-danger alert-dismissible fade show">Erro ao substituir aluno: ' . $conn->error . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                        }
                                        $insert_stmt->close();
                                    }
                                }

                                // Processar movimento de aluno para este ônibus
                                if (isset($_POST['mover_para_onibus'])) {
                                    $aluno_id = $_POST['aluno_mover_id'];

                                    // Verificar se o aluno já não está alocado neste ônibus
                                    $check_alocacao = $conn->prepare("SELECT id FROM alocacoes_onibus WHERE aluno_id = ? AND onibus_id = ? AND evento_id = ?");
                                    $check_alocacao->bind_param("iii", $aluno_id, $onibus_id, $evento_id);
                                    $check_alocacao->execute();

                                    if ($check_alocacao->get_result()->num_rows == 0) {
                                        // Aluno não está alocado neste ônibus, então podemos movê-lo
                                        $insert_alocacao = $conn->prepare("INSERT INTO alocacoes_onibus (aluno_id, onibus_id, evento_id) VALUES (?, ?, ?)");
                                        $insert_alocacao->bind_param("iii", $aluno_id, $onibus_id, $evento_id);

                                        if ($insert_alocacao->execute()) {
                                            echo '<div class="alert alert-success alert-dismissible fade show">Aluno movido para este ônibus com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                        } else {
                                            echo '<div class="alert alert-danger alert-dismissible fade show">Erro ao mover aluno: ' . $conn->error . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                        }
                                        $insert_alocacao->close();
                                    } else {
                                        echo '<div class="alert alert-warning alert-dismissible fade show">Este aluno já está alocado neste ônibus.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                    }
                                    $check_alocacao->close();
                                }

                                // Processar edição de aluno
                                if (isset($_POST['editar_aluno'])) {
                                    $aluno_id = $_POST['aluno_id'];
                                    $novo_nome = trim($_POST['nome']);
                                    $nova_serie = trim($_POST['serie']);
                                    $novo_curso = trim($_POST['curso']);
                                    $novo_telefone = trim($_POST['telefone']);

                                    if (!empty($novo_nome)) {
                                        $update_stmt = $conn->prepare("UPDATE alunos SET nome = ?, serie = ?, curso = ?, telefone = ? WHERE id = ?");
                                        $update_stmt->bind_param("sssss", $novo_nome, $nova_serie, $novo_curso, $novo_telefone, $aluno_id);

                                        if ($update_stmt->execute()) {
                                            echo '<div class="alert alert-success alert-dismissible fade show">Dados do aluno atualizados com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                        } else {
                                            echo '<div class="alert alert-danger alert-dismissible fade show">Erro ao atualizar aluno: ' . $conn->error . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                                        }
                                        $update_stmt->close();
                                    }
                                }

                                if (isset($_POST['alunos'])) {
                                    $data_hoje = date('Y-m-d');
                                    $alunos_embarcaram = [];
                                    $enviar_email = isset($_POST['salvar_enviar']);

                                    // Primeiro, identificar quem embarcou
                                    foreach ($_POST['alunos'] as $aluno_id => $status) {
                                        if ($status == 'embarcou') {
                                            $alunos_embarcaram[] = $aluno_id;
                                        }
                                    }

                                    // Se alguém embarcou, marcar todos os outros como não embarcaram
                                    if (!empty($alunos_embarcaram)) {
                                        // Buscar todos os alunos alocados neste ônibus para este evento
                                        $todos_alunos_query = "
                                            SELECT a.id
                                            FROM alunos a
                                            INNER JOIN alocacoes_onibus ao ON a.id = ao.aluno_id
                                            WHERE ao.onibus_id = $onibus_id AND ao.evento_id = $evento_id
                                        ";
                                        $todos_alunos_result = $conn->query($todos_alunos_query);

                                        while ($aluno_row = $todos_alunos_result->fetch_assoc()) {
                                            $aluno_id_atual = $aluno_row['id'];

                                            if (in_array($aluno_id_atual, $alunos_embarcaram)) {
                                                // Este aluno embarcou - marcar como embarcado
                                                $check_query = "SELECT id FROM presencas WHERE aluno_id = $aluno_id_atual AND data = '$data_hoje'";
                                                $check_result = $conn->query($check_query);

                                                if ($check_result->num_rows == 0) {
                                                    $conn->query("INSERT INTO presencas (aluno_id, evento_id, data, presenca_embarque) VALUES ($aluno_id_atual, $evento_id, '$data_hoje', 1)");
                                                } else {
                                                    $conn->query("UPDATE presencas SET presenca_embarque = 1 WHERE aluno_id = $aluno_id_atual AND data = '$data_hoje'");
                                                }
                                            } else {
                                                // Este aluno não embarcou - marcar como não embarcado E desalocar do ônibus
                                                $check_query = "SELECT id FROM presencas WHERE aluno_id = $aluno_id_atual AND data = '$data_hoje'";
                                                $check_result = $conn->query($check_query);

                                                if ($check_result->num_rows == 0) {
                                                    $conn->query("INSERT INTO presencas (aluno_id, evento_id, data, presenca_embarque) VALUES ($aluno_id_atual, $evento_id, '$data_hoje', 0)");
                                                } else {
                                                    $conn->query("UPDATE presencas SET presenca_embarque = 0 WHERE aluno_id = $aluno_id_atual AND data = '$data_hoje'");
                                                }

                                                // IMPORTANTE: Não desalocar o aluno do ônibus
                                                // A alocação deve persistir entre os dias
                                                // Apenas registrar a ausência na tabela presencas
                                                // $conn->query("DELETE FROM alocacoes_onibus WHERE aluno_id = $aluno_id_atual AND onibus_id = $onibus_id AND evento_id = $evento_id");
                                            }
                                        }
                                    } else {
                                        // Se ninguém embarcou, processar normalmente
                                        foreach ($_POST['alunos'] as $aluno_id => $status) {
                                            if ($status == 'nao_embarcou') {
                                                // Remover presença se existir
                                                $conn->query("DELETE FROM presencas WHERE aluno_id = $aluno_id AND data = '$data_hoje'");
                                                // IMPORTANTE: Não desalocar o aluno do ônibus
                                                // A alocação deve persistir entre os dias
                                                // Apenas registrar a ausência na tabela presencas
                                                // $conn->query("DELETE FROM alocacoes_onibus WHERE aluno_id = $aluno_id AND onibus_id = $onibus_id AND evento_id = $evento_id");
                                            }
                                        }
                                    }

                                    echo '<div class="alert alert-success alert-dismissible fade show">Status atualizado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';

                                    // Enviar email com relatório de presença apenas se solicitado
                                    if ($enviar_email) {
                                        enviarRelatorioPresenca($conn, $evento_id, $onibus_id, $data_hoje, $alunos_embarcaram, $todos_alunos_result);
                                    }
                                }
                            }

                            // Definir datas
                            $data_hoje = date('Y-m-d');
                            $data_ontem = date('Y-m-d', strtotime('-1 day'));

                            // Listar alunos alocados no ônibus atual E alunos não alocados do evento
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
                                    -- Segundo: alunos que embarcaram ontem (desc)
                                    MAX(p_ontem.presenca_embarque) DESC,
                                    -- Terceiro: alunos que não embarcaram ontem (asc, para que NULL fique por último)
                                    CASE WHEN MAX(p_ontem.presenca_embarque) IS NULL THEN 1 ELSE 0 END ASC,
                                    -- Quarto: ordem alfabética por nome
                                    a.nome ASC
                            ";
                            $alunos_result = $conn->query($alunos_query);

                            $total_alocados = 0;
                            $total_nao_alocados = 0;
                            while ($row = $alunos_result->fetch_assoc()) {
                                if ($row['alocado_neste_onibus'] == 1) {
                                    $total_alocados++;
                                } else {
                                    $total_nao_alocados++;
                                }
                            }
                            $alunos_result->data_seek(0); // Reset pointer

                            echo '<div class="alert alert-info">';
                            echo 'Encontrados ' . $total_alocados . ' alunos alocados neste ônibus';
                            if ($total_nao_alocados > 0) {
                                echo ' e ' . $total_nao_alocados . ' alunos disponíveis para substituição';
                            }
                            echo '. <a href="alocacoes.php" class="alert-link">Gerenciar alocações</a>';
                            echo '</div>';

                            // Estatísticas em tempo real
                            echo '<div class="row mb-3">';
                            echo '<div class="col-md-4">';
                            echo '<div class="card text-center">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title text-primary">Total Alunos</h5>';
                            echo '<h2 class="card-text" id="stat-total">' . $total_alocados . '</h2>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="col-md-4">';
                            echo '<div class="card text-center">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title text-success">Embarcaram</h5>';
                            echo '<h2 class="card-text" id="stat-embarcados">0</h2>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="col-md-4">';
                            echo '<div class="card text-center">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title text-danger">Não Embarcaram</h5>';
                            echo '<h2 class="card-text" id="stat-nao-embarcados">' . $total_alocados . '</h2>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';

                            if ($alunos_result->num_rows > 0) {
                                echo '<form method="POST">';
                                echo '<div class="table-responsive">';
                                echo '<table class="table table-striped">';
                                echo '<thead><tr><th>Nome</th><th>Curso</th><th>Status</th><th>Alocação</th><th>Ações</th></tr></thead>';
                                echo '<tbody>';

                                while ($aluno = $alunos_result->fetch_assoc()) {
                                    $aluno_id = $aluno['id'];

                                    // Usar diretamente o valor do banco de dados
                                    $presenca_embarque = $aluno['presenca_embarque'];
                                    $status = ($presenca_embarque === null) ? 'nao_embarcou' : (($presenca_embarque == 1) ? 'embarcou' : 'nao_embarcou');
                                    $btn_class = ($status == 'embarcou') ? 'btn-success' : 'btn-danger';
                                    $btn_text = ($status == 'embarcou') ? 'Embarcou' : 'Não Embarcou';

                                    // Verificar se embarcou ontem para indicação visual
                                    $embarcou_ontem = $aluno['embarcou_ontem'];
                                    $badge_ontem = '';
                                    if ($embarcou_ontem == 1) {
                                        $badge_ontem = '<span class="badge bg-success ms-2">Embarcou ontem</span>';
                                    } elseif ($embarcou_ontem === 0) {
                                        $badge_ontem = '<span class="badge bg-warning text-dark ms-2">Não embarcou ontem</span>';
                                    }

                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($aluno['nome']) . $badge_ontem . '</td>';
                                    echo '<td>' . htmlspecialchars($aluno['curso']) . '</td>';
                                    echo '<td>';

                                    // Só mostrar controles de presença se o aluno estiver alocado neste ônibus
                                    if ($aluno['alocado_neste_onibus'] == 1) {
                                        echo '<input type="hidden" name="alunos[' . $aluno_id . ']" value="' . $status . '" id="status_' . $aluno_id . '">';
                                        echo '<button type="button" class="btn ' . $btn_class . ' btn-sm" id="btn_' . $aluno_id . '" onclick="toggleStatus(' . $aluno_id . ')">' . $btn_text . '</button>';
                                    } else {
                                        echo '<span class="text-muted">Não alocado</span>';
                                    }
                                    echo '</td>';
                                    echo '<td>';

                                    // Indicador de alocação
                                    if ($aluno['alocado_neste_onibus'] == 1) {
                                        echo '<span class="badge bg-success">Alocado</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary">Disponível</span>';
                                    }
                                    echo '</td>';
                                    echo '<td>';

                                    // Botões de ação
                                    echo '<button type="button" class="btn btn-outline-primary btn-sm" onclick="editarAluno(' . $aluno_id . ', \'' . addslashes($aluno['nome']) . '\', \'' . addslashes($aluno['serie']) . '\', \'' . addslashes($aluno['curso']) . '\', \'' . addslashes($aluno['telefone']) . '\')">';
                                    echo '<i class="fas fa-edit"></i> Editar';
                                    echo '</button>';

                                    if ($aluno['alocado_neste_onibus'] == 1) {
                                        // Aluno alocado - pode ser substituído
                                        echo '<button type="button" class="btn btn-outline-warning btn-sm ms-1" onclick="substituirAluno(' . $aluno_id . ', \'' . addslashes($aluno['nome']) . '\')">';
                                        echo '<i class="fas fa-exchange-alt"></i> Substituir';
                                        echo '</button>';
                                    } else {
                                        // Aluno não alocado - pode ser movido para este ônibus
                                        echo '<button type="button" class="btn btn-outline-success btn-sm ms-1" onclick="moverParaOnibus(' . $aluno_id . ', \'' . addslashes($aluno['nome']) . '\')">';
                                        echo '<i class="fas fa-arrow-right"></i> Mover p/ Ônibus';
                                        echo '</button>';
                                    }
                                    echo '</td>';
                                    echo '</tr>';
                                }

                                echo '</tbody>';
                                echo '</table>';
                                echo '</div>';
                                echo '<button type="submit" class="btn btn-success" name="salvar_enviar" value="1">';
                                echo '<i class="fas fa-save"></i> Salvar Presença & Enviar Relatório';
                                echo '</button>';
                                echo '<button type="submit" class="btn btn-outline-success ms-2" name="salvar_somente" value="1">';
                                echo '<i class="fas fa-save"></i> Salvar Apenas';
                                echo '</button>';
                                echo '<button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#cadastrarAlunoModal">';
                                echo '<i class="fas fa-plus"></i> Cadastrar Novo Aluno';
                                echo '</button>';
                                echo '</form>';
                            } else {
                                echo '<div class="alert alert-warning">Nenhum aluno cadastrado. <a href="import_students.php">Importar alunos</a></div>';
                                echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cadastrarAlunoModal">';
                                echo '<i class="fas fa-plus"></i> Cadastrar Primeiro Aluno';
                                echo '</button>';
                            }
                        }

                        $conn->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição de Aluno -->
    <div class="modal fade" id="editarAlunoModal" tabindex="-1" aria-labelledby="editarAlunoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarAlunoModalLabel">Editar Dados do Aluno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="editar_aluno" value="1">
                        <input type="hidden" name="aluno_id" id="edit_aluno_id">

                        <div class="mb-3">
                            <label for="edit_nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="edit_nome" name="nome" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_serie" class="form-label">Série</label>
                            <input type="text" class="form-control" id="edit_serie" name="serie" placeholder="Ex: 1ª Série, 2ª Série, 3ª Série">
                        </div>

                        <div class="mb-3">
                            <label for="edit_curso" class="form-label">Curso</label>
                            <input type="text" class="form-control" id="edit_curso" name="curso" placeholder="Ex: Informática, Administração">
                        </div>

                        <div class="mb-3">
                            <label for="edit_telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="edit_telefone" name="telefone" placeholder="Ex: (11) 99999-9999">
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

    <!-- Modal de Cadastro de Novo Aluno -->
    <div class="modal fade" id="cadastrarAlunoModal" tabindex="-1" aria-labelledby="cadastrarAlunoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cadastrarAlunoModalLabel">Cadastrar Novo Aluno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="cadastrar_aluno" value="1">

                        <div class="mb-3">
                            <label for="novo_nome" class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="novo_nome" name="novo_nome" required>
                        </div>

                        <div class="mb-3">
                            <label for="nova_serie" class="form-label">Série</label>
                            <input type="text" class="form-control" id="nova_serie" name="nova_serie" placeholder="Ex: 1ª Série, 2ª Série, 3ª Série">
                        </div>

                        <div class="mb-3">
                            <label for="novo_curso" class="form-label">Curso</label>
                            <input type="text" class="form-control" id="novo_curso" name="novo_curso" placeholder="Ex: Informática, Administração">
                        </div>

                        <div class="mb-3">
                            <label for="novo_telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="novo_telefone" name="novo_telefone" placeholder="Ex: (11) 99999-9999">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Cadastrar e Alocar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Substituição de Aluno -->
    <div class="modal fade" id="substituirAlunoModal" tabindex="-1" aria-labelledby="substituirAlunoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="substituirAlunoModalLabel">Substituir Aluno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="substituir_aluno" value="1">
                        <input type="hidden" name="aluno_antigo_id" id="sub_aluno_antigo_id">

                        <div class="alert alert-info">
                            <strong>Substituindo:</strong> <span id="aluno_antigo_nome"></span>
                        </div>

                        <div class="mb-3">
                            <label for="sub_novo_nome" class="form-label">Nome do Novo Aluno *</label>
                            <input type="text" class="form-control" id="sub_novo_nome" name="sub_novo_nome" required>
                        </div>

                        <div class="mb-3">
                            <label for="sub_nova_serie" class="form-label">Série</label>
                            <input type="text" class="form-control" id="sub_nova_serie" name="sub_nova_serie" placeholder="Ex: 1ª Série, 2ª Série, 3ª Série">
                        </div>

                        <div class="mb-3">
                            <label for="sub_novo_curso" class="form-label">Curso</label>
                            <input type="text" class="form-control" id="sub_novo_curso" name="sub_novo_curso" placeholder="Ex: Informática, Administração">
                        </div>

                        <div class="mb-3">
                            <label for="sub_novo_telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="sub_novo_telefone" name="sub_novo_telefone" placeholder="Ex: (11) 99999-9999">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Substituir Aluno</button>
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
                button.className = 'btn btn-success btn-sm';
                button.textContent = 'Embarcou';
                hiddenInput.value = 'embarcou';

                // Adicionar efeito visual de confirmação
                button.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    button.style.transform = 'scale(1)';
                }, 200);
            } else {
                // Mudar para não embarcou
                button.className = 'btn btn-danger btn-sm';
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
            const feedback = document.createElement('span');
            feedback.textContent = mensagem;
            feedback.style.position = 'absolute';
            feedback.style.background = 'rgba(0,0,0,0.8)';
            feedback.style.color = 'white';
            feedback.style.padding = '5px 10px';
            feedback.style.borderRadius = '3px';
            feedback.style.fontSize = '12px';
            feedback.style.zIndex = '1000';
            feedback.style.pointerEvents = 'none';
            feedback.style.boxShadow = '0 2px 5px rgba(0,0,0,0.3)';
            feedback.style.transition = 'opacity 0.3s ease';

            // Posicionar relativo ao botão
            const rect = elemento.getBoundingClientRect();
            feedback.style.left = rect.left + 'px';
            feedback.style.top = (rect.top - 35) + 'px';

            document.body.appendChild(feedback);

            // Animação de fade in
            feedback.style.opacity = '0';
            setTimeout(() => {
                feedback.style.opacity = '1';
            }, 10);

            // Remover após 1.5 segundos
            setTimeout(() => {
                feedback.style.opacity = '0';
                setTimeout(() => {
                    if (document.body.contains(feedback)) {
                        document.body.removeChild(feedback);
                    }
                }, 300);
            }, 1500);
        }

        // Adicionar efeito hover aos botões de embarque
        document.addEventListener('DOMContentLoaded', function() {
            const botoesEmbarque = document.querySelectorAll('[id^="btn_"]');

            botoesEmbarque.forEach(botao => {
                botao.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05)';
                    this.style.transition = 'transform 0.2s ease';
                });

                botao.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });

        function editarAluno(id, nome, serie, curso, telefone) {
            document.getElementById('edit_aluno_id').value = id;
            document.getElementById('edit_nome').value = nome;
            document.getElementById('edit_serie').value = serie;
            document.getElementById('edit_curso').value = curso;
            document.getElementById('edit_telefone').value = telefone;

            const modal = new bootstrap.Modal(document.getElementById('editarAlunoModal'));
            modal.show();
        }

        function substituirAluno(id, nome) {
            document.getElementById('sub_aluno_antigo_id').value = id;
            document.getElementById('aluno_antigo_nome').textContent = nome;

            const modal = new bootstrap.Modal(document.getElementById('substituirAlunoModal'));
            modal.show();
        }

        function moverParaOnibus(id, nome) {
            if (confirm('Tem certeza que deseja mover ' + nome + ' para este ônibus?')) {
                // Criar um form temporário para enviar a requisição
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';

                const inputMover = document.createElement('input');
                inputMover.type = 'hidden';
                inputMover.name = 'mover_para_onibus';
                inputMover.value = '1';
                form.appendChild(inputMover);

                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'aluno_mover_id';
                inputId.value = id;
                form.appendChild(inputId);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
