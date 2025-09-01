<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição para Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-success {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 10px;
        }

        .btn-success:hover {
            background: linear-gradient(45deg, #218838, #1aa085);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .success-message {
            display: none;
        }

        .evento-info {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="loading" class="loading">
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <div class="mt-2 text-light">Carregando formulário...</div>
                </div>

                <div id="form-container">
                    <!-- O formulário será carregado aqui via AJAX -->
                </div>

                <div id="success-message" class="success-message text-center">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-success mb-3">
                                <i class="fas fa-check-circle fa-4x"></i>
                            </div>
                            <h3 class="text-success">Inscrição Enviada!</h3>
                            <p class="text-muted">Sua inscrição foi enviada com sucesso. Você será notificado sobre o status da sua candidatura.</p>
                            <button class="btn btn-primary" onclick="location.reload()">Nova Inscrição</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Obter o código da URL
        const urlParams = new URLSearchParams(window.location.search);
        const shortCode = urlParams.get('code') || window.location.pathname.split('/').pop();

        if (!shortCode || shortCode === 'inscricao') {
            document.getElementById('form-container').innerHTML = `
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-danger mb-3">
                            <i class="fas fa-exclamation-triangle fa-4x"></i>
                        </div>
                        <h3>Código de Inscrição Inválido</h3>
                        <p class="text-muted">O código de inscrição fornecido não é válido ou expirou.</p>
                        <p class="text-muted">Verifique o QR Code ou link e tente novamente.</p>
                    </div>
                </div>
            `;
        } else {
            loadForm(shortCode);
        }

        function loadForm(shortCode) {
            const loading = document.getElementById('loading');
            const formContainer = document.getElementById('form-container');

            loading.style.display = 'block';

            fetch('https://posicionadosmarilia.com.br/onibus/qr_manager.php?action=public_form&code=' + shortCode)
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';

                    if (data.success) {
                        formContainer.innerHTML = data.form_html;
                        initializeForm();
                    } else {
                        formContainer.innerHTML = `
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="text-danger mb-3">
                                        <i class="fas fa-exclamation-triangle fa-4x"></i>
                                    </div>
                                    <h3>Erro ao Carregar Formulário</h3>
                                    <p class="text-muted">${data.message}</p>
                                </div>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    loading.style.display = 'none';
                    console.error('Erro:', error);
                    formContainer.innerHTML = `
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="text-danger mb-3">
                                    <i class="fas fa-exclamation-triangle fa-4x"></i>
                                </div>
                                <h3>Erro de Conexão</h3>
                                <p class="text-muted">Não foi possível conectar ao servidor. Tente novamente mais tarde.</p>
                            </div>
                        </div>
                    `;
                });
        }

        function initializeForm() {
            const form = document.getElementById('candidatura-form');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Mostrar loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';

                const formData = new FormData(form);

                fetch('https://posicionadosmarilia.com.br/onibus/qr_manager.php?action=submit_candidatura', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar mensagem de sucesso
                        document.getElementById('form-container').style.display = 'none';
                        document.getElementById('success-message').style.display = 'block';
                    } else {
                        // Mostrar erro
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao enviar inscrição. Tente novamente.');
                })
                .finally(() => {
                    // Restaurar botão
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }
    </script>
</body>
</html></content>
<parameter name="filePath">c:\laragon\www\onibus\inscricao_publica.php
