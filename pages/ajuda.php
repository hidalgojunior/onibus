<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajuda - Sistema de Ônibus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet">
    <style>
        .markdown-content {
            line-height: 1.6;
        }

        .markdown-content h1,
        .markdown-content h2,
        .markdown-content h3,
        .markdown-content h4,
        .markdown-content h5,
        .markdown-content h6 {
            color: #2c3e50;
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .markdown-content h1 {
            border-bottom: 3px solid #3498db;
            padding-bottom: 0.5rem;
            font-size: 2.5rem;
        }

        .markdown-content h2 {
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.3rem;
            font-size: 2rem;
        }

        .markdown-content h3 {
            color: #34495e;
            font-size: 1.5rem;
        }

        .markdown-content ul,
        .markdown-content ol {
            padding-left: 2rem;
        }

        .markdown-content li {
            margin-bottom: 0.5rem;
        }

        .markdown-content code {
            background-color: #f8f9fa;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }

        .markdown-content pre {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
            overflow-x: auto;
        }

        .markdown-content blockquote {
            border-left: 4px solid #3498db;
            padding-left: 1rem;
            margin: 1.5rem 0;
            color: #555;
            font-style: italic;
        }

        .markdown-content table {
            width: 100%;
            margin: 1rem 0;
            border-collapse: collapse;
        }

        .markdown-content table th,
        .markdown-content table td {
            border: 1px solid #dee2e6;
            padding: 0.75rem;
            text-align: left;
        }

        .markdown-content table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .markdown-content .alert {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0.5rem;
        }

        .markdown-content .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .markdown-content .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }

        .markdown-content .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }

        .markdown-content .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .markdown-content .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
        }

        .markdown-content .badge-primary {
            background-color: #007bff;
        }

        .markdown-content .badge-success {
            background-color: #28a745;
        }

        .markdown-content .badge-info {
            background-color: #17a2b8;
        }

        .markdown-content .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .markdown-content .badge-danger {
            background-color: #dc3545;
        }

        .markdown-content .text-center {
            text-align: center;
        }

        .markdown-content .text-muted {
            color: #6c757d;
        }

        .markdown-content .text-success {
            color: #28a745;
        }

        .markdown-content .text-danger {
            color: #dc3545;
        }

        .markdown-content .text-warning {
            color: #ffc107;
        }

        .markdown-content .alert-link {
            color: #007bff;
            text-decoration: underline;
        }

        .help-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        .help-header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .help-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .toc {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        .toc h5 {
            color: #495057;
            margin-bottom: 1rem;
        }

        .toc ul {
            list-style: none;
            padding: 0;
        }

        .toc li {
            margin-bottom: 0.5rem;
        }

        .toc a {
            color: #007bff;
            text-decoration: none;
            padding: 0.25rem 0;
            display: block;
        }

        .toc a:hover {
            color: #0056b3;
            background-color: rgba(0, 123, 255, 0.1);
            padding-left: 0.5rem;
            margin-left: -0.5rem;
            border-radius: 0.25rem;
        }

        .search-container {
            margin-bottom: 2rem;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #dee2e6;
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        .search-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .highlight {
            background-color: #fff3cd;
            padding: 0.2rem;
        }

        @media (max-width: 768px) {
            .help-header h1 {
                font-size: 2rem;
            }

            .help-header p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="help-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h1><i class="fas fa-question-circle me-3"></i>Ajuda do Sistema</h1>
                    <p>Guia completo para utilização do Sistema de Gerenciamento de Ônibus</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <!-- Table of Contents Sidebar -->
            <div class="col-md-3">
                <div class="toc sticky-top" style="top: 2rem;">
                    <h5><i class="fas fa-list me-2"></i>Índice</h5>
                    <div id="toc-content">
                        <!-- Table of contents will be generated here -->
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <!-- Search -->
                <div class="search-container">
                    <input type="text" class="search-input" id="searchInput" placeholder="Buscar na documentação...">
                </div>

                <!-- Loading -->
                <div id="loading" class="text-center d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <div class="mt-2">Carregando documentação...</div>
                </div>

                <!-- Content -->
                <div id="markdown-content" class="markdown-content">
                    <!-- Markdown content will be loaded here -->
                </div>

                <!-- No results message -->
                <div id="no-results" class="no-results d-none">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h4>Nenhum resultado encontrado</h4>
                    <p>Tente usar outros termos de busca.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container text-center">
            <p class="mb-0">
                <i class="fas fa-bus me-2"></i>
                Sistema de Gerenciamento de Ônibus - Documentação de Ajuda
            </p>
            <small class="text-muted">
                Atualizado em <span id="last-updated"></span>
            </small>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/4.0.0/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script>
        let originalContent = '';

        // Configure marked options
        marked.setOptions({
            breaks: true,
            gfm: true,
            headerIds: true,
            mangle: false
        });

        // Load README content
        async function loadReadme() {
            const loading = document.getElementById('loading');
            const content = document.getElementById('markdown-content');

            loading.classList.remove('d-none');
            content.innerHTML = '';

            try {
                const response = await fetch('README.md');
                if (!response.ok) {
                    throw new Error('Arquivo não encontrado');
                }

                const markdown = await response.text();
                originalContent = markdown;

                const html = marked.parse(markdown);
                content.innerHTML = html;

                // Generate table of contents
                generateTOC(markdown);

                // Update last modified date
                updateLastModified();

                // Highlight code blocks
                Prism.highlightAll();

            } catch (error) {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <h4><i class="fas fa-exclamation-triangle me-2"></i>Erro ao carregar documentação</h4>
                        <p>Não foi possível carregar o arquivo README.md. Verifique se o arquivo existe no diretório raiz do sistema.</p>
                        <p><strong>Erro:</strong> ${error.message}</p>
                    </div>
                `;
            } finally {
                loading.classList.add('d-none');
            }
        }

        // Generate table of contents
        function generateTOC(markdown) {
            const tocContent = document.getElementById('toc-content');
            const headers = markdown.match(/^#{1,6} .+$/gm) || [];

            if (headers.length === 0) {
                tocContent.innerHTML = '<p class="text-muted">Nenhum índice disponível</p>';
                return;
            }

            let tocHtml = '<ul>';
            let currentLevel = 1;

            headers.forEach(header => {
                const level = header.match(/^#+/)[0].length;
                const text = header.replace(/^#+\s*/, '');
                const id = text.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();

                // Close previous levels if needed
                while (currentLevel > level) {
                    tocHtml += '</ul></li>';
                    currentLevel--;
                }

                // Open new levels if needed
                while (currentLevel < level) {
                    tocHtml += '<ul>';
                    currentLevel++;
                }

                tocHtml += `<li><a href="#${id}">${text}</a></li>`;
            });

            // Close remaining levels
            while (currentLevel > 1) {
                tocHtml += '</ul></li>';
                currentLevel--;
            }

            tocHtml += '</ul>';
            tocContent.innerHTML = tocHtml;

            // Add smooth scrolling to TOC links
            tocContent.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        }

        // Search functionality
        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
            const content = document.getElementById('markdown-content');
            const noResults = document.getElementById('no-results');

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();

                if (query === '') {
                    content.innerHTML = marked.parse(originalContent);
                    noResults.classList.add('d-none');
                    content.classList.remove('d-none');
                    Prism.highlightAll();
                    return;
                }

                const html = marked.parse(originalContent);
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                const textNodes = [];
                const walker = document.createTreeWalker(
                    tempDiv,
                    NodeFilter.SHOW_TEXT,
                    null,
                    false
                );

                let node;
                while (node = walker.nextNode()) {
                    if (node.textContent.toLowerCase().includes(query)) {
                        textNodes.push(node);
                    }
                }

                if (textNodes.length === 0) {
                    content.classList.add('d-none');
                    noResults.classList.remove('d-none');
                    return;
                }

                // Highlight search terms
                textNodes.forEach(node => {
                    const text = node.textContent;
                    const highlighted = text.replace(
                        new RegExp(`(${query})`, 'gi'),
                        '<span class="highlight">$1</span>'
                    );
                    const span = document.createElement('span');
                    span.innerHTML = highlighted;
                    node.parentNode.replaceChild(span, node);
                });

                content.innerHTML = tempDiv.innerHTML;
                content.classList.remove('d-none');
                noResults.classList.add('d-none');
                Prism.highlightAll();
            });
        }

        // Update last modified date
        function updateLastModified() {
            const lastUpdated = document.getElementById('last-updated');
            const now = new Date();
            lastUpdated.textContent = now.toLocaleDateString('pt-BR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            loadReadme();
            setupSearch();
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function() {
            loadReadme();
        });
    </script>
</body>
</html>
