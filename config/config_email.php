<?php
// Configurações de email para o sistema de presença
// Este arquivo contém as configurações para envio de relatórios por email

// Lista de emails que receberão os relatórios de presença
$EMAIL_DESTINATARIOS = [
    'coordenador.pedagogico@escola.com',
    'diretor@escola.com',
    'transporte@escola.com'
];

// Configurações do servidor de email (para função mail() do PHP)
$EMAIL_CONFIG = [
    'from_name' => 'Sistema de Controle de Presença',
    'from_email' => 'sistema@escola.com',
    'reply_to' => 'noreply@escola.com'
];

// Assunto base para os emails
$EMAIL_ASSUNTO_BASE = 'Relatório de Presença - Ônibus';

// Função para obter destinatários
function getEmailDestinatarios() {
    global $EMAIL_DESTINATARIOS;
    return $EMAIL_DESTINATARIOS;
}

// Função para obter configurações de email
function getEmailConfig() {
    global $EMAIL_CONFIG;
    return $EMAIL_CONFIG;
}

// Função para obter assunto base
function getEmailAssuntoBase() {
    global $EMAIL_ASSUNTO_BASE;
    return $EMAIL_ASSUNTO_BASE;
}
?></content>
<parameter name="filePath">c:\laragon\www\onibus\config_email.php
