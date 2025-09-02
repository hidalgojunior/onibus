<?php
// Configurações da página
$page_title = "Imprimir QR Code";
$page_description = "Impressão de QR Code do Aluno";

// Incluir configuração do banco
include 'config/config.php';

// Conectar ao banco
$conn = getDatabaseConnection();

// Verificar se foi passado o ID do QR Code
if (!isset($_POST['qr_id']) && !isset($_GET['qr_id'])) {
    header('Location: qr-codes-professional.php');
    exit;
}

$qr_id = isset($_POST['qr_id']) ? intval($_POST['qr_id']) : intval($_GET['qr_id']);

// Buscar dados do QR Code
$query = "
    SELECT q.*, a.nome as aluno_nome, a.serie, a.curso, a.responsavel_nome, a.telefone
    FROM qr_codes q
    LEFT JOIN alunos a ON q.aluno_id = a.id
    WHERE q.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $qr_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: qr-codes-professional.php?error=QR Code não encontrado');
    exit;
}

$qr_data = $result->fetch_assoc();

// Gerar URL do QR Code usando API gratuita
$qr_text = $qr_data['codigo_qr'];
$qr_size = '300x300';
$qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?size={$qr_size}&data=" . urlencode($qr_text);

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
}

.qr-card {
    border: 2px solid #000;
    padding: 20px;
    margin: 20px auto;
    max-width: 400px;
    text-align: center;
    background: white;
}

.qr-code-img {
    border: 1px solid #ddd;
    padding: 10px;
    background: white;
}

.student-info {
    margin: 20px 0;
    font-family: Arial, sans-serif;
}

.qr-instructions {
    font-size: 12px;
    color: #666;
    margin-top: 15px;
    line-height: 1.4;
}
</style>';

$content = $print_css . '
<div class="container-fluid py-4">
    <div class="row no-print mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-print text-primary me-2"></i>
                        Imprimir QR Code
                    </h1>
                    <p class="text-muted mb-0">QR Code do aluno ' . htmlspecialchars($qr_data['aluno_nome']) . '</p>
                </div>
                <div>
                    <button class="btn btn-primary me-2" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>
                        Imprimir
                    </button>
                    <a href="qr-codes-professional.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="print-area">
        <div class="qr-card">
            <h2 style="margin-bottom: 20px; color: #333;">QR Code do Estudante</h2>
            
            <div class="student-info">
                <h3 style="color: #007bff; margin-bottom: 10px;">' . htmlspecialchars($qr_data['aluno_nome']) . '</h3>
                <p style="margin: 5px 0;"><strong>Série:</strong> ' . htmlspecialchars($qr_data['serie']) . '</p>
                <p style="margin: 5px 0;"><strong>Curso:</strong> ' . htmlspecialchars($qr_data['curso']) . '</p>
                <p style="margin: 5px 0;"><strong>Código:</strong> ' . htmlspecialchars($qr_data['codigo_qr']) . '</p>
            </div>

            <div style="margin: 30px 0;">
                <img src="' . $qr_api_url . '" alt="QR Code" class="qr-code-img" style="max-width: 250px; height: auto;">
            </div>

            <div class="qr-instructions">
                <p><strong>INSTRUÇÕES DE USO:</strong></p>
                <p>• Este QR Code é individual e intransferível</p>
                <p>• Apresente no momento do embarque/desembarque</p>
                <p>• Mantenha o código em bom estado de conservação</p>
                <p>• Em caso de perda, solicite nova via na secretaria</p>
                <p><em>Gerado em: ' . date('d/m/Y H:i', strtotime($qr_data['data_geracao'])) . '</em></p>
            </div>
        </div>
    </div>

    <div class="row no-print mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Informações do QR Code
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="120">Aluno:</th>
                                    <td>' . htmlspecialchars($qr_data['aluno_nome']) . '</td>
                                </tr>
                                <tr>
                                    <th>Série:</th>
                                    <td>' . htmlspecialchars($qr_data['serie']) . '</td>
                                </tr>
                                <tr>
                                    <th>Curso:</th>
                                    <td>' . htmlspecialchars($qr_data['curso']) . '</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="120">Código QR:</th>
                                    <td><code>' . htmlspecialchars($qr_data['codigo_qr']) . '</code></td>
                                </tr>
                                <tr>
                                    <th>Gerado em:</th>
                                    <td>' . date('d/m/Y H:i:s', strtotime($qr_data['data_geracao'])) . '</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-' . ($qr_data['ativo'] ? 'success' : 'secondary') . '">
                                            ' . ($qr_data['ativo'] ? 'Ativo' : 'Inativo') . '
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-focus na impressão se veio via POST
' . (isset($_POST['qr_id']) ? 'window.onload = function() { window.print(); };' : '') . '
</script>
';

// Incluir layout
include 'includes/layout-professional.php';
$conn->close();
?>
