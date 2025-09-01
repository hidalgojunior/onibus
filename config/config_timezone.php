<?php
// Configuração de timezone para horário brasileiro
date_default_timezone_set('America/Sao_Paulo');

// Configurações regionais brasileiras
setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'portuguese');

// Configuração de moeda brasileira
setlocale(LC_MONETARY, 'pt_BR.UTF-8', 'pt_BR', 'portuguese');

// Função para formatar data em português brasileiro
if (!function_exists('formatarDataBR')) {
    function formatarDataBR($data, $formato = 'completa') {
        $timestamp = strtotime($data);

        // Mapeamento de meses em português
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
        ];

        // Mapeamento de dias da semana em português
        $dias_semana = [
            0 => 'domingo', 1 => 'segunda-feira', 2 => 'terça-feira', 3 => 'quarta-feira',
            4 => 'quinta-feira', 5 => 'sexta-feira', 6 => 'sábado'
        ];

        $dia = date('j', $timestamp);
        $mes = (int)date('n', $timestamp);
        $ano = date('Y', $timestamp);
        $dia_semana = (int)date('w', $timestamp);

        switch ($formato) {
            case 'completa':
                return $dias_semana[$dia_semana] . ', ' . $dia . ' de ' . $meses[$mes] . ' de ' . $ano;
            case 'curta':
                return date('d/m/Y', $timestamp);
            case 'hora':
                return date('H:i', $timestamp);
            case 'datahora':
                return date('d/m/Y H:i', $timestamp);
            case 'mes_ano':
                return $meses[$mes] . ' de ' . $ano;
            default:
                return date($formato, $timestamp);
        }
    }
}

// Função para obter data/hora atual formatada
if (!function_exists('agoraBR')) {
    function agoraBR($formato = 'd/m/Y H:i:s') {
        return date($formato);
    }
}
