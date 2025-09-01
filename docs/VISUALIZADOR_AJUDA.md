# Visualizador de Ajuda - Sistema de Ônibus

## 📖 Sobre

Este visualizador foi criado para exibir o conteúdo do arquivo `README.md` de forma bonita e organizada na web, sem estar vinculado ao sistema de gerenciamento de eventos e ônibus.

## 🚀 Como Usar

### Acesso Direto
- **Página Principal**: `ajuda.php` - Versão completa com biblioteca Marked.js
- **Versão Simples**: `ajuda_simples.php` - Versão sem dependências externas
- **Teste**: `teste_readme.php` - Para verificar se o README.md está acessível

### Funcionalidades

#### 🔍 Busca
- Campo de busca no topo da página
- Destaca termos encontrados em amarelo
- Busca em tempo real conforme você digita

#### 📋 Índice (TOC)
- Lista todos os títulos do documento
- Links para navegação rápida
- Rolagem suave até a seção desejada

#### 📱 Design Responsivo
- Funciona em desktop, tablet e mobile
- Layout adaptável com Bootstrap 5
- Tema moderno com gradiente

#### 🎨 Destaques
- Headers com cores e estilos diferenciados
- Código com syntax highlighting
- Tabelas, listas e citações formatadas
- Alertas coloridos para diferentes tipos de informação

## 🛠️ Tecnologias Utilizadas

### Versão Completa (`ajuda.php`)
- **Marked.js**: Para conversão avançada de Markdown para HTML
- **Prism.js**: Para syntax highlighting de código
- **Bootstrap 5**: Framework CSS responsivo
- **FontAwesome**: Ícones

### Versão Simples (`ajuda_simples.php`)
- **Conversão própria**: Função JavaScript simples para Markdown básico
- **Bootstrap 5**: Framework CSS responsivo
- **FontAwesome**: Ícones
- **Zero dependências externas**: Funciona mesmo sem internet

## 📁 Arquivos Criados

- `ajuda.php` - Visualizador completo com Marked.js
- `ajuda_simples.php` - Visualizador simples sem dependências
- `teste_readme.php` - Script de teste para verificar README.md

## 🔧 Personalização

### Alterar Cores
As cores podem ser personalizadas editando o CSS no `<style>` das páginas:

```css
.help-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

### Adicionar Novos Estilos
Para adicionar suporte a novos elementos Markdown, edite as funções de conversão:

- **ajuda.php**: Modificar configuração do Marked.js
- **ajuda_simples.php**: Editar função `simpleMarkdownToHtml()`

## 🌐 Navegação

O link "Ajuda" no menu principal (`navbar.php`) agora aponta para `ajuda.php`.

## 📝 Estrutura do README.md

O visualizador espera que o `README.md` siga a estrutura padrão:

```markdown
# Título Principal

## Seção 1
Conteúdo da seção 1

## Seção 2
Conteúdo da seção 2

### Subseção
Conteúdo da subseção
```

## 🔍 Solução de Problemas

### Erro: "Arquivo não encontrado"
- Verifique se o `README.md` existe no diretório raiz
- Execute `teste_readme.php` para diagnosticar

### Erro: "Erro ao carregar documentação"
- Verifique permissões do arquivo
- Teste a versão simples (`ajuda_simples.php`)

### Busca não funciona
- Verifique se JavaScript está habilitado
- Teste com diferentes navegadores

## 📊 Performance

- **Carregamento**: ~50KB (versão simples) / ~200KB (versão completa)
- **Busca**: Processamento em tempo real no navegador
- **Responsividade**: Otimizado para dispositivos móveis

## 🎯 Casos de Uso

- **Documentação interna**: Para usuários do sistema
- **Ajuda online**: Disponibilizar documentação na web
- **Intranet**: Para empresas com documentação interna
- **Projetos open source**: Exibir README de projetos

---

**Nota**: Este visualizador é independente e não interfere no funcionamento do sistema de gerenciamento de ônibus.
