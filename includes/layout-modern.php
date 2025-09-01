<?php
/**
 * Layout moderno e responsivo para o sistema TransportePro
 * 
 * Parâmetros esperados:
 * - $page_title: Título da página
 * - $page_description: Descrição da página (opcional)
 * - $breadcrumb: Array com breadcrumb (opcional)
 * - $custom_css: CSS adicional (opcional)
 * - $custom_js: JavaScript adicional (opcional)
 */

// Configurações padrão
$page_title = $page_title ?? 'TransportePro';
$page_description = $page_description ?? 'Sistema de Gestão de Transporte Escolar';
$breadcrumb = $breadcrumb ?? [];
$custom_css = $custom_css ?? '';
$custom_js = $custom_js ?? '';

// Incluir configuração
include_once 'config/config.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
    <meta name="author" content="TransportePro">
    
    <title><?= htmlspecialchars($page_title) ?> - TransportePro</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Theme CSS -->
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            
            --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            --gradient-success: linear-gradient(135deg, #28a745, #20c997);
            --gradient-warning: linear-gradient(135deg, #ffc107, #fd7e14);
            --gradient-danger: linear-gradient(135deg, #dc3545, #e83e8c);
            --gradient-info: linear-gradient(135deg, #17a2b8, #6f42c1);
            
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 40px rgba(0,0,0,0.1);
            
            --border-radius-sm: 6px;
            --border-radius-md: 12px;
            --border-radius-lg: 20px;
            
            --transition-base: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--dark-color);
            font-size: 14px;
            line-height: 1.6;
        }
        
        /* Layout Components */
        .main-container {
            min-height: calc(100vh - 76px);
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        
        .page-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius-lg);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            padding: 2rem;
        }
        
        .content-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius-lg);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-md);
            transition: var(--transition-base);
        }
        
        .content-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        
        /* Buttons */
        .btn-modern {
            border-radius: var(--border-radius-md);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: var(--transition-base);
            position: relative;
            overflow: hidden;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .btn-gradient-primary {
            background: var(--gradient-primary);
            border: none;
            color: white;
        }
        
        .btn-gradient-success {
            background: var(--gradient-success);
            border: none;
            color: white;
        }
        
        .btn-gradient-warning {
            background: var(--gradient-warning);
            border: none;
            color: white;
        }
        
        .btn-gradient-danger {
            background: var(--gradient-danger);
            border: none;
            color: white;
        }
        
        /* Forms */
        .form-modern .form-control {
            border-radius: var(--border-radius-md);
            border: 2px solid rgba(0,0,0,0.1);
            padding: 0.75rem 1rem;
            transition: var(--transition-base);
            background: rgba(255, 255, 255, 0.8);
        }
        
        .form-modern .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }
        
        .form-modern .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        /* Tables */
        .table-modern {
            background: white;
            border-radius: var(--border-radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .table-modern thead th {
            background: var(--gradient-primary);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }
        
        .table-modern tbody td {
            padding: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            vertical-align: middle;
        }
        
        .table-modern tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }
        
        /* Badges */
        .badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.75rem;
        }
        
        /* Breadcrumb */
        .breadcrumb-modern {
            background: none;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumb-modern .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition-base);
        }
        
        .breadcrumb-modern .breadcrumb-item a:hover {
            color: var(--secondary-color);
        }
        
        .breadcrumb-modern .breadcrumb-item.active {
            color: var(--dark-color);
            font-weight: 600;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        
        /* Loading States */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(102, 126, 234, 0.3);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            
            .page-header {
                padding: 1.5rem;
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gradient-primary);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }
        
        <?= $custom_css ?>
    </style>
</head>

<body>
    <?php include 'includes/navbar-modern.php'; ?>
    
    <div class="container-fluid px-4 main-container">
        <?php if (!empty($breadcrumb) || !empty($page_title)): ?>
            <div class="page-header fade-in-up">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <?php if (!empty($breadcrumb)): ?>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-modern">
                                    <?php foreach ($breadcrumb as $item): ?>
                                        <?php if (isset($item['url'])): ?>
                                            <li class="breadcrumb-item">
                                                <a href="<?= $item['url'] ?>">
                                                    <?php if (isset($item['icon'])): ?>
                                                        <i class="<?= $item['icon'] ?> me-1"></i>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($item['title']) ?>
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li class="breadcrumb-item active">
                                                <?php if (isset($item['icon'])): ?>
                                                    <i class="<?= $item['icon'] ?> me-1"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($item['title']) ?>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ol>
                            </nav>
                        <?php endif; ?>
                        
                        <h1 class="display-6 fw-bold text-dark mb-1"><?= htmlspecialchars($page_title) ?></h1>
                        <?php if (!empty($page_description)): ?>
                            <p class="text-muted mb-0"><?= htmlspecialchars($page_description) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4 text-md-end">
                        <?php if (isset($page_actions)): ?>
                            <?= $page_actions ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="fade-in">
            <?php 
            // Incluir o conteúdo da página
            if (isset($content_file) && file_exists($content_file)) {
                include $content_file;
            }
            ?>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay d-none">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <p class="mt-3 text-muted">Carregando...</p>
        </div>
    </div>
    
    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Global Scripts -->
    <script>
        // Função para mostrar loading
        function showLoading() {
            document.getElementById('loadingOverlay').classList.remove('d-none');
        }
        
        // Função para esconder loading
        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('d-none');
        }
        
        // Sistema de toast moderno
        function showToast(type, title, message) {
            const toastContainer = document.getElementById('toastContainer');
            const toastId = 'toast_' + Date.now();
            
            const iconMap = {
                'success': 'fas fa-check-circle text-success',
                'error': 'fas fa-exclamation-circle text-danger',
                'warning': 'fas fa-exclamation-triangle text-warning',
                'info': 'fas fa-info-circle text-info'
            };
            
            const toastHTML = `
                <div id="${toastId}" class="toast align-items-center border-0 shadow-lg" role="alert" style="
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(20px);
                    border-radius: 12px;
                    margin-bottom: 0.5rem;
                ">
                    <div class="d-flex">
                        <div class="toast-body d-flex align-items-center">
                            <i class="${iconMap[type]} me-3" style="font-size: 1.2rem;"></i>
                            <div>
                                <div class="fw-semibold text-dark">${title}</div>
                                <small class="text-muted">${message}</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }
        
        // Função para confirmar ações
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }
        
        // Animações de entrada para elementos dinâmicos
        function animateIn(element) {
            element.classList.add('fade-in-up');
        }
        
        // Inicialização da página
        document.addEventListener('DOMContentLoaded', function() {
            // Esconder loading inicial
            hideLoading();
            
            // Adicionar animações aos elementos
            const elements = document.querySelectorAll('.content-card, .page-header');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.classList.add('fade-in-up');
                }, index * 100);
            });
        });
        
        <?= $custom_js ?>
    </script>
</body>
</html>
