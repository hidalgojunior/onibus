<?php
$current_page = "autorizacoes";
include '../includes/page-layout.php';

// Incluir configuração do banco
include '../config/config.php';

// Configuração do breadcrumb
$breadcrumb = [
    ['label' => 'Autorizações']
];

// Ações do header
$actions = [
    [
        'url' => '#', 
        'icon' => 'fas fa-file-pdf', 
        'label' => 'Gerar PDF'
    ],
    [
        'url' => '#', 
        'icon' => 'fas fa-print', 
        'label' => 'Imprimir'
    ]
];

// Renderizar header simplificado
renderHeader("Autorizações");

// Obter conexão com banco
$conn = getDatabaseConnection();
?>

<!-- Container Principal -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-gray-600 to-gray-700 rounded-xl p-8 mb-8 text-white">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-6 md:mb-0">
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-file-contract mr-3"></i>Autorizações de Transporte
                </h1>
                <p class="text-lg opacity-90">Gere documentos oficiais para transporte de alunos</p>
            </div>
            <div class="text-center">
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                    <i class="fas fa-stamp text-4xl mb-2"></i>
                    <p class="text-sm opacity-80">Oficial</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6">
            <style>
                .autorizacao-preview {
                    border: 1px solid #e5e7eb;
                    padding: 20px;
                    margin-top: 20px;
                    background-color: #f9fafb;
                    font-family: Arial, sans-serif;
                    border-radius: 8px;
                }
                .autorizacao-content {
                    white-space: pre-line;
                }
                .page-break-after {
                    page-break-after: always;
                }
                @media print {
                    .no-print {
                        display: none !important;
                    }
                    .autorizacao-preview {
                        border: 1px solid #000;
                        background-color: white;
                        border-radius: 0;
                        margin: 0;
                        padding: 20px;
                        box-shadow: none;
                    }
                    .page-break-after {
                        page-break-after: always;
                    }
                    .page-break-after:last-child {
                        page-break-after: avoid;
                    }
                    /* Esconder elementos de interface */
                    .bg-gray-50, .border-gray-300, .text-gray-500 {
                        display: none !important;
                    }
                    /* Garantir que texto seja preto na impressão */
                    .autorizacao-content {
                        color: #000 !important;
                    }
                }
            </style>

            <!-- Formulário de Geração -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Selecionar Alunos e Configurações</h2>
                
                <form method="POST" class="space-y-6" id="autorizacaoForm">
                    <!-- Seleção de Alunos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            <i class="fas fa-users mr-2"></i>Selecionar Alunos
                        </label>
                        
                        <!-- Botões de Seleção Rápida -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button 
                                type="button" 
                                onclick="selecionarTodos(true)"
                                class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            >
                                <i class="fas fa-check-double mr-1"></i>Selecionar Todos
                            </button>
                            <button 
                                type="button" 
                                onclick="selecionarTodos(false)"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            >
                                <i class="fas fa-times mr-1"></i>Desmarcar Todos
                            </button>
                            <button 
                                type="button" 
                                onclick="filtrarPorSerie()"
                                class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            >
                                <i class="fas fa-filter mr-1"></i>Filtrar por Série
                            </button>
                        </div>

                        <!-- Campo de Busca -->
                        <div class="mb-4">
                            <input 
                                type="text" 
                                id="buscaAluno" 
                                placeholder="Buscar aluno por nome..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                onkeyup="filtrarAlunos()"
                            >
                        </div>

                        <!-- Lista de Alunos -->
                        <div class="border border-gray-300 rounded-lg max-h-96 overflow-y-auto">
                            <div class="p-4 bg-gray-50 border-b border-gray-300">
                                <span class="text-sm font-medium text-gray-700">
                                    <span id="contadorSelecionados">0</span> aluno(s) selecionado(s)
                                </span>
                            </div>
                            <div class="divide-y divide-gray-200" id="listaAlunos">
                                <?php
                                $sql = "SELECT a.id, a.nome, a.serie, a.curso, r.nome as responsavel_nome, r.rg as responsavel_rg
                                        FROM alunos a
                                        LEFT JOIN responsaveis r ON a.id = r.aluno_id
                                        ORDER BY a.nome";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $nome_completo = htmlspecialchars($row['nome'] ?? '');
                                        $serie_curso = htmlspecialchars(($row['curso'] ?? '') . ' / ' . ($row['serie'] ?? ''));
                                        $display_name = $nome_completo . ' - ' . htmlspecialchars($row['serie'] ?? '');
                                        
                                        echo '<div class="p-4 hover:bg-gray-50 aluno-item" data-nome="' . strtolower($nome_completo) . '" data-serie="' . htmlspecialchars($row['serie'] ?? '') . '">';
                                        echo '<label class="flex items-center cursor-pointer">';
                                        echo '<input type="checkbox" name="alunos_ids[]" value="' . $row['id'] . '" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded aluno-checkbox" onchange="atualizarContador()">';
                                        echo '<div class="flex-1">';
                                        echo '<div class="font-medium text-gray-900">' . $display_name . '</div>';
                                        echo '<div class="text-sm text-gray-600">Curso/Turma: ' . $serie_curso . '</div>';
                                        if ($row['responsavel_nome']) {
                                            echo '<div class="text-xs text-gray-500">Responsável: ' . htmlspecialchars($row['responsavel_nome']) . '</div>';
                                        }
                                        echo '</div>';
                                        echo '</label>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="p-4 text-center text-gray-500">Nenhum aluno cadastrado</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tipo de Autorização -->
                        <div>
                            <label for="tipo_autorizacao" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-file-contract mr-2"></i>Tipo de Autorização
                            </label>
                            <select 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                id="tipo_autorizacao" 
                                name="tipo_autorizacao" 
                                required
                            >
                                <option value="">Escolha o tipo...</option>
                                <option value="saida" <?php echo (isset($_POST['tipo_autorizacao']) && $_POST['tipo_autorizacao'] == 'saida') ? 'selected' : ''; ?>>
                                    Autorização de Saída
                                </option>
                                <option value="uso_imagem" <?php echo (isset($_POST['tipo_autorizacao']) && $_POST['tipo_autorizacao'] == 'uso_imagem') ? 'selected' : ''; ?>>
                                    Autorização de Uso de Imagem
                                </option>
                            </select>
                        </div>

                        <!-- Motivo da Saída -->
                        <div>
                            <label for="motivo" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clipboard-list mr-2"></i>Motivo da Saída
                            </label>
                            <input 
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                id="motivo" 
                                name="motivo" 
                                value="<?php echo isset($_POST['motivo']) ? htmlspecialchars($_POST['motivo'] ?? '') : ''; ?>" 
                                placeholder="Ex: Evento Institucional, Visita Técnica"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Evento -->
                        <div>
                            <label for="evento" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2"></i>Evento
                            </label>
                            <input 
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                id="evento" 
                                name="evento" 
                                value="<?php echo isset($_POST['evento']) ? htmlspecialchars($_POST['evento'] ?? '') : 'Bootcamp Jovem Programador'; ?>" 
                                placeholder="Nome do evento"
                            >
                        </div>

                        <!-- Formato de Geração -->
                        <div>
                            <label for="formato_geracao" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-cog mr-2"></i>Formato de Geração
                            </label>
                            <select 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                id="formato_geracao" 
                                name="formato_geracao"
                            >
                                <option value="individual">Uma autorização por aluno</option>
                                <option value="coletiva" <?php echo (isset($_POST['formato_geracao']) && $_POST['formato_geracao'] == 'coletiva') ? 'selected' : ''; ?>>
                                    Todas em uma página
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex flex-wrap gap-3 pt-4">
                        <button 
                            type="submit" 
                            name="gerar" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200"
                            id="btnGerar"
                        >
                            <i class="fas fa-file-alt mr-2"></i>Gerar Autorizações
                        </button>
                        <button 
                            type="button" 
                            onclick="window.print()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 no-print"
                        >
                            <i class="fas fa-print mr-2"></i>Imprimir
                        </button>
                        <a 
                            href="../index.php" 
                            class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-6 py-3 rounded-lg font-medium transition-colors duration-200 no-print"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>Voltar ao Dashboard
                        </a>
                    </div>
                </form>
            </div>

            <!-- Preview da Autorização -->
            <?php
            if (isset($_POST['gerar']) && !empty($_POST['alunos_ids']) && !empty($_POST['tipo_autorizacao'])) {
                $alunos_ids = $_POST['alunos_ids'];
                $tipo_autorizacao = $_POST['tipo_autorizacao'];
                $motivo = isset($_POST['motivo']) ? $_POST['motivo'] : '';
                $evento = isset($_POST['evento']) ? $_POST['evento'] : '';
                $formato_geracao = isset($_POST['formato_geracao']) ? $_POST['formato_geracao'] : 'individual';

                // Buscar modelo de autorização
                $sql = "SELECT * FROM modelos_autorizacao WHERE tipo = ? AND ativo = 1 LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $tipo_autorizacao);
                $stmt->execute();
                $modelo = $stmt->get_result()->fetch_assoc();

                if ($modelo) {
                    echo '<div class="mt-8">';
                    echo '<div class="flex items-center justify-between mb-6">';
                    echo '<h3 class="text-xl font-bold text-gray-900">Autorizações Geradas</h3>';
                    echo '<span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">' . count($alunos_ids) . ' autorização(ões)</span>';
                    echo '</div>';

                    if ($formato_geracao == 'coletiva') {
                        // Formato coletivo - todas as autorizações em uma página
                        echo '<div class="autorizacao-preview">';
                        echo '<h4 class="text-center mb-6 text-lg font-bold">' . htmlspecialchars($modelo['nome']) . '</h4>';
                        
                        $contador = 1;
                        foreach ($alunos_ids as $aluno_id) {
                            // Buscar dados do aluno e responsável
                            $sql = "SELECT a.*, r.id as responsavel_id, r.nome as responsavel_nome, r.rg as responsavel_rg, r.email as responsavel_email
                                    FROM alunos a
                                    LEFT JOIN responsaveis r ON a.id = r.aluno_id
                                    WHERE a.id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $aluno_id);
                            $stmt->execute();
                            $aluno = $stmt->get_result()->fetch_assoc();

                            if ($aluno) {
                                $conteudo = $modelo['conteudo'];

                                // Substituir placeholders
                                $placeholders = [
                                    '{{NomeResponsavel}}' => $aluno['responsavel_nome'] ?: '_______________________________',
                                    '{{RGResponsavel}}' => $aluno['responsavel_rg'] ?: '_______________________________',
                                    '{{Estudante}}' => $aluno['nome'],
                                    '{{RG_RM}}' => ($aluno['rg'] ? $aluno['rg'] : '_______________') . ' / ' . ($aluno['rm'] ? $aluno['rm'] : '_______________'),
                                    '{{Curso_Turma}}' => ($aluno['curso'] ?: '_______________') . ' / ' . ($aluno['serie'] ?: '_______________'),
                                    '{{Motivo}}' => $motivo ?: '_______________________________',
                                    '{{Evento}}' => $evento,
                                    '{{data}}' => date('d/m/Y')
                                ];

                                foreach ($placeholders as $placeholder => $value) {
                                    $conteudo = str_replace($placeholder, $value, $conteudo);
                                }

                                echo '<div class="border-b border-gray-300 pb-6 mb-6">';
                                echo '<div class="text-right text-sm text-gray-500 mb-2">Autorização ' . $contador . ' de ' . count($alunos_ids) . '</div>';
                                echo '<div class="autorizacao-content">' . $conteudo . '</div>';
                                echo '</div>';

                                // Salvar autorização no banco
                                $sql_insert = "INSERT INTO autorizacoes (aluno_id, responsavel_id, tipo, conteudo, data_geracao)
                                               VALUES (?, ?, ?, ?, ?)";
                                $stmt_insert = $conn->prepare($sql_insert);
                                $responsavel_id = isset($aluno['responsavel_id']) ? $aluno['responsavel_id'] : null;
                                $data_geracao = date('Y-m-d H:i:s');
                                $stmt_insert->bind_param("iisss", $aluno_id, $responsavel_id, $tipo_autorizacao, $conteudo, $data_geracao);
                                $stmt_insert->execute();

                                $contador++;
                            }
                        }
                        echo '</div>';
                    } else {
                        // Formato individual - uma autorização por vez
                        foreach ($alunos_ids as $aluno_id) {
                            // Buscar dados do aluno e responsável
                            $sql = "SELECT a.*, r.id as responsavel_id, r.nome as responsavel_nome, r.rg as responsavel_rg, r.email as responsavel_email
                                    FROM alunos a
                                    LEFT JOIN responsaveis r ON a.id = r.aluno_id
                                    WHERE a.id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $aluno_id);
                            $stmt->execute();
                            $aluno = $stmt->get_result()->fetch_assoc();

                            if ($aluno) {
                                $conteudo = $modelo['conteudo'];

                                // Substituir placeholders
                                $placeholders = [
                                    '{{NomeResponsavel}}' => $aluno['responsavel_nome'] ?: '_______________________________',
                                    '{{RGResponsavel}}' => $aluno['responsavel_rg'] ?: '_______________________________',
                                    '{{Estudante}}' => $aluno['nome'],
                                    '{{RG_RM}}' => ($aluno['rg'] ? $aluno['rg'] : '_______________') . ' / ' . ($aluno['rm'] ? $aluno['rm'] : '_______________'),
                                    '{{Curso_Turma}}' => ($aluno['curso'] ?: '_______________') . ' / ' . ($aluno['serie'] ?: '_______________'),
                                    '{{Motivo}}' => $motivo ?: '_______________________________',
                                    '{{Evento}}' => $evento,
                                    '{{data}}' => date('d/m/Y')
                                ];

                                foreach ($placeholders as $placeholder => $value) {
                                    $conteudo = str_replace($placeholder, $value, $conteudo);
                                }

                                echo '<div class="autorizacao-preview mb-8 page-break-after">';
                                echo '<div class="flex items-center justify-between mb-4">';
                                echo '<h4 class="text-lg font-bold">' . htmlspecialchars($modelo['nome']) . '</h4>';
                                echo '<div class="text-sm text-gray-600">';
                                echo '<i class="fas fa-user mr-1"></i>' . htmlspecialchars($aluno['nome']) . ' - ' . htmlspecialchars($aluno['serie']);
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="autorizacao-content">' . $conteudo . '</div>';
                                echo '</div>';

                                // Salvar autorização no banco
                                $sql_insert = "INSERT INTO autorizacoes (aluno_id, responsavel_id, tipo, conteudo, data_geracao)
                                               VALUES (?, ?, ?, ?, ?)";
                                $stmt_insert = $conn->prepare($sql_insert);
                                $responsavel_id = isset($aluno['responsavel_id']) ? $aluno['responsavel_id'] : null;
                                $data_geracao = date('Y-m-d H:i:s');
                                $stmt_insert->bind_param("iisss", $aluno_id, $responsavel_id, $tipo_autorizacao, $conteudo, $data_geracao);
                                $stmt_insert->execute();
                            }
                        }
                    }
                    echo '</div>';

                    // Exibir mensagem de sucesso
                    echo '<div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">';
                    echo '<div class="flex items-center">';
                    echo '<i class="fas fa-check-circle mr-3"></i>';
                    echo '<div>';
                    echo '<strong>Sucesso!</strong> ' . count($alunos_ids) . ' autorização(ões) gerada(s) e salva(s) no sistema.';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';

                } else {
                    echo '<div class="mt-8 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Nenhum modelo ativo encontrado para este tipo de autorização.
                          </div>';
                }
            } elseif (isset($_POST['gerar'])) {
                echo '<div class="mt-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Por favor, selecione pelo menos um aluno e o tipo de autorização.
                      </div>';
            }
            ?>
        </div>
    </div>
