<?php
if (!isset($page_title)) $page_title = "Dashboard";
if (!isset($page_description)) $page_description = "Gestão de Frota";
if (!isset($content)) $content = "";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Sistema Ônibus</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1e3a5f;
            --secondary-color: #2c5282;
            --accent-color: #4299e1;
            --success-color: #38a169;
            --warning-color: #d69e2e;
            --danger-color: #e53e3e;
            --light-gray: #f7fafc;
            --medium-gray: #edf2f7;
            --text-gray: #718096;
            --text-dark: #2d3748;
            --white: #ffffff;
            --border-radius: 12px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--light-gray);
            color: var(--text-dark);
            line-height: 1.5;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background-color: var(--primary-color);
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            padding: 0;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            color: var(--white);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .sidebar-logo i {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }
        
        .sidebar-subtitle {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.25rem;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin: 0.25rem 1rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white);
            transform: translateX(4px);
        }
        
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: var(--white);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }
        
        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            background-color: var(--light-gray);
        }
        
        /* Header Styles */
        .main-header {
            background-color: var(--white);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--medium-gray);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }
        
        .header-title p {
            color: var(--text-gray);
            margin: 0;
            font-size: 0.875rem;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .notification-icon {
            position: relative;
            color: var(--text-gray);
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        
        .notification-icon:hover {
            color: var(--primary-color);
        }
        
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--danger-color);
            color: var(--white);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--border-radius);
            transition: background-color 0.2s ease;
        }
        
        .user-menu:hover {
            background-color: var(--medium-gray);
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            background-color: var(--primary-color);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        /* Content Area Styles */
        .content-area {
            padding: 2rem;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        
        .stat-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }
        
        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .stat-title {
            color: var(--text-gray);
            font-size: 0.875rem;
            font-weight: 500;
            margin: 0;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--white);
        }
        
        .stat-icon.primary { background-color: var(--primary-color); }
        .stat-icon.success { background-color: var(--success-color); }
        .stat-icon.warning { background-color: var(--warning-color); }
        .stat-icon.danger { background-color: var(--danger-color); }
        .stat-icon.info { background-color: var(--accent-color); }
        
        .stat-content {
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1;
            margin-bottom: 0.25rem;
        }
        
        .stat-subtitle {
            color: var(--text-gray);
            font-size: 0.875rem;
            margin: 0;
        }
        
        .stat-footer {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
        }
        
        .stat-change {
            font-weight: 600;
            margin-right: 0.5rem;
        }
        
        .stat-change.positive { color: var(--success-color); }
        .stat-change.negative { color: var(--danger-color); }
        .stat-change.neutral { color: var(--text-gray); }
        
        .stat-period {
            color: var(--text-gray);
        }
        
        /* Chart Container */
        .chart-container {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .chart-header {
            margin-bottom: 1.5rem;
        }
        
        .chart-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0 0 0.25rem 0;
        }
        
        .chart-subtitle {
            color: var(--text-gray);
            font-size: 0.875rem;
            margin: 0;
        }
        
        .chart-wrapper {
            position: relative;
            height: 350px;
            width: 100%;
        }
        
        .chart-wrapper canvas {
            max-height: 350px !important;
            width: 100% !important;
        }
        
        /* Chart Grid Layouts */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        
        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0,0,0,.1);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .content-area {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
        
        /* Alert Styles */
        .alert-item {
            display: flex;
            align-items: flex-start;
            padding: 1rem;
            margin-bottom: 0.75rem;
            background: var(--light-gray);
            border-radius: var(--border-radius);
            border-left: 4px solid var(--warning-color);
            transition: all 0.2s ease;
        }
        
        .alert-item:hover {
            background: #f8f9fa;
            transform: translateX(2px);
        }
        
        .alert-icon {
            color: var(--warning-color);
            margin-right: 0.75rem;
            font-size: 1.1rem;
            margin-top: 0.125rem;
            flex-shrink: 0;
        }
        
        .alert-content {
            flex-grow: 1;
        }
        
        .alert-title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }
        
        .alert-description {
            color: var(--text-gray);
            font-size: 0.8rem;
            line-height: 1.4;
        }
        
        /* Bottom Row Grid */
        .bottom-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .bottom-row {
                grid-template-columns: 1fr;
            }
        }
        
        .alert-content {
            flex: 1;
        }
        
        .alert-title {
            font-weight: 600;
            color: var(--text-dark);
            margin: 0 0 0.25rem 0;
            font-size: 0.875rem;
        }
        
        .alert-description {
            color: var(--text-gray);
            font-size: 0.8rem;
            margin: 0;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--medium-gray);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--text-gray);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }
        
        <?php if (isset($custom_css)) echo $custom_css; ?>
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-logo">
                <i class="fas fa-bus"></i>
                <div>
                    <div>Sistema Ônibus</div>
                    <div class="sidebar-subtitle">Gestão de Frota</div>
                </div>
            </a>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-item">
                <a href="dashboard-professional.php" class="nav-link <?php echo ($page_title == 'Dashboard') ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i>
                    Dashboard
                </a>
            </div>
            
            <div class="nav-item">
                <a href="eventos-professional.php" class="nav-link <?php echo ($page_title == 'Eventos') ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt"></i>
                    Eventos
                </a>
            </div>
            
            <div class="nav-item">
                <a href="onibus-professional.php" class="nav-link <?php echo ($page_title == 'Ônibus') ? 'active' : ''; ?>">
                    <i class="fas fa-bus"></i>
                    Ônibus
                </a>
            </div>
            
            <div class="nav-item">
                <a href="alocacoes-professional.php" class="nav-link <?php echo ($page_title == 'Alocações') ? 'active' : ''; ?>">
                    <i class="fas fa-route"></i>
                    Alocações
                </a>
            </div>
            
            <div class="nav-item">
                <a href="alunos-professional.php" class="nav-link <?php echo ($page_title == 'Alunos') ? 'active' : ''; ?>">
                    <i class="fas fa-user-graduate"></i>
                    Alunos
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    Relatórios
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-cog"></i>
                    Configurações
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="main-header">
            <div class="header-content">
                <div class="header-title">
                    <h1><?php echo $page_title; ?></h1>
                    <?php if (isset($page_description)): ?>
                        <p><?php echo $page_description; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="header-actions">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </div>
                    
                    <div class="user-menu">
                        <div class="user-avatar">A</div>
                        <span>Admin</span>
                        <i class="fas fa-chevron-down ms-1"></i>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content Area -->
        <main class="content-area">
            <?php echo $content; ?>
        </main>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
        }
        
        // Loading functions
        function showLoading() {
            // Add loading spinner logic here
        }
        
        function hideLoading() {
            // Remove loading spinner logic here
        }
        
        // Toast notification function
        function showToast(type, title, message) {
            // Add toast notification logic here
            console.log(`${type}: ${title} - ${message}`);
        }
        
        <?php if (isset($custom_js)) echo $custom_js; ?>
    </script>
</body>
</html>
