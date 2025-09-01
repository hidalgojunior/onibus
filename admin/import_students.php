<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Alunos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h1 class="card-title mb-0">Importar Alunos</h1>
                    </div>
                    <div class="card-body">
                        <?php
                        // Incluir arquivo de configuração
                        include 'config.php';

                        // Obter configuração do banco
                        $config = getDatabaseConfig();
                        $conn = new mysqli($config['host'], $config['usuario'], $config['senha'], $config['banco']);

                        if ($conn->connect_error) {
                            echo '<div class="alert alert-danger">Erro de conexão: ' . $conn->connect_error . '</div>';
                        } else {
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $alunos_texto = $_POST['alunos_texto'];
                                $linhas = explode("\n", trim($alunos_texto));
                                $importados = 0;
                                $erros = [];

                                foreach ($linhas as $linha) {
                                    $linha = trim($linha);
                                    if (!empty($linha)) {
                                        // Formato esperado: Nome - Série - Curso - Telefone
                                        $partes = explode(' - ', $linha);
                                        if (count($partes) >= 4) {
                                            // Se tem mais de 4 partes, juntar as primeiras como nome
                                            if (count($partes) > 4) {
                                                $nome = trim(implode(' - ', array_slice($partes, 0, -3)));
                                                $serie = trim($partes[count($partes)-3]);
                                                $curso = trim($partes[count($partes)-2]);
                                                $telefone = trim($partes[count($partes)-1]);
                                            } else {
                                                $nome = trim($partes[0]);
                                                $serie = trim($partes[1]);
                                                $curso = trim($partes[2]);
                                                $telefone = trim($partes[3]);
                                            }

                                            // Inserir aluno
                                            $stmt = $conn->prepare("INSERT INTO alunos (nome, serie, curso, telefone) VALUES (?, ?, ?, ?)");
                                            $stmt->bind_param("ssss", $nome, $serie, $curso, $telefone);

                                            if ($stmt->execute()) {
                                                $importados++;
                                            } else {
                                                $erros[] = "Erro ao importar: $nome - " . $stmt->error;
                                            }
                                            $stmt->close();
                                        } elseif (count($partes) == 3) {
                                            // Formato: Nome - Nome - Telefone (caso especial)
                                            $nome = trim($partes[0] . ' - ' . $partes[1]);
                                            $telefone = trim($partes[2]);
                                            $serie = 'Não informado';
                                            $curso = 'Não informado';

                                            $stmt = $conn->prepare("INSERT INTO alunos (nome, serie, curso, telefone) VALUES (?, ?, ?, ?)");
                                            $stmt->bind_param("ssss", $nome, $serie, $curso, $telefone);

                                            if ($stmt->execute()) {
                                                $importados++;
                                            } else {
                                                $erros[] = "Erro ao importar: $nome - " . $stmt->error;
                                            }
                                            $stmt->close();
                                        } else {
                                            $erros[] = "Formato inválido (menos de 3 partes): $linha";
                                        }
                                    }
                                }

                                if ($importados > 0) {
                                    echo '<div class="alert alert-success">' . $importados . ' alunos importados com sucesso!</div>';
                                }
                                if (!empty($erros)) {
                                    echo '<div class="alert alert-warning"><strong>Erros:</strong><ul>';
                                    foreach ($erros as $erro) {
                                        echo '<li>' . $erro . '</li>';
                                    }
                                    echo '</ul></div>';
                                }
                            }

                            // Exemplo de dados
                            $exemplo = "Amanda Nayara Graciano Silva - 3ª Série - Mtec PI - Administração - 1499102-4148
