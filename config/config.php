<?php
// Configuração de timezone brasileiro
require_once 'config_timezone.php';

// Arquivo de configuração de conexão com o banco de dados
// Sempre usa o banco de dados online

// Verificar se as funções já existem para evitar redeclaração
if (!function_exists('getDatabaseConfig')) {
    function getDatabaseConfig() {
        return [
            'host' => '177.153.208.104',
            'usuario' => 'onibus',
            'senha' => 'Devisate@2025@',
            'banco' => 'onibus',
            'ambiente' => 'online'
        ];
    }
}

if (!function_exists('getDatabaseConnection')) {
    function getDatabaseConnection() {
        $config = getDatabaseConfig();

        $conn = new mysqli($config['host'], $config['usuario'], $config['senha'], $config['banco']);

        if ($conn->connect_error) {
            die("Erro de conexão: " . $conn->connect_error);
        }

        return $conn;
    }
}
