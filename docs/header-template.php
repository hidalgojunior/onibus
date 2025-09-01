<?php
/**
 * TEMPLATE PADRÃO PARA HEADERS PERSONALIZADOS
 * Sistema de Transporte Escolar
 * 
 * Use este template como base para criar headers consistentes
 * em todas as páginas do sistema.
 */

// ============================================
// CONFIGURAÇÃO BÁSICA DA PÁGINA
// ============================================
$current_page = "nome_da_pagina"; // onibus, alocacao, autorizacoes, etc.
include '../includes/page-layout.php';

// ============================================
// CONFIGURAÇÃO DO BREADCRUMB
// ============================================
$breadcrumb = [
    // Para páginas simples (apenas o nome da página)
    ['label' => 'Nome da Página']
    
    // Para páginas com navegação hierárquica
    // ['label' => 'Categoria', 'url' => 'categoria.php'],
    // ['label' => 'Subcategoria']
];

// ============================================
// AÇÕES DO HEADER (BOTÕES)
// ============================================
$actions = [
    [
        'url' => 'link_para_acao.php',     // URL da ação
        'icon' => 'fas fa-icon-name',      // Ícone FontAwesome
        'label' => 'Nome da Ação'         // Texto do botão
    ],
    [
        'url' => 'outra_acao.php',
        'icon' => 'fas fa-another-icon',
        'label' => 'Outra Ação'
    ]
    // Máximo recomendado: 2-3 ações por header
];

// ============================================
// ESTATÍSTICAS OPCIONAIS (PARA DASHBOARDS)
// ============================================
$stats_header = [
    ['number' => 10, 'label' => 'Total de Itens'],
    ['number' => 5, 'label' => 'Ativos'],
    ['number' => 3, 'label' => 'Pendentes'],
    ['number' => 2, 'label' => 'Concluídos']
    // Use apenas em páginas que precisam mostrar números importantes
];

// ============================================
// RENDERIZAÇÃO DO HEADER
// ============================================
renderHeader("Título da Página", "Descrição opcional da funcionalidade", [
    'icon' => 'fas fa-icon-principal',     // Ícone principal da página
    'breadcrumb' => $breadcrumb,           // Navegação breadcrumb
    'stats' => $stats_header,              // Estatísticas (opcional)
    'actions' => $actions                  // Botões de ação
]);
?>

<!-- ============================================ -->
<!-- CONTEÚDO DA PÁGINA -->
<!-- ============================================ -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Seu conteúdo aqui -->
    <div class="card">
        <div class="card-header bg-primary">
            <h2 class="text-xl font-semibold text-white">
                <i class="fas fa-icon mr-2"></i>
                Título da Seção
            </h2>
        </div>
        <div class="card-body">
            <p>Conteúdo da página...</p>
        </div>
    </div>

</div>

<?php renderFooter(); ?>

<!--
=============================================
GUIA DE ÍCONES RECOMENDADOS POR PÁGINA
=============================================

Dashboard:      fas fa-tachometer-alt
Eventos:        fas fa-calendar-alt
Candidaturas:   fas fa-users
Ônibus:         fas fa-bus
Alocação:       fas fa-map-marked-alt
Alocações:      fas fa-list-ul
Autorizações:   fas fa-check-circle
Relatórios:     fas fa-chart-bar
Configurações:  fas fa-cogs
Usuários:       fas fa-user-circle

=============================================
CORES DO TEMA DE TRANSPORTE
=============================================

Azul Escuro:    #1E3A8A (confiança, cabeçalhos)
Azul Claro:     #3B82F6 (botões de ação)
Verde Médio:    #10B981 (aprovado, sucesso)
Verde Claro:    #6EE7B7 (destaques sutis)
Amarelo Ônibus: #FACC15 (ícones de transporte)
Cinza Claro:    #F3F4F6 (fundos)
Cinza Médio:    #9CA3AF (texto secundário)
Preto Suave:    #111827 (texto principal)

=============================================
-->