Ana Eliza Ribeiro Moura - 1ª Série - Mtec PI - Administração - 55 14 991195084
Ana Laura Pereira Candido - 1ª Série - Mtec PI - Administração - 5514996502749
Ana Laura de Lima Carvalho - 3ª Série - Mtec PI - Administração - 14999025335
André Ricardo da Silva Nunes Gomes - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14991630637
Beatriz Batista da Silva - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14991838771
Davi Diniz Dalevedo - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14 997200464
Davi Ferreira Neris - 1ª Série - Mtec PI - Desenvolvimento de Sistemas - 14 98178-9778
Davi Teixeira Galdino Dos Santos - 1ª Série - Mtec PI - Desenvolvimento de Sistemas - 14997850841
Eduarda Leal Tavares - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14998260310
Eleno Doi Pillon - 1ª Série - Mtec PI - Desenvolvimento de Sistemas - (14) 99777-9980
Elves Moreira Santos Júnior - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14 99807-2635
Felipe dos Santos Leonardo - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14997430586
Fernanda Alves Diniz - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14998945004
Gabriel Fernando de Souza - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14998963540
Gabriel Ferreira Ferraz - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14 99831-8941
Guilherme Santos Baldasso - 3ª Série - Mtec PI - Desenvolvimento de Sistemas - 14998478624
Guilherme Souza de Abreu - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - (14) 99887-1458
Gustavo Garbelini Piacente - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14 999010145
Heder de Souza Silva - 1ª Série - Mtec PI - Desenvolvimento de Sistemas - 14 9 9740-6683
Isabella da Silva Ferreira - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14991343456
Izadora Cibantos de Souza - 3ª Série - Mtec PI - Administração - 14 996522135
Jean Luccas Alves dos Santos - 2ª Série - Mtec PI - Administração - 14997210824
João Pedro Martins da Silva - 3ª Série - Mtec PI - Desenvolvimento de Sistemas - 16981801683
João Pedro dos Santos Pereira - 3ª Série - Mtec PI - Desenvolvimento de Sistemas - 14 996175034
Leandro Ladislau do Amaral - 3ª Série - Mtec PI - Desenvolvimento de Sistemas - 14997253180
Lorena dos Santos Odilon Silva - 2ª Série - Mtec PI - Administração - 14997973219
Lucas Rezende Alves - 3ª Série - Mtec PI - Desenvolvimento de Sistemas - 14996211632
Miguel Antonio - Filho do Alessandro - 14981372604
Miguel Gabriel Luz de Melo - 1ª Série - Mtec PI - Desenvolvimento de Sistemas - (14)998430901
Miguel Roeda Silva - 1ª Série - Mtec PI - Desenvolvimento de Sistemas - 14 996108271
Pedro Andrade Ormond - 3ª Série - Mtec PI - Desenvolvimento de Sistemas - 14996715989
Rafael Panciera Queroli - 3ª Série - Mtec PI - Desenvolvimento de Sistemas - 14996546068
Rafael Viana Pereira - 1ª Série - Mtec PI - Desenvolvimento de Sistemas - 21 995100625
Rafaela Bossoni Barreto - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14991014339
Raphaella Orasmo Dalla Pria - 2ª Série - Mtec PI - Administração - 14 99705-6868
Raquel Lissa Yamauti - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14998172009
Rayane Vitória Barberdes de Souza - 2ª Série - Mtec PI - Administração - 14991286482
Ryan Augusto Resende de Oliveira - 1ª Série - Mtec PI - Desenvolvimento de Sistemas - 5514991143901
Sibylla Daniel da Conceição - 2ª Série - Mtec PI - Desenvolvimento de Sistemas - 14 991674775
Vitor Hugo Basta Fernandes - 2ª Série - Mtec PI - Administração - 14998034903";

                            echo '<form method="POST">';
                            echo '<div class="mb-3">';
                            echo '<label for="alunos_texto" class="form-label">Cole os dados dos alunos (um por linha, formato: Nome - Série - Curso - Telefone)</label>';
                            echo '<textarea class="form-control" id="alunos_texto" name="alunos_texto" rows="20" placeholder="Cole aqui os dados...">' . htmlspecialchars($exemplo) . '</textarea>';
                            echo '</div>';
                            echo '<button type="submit" class="btn btn-info">Importar Alunos</button>';
                            echo '</form>';
                        }

                        $conn->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
