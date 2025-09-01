<?php
/**
 * Layout Base Simplificado para Páginas
 */

function renderHeader($title, $subtitle = null, $options = []) {
    global $current_page;
    
    // Detectar se estamos na raiz ou em subpasta
    $is_root = (basename(dirname($_SERVER['SCRIPT_NAME'])) === 'onibus');
    $base_path = $is_root ? '' : '../';
    $pages_path = $is_root ? 'pages/' : '';
    
    // Opções padrão
    $defaults = [
        'icon' => 'fas fa-tachometer-alt',
        'breadcrumb' => [],
        'stats' => [],
        'actions' => []
    ];
    
    $options = array_merge($defaults, $options);
    
    // Ajustar URLs das ações baseado no contexto
    if (!empty($options['actions'])) {
        foreach ($options['actions'] as &$action) {
            if (isset($action['url'])) {
                // Se a URL começa com 'pages/', ajustar baseado no contexto
                if (strpos($action['url'], 'pages/') === 0) {
                    $action['url'] = $is_root ? $action['url'] : str_replace('pages/', '', $action['url']);
                }
                // Se a URL é apenas um nome de arquivo (como 'index.php'), usar base_path
                elseif (!strpos($action['url'], '/') && !strpos($action['url'], 'http') && $action['url'] !== '#') {
                    $action['url'] = $base_path . $action['url'];
                }
            }
        }
    }
    
    // Definir ícones específicos por página
    $page_icons = [
        'home' => 'fas fa-home',
        'eventos' => 'fas fa-calendar-alt',
        'candidaturas' => 'fas fa-users',
        'onibus' => 'fas fa-bus',
        'alocacao' => 'fas fa-map-marked-alt',
        'alocacoes' => 'fas fa-list-ul',
        'autorizacoes' => 'fas fa-check-circle'
    ];
    
    if (isset($page_icons[$current_page ?? ''])) {
        $options['icon'] = $page_icons[$current_page];
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title ?? 'Sistema de Ônibus') ?> - Sistema de Ônibus</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link href="<?= $base_path ?>assets/css/transport-theme.css" rel="stylesheet">
        
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            // Azul - Confiança, segurança e tecnologia
                            'azul-escuro': '#1E3A8A',
                            'azul-claro': '#3B82F6',
                            // Verde - Aprovação, segurança no trajeto
                            'verde-medio': '#10B981',
                            'verde-claro': '#6EE7B7',
                            // Amarelo - Referência ao ônibus escolar
                            'amarelo-onibus': '#FACC15',
                            // Cinza neutro
                            'cinza-claro': '#F3F4F6',
                            'cinza-medio': '#9CA3AF',
                            'preto-suave': '#111827',
                            // Aliases para compatibilidade
                            primary: '#3B82F6',
                            secondary: '#1E3A8A',
                            success: '#10B981',
                            warning: '#FACC15',
                            danger: '#EF4444',
                        }
                    }
                }
            }
        </script>
    </head>
    <body class="bg-gray-50 min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="<?= $base_path ?>index.php" class="flex items-center space-x-2">
                            <i class="fas fa-bus text-amarelo-onibus text-2xl"></i>
                            <span class="text-xl font-bold text-preto-suave">Sistema Ônibus</span>
                        </a>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="<?= $base_path ?>index.php" class="nav-link <?= ($current_page ?? '') === 'home' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">
                            <i class="fas fa-home mr-1"></i>Dashboard
                        </a>
                        <a href="<?= $pages_path ?>eventos.php" class="nav-link <?= ($current_page ?? '') === 'eventos' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">
                            <i class="fas fa-calendar mr-1"></i>Eventos
                        </a>
                        <a href="<?= $pages_path ?>candidaturas.php" class="nav-link <?= ($current_page ?? '') === 'candidaturas' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">
                            <i class="fas fa-users mr-1"></i>Candidaturas
                        </a>
                        <a href="<?= $pages_path ?>onibus.php" class="nav-link <?= ($current_page ?? '') === 'onibus' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">
                            <i class="fas fa-bus mr-1"></i>Ônibus
                        </a>
                        <a href="<?= $pages_path ?>alocacao.php" class="nav-link <?= ($current_page ?? '') === 'alocacao' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">
                            <i class="fas fa-map-marked-alt mr-1"></i>Alocação
                        </a>
                        <a href="<?= $pages_path ?>alocacoes.php" class="nav-link <?= ($current_page ?? '') === 'alocacoes' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">
                            <i class="fas fa-list mr-1"></i>Alocações
                        </a>
                        <a href="<?= $pages_path ?>autorizacoes.php" class="nav-link <?= ($current_page ?? '') === 'autorizacoes' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-700 hover:text-blue-600' ?>">
                            <i class="fas fa-check-circle mr-1"></i>Autorizações
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
                <div class="px-4 pt-2 pb-3 space-y-1">
                    <a href="<?= $base_path ?>index.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <a href="<?= $pages_path ?>eventos.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600">
                        <i class="fas fa-calendar mr-2"></i>Eventos
                    </a>
                    <a href="<?= $pages_path ?>candidaturas.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600">
                        <i class="fas fa-users mr-2"></i>Candidaturas
                    </a>
                    <a href="<?= $pages_path ?>onibus.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600">
                        <i class="fas fa-bus mr-2"></i>Ônibus
                    </a>
                    <a href="<?= $pages_path ?>alocacao.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600">
                        <i class="fas fa-map-marked-alt mr-2"></i>Alocação
                    </a>
                    <a href="<?= $pages_path ?>alocacoes.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600">
                        <i class="fas fa-list mr-2"></i>Alocações
                    </a>
                    <a href="<?= $pages_path ?>autorizacoes.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600">
                        <i class="fas fa-check-circle mr-2"></i>Autorizações
                    </a>
                </div>
            </div>
        </nav>

        <!-- Page Header Simplificado -->
        <div class="page-header">
            <div class="header-decoration top-right">
                <i class="fas fa-bus"></i>
            </div>
            <div class="header-decoration bottom-left">
                <i class="fas fa-route"></i>
            </div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="page-header-content">
                    <!-- Título da Seção Simplificado -->
                    <div class="flex items-center justify-center">
                        <div class="text-center">
                            <div class="header-icon mx-auto mb-3">
                                <i class="<?= htmlspecialchars($options['icon'] ?? 'fas fa-home') ?>"></i>
                            </div>
                            <h1 class="page-title-simple"><?= htmlspecialchars($title ?? 'Página') ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="flex-1">
    <?php
}