</div>

<!-- JavaScript para funcionalidades de seleção múltipla -->
<script>
function selecionarTodos(selecionar) {
    const checkboxes = document.querySelectorAll('.aluno-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selecionar;
    });
    atualizarContador();
}

function atualizarContador() {
    const checkboxes = document.querySelectorAll('.aluno-checkbox:checked');
    const contador = checkboxes.length;
    document.getElementById('contadorSelecionados').textContent = contador;
    
    // Atualizar texto do botão
    const btnGerar = document.getElementById('btnGerar');
    if (contador === 0) {
        btnGerar.innerHTML = '<i class="fas fa-file-alt mr-2"></i>Gerar Autorizações';
        btnGerar.disabled = false;
    } else if (contador === 1) {
        btnGerar.innerHTML = '<i class="fas fa-file-alt mr-2"></i>Gerar 1 Autorização';
        btnGerar.disabled = false;
    } else {
        btnGerar.innerHTML = '<i class="fas fa-file-alt mr-2"></i>Gerar ' + contador + ' Autorizações';
        btnGerar.disabled = false;
    }
}

function filtrarAlunos() {
    const busca = document.getElementById('buscaAluno').value.toLowerCase();
    const itens = document.querySelectorAll('.aluno-item');
    
    itens.forEach(item => {
        const nome = item.getAttribute('data-nome');
        if (nome.includes(busca)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function filtrarPorSerie() {
    // Obter todas as séries únicas
    const itens = document.querySelectorAll('.aluno-item');
    const series = new Set();
    
    itens.forEach(item => {
        const serie = item.getAttribute('data-serie');
        if (serie) {
            series.add(serie);
        }
    });
    
    // Criar modal de seleção
    let opcoes = '<option value="">Todas as séries</option>';
    Array.from(series).sort().forEach(serie => {
        opcoes += `<option value="${serie}">${serie}</option>`;
    });
    
    const serieEscolhida = prompt('Escolha a série para filtrar:\n\n' + 
        Array.from(series).sort().join('\n') + 
        '\n\nDigite a série ou deixe vazio para mostrar todas:');
    
    if (serieEscolhida !== null) {
        itens.forEach(item => {
            const serie = item.getAttribute('data-serie');
            if (serieEscolhida === '' || serie === serieEscolhida) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
}

// Validação do formulário
document.getElementById('autorizacaoForm').addEventListener('submit', function(e) {
    const checkboxes = document.querySelectorAll('.aluno-checkbox:checked');
    if (checkboxes.length === 0) {
        e.preventDefault();
        alert('Por favor, selecione pelo menos um aluno para gerar as autorizações.');
        return false;
    }
    
    const tipoAutorizacao = document.getElementById('tipo_autorizacao').value;
    if (!tipoAutorizacao) {
        e.preventDefault();
        alert('Por favor, selecione o tipo de autorização.');
        return false;
    }
    
    // Confirmação para múltiplas autorizações
    if (checkboxes.length > 1) {
        const confirmar = confirm(`Você está prestes a gerar ${checkboxes.length} autorizações. Deseja continuar?`);
        if (!confirmar) {
            e.preventDefault();
            return false;
        }
    }
});

// Inicializar contador
document.addEventListener('DOMContentLoaded', function() {
    atualizarContador();
});
</script>

<?php renderFooter(); ?>
