<?php
// Configurações da página
$page_title = "QR Codes dos Alunos";
$page_description = "Geração e Gestão de QR Codes Individuais";

// Incluir configuração do banco
include 'config/config.php';

// Conectar ao banco
$conn = getDatabaseConnection();

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['gerar_qr']) && isset($_POST['aluno_id'])) {
        $aluno_id = intval($_POST['aluno_id']);
        
        // Verificar se já existe QR Code para este aluno
        $check = $conn->prepare("SELECT id FROM qr_codes WHERE aluno_id = ?");
        $check->bind_param("i", $aluno_id);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows == 0) {
            // Gerar novo QR Code
            $codigo_qr = 'ALN-' . str_pad($aluno_id, 5, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(md5($aluno_id . time()), 0, 8));
            $data_geracao = date('Y-m-d H:i:s');
            $validade = date('Y-m-d H:i:s', strtotime('+1 year'));
            
            $insert = $conn->prepare("INSERT INTO qr_codes (aluno_id, codigo_qr, data_geracao, ativo, validade) VALUES (?, ?, ?, 1, ?)");
            $insert->bind_param("isss", $aluno_id, $codigo_qr, $data_geracao, $validade);
            
            if ($insert->execute()) {
                $success_message = "QR Code gerado com sucesso!";
            } else {
                $error_message = "Erro ao gerar QR Code: " . $conn->error;
            }
        } else {
            $error_message = "QR Code já existe para este aluno!";
        }
    }
    
    if (isset($_POST['regenerar_qr']) && isset($_POST['qr_id'])) {
        $qr_id = intval($_POST['qr_id']);
        
        // Buscar aluno_id
        $aluno_result = $conn->query("SELECT aluno_id FROM qr_codes WHERE id = $qr_id");
        if ($aluno_result && $aluno = $aluno_result->fetch_assoc()) {
            $aluno_id = $aluno['aluno_id'];
            
            // Gerar novo código
            $codigo_qr = 'ALN-' . str_pad($aluno_id, 5, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(md5($aluno_id . time()), 0, 8));
            $data_geracao = date('Y-m-d H:i:s');
            $validade = date('Y-m-d H:i:s', strtotime('+1 year'));
            
            $update = $conn->prepare("UPDATE qr_codes SET codigo_qr = ?, data_geracao = ?, validade = ? WHERE id = ?");
            $update->bind_param("sssi", $codigo_qr, $data_geracao, $validade, $qr_id);
            
            if ($update->execute()) {
                $success_message = "QR Code regenerado com sucesso!";
            } else {
                $error_message = "Erro ao regenerar QR Code: " . $conn->error;
            }
        }
    }
    
    if (isset($_POST['toggle_status']) && isset($_POST['qr_id'])) {
        $qr_id = intval($_POST['qr_id']);
        $novo_status = intval($_POST['novo_status']);
        
        $update = $conn->prepare("UPDATE qr_codes SET ativo = ? WHERE id = ?");
        $update->bind_param("ii", $novo_status, $qr_id);
        
        if ($update->execute()) {
            $action = $novo_status ? 'ativado' : 'desativado';
            $success_message = "QR Code $action com sucesso!";
        } else {
            $error_message = "Erro ao alterar status: " . $conn->error;
        }
    }
}

// Obter estatísticas
$stats = [
    'total_qr_codes' => 0,
    'qr_codes_ativos' => 0,
    'alunos_sem_qr' => 0,
    'usos_hoje' => 0
];

$result = $conn->query("SELECT COUNT(*) as total FROM qr_codes");
if ($result && $row = $result->fetch_assoc()) {
    $stats['total_qr_codes'] = $row['total'];
}

$result = $conn->query("SELECT COUNT(*) as total FROM qr_codes WHERE ativo = 1");
if ($result && $row = $result->fetch_assoc()) {
    $stats['qr_codes_ativos'] = $row['total'];
}