function renderFooter() {
    ?>
        </main>
        
        <!-- Footer Personalizado -->
        <footer class="bg-azul-escuro text-white py-8 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Logo e Descrição -->
                    <div class="col-span-1">
                        <div class="flex items-center space-x-2 mb-4">
                            <i class="fas fa-bus text-amarelo-onibus text-2xl"></i>
                            <span class="text-xl font-bold">Sistema Ônibus</span>
                        </div>
                        <p class="text-blue-200 text-sm">
                            Sistema completo para gestão de transporte escolar, 
                            facilitando o controle de rotas, eventos e candidaturas.
                        </p>
                    </div>
                    
                    <!-- Links Rápidos -->
                    <div class="col-span-1">
                        <h3 class="text-lg font-semibold mb-4">Links Rápidos</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="../index.php" class="text-blue-200 hover:text-white transition-colors"><i class="fas fa-home mr-2"></i>Dashboard</a></li>
                            <li><a href="eventos.php" class="text-blue-200 hover:text-white transition-colors"><i class="fas fa-calendar mr-2"></i>Eventos</a></li>
                            <li><a href="onibus.php" class="text-blue-200 hover:text-white transition-colors"><i class="fas fa-bus mr-2"></i>Frota</a></li>
                            <li><a href="autorizacoes.php" class="text-blue-200 hover:text-white transition-colors"><i class="fas fa-check-circle mr-2"></i>Autorizações</a></li>
                        </ul>
                    </div>
                    
                    <!-- Informações -->
                    <div class="col-span-1">
                        <h3 class="text-lg font-semibold mb-4">Informações</h3>
                        <div class="space-y-2 text-sm text-blue-200">
                            <p><i class="fas fa-calendar mr-2"></i>Versão 2.0</p>
                            <p><i class="fas fa-code mr-2"></i>Desenvolvido com TailwindCSS</p>
                            <p><i class="fas fa-shield-alt mr-2"></i>Sistema Seguro</p>
                        </div>
                    </div>
                </div>
                
                <!-- Linha de Copyright -->
                <div class="border-t border-blue-800 mt-8 pt-6 text-center">
                    <p class="text-blue-200 text-sm">
                        &copy; <?= date('Y') ?> Sistema de Transporte Escolar. Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script src="../assets/js/app.js"></script>
        <script>
            // Mobile menu toggle
            document.getElementById('mobile-menu-button').addEventListener('click', function() {
                const menu = document.getElementById('mobile-menu');
                menu.classList.toggle('hidden');
            });
        </script>
    </body>
    </html>
    <?php
}
?>
