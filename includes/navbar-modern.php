<!-- Navbar Profissional Moderna -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    backdrop-filter: blur(20px);
    box-shadow: 0 2px 20px rgba(0,0,0,0.1);
    border-bottom: 1px solid rgba(255,255,255,0.1);
">
    <div class="container-fluid px-4">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center" href="index.php" style="font-weight: 700;">
            <div class="me-3" style="
                width: 45px;
                height: 45px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(10px);
            ">
                <i class="fas fa-bus text-white" style="font-size: 1.3rem;"></i>
            </div>
            <div>
                <div class="text-white" style="font-size: 1.1rem; line-height: 1;">TransportePro</div>
                <small class="text-white-50" style="font-size: 0.7rem;">Sistema de Gestão</small>
            </div>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Main Navigation -->
            <ul class="navbar-nav me-auto">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-3 mx-1 d-flex align-items-center" href="index.php" 
                       style="transition: all 0.3s ease; font-weight: 500;">
                        <i class="fas fa-chart-line me-2"></i>Dashboard
                    </a>
                </li>

                <!-- Eventos -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded-3 mx-1 d-flex align-items-center" 
                       href="#" id="eventosDropdown" role="button" data-bs-toggle="dropdown"
                       style="transition: all 0.3s ease; font-weight: 500;">
                        <i class="fas fa-calendar-alt me-2"></i>Eventos
                    </a>
                    <ul class="dropdown-menu border-0 shadow-lg" style="
                        background: rgba(255, 255, 255, 0.95);
                        backdrop-filter: blur(20px);
                        border-radius: 12px;
                        margin-top: 0.5rem;
                    ">
                        <li><a class="dropdown-item py-2" href="eventos.php">
                            <i class="fas fa-plus-circle me-2 text-success"></i>Gerenciar Eventos
                        </a></li>
                        <li><a class="dropdown-item py-2" href="pages/eventos-new.php">
                            <i class="fas fa-calendar-plus me-2 text-primary"></i>Novo Evento
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2" href="pages/candidaturas.php">
                            <i class="fas fa-user-check me-2 text-info"></i>Candidaturas
                        </a></li>
                    </ul>
                </li>

                <!-- Transporte -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded-3 mx-1 d-flex align-items-center" 
                       href="#" id="transporteDropdown" role="button" data-bs-toggle="dropdown"
                       style="transition: all 0.3s ease; font-weight: 500;">
                        <i class="fas fa-bus me-2"></i>Transporte
                    </a>
                    <ul class="dropdown-menu border-0 shadow-lg" style="
                        background: rgba(255, 255, 255, 0.95);
                        backdrop-filter: blur(20px);
                        border-radius: 12px;
                        margin-top: 0.5rem;
                    ">
                        <li><a class="dropdown-item py-2" href="pages/onibus.php">
                            <i class="fas fa-bus me-2 text-warning"></i>Gerenciar Ônibus
                        </a></li>
                        <li><a class="dropdown-item py-2" href="pages/alocacao.php">
                            <i class="fas fa-route me-2 text-primary"></i>Alocação Automática
                        </a></li>
                        <li><a class="dropdown-item py-2" href="pages/alocacoes.php">
                            <i class="fas fa-users-cog me-2 text-info"></i>Gerenciar Alocações
                        </a></li>
                    </ul>
                </li>

                <!-- Alunos -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded-3 mx-1 d-flex align-items-center" 
                       href="#" id="alunosDropdown" role="button" data-bs-toggle="dropdown"
                       style="transition: all 0.3s ease; font-weight: 500;">
                        <i class="fas fa-graduation-cap me-2"></i>Alunos
                    </a>
                    <ul class="dropdown-menu border-0 shadow-lg" style="
                        background: rgba(255, 255, 255, 0.95);
                        backdrop-filter: blur(20px);
                        border-radius: 12px;
                        margin-top: 0.5rem;
                    ">
                        <li><a class="dropdown-item py-2" href="import_students.php">
                            <i class="fas fa-file-import me-2 text-success"></i>Importar Alunos
                        </a></li>
                        <li><a class="dropdown-item py-2" href="import_allocate_students.php">
                            <i class="fas fa-users-cog me-2 text-primary"></i>Importar e Alocar
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2" href="pages/autorizacoes.php">
                            <i class="fas fa-shield-alt me-2 text-warning"></i>Autorizações
                        </a></li>
                    </ul>
                </li>

                <!-- Relatórios -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded-3 mx-1 d-flex align-items-center" 
                       href="#" id="relatoriosDropdown" role="button" data-bs-toggle="dropdown"
                       style="transition: all 0.3s ease; font-weight: 500;">
                        <i class="fas fa-chart-bar me-2"></i>Relatórios
                    </a>
                    <ul class="dropdown-menu border-0 shadow-lg" style="
                        background: rgba(255, 255, 255, 0.95);
                        backdrop-filter: blur(20px);
                        border-radius: 12px;
                        margin-top: 0.5rem;
                    ">
                        <li><a class="dropdown-item py-2" href="pages/daily_report.php">
                            <i class="fas fa-calendar-day me-2 text-info"></i>Relatório Diário
                        </a></li>
                        <li><a class="dropdown-item py-2" href="#" onclick="gerarRelatorioGeral()">
                            <i class="fas fa-file-pdf me-2 text-danger"></i>Relatório Geral
                        </a></li>
                        <li><a class="dropdown-item py-2" href="#" onclick="exportarDados()">
                            <i class="fas fa-download me-2 text-success"></i>Exportar Dados
                        </a></li>
                    </ul>
                </li>
            </ul>

            <!-- Right Side -->
            <ul class="navbar-nav">
                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link px-3 py-2 rounded-3 position-relative" href="#" id="notificationsDropdown" 
                       role="button" data-bs-toggle="dropdown" style="transition: all 0.3s ease;">
                        <i class="fas fa-bell" style="font-size: 1.1rem;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              style="font-size: 0.6rem;">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="
                        background: rgba(255, 255, 255, 0.95);
                        backdrop-filter: blur(20px);
                        border-radius: 12px;
                        margin-top: 0.5rem;
                        width: 300px;
                    ">
                        <li class="px-3 py-2 border-bottom">
                            <h6 class="mb-0 text-dark">Notificações</h6>
                        </li>
                        <li><a class="dropdown-item py-2" href="#">
                            <div class="d-flex">
                                <i class="fas fa-exclamation-triangle text-warning me-3 mt-1"></i>
                                <div>
                                    <div class="fw-semibold">Ônibus 001 precisa manutenção</div>
                                    <small class="text-muted">2 horas atrás</small>
                                </div>
                            </div>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center py-2" href="#">Ver todas</a></li>
                    </ul>
                </li>

                <!-- Environment Badge -->
                <li class="nav-item">
                    <span class="navbar-text me-3">
                        <div class="d-flex align-items-center">
                            <div class="me-2" style="
                                width: 8px;
                                height: 8px;
                                background: #28a745;
                                border-radius: 50%;
                                animation: pulse 2s infinite;
                            "></div>
                            <?php
                            include '../config/config.php';
                            $config = getDatabaseConfig();
                            echo '<span class="text-white-50 small">' . ucfirst($config['ambiente']) . '</span>';
                            ?>
                        </div>
                    </span>
                </li>

                <!-- User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded-3 d-flex align-items-center" 
                       href="#" id="userDropdown" role="button" data-bs-toggle="dropdown"
                       style="transition: all 0.3s ease;">
                        <div class="me-2" style="
                            width: 32px;
                            height: 32px;
                            background: rgba(255, 255, 255, 0.2);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        ">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <span class="text-white">Admin</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="
                        background: rgba(255, 255, 255, 0.95);
                        backdrop-filter: blur(20px);
                        border-radius: 12px;
                        margin-top: 0.5rem;
                    ">
                        <li><a class="dropdown-item py-2" href="#">
                            <i class="fas fa-user-cog me-2"></i>Configurações
                        </a></li>
                        <li><a class="dropdown-item py-2" href="pages/ajuda.php">
                            <i class="fas fa-question-circle me-2"></i>Ajuda
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2" href="#" onclick="logout()">
                            <i class="fas fa-sign-out-alt me-2 text-danger"></i>Sair
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- CSS adicional para animações -->
<style>
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .navbar-nav .nav-link:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        transform: translateY(-1px);
    }
    
    .dropdown-item:hover {
        background: rgba(102, 126, 234, 0.1) !important;
        color: #667eea !important;
    }
    
    .navbar {
        transition: all 0.3s ease;
    }
    
    body {
        padding-top: 76px; /* Compensar navbar fixa */
    }
</style>

<script>
function logout() {
    if(confirm('Deseja realmente sair do sistema?')) {
        window.location.href = 'logout.php';
    }
}

function gerarRelatorioGeral() {
    // Implementar geração de relatório
    alert('Funcionalidade em desenvolvimento');
}

function exportarDados() {
    // Implementar exportação
    alert('Funcionalidade em desenvolvimento');
}
</script>