$result = $conn->query("SELECT COUNT(*) as total FROM alunos a LEFT JOIN qr_codes q ON a.id = q.aluno_id WHERE q.id IS NULL");
if ($result && $row = $result->fetch_assoc()) {
    $stats['alunos_sem_qr'] = $row['total'];
}

$hoje = date('Y-m-d');
$result = $conn->query("SELECT COUNT(*) as total FROM presencas WHERE data = '$hoje'");
if ($result && $row = $result->fetch_assoc()) {
    $stats['usos_hoje'] = $row['total'];
}

// Buscar QR Codes
$qr_codes_query = "
    SELECT q.*, a.nome as aluno_nome, a.serie, a.curso 
    FROM qr_codes q
    LEFT JOIN alunos a ON q.aluno_id = a.id
    ORDER BY a.nome
";
$qr_codes = $conn->query($qr_codes_query);

// Buscar alunos sem QR Code
$alunos_sem_qr_query = "
    SELECT a.* 
    FROM alunos a 
    LEFT JOIN qr_codes q ON a.id = q.aluno_id 
    WHERE q.id IS NULL 
    ORDER BY a.nome
";
$alunos_sem_qr = $conn->query($alunos_sem_qr_query);

// CSS personalizado
$custom_css = '
.qr-code-display {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
    border: 2px dashed #dee2e6;
    text-align: center;
    margin: 10px 0;
}

.qr-code-string {
    background: #e9ecef;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 0.9em;
    word-break: break-all;
}
';

// Conteúdo da página
ob_start();
?>

