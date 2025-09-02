<?php
// Configurações da página
$page_title = "Lista de Alunos para Controle Manual";
$page_description = "Impressão de Lista para Embarque/Desembarque";

// Incluir configuração do banco
include 'config/config.php';

// Conectar ao banco
$conn = getDatabaseConnection();

// Verificar se foi passado o ID do ônibus
$onibus_id = isset($_GET['onibus_id']) ? intval($_GET['onibus_id']) : null;
$data_viagem = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');

// Buscar dados do ônibus
$onibus_data = null;
if ($onibus_id) {
    $query = "SELECT * FROM onibus WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $onibus_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $onibus_data = $result->fetch_assoc();
}

// Buscar alunos alocados no ônibus
$alunos = [];
if ($onibus_id) {
    $query = "
        SELECT a.*, ao.ponto_embarque, ao.horario_embarque, ao.turno, ao.observacoes as alocacao_obs,
               q.codigo_qr, q.ativo as qr_ativo
        FROM alunos a
        INNER JOIN alocacoes_onibus ao ON a.id = ao.aluno_id
        LEFT JOIN qr_codes q ON a.id = q.aluno_id
        WHERE ao.onibus_id = ? AND ao.ativo = 1
        ORDER BY a.nome
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $onibus_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $alunos = $result->fetch_all(MYSQLI_ASSOC);
}

// CSS para impressão
$print_css = '
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-area, .print-area * {
        visibility: visible;
    }
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
    .page-break {
        page-break-before: always;
    }
}

.lista-container {
    font-family: Arial, sans-serif;
    max-width: 210mm;
    margin: 0 auto;
    padding: 20px;
    background: white;
}

.cabecalho {
    border: 2px solid #000;
    padding: 15px;
    margin-bottom: 20px;
    text-align: center;
    background: #f8f9fa;
}

.info-onibus {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
    padding: 10px;
    border: 1px solid #ddd;
}

.tabela-alunos {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
}

.tabela-alunos th,
.tabela-alunos td {
    border: 1px solid #000;
    padding: 8px;
    text-align: left;
    font-size: 12px;
}

.tabela-alunos th {
    background: #e9ecef;
    font-weight: bold;
    text-align: center;
}

.checkbox-col {
    width: 15%;
    text-align: center;
}

.nome-col {
    width: 70%;
}

