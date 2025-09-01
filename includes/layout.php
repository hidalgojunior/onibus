<?php
/**
 * Layout Base do Sistema - TailwindCSS
 * Usado por todas as páginas do sistema
 */

// Função para incluir arquivos com caminho correto
function includeFile($path) {
    $fullPath = __DIR__ . '/' . $path;
    if (file_exists($fullPath)) {
        include $fullPath;
    } else {
        // Fallback para caminhos antigos
        $fallbackPath = __DIR__ . '/../' . $path;
        if (file_exists($fallbackPath)) {
            include $fallbackPath;
        }
    }
}

// Configurações da página
$pageTitle = $pageTitle ?? 'Sistema de Ônibus';
$currentPage = $currentPage ?? '';
$additionalCSS = $additionalCSS ?? '';
$additionalJS = $additionalJS ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos customizados -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- TailwindCSS Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a'
                        }
                    },
                    animation: {
                        'fadeIn': 'fadeIn 0.3s ease-in-out',
                        'slideUp': 'slideUp 0.3s ease-in-out'
                    }
                }
            }
        }
    </script>
    
    <?= $additionalCSS ?>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-2 text-white text-xl font-bold">
                        <i class="fas fa-bus"></i>
                        <span>Sistema de Ônibus</span>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Menu Principal -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="index.php" class="nav-link <?= $currentPage === 'home' ? 'bg-blue-700' : '' ?>">
                            <i class="fas fa-home"></i> Início
                        </a>
                        
                        <!-- Dropdown Eventos -->
                        <div class="relative dropdown">
                            <button class="nav-link flex items-center space-x-1" data-toggle="dropdown">
                                <i class="fas fa-calendar"></i>
                                <span>Eventos</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div class="dropdown-menu hidden">
                                <a href="pages/eventos.php" class="dropdown-item">
                                    <i class="fas fa-calendar-plus"></i> Gerenciar Eventos
                                </a>
                                <a href="pages/candidaturas.php" class="dropdown-item">
                                    <i class="fas fa-users"></i> Candidaturas
                                </a>
                            </div>
                        </div>
                        
                        <!-- Dropdown Ônibus -->
                        <div class="relative dropdown">
                            <button class="nav-link flex items-center space-x-1" data-toggle="dropdown">
                                <i class="fas fa-bus"></i>
                                <span>Ônibus</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div class="dropdown-menu hidden">
                                <a href="pages/onibus.php" class="dropdown-item">
                                    <i class="fas fa-bus"></i> Gerenciar Ônibus
                                </a>
                                <a href="pages/alocacao.php" class="dropdown-item">
                                    <i class="fas fa-random"></i> Alocação Automática
                                </a>
                            </div>
                        </div>
                        
                        <!-- Dropdown Sistema -->
                        <div class="relative dropdown">
                            <button class="nav-link flex items-center space-x-1" data-toggle="dropdown">
                                <i class="fas fa-cogs"></i>
                                <span>Sistema</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div class="dropdown-menu hidden">
                                <a href="admin/install.php" class="dropdown-item">
                                    <i class="fas fa-database"></i> Instalar Banco
                                </a>
                                <a href="admin/update_database.php" class="dropdown-item">
                                    <i class="fas fa-sync"></i> Atualizar Banco
                                </a>
                                <a href="admin/update_autorizacoes.php" class="dropdown-item">
                                    <i class="fas fa-shield-alt"></i> Atualizar Autorizações
                                </a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <a href="debug/" class="dropdown-item">
                                    <i class="fas fa-bug"></i> Debug
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menu Mobile -->
                    <div class="md:hidden">
                        <button id="mobile-menu-btn" class="nav-link">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-blue-700">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="index.php" class="block px-3 py-2 text-blue-100 hover:text-white hover:bg-blue-800 rounded-md">
                    <i class="fas fa-home"></i> Início
                </a>
                <a href="pages/eventos.php" class="block px-3 py-2 text-blue-100 hover:text-white hover:bg-blue-800 rounded-md">
                    <i class="fas fa-calendar"></i> Eventos
                </a>
                <a href="pages/candidaturas.php" class="block px-3 py-2 text-blue-100 hover:text-white hover:bg-blue-800 rounded-md">
                    <i class="fas fa-users"></i> Candidaturas
                </a>
                <a href="pages/onibus.php" class="block px-3 py-2 text-blue-100 hover:text-white hover:bg-blue-800 rounded-md">
                    <i class="fas fa-bus"></i> Ônibus
                </a>
                <a href="admin/" class="block px-3 py-2 text-blue-100 hover:text-white hover:bg-blue-800 rounded-md">
                    <i class="fas fa-cogs"></i> Administração
                </a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <?php
        // Incluir conteúdo da página
        if (isset($pageContent)) {
            echo $pageContent;
        }
        ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-bus"></i>
                    <span class="font-semibold">Sistema de Ônibus</span>
                    <span class="text-gray-400">v2.0</span>
                </div>
                <div class="mt-4 md:mt-0 text-gray-400 text-sm">
                    © <?= date('Y') ?> - Desenvolvido com TailwindCSS
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="assets/js/app.js"></script>
    
    <!-- Mobile menu toggle -->
    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
    
    <?= $additionalJS ?>
</body>
</html>
