<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-bus"></i> Sistema de Ônibus
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-home"></i> Início
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="gerenciamentoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cogs"></i> Gerenciamento
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="gerenciamentoDropdown">
                        <li><a class="dropdown-item" href="eventos.php">Gerenciar Eventos</a></li>
                        <li><a class="dropdown-item" href="onibus.php">Gerenciar Ônibus</a></li>
                        <li><a class="dropdown-item" href="alocacao.php">Alocação Automática</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="install.php">Instalar Banco</a></li>
                        <li><a class="dropdown-item" href="update_database.php">Atualizar Banco</a></li>
                        <li><a class="dropdown-item" href="update_autorizacoes.php">Atualizar Autorizações</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="alunosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-users"></i> Alunos
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="alunosDropdown">
                        <li><a class="dropdown-item" href="alocacoes.php">Gerenciar Alocações</a></li>
                        <li><a class="dropdown-item" href="import_students.php">Importar Alunos</a></li>
                        <li><a class="dropdown-item" href="import_allocate_students.php">Importar e Alocar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="candidaturas.php">
                            <i class="fas fa-qrcode me-1"></i>Gerenciar Candidaturas
                        </a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="presencaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bus"></i> Controle
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="presencaDropdown">
                        <li><a class="dropdown-item" href="presence.php">Controle de Embarque</a></li>
                        <li><a class="dropdown-item" href="return_control.php">Controle de Retorno</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="relatoriosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-chart-bar"></i> Relatórios
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="relatoriosDropdown">
                        <li><a class="dropdown-item" href="daily_report.php">Relatório Diário</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="autorizacoesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-signature"></i> Autorizações
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="autorizacoesDropdown">
                        <li><a class="dropdown-item" href="autorizacoes.php">Gerar Autorização</a></li>
                        <li><a class="dropdown-item" href="listar_autorizacoes.php">Listar Autorizações</a></li>
                    </ul>
                </li>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="navbar-text me-3">
                        <i class="fas fa-server"></i>
                        <?php
                        include 'config/config.php';
                        $config = getDatabaseConfig();
                        echo ucfirst($config['ambiente']);
                        ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ajuda.php">
                        <i class="fas fa-question-circle"></i> Ajuda
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
