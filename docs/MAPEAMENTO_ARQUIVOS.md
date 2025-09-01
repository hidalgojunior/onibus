# 📁 Mapeamento de Arquivos - Sistema Organizado

## 🔄 Como Encontrar os Arquivos Após a Reorganização

### 📋 Mapeamento de Localização

| Arquivo Original | Nova Localização | Tipo |
|------------------|------------------|------|
| `config.php` | `config/config.php` | Config |
| `config_email.php` | `config/config_email.php` | Config |
| `config_timezone.php` | `config/config_timezone.php` | Config |
| `ajax_*.php` | `api/ajax_*.php` | API |
| `navbar.php` | `includes/navbar.php` | Include |
| `eventos.php` | `pages/eventos.php` | Page |
| `candidaturas.php` | `pages/candidaturas.php` | Page |
| `onibus.php` | `pages/onibus.php` | Page |
| `alocacao.php` | `pages/alocacao.php` | Page |
| `alocacoes.php` | `pages/alocacoes.php` | Page |
| `autorizacoes.php` | `pages/autorizacoes.php` | Page |
| `install.php` | `admin/install.php` | Admin |
| `update_*.php` | `admin/update_*.php` | Admin |
| `import_*.php` | `admin/import_*.php` | Admin |
| `debug.php` | `debug/debug.php` | Debug |
| `teste_*.php` | `debug/teste_*.php` | Debug |
| `test_*.php` | `debug/test_*.php` | Debug |
| `README*.md` | `docs/README*.md` | Docs |
| `*.sql` | `sql/*.sql` | SQL |
| `erro_*.html` | `public/erro_*.html` | Public |

### 🔧 Atualizações Necessárias nos Includes

#### Para páginas em `pages/`:
```php
// ANTES
include 'config.php';
include 'navbar.php';

// DEPOIS
include '../config/config.php';
include '../includes/layout.php';
```

#### Para páginas em `admin/`:
```php
// ANTES
include 'config.php';

// DEPOIS
include '../config/config.php';
```

#### Para arquivos em `api/`:
```php
// ANTES
include 'config.php';

// DEPOIS
include '../config/config.php';
```

### 📨 Atualizações de AJAX URLs

#### No JavaScript das páginas:
```javascript
// ANTES
fetch('ajax_eventos.php')

// DEPOIS (para páginas em pages/)
fetch('../api/ajax_eventos.php')

// DEPOIS (para páginas na raiz)
fetch('api/ajax_eventos.php')
```

### 🔗 Atualizações de Links

#### Na navegação:
```html
<!-- ANTES -->
<a href="eventos.php">Eventos</a>
<a href="candidaturas.php">Candidaturas</a>

<!-- DEPOIS (a partir da raiz) -->
<a href="pages/eventos.php">Eventos</a>
<a href="pages/candidaturas.php">Candidaturas</a>
```

### 📁 Estrutura de CSS/JS

#### Nos arquivos HTML/PHP:
```html
<!-- Para páginas na raiz -->
<link rel="stylesheet" href="assets/css/styles.css">
<script src="assets/js/app.js"></script>

<!-- Para páginas em subpastas -->
<link rel="stylesheet" href="../assets/css/styles.css">
<script src="../assets/js/app.js"></script>
```

## 🛠️ Script de Atualização Automática

Aqui está um exemplo de como atualizar automaticamente os includes:

```php
// Função helper para includes relativos
function includeFile($file) {
    $paths = [
        __DIR__ . '/' . $file,                    // Mesmo diretório
        __DIR__ . '/../' . $file,                 // Diretório pai
        __DIR__ . '/../config/' . $file,          // Pasta config
        __DIR__ . '/../includes/' . $file,        // Pasta includes
        __DIR__ . '/../api/' . $file,             // Pasta api
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            include $path;
            return true;
        }
    }
    
    return false;
}

// Uso
includeFile('config.php');
includeFile('navbar.php');
```

## ⚠️ Pontos de Atenção

### 1. **Caminhos Relativos**
- Sempre verificar se os includes estão corretos
- Usar `__DIR__` para caminhos absolutos quando possível

### 2. **AJAX Calls**
- Atualizar todas as chamadas AJAX para apontar para `api/`
- Verificar se as respostas continuam funcionando

### 3. **Assets (CSS/JS)**
- Verificar se todos os arquivos CSS/JS carregam corretamente
- Ajustar caminhos conforme a profundidade da pasta

### 4. **Formulários**
- Verificar se os actions dos formulários estão corretos
- Atualizar para apontar para `api/` onde necessário

## 🚀 Comandos PowerShell para Atualização

```powershell
# Buscar arquivos que precisam ser atualizados
Get-ChildItem -Recurse -Include "*.php" | Select-String "include.*config\.php"

# Buscar chamadas AJAX que precisam ser atualizadas
Get-ChildItem -Recurse -Include "*.php","*.js" | Select-String "ajax_.*\.php"

# Buscar links que precisam ser atualizados
Get-ChildItem -Recurse -Include "*.php" | Select-String "href.*\.php"
```

## ✅ Checklist de Migração

- [x] Estrutura de pastas criada
- [x] Arquivos movidos para locais corretos
- [x] Layout base com TailwindCSS criado
- [x] Dashboard principal migrado
- [x] CSS/JS organizados
- [ ] Atualizar includes nas páginas
- [ ] Migrar páginas principais
- [ ] Testar todas as funcionalidades
- [ ] Atualizar documentação

## 🎯 Próximos Passos

1. **Migrar páginas uma por uma**
2. **Testar cada funcionalidade após migração**
3. **Atualizar links e includes conforme necessário**
4. **Documentar alterações específicas**
