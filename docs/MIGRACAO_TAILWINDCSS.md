# 🎨 Migração para TailwindCSS - Sistema de Ônibus

## 🏗️ Nova Estrutura de Pastas

```
onibus/
├── 📁 admin/              # Ferramentas administrativas
│   ├── install.php
│   ├── update_database.php
│   ├── update_autorizacoes.php
│   └── import_*.php
├── 📁 api/                # Endpoints AJAX/API
│   ├── ajax_candidaturas.php
│   ├── ajax_eventos.php
│   ├── ajax_onibus.php
│   ├── ajax_alocacao.php
│   └── ajax_alocacoes.php
├── 📁 assets/             # Recursos estáticos
│   ├── 📁 css/
│   │   └── styles.css     # Estilos customizados TailwindCSS
│   ├── 📁 js/
│   │   └── app.js         # JavaScript principal
│   └── 📁 images/         # Imagens do sistema
├── 📁 config/             # Configurações
│   ├── config.php
│   ├── config_email.php
│   ├── config_timezone.php
│   └── exemplo_config_email.php
├── 📁 debug/              # Ferramentas de debug/teste
│   ├── diagnostico_*.php
│   ├── teste_*.php
│   └── test_*.php
├── 📁 docs/               # Documentação
│   ├── README.md
│   ├── README_PRESENCA.md
│   ├── README_QR_SYSTEM.md
│   └── VISUALIZADOR_AJUDA.md
├── 📁 includes/           # Arquivos incluídos
│   ├── layout.php         # Layout base TailwindCSS
│   ├── navbar.php
│   ├── get_autorizacao.php
│   ├── listar_autorizacoes.php
│   ├── presence.php
│   └── qr_manager.php
├── 📁 inscricao/          # Inscrição pública
│   └── index.php
├── 📁 pages/              # Páginas principais
│   ├── candidaturas.php
│   ├── eventos.php
│   ├── onibus.php
│   ├── alocacao.php
│   ├── alocacoes.php
│   ├── autorizacoes.php
│   ├── ajuda.php
│   ├── ajuda_simples.php
│   └── daily_report.php
├── 📁 public/             # Arquivos públicos
│   ├── erro_*.html
│   └── inscricao_publica.php
├── 📁 scripts/            # Scripts utilitários
│   ├── check_*.php
│   ├── clean_*.php
│   ├── fix_*.php
│   ├── setup_*.php
│   └── return_control.php
├── 📁 sql/                # Arquivos SQL
│   ├── install_database.sql
│   └── setup_qr_system.sql
├── .htaccess
└── index.php              # Dashboard principal (TailwindCSS)
```

## 🎨 Migração para TailwindCSS

### ✅ Componentes Migrados

#### 1. **Layout Base** (`includes/layout.php`)
- Navbar responsiva com TailwindCSS
- Footer moderno
- Estrutura mobile-first
- Sistema de dropdowns
- Integração com CDN do TailwindCSS

#### 2. **Dashboard Principal** (`index.php`)
- Cards de estatísticas responsivos
- Grid layout adaptativo
- Ações rápidas com hover effects
- Design moderno e limpo
- Gradientes e sombras

#### 3. **Sistema CSS** (`assets/css/styles.css`)
- Classes customizadas usando @apply
- Componentes reutilizáveis:
  - `.btn-custom` (botões)
  - `.card`, `.card-header`, `.card-body`
  - `.alert` (notificações)
  - `.badge` (etiquetas)
  - `.modal` (modais)
  - `.form-control` (formulários)

#### 4. **JavaScript** (`assets/js/app.js`)
- Utilitários modernos (fetch, promises)
- Sistema de notificações toast
- Componentes dinâmicos
- Helpers para formulários e tabelas

### 🛠️ Funcionalidades do Sistema CSS

#### Botões
```css
.btn-primary    # Azul - Ações principais
.btn-success    # Verde - Ações de sucesso
.btn-warning    # Amarelo - Ações de atenção
.btn-danger     # Vermelho - Ações perigosas
.btn-secondary  # Cinza - Ações secundárias
.btn-outline    # Contorno - Ações alternativas
```

#### Cards
```css
.card           # Container principal
.card-header    # Cabeçalho do card
.card-body      # Corpo do card
.card-footer    # Rodapé do card
```

#### Alerts
```css
.alert-info     # Informação (azul)
.alert-success  # Sucesso (verde)
.alert-warning  # Aviso (amarelo)
.alert-danger   # Erro (vermelho)
```

### 📱 Responsividade

O sistema agora é **100% responsivo** usando:
- **Mobile-first approach**
- **Grid system flexível**
- **Breakpoints padrão TailwindCSS**:
  - `sm:` 640px+
  - `md:` 768px+
  - `lg:` 1024px+
  - `xl:` 1280px+

### 🎯 Benefícios da Migração

1. **Performance**
   - CSS otimizado via CDN
   - Classes utilitárias eficientes
   - Menor tamanho final

2. **Manutenibilidade**
   - Código mais limpo
   - Componentes reutilizáveis
   - Padrões consistentes

3. **Responsividade**
   - Design mobile-first
   - Layout adaptativo
   - Melhor UX em todos dispositivos

4. **Produtividade**
   - Desenvolvimento mais rápido
   - Classes prontas para uso
   - Documentação extensa

## 🚀 Próximos Passos

### Páginas para Migrar
- [ ] `pages/eventos.php`
- [ ] `pages/candidaturas.php`
- [ ] `pages/onibus.php`
- [ ] `pages/alocacao.php`
- [ ] `pages/alocacoes.php`
- [ ] `pages/autorizacoes.php`

### Padrão de Migração

1. **Incluir layout base**:
```php
<?php
$pageTitle = 'Título da Página';
$currentPage = 'nome_da_pagina';

// Conteúdo da página
ob_start();
?>
<!-- HTML com TailwindCSS aqui -->
<?php
$pageContent = ob_get_clean();
include '../includes/layout.php';
?>
```

2. **Usar classes TailwindCSS**:
```html
<!-- Ao invés de Bootstrap -->
<div class="container">
  <div class="row">
    <div class="col-md-6">

<!-- Usar TailwindCSS -->
<div class="max-w-7xl mx-auto">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
```

3. **Usar componentes customizados**:
```html
<!-- Botões -->
<button class="btn-custom btn-primary">Salvar</button>

<!-- Cards -->
<div class="card">
  <div class="card-header">Título</div>
  <div class="card-body">Conteúdo</div>
</div>

<!-- Alerts -->
<div class="alert alert-success">Sucesso!</div>
```

## 📖 Recursos Úteis

- **TailwindCSS Docs**: https://tailwindcss.com/docs
- **Classes Utilitárias**: Consulte `assets/css/styles.css`
- **Componentes JS**: Consulte `assets/js/app.js`
- **Layout Base**: Consulte `includes/layout.php`

## 🎨 Paleta de Cores

```css
Primária:   #3b82f6 (blue-600)
Secundária: #1e40af (blue-700)
Sucesso:    #10b981 (green-600)
Aviso:      #f59e0b (yellow-600)
Erro:       #ef4444 (red-600)
Escuro:     #1f2937 (gray-800)
Claro:      #f8fafc (gray-50)
```

---

**✅ Status**: Layout base e dashboard migrados para TailwindCSS
**🎯 Próximo**: Migrar páginas principais seguindo o padrão estabelecido