.assinaturas {
    margin-top: 40px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

.assinatura-box {
    border-top: 1px solid #000;
    padding-top: 5px;
    text-align: center;
    font-size: 12px;
}

.rodape {
    margin-top: 30px;
    font-size: 10px;
    color: #666;
    text-align: center;
    border-top: 1px solid #ddd;
    padding-top: 10px;
}
</style>';

// Conteúdo da página
ob_start();
?>

<div class="container-fluid py-4">
    <!-- Controles no topo -->
    <div class="row no-print mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-list text-primary me-2"></i>
                        Lista de Alunos para Controle Manual
                    </h1>
                    <p class="text-muted mb-0">Impressão de lista para embarque/desembarque</p>
                </div>
                <div>
                    <button class="btn btn-primary me-2" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>
                        Imprimir
                    </button>
                    <a href="onibus-professional.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de seleção -->
    <div class="row no-print mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter text-info me-2"></i>
                        Selecionar Ônibus e Data
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <label for="onibus_id" class="form-label">Ônibus</label>
                            <select class="form-select" id="onibus_id" name="onibus_id" required>
                                <option value="">Selecione um ônibus...</option>
                                <?php
                                $onibus_query = "SELECT * FROM onibus WHERE ativo = 1 ORDER BY numero";
                                $onibus_result = $conn->query($onibus_query);
                                if ($onibus_result) {
                                    while ($onibus = $onibus_result->fetch_assoc()) {
                                        $selected = ($onibus_id == $onibus['id']) ? 'selected' : '';
                                        echo "<option value='{$onibus['id']}' $selected>Ônibus {$onibus['numero']} - {$onibus['rota_descricao']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="data" class="form-label">Data da Viagem</label>
                            <input type="date" class="form-control" id="data" name="data" value="<?= $data_viagem ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">
                                <i class="fas fa-search me-2"></i>
                                Gerar Lista
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Área de impressão -->
    <?php if ($onibus_data && !empty($alunos)): ?>
        <div class="print-area">
            <div class="lista-container">
                <!-- Cabeçalho -->
                <div class="cabecalho">
                    <h2 style="margin: 0;">LISTA DE CONTROLE DE EMBARQUE/DESEMBARQUE</h2>
                    <p style="margin: 5px 0 0 0;">Sistema de Transporte Escolar</p>
                </div>

                <!-- Informações do Ônibus -->
                <div class="info-onibus">
                    <div>
                        <strong>Ônibus:</strong> <?= htmlspecialchars($onibus_data['numero'] ?? '') ?><br>
                        <strong>Rota:</strong> <?= htmlspecialchars($onibus_data['rota_descricao'] ?? 'Não informada') ?><br>
                        <strong>Capacidade:</strong> <?= $onibus_data['capacidade'] ?> alunos<br>
                        <strong>Total Alocados:</strong> <?= count($alunos) ?> alunos
                    </div>
                    <div>
                        <strong>Data:</strong> <?= date('d/m/Y', strtotime($data_viagem)) ?><br>
                        <strong>Motorista:</strong> <?= htmlspecialchars($onibus_data['motorista_nome'] ?? 'Não informado') ?><br>
                        <strong>Monitor:</strong> <?= htmlspecialchars($onibus_data['monitor_nome'] ?? 'Não informado') ?><br>
                        <strong>Gerado em:</strong> <?= date('d/m/Y H:i') ?>
                    </div>
                    <div>
                        <strong style="color: #dc3545;">EMERGÊNCIA:</strong><br>
                        <strong><?= htmlspecialchars($onibus_data['responsavel_emergencia_nome'] ?? 'Não informado') ?></strong><br>
                        <strong><?= htmlspecialchars($onibus_data['responsavel_emergencia_whatsapp'] ?? 'Não informado') ?></strong><br>
                        <small class="text-muted">Contato para emergências</small>
                    </div>
                </div>

                <!-- Tabela de Alunos -->
                <table class="tabela-alunos">
                    <thead>
                        <tr>
                            <th class="checkbox-col">EMBARQUE</th>
                            <th class="checkbox-col">DESEMBARQUE</th>
                            <th class="nome-col">NOME DO ALUNO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alunos as $index => $aluno): ?>
                            <tr>
                                <td class="checkbox-col">☐</td>
                                <td class="checkbox-col">☐</td>
                                <td class="nome-col">
                                    <strong><?= htmlspecialchars($aluno['nome'] ?? 'Nome não informado') ?></strong><br>
                                    <small><?= htmlspecialchars($aluno['serie'] ?? '') ?> - <?= htmlspecialchars($aluno['curso'] ?? '') ?></small>
                                    <?php if ($aluno['qr_ativo']): ?>
                                        <br><small style="color: #28a745;">✓ QR Code</small>
                                    <?php else: ?>
                                        <br><small style="color: #dc3545;">✗ Sem QR</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <!-- Linhas extras para preenchimento manual -->
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <tr>
                                <td class="checkbox-col">☐</td>
                                <td class="checkbox-col">☐</td>
                                <td class="nome-col">&nbsp;</td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>

                <!-- Resumo -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin: 20px 0; padding: 10px; border: 1px solid #ddd; background: #f8f9fa;">
                    <div style="text-align: center;">
                        <strong>Total Embarcaram:</strong><br>
                        <span style="font-size: 18px; border-bottom: 1px solid #000; display: inline-block; width: 50px; height: 25px;"></span>
                    </div>
                    <div style="text-align: center;">
                        <strong>Total Desembarcaram:</strong><br>
                        <span style="font-size: 18px; border-bottom: 1px solid #000; display: inline-block; width: 50px; height: 25px;"></span>
                    </div>
                    <div style="text-align: center;">
                        <strong>Ausentes:</strong><br>
                        <span style="font-size: 18px; border-bottom: 1px solid #000; display: inline-block; width: 50px; height: 25px;"></span>
                    </div>
                </div>

                <!-- Assinaturas -->
                <div class="assinaturas">
                    <div class="assinatura-box">
                        <div style="height: 40px;"></div>
                        <div>Assinatura do Motorista</div>
                        <div><?= htmlspecialchars($onibus_data['motorista_nome'] ?? 'Não informado') ?></div>
                    </div>
                    <div class="assinatura-box">
                        <div style="height: 40px;"></div>
                        <div>Assinatura do Monitor</div>
                        <div><?= htmlspecialchars($onibus_data['monitor_nome'] ?? 'Não informado') ?></div>
                    </div>
                </div>

                <!-- Rodapé -->
                <div class="rodape">
                    <p><strong>INSTRUÇÕES:</strong></p>
                    <p>Marque ☑ na coluna EMBARQUE quando o aluno embarcar e na coluna DESEMBARQUE quando desembarcar</p>
                    <p><strong>Em caso de emergência, contate:</strong> <?= htmlspecialchars($onibus_data['responsavel_emergencia_nome'] ?? 'Responsável não cadastrado') ?> - <?= htmlspecialchars($onibus_data['responsavel_emergencia_whatsapp'] ?? '') ?></p>
                    <p>Sistema de Controle de Transporte Escolar - Gerado automaticamente em <?= date('d/m/Y H:i:s') ?></p>
                </div>
            </div>
        </div>

    <?php elseif ($onibus_data && empty($alunos)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Nenhum aluno alocado para este ônibus.
        </div>

    <?php elseif (!$onibus_id): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Selecione um ônibus e data para gerar a lista de controle manual.
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean() . $print_css;

// Incluir layout
include 'includes/layout-professional.php';
$conn->close();
?>