<div class="container-fluid py-4">
    <!-- Alerts -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= htmlspecialchars($success_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= htmlspecialchars($error_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-qrcode text-primary me-2"></i>
                        QR Codes dos Alunos
                    </h1>
                    <p class="text-muted mb-0">Geração e gestão de QR Codes individuais para controle de presenças</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#gerarQRModal">
                        <i class="fas fa-plus me-2"></i>
                        Gerar QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle bg-primary p-3">
                            <i class="fas fa-qrcode text-white fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="card-title text-primary mb-1"><?= number_format($stats['total_qr_codes']) ?></h3>
                    <p class="card-text text-muted mb-0">Total QR Codes</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle bg-success p-3">
                            <i class="fas fa-check-circle text-white fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="card-title text-success mb-1"><?= number_format($stats['qr_codes_ativos']) ?></h3>
                    <p class="card-text text-muted mb-0">QR Codes Ativos</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle bg-warning p-3">
                            <i class="fas fa-user-times text-white fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="card-title text-warning mb-1"><?= number_format($stats['alunos_sem_qr']) ?></h3>
                    <p class="card-text text-muted mb-0">Alunos sem QR</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="rounded-circle bg-info p-3">
                            <i class="fas fa-chart-line text-white fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="card-title text-info mb-1"><?= number_format($stats['usos_hoje']) ?></h3>
                    <p class="card-text text-muted mb-0">Usos Hoje</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de QR Codes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list text-primary me-2"></i>
                        QR Codes Cadastrados
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Aluno</th>
                                    <th>Série/Curso</th>
                                    <th>Código QR</th>
                                    <th>Data Geração</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($qr_codes && $qr_codes->num_rows > 0): ?>
                                    <?php while ($qr = $qr_codes->fetch_assoc()): ?>
                                        <?php
                                        $status_class = $qr['ativo'] ? 'ativo' : 'inativo';
                                        $status_text = $qr['ativo'] ? 'Ativo' : 'Inativo';
                                        $status_badge = $qr['ativo'] ? 'success' : 'secondary';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user text-muted me-2"></i>
                                                    <span class="fw-semibold"><?= htmlspecialchars($qr['aluno_nome']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark"><?= htmlspecialchars($qr['serie']) ?> - <?= htmlspecialchars($qr['curso']) ?></span>
                                            </td>
                                            <td>
                                                <div class="qr-code-string">
                                                    <small><?= htmlspecialchars($qr['codigo_qr']) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($qr['data_geracao'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $status_badge ?>"><?= $status_text ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary" 
                                                            onclick="visualizarQR('<?= htmlspecialchars($qr['codigo_qr']) ?>', '<?= htmlspecialchars($qr['aluno_nome']) ?>')"
                                                            title="Visualizar QR">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-primary" 
                                                            onclick="imprimirQR(<?= $qr['id'] ?>)"
                                                            title="Imprimir QR">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning" 
                                                            onclick="regenerarQR(<?= $qr['id'] ?>)"
                                                            title="Regenerar QR">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-<?= $qr['ativo'] ? 'danger' : 'success' ?>" 
                                                            onclick="toggleStatus(<?= $qr['id'] ?>, <?= $qr['ativo'] ? 'false' : 'true' ?>)"
                                                            title="<?= $qr['ativo'] ? 'Desativar' : 'Ativar' ?>">
                                                        <i class="fas fa-<?= $qr['ativo'] ? 'times' : 'check' ?>"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-qrcode fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Nenhum QR Code cadastrado</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Gerar QR Code -->
<div class="modal fade" id="gerarQRModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode text-primary me-2"></i>
                    Gerar QR Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="aluno_id" class="form-label">Selecionar Aluno</label>
                        <select class="form-select" id="aluno_id" name="aluno_id" required>
                            <option value="">Escolha um aluno...</option>
                            <?php if ($alunos_sem_qr && $alunos_sem_qr->num_rows > 0): ?>
                                <?php while ($aluno = $alunos_sem_qr->fetch_assoc()): ?>
                                    <option value="<?= $aluno['id'] ?>">
                                        <?= htmlspecialchars($aluno['nome']) ?> - <?= htmlspecialchars($aluno['serie']) ?> (<?= htmlspecialchars($aluno['curso']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        O QR Code será gerado automaticamente e ficará válido por 1 ano.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="gerar_qr" class="btn btn-primary">
                        <i class="fas fa-qrcode me-2"></i>
                        Gerar QR Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function visualizarQR(codigo, nomeAluno) {
    // Criar modal para visualizar QR Code
    const modalHtml = `
        <div class="modal fade" id="visualizarQRModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-qrcode text-primary me-2"></i>
                            QR Code - ${nomeAluno}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <h6 class="text-muted">Código: ${codigo}</h6>
                        </div>
                        <div class="qr-display-container">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(codigo)}" 
                                 alt="QR Code" 
                                 class="img-fluid border rounded"
                                 style="max-width: 300px; background: white; padding: 20px;">
                        </div>
                        <div class="mt-3">
                            <p class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                QR Code para controle de presença no embarque/desembarque
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" onclick="downloadQR('${codigo}', '${nomeAluno}')">
                            <i class="fas fa-download me-2"></i>
                            Baixar QR Code
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal existente se houver
    const existingModal = document.getElementById('visualizarQRModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Adicionar novo modal ao body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('visualizarQRModal'));
    modal.show();
    
    // Remover modal do DOM quando fechado
    document.getElementById('visualizarQRModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function downloadQR(codigo, nomeAluno) {
    // Buscar dados completos do aluno
    const qrRow = Array.from(document.querySelectorAll('tbody tr')).find(row => {
        const qrCode = row.querySelector('.qr-code-string');
        return qrCode && qrCode.textContent.trim() === codigo;
    });
    
    let serie = '', curso = '';
    if (qrRow) {
        const serieElement = qrRow.querySelector('.badge');
        if (serieElement) {
            const serieTexto = serieElement.textContent.trim();
            const parts = serieTexto.split(' - ');
            serie = parts[0] || '';
            curso = parts[1] || '';
        }
    }
    
    // Criar canvas para gerar imagem com QR Code + informações
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    // Dimensões do canvas
    canvas.width = 600;
    canvas.height = 750;
    
    // Fundo branco
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Carregar imagem do QR Code
    const qrImg = new Image();
    qrImg.crossOrigin = 'anonymous';
    qrImg.onload = function() {
        // Desenhar QR Code centralizado
        const qrSize = 300;
        const qrX = (canvas.width - qrSize) / 2;
        const qrY = 50;
        ctx.drawImage(qrImg, qrX, qrY, qrSize, qrSize);
        
        // Configurar fonte
        ctx.fillStyle = '#000000';
        ctx.textAlign = 'center';
        
        // Título
        ctx.font = 'bold 24px Arial';
        ctx.fillText('QR Code do Estudante', canvas.width / 2, 30);
        
        // Nome do aluno
        ctx.font = 'bold 20px Arial';
        ctx.fillStyle = '#007bff';
        ctx.fillText(nomeAluno, canvas.width / 2, qrY + qrSize + 50);
        
        // Informações do aluno
        ctx.font = '16px Arial';
        ctx.fillStyle = '#333333';
        let yPos = qrY + qrSize + 90;
        
        if (serie) {
            ctx.fillText('Série: ' + serie, canvas.width / 2, yPos);
            yPos += 30;
        }
        
        if (curso) {
            ctx.fillText('Curso: ' + curso, canvas.width / 2, yPos);
            yPos += 30;
        }
        
        // Código QR
        ctx.font = '14px monospace';
        ctx.fillStyle = '#666666';
        ctx.fillText('Código: ' + codigo, canvas.width / 2, yPos + 20);
        
        // Instruções
        ctx.font = '12px Arial';
        ctx.fillStyle = '#999999';
        yPos += 60;
        
        const instrucoes = [
            'INSTRUÇÕES DE USO:',
            '• Este QR Code é individual e intransferível',
            '• Apresente no momento do embarque/desembarque',
            '• Mantenha o código em bom estado',
            '• Em caso de perda, solicite nova via'
        ];
        
        instrucoes.forEach((instrucao, index) => {
            if (index === 0) {
                ctx.font = 'bold 12px Arial';
                ctx.fillStyle = '#333333';
            } else {
                ctx.font = '11px Arial';
                ctx.fillStyle = '#666666';
            }
            ctx.fillText(instrucao, canvas.width / 2, yPos + (index * 20));
        });
        
        // Data de geração
        ctx.font = '10px Arial';
        ctx.fillStyle = '#999999';
        const agora = new Date();
        const dataFormatada = agora.toLocaleDateString('pt-BR') + ' ' + agora.toLocaleTimeString('pt-BR');
        ctx.fillText('Gerado em: ' + dataFormatada, canvas.width / 2, canvas.height - 20);
        
        // Converter canvas para blob e fazer download
        canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `QR_Code_${nomeAluno.replace(/[^a-zA-Z0-9]/g, '_')}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }, 'image/png');
    };
    
    qrImg.onerror = function() {
        // Fallback: download só o QR Code se houver erro
        const link = document.createElement('a');
        link.href = `https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=${encodeURIComponent(codigo)}`;
        link.download = `QR_Code_${nomeAluno.replace(/[^a-zA-Z0-9]/g, '_')}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };
    
    // Carregar QR Code
    qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(codigo)}`;
}

function imprimirQR(qrId) {
    if (confirm('Deseja imprimir o QR Code?')) {
        // Abrir página de impressão em nova aba
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'imprimir-qr.php';
        form.target = '_blank';
        form.innerHTML = `<input type="hidden" name="qr_id" value="${qrId}">`;
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
}

function regenerarQR(qrId) {
    if (confirm('Tem certeza que deseja regenerar este QR Code? O código atual será invalidado.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="regenerar_qr" value="1">
            <input type="hidden" name="qr_id" value="${qrId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleStatus(qrId, novoStatus) {
    const acao = novoStatus ? 'ativar' : 'desativar';
    if (confirm(`Tem certeza que deseja ${acao} este QR Code?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="toggle_status" value="1">
            <input type="hidden" name="qr_id" value="${qrId}">
            <input type="hidden" name="novo_status" value="${novoStatus}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
$content = ob_get_clean();

// Incluir layout
include 'includes/layout-professional.php';
$conn->close();
?>
