<?php
/**
 * EXEMPLO DE CONFIGURAÇÃO DE EMAIL
 *
 * Este é um arquivo de exemplo mostrando como configurar
 * o sistema de email para diferentes cenários escolares.
 *
 * COPIE as configurações que desejar para o arquivo config_email.php
 */

// =================================================================
// CENÁRIO 1: ESCOLA PEQUENA (até 200 alunos)
// =================================================================

$CONFIG_ESCOLA_PEQUENA = [
    'destinatarios' => [
        'diretor@escola.com',
        'coordenador@escola.com',
        'secretaria@escola.com'
    ],
    'config' => [
        'from_name' => 'Sistema Escolar - Controle de Presença',
        'from_email' => 'sistema@escola.com',
        'reply_to' => 'noreply@escola.com'
    ]
];

// =================================================================
// CENÁRIO 2: ESCOLA MÉDIA (200-500 alunos)
// =================================================================

$CONFIG_ESCOLA_MEDIA = [
    'destinatarios' => [
        'diretor@escola.com',
        'vice.diretor@escola.com',
        'coordenador.pedagogico@escola.com',
        'coordenador.transporte@escola.com',
        'responsavel.ti@escola.com'
    ],
    'config' => [
        'from_name' => 'Sistema de Gestão Escolar',
        'from_email' => 'gestao@escola.com',
        'reply_to' => 'noreply@escola.com'
    ]
];

// =================================================================
// CENÁRIO 3: REDE DE ESCOLAS
// =================================================================

$CONFIG_REDE_ESCOLAR = [
    'destinatarios' => [
        // Direção geral
        'diretor.geral@redeescolar.com',
        'coordenador.geral@redeescolar.com',

        // Direção da unidade
        'diretor@unidade1.escola.com',
        'coordenador@unidade1.escola.com',

        // Transporte
        'transporte@redeescolar.com',

        // TI
        'suporte@redeescolar.com'
    ],
    'config' => [
        'from_name' => 'Sistema de Controle de Presença - Rede Escolar',
        'from_email' => 'sistema@redeescolar.com',
        'reply_to' => 'noreply@redeescolar.com'
    ]
];

// =================================================================
// CONFIGURAÇÕES POR TIPO DE EVENTO
// =================================================================

/**
 * Configurações específicas para diferentes tipos de evento
 * Útil quando diferentes pessoas precisam receber relatórios
 * de diferentes tipos de atividade
 */
$CONFIG_POR_EVENTO = [
    'bootcamp' => [
        'destinatarios' => [
            'coordenador.ti@escola.com',
            'professor.programacao@escola.com'
        ],
        'assunto' => 'Relatório Bootcamp - Presença'
    ],

    'excursao' => [
        'destinatarios' => [
            'coordenador.pedagogico@escola.com',
            'responsavel.transporte@escola.com'
        ],
        'assunto' => 'Relatório Excursão - Presença'
    ],

    'evento_esportivo' => [
        'destinatarios' => [
            'coordenador.esportivo@escola.com',
            'professor.educacao.fisica@escola.com'
        ],
        'assunto' => 'Relatório Evento Esportivo - Presença'
    ]
];

// =================================================================
// CONFIGURAÇÕES AVANÇADAS
// =================================================================

/**
 * Configurações para múltiplos servidores de email
 * Útil para redundância ou diferentes tipos de relatório
 */
$CONFIG_SERVIDORES_MULTIPLOS = [
    'primario' => [
        'host' => 'smtp.escola.com',
        'porta' => 587,
        'seguranca' => 'tls',
        'usuario' => 'sistema@escola.com',
        'senha' => 'senha_segura'
    ],

    'backup' => [
        'host' => 'smtp.gmail.com',
        'porta' => 587,
        'seguranca' => 'tls',
        'usuario' => 'backup@escola.com',
        'senha' => 'senha_backup'
    ]
];

// =================================================================
// FUNÇÕES DE EXEMPLO
// =================================================================

/**
 * Exemplo de função para obter configuração por tamanho da escola
 */
function getConfigPorTamanhoEscola($tamanho) {
    global $CONFIG_ESCOLA_PEQUENA, $CONFIG_ESCOLA_MEDIA, $CONFIG_REDE_ESCOLAR;

    switch ($tamanho) {
        case 'pequena':
            return $CONFIG_ESCOLA_PEQUENA;
        case 'media':
            return $CONFIG_ESCOLA_MEDIA;
        case 'rede':
            return $CONFIG_REDE_ESCOLAR;
        default:
            return $CONFIG_ESCOLA_PEQUENA;
    }
}

/**
 * Exemplo de função para configuração por tipo de evento
 */
function getConfigPorEvento($tipoEvento) {
    global $CONFIG_POR_EVENTO;

    return $CONFIG_POR_EVENTO[$tipoEvento] ?? $CONFIG_POR_EVENTO['bootcamp'];
}

/**
 * Exemplo de função para validar configuração
 */
function validarConfiguracaoEmail($config) {
    $erros = [];

    // Verifica se há destinatários
    if (empty($config['destinatarios'])) {
        $erros[] = 'Nenhum destinatário configurado';
    }

    // Verifica formato dos emails
    foreach ($config['destinatarios'] as $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = "Email inválido: $email";
        }
    }

    // Verifica configurações obrigatórias
    if (empty($config['config']['from_email'])) {
        $erros[] = 'Email do remetente não configurado';
    }

    return $erros;
}

// =================================================================
// INSTRUÇÕES DE USO
// =================================================================

/*
Para usar este arquivo de exemplo:

1. COPIE as configurações desejadas
2. COLE no arquivo config_email.php
3. PERSONALIZE os emails e nomes
4. TESTE o envio de emails

Exemplo de uso:

$config = getConfigPorTamanhoEscola('media');
$EMAIL_DESTINATARIOS = $config['destinatarios'];
$EMAIL_CONFIG = $config['config'];

Ou para eventos específicos:

$configEvento = getConfigPorEvento('bootcamp');
$destinatariosEspecificos = $configEvento['destinatarios'];

*/

?></content>
<parameter name="filePath">c:\laragon\www\onibus\exemplo_config_email.php
