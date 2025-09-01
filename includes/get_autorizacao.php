<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn = getDatabaseConnection();

    $sql = "SELECT aut.*, a.nome as aluno_nome, r.nome as responsavel_nome
            FROM autorizacoes aut
            JOIN alunos a ON aut.aluno_id = a.id
            LEFT JOIN responsaveis r ON aut.responsavel_id = r.id
            WHERE aut.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $autorizacao = $result->fetch_assoc();

        echo '<div class="autorizacao-preview" style="border: 1px solid #ddd; padding: 20px; background-color: #f9f9f9; font-family: Arial, sans-serif;">';
        echo '<h4 class="text-center mb-4">';

        if ($autorizacao['tipo'] == 'saida') {
            echo 'Autorização de Saída';
        } else {
            echo 'Autorização de Uso de Imagem';
        }

        echo '</h4>';
        echo '<div style="white-space: pre-line;">' . $autorizacao['conteudo'] . '</div>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-danger">Autorização não encontrada.</div>';
    }

    $conn->close();
} else {
    echo '<div class="alert alert-danger">ID da autorização não fornecido.</div>';
}
?>
