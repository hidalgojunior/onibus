# Sistema de QR Code - Candidaturas de Eventos

## 📱 Sobre o Sistema

O Sistema de QR Code permite que alunos se candidatem a eventos através de links públicos gerados automaticamente. Cada evento pode ter seu próprio QR Code único que direciona para um formulário de inscrição personalizado.

## 🚀 Como Usar

### 1. Gerar QR Code para um Evento

1. **Acesse a página de eventos**: `eventos.php`
2. **Localize o evento desejado** na tabela
3. **Clique no botão "Gerar"** na coluna QR Code
4. **Confirme a geração** do QR Code

### 2. Após Gerar o QR Code

Após gerar o QR Code, você terá acesso a:

- **📱 QR Code**: Imagem para impressão/distribuição
- **🔗 Link Público**: URL curta para compartilhamento
- **📋 Botão Copiar**: Copiar link para área de transferência
- **📥 Botão Download**: Baixar imagem do QR Code

### 3. Gerenciar Candidaturas

1. **Acesse "Gerenciar Candidaturas"** no menu Alunos
2. **Visualize todas as inscrições** recebidas
3. **Avalie cada candidatura**:
   - ✅ **Aprovar**: Candidato é adicionado à tabela de alunos
   - ❌ **Reprovar**: Candidatura é rejeitada
   - 🚫 **Cancelar**: Candidatura é cancelada

## 🔧 Funcionalidades Técnicas

### URLs Públicas
- **Formato**: `https://posicionadosmarilia.com.br/inscricao/CODIGO`
- **Exemplo**: `https://posicionadosmarilia.com.br/inscricao/AbCdEf12`
- **Redirecionamento automático** via `.htaccess`

### Formulário de Inscrição
- **Campos obrigatórios**:
  - Nome completo
  - Telefone
  - Série (1ª, 2ª, 3ª)
  - Curso
- **Campos opcionais**:
  - Email
  - Observações

### Segurança
- **Proteção de arquivos sensíveis** via `.htaccess`
- **Validação de dados** no frontend e backend
- **Prevenção de SQL Injection** com prepared statements

## 📊 Estrutura do Banco de Dados

### Tabela `qr_codes`
```sql
- id: INT (PK, Auto Increment)
- evento_id: INT (FK para eventos)
- short_code: VARCHAR(20) UNIQUE
- short_url: VARCHAR(255)
- original_url: VARCHAR(255) NULL
- qr_code_url: VARCHAR(500)
- public_url: VARCHAR(255)
- created_at: TIMESTAMP
- updated_at: TIMESTAMP
```

### Tabela `candidaturas_eventos`
```sql
- id: INT (PK, Auto Increment)
- evento_id: INT (FK para eventos)
- nome: VARCHAR(255)
- telefone: VARCHAR(20)
- serie: ENUM('1', '2', '3')
- curso: VARCHAR(100)
- email: VARCHAR(255) NULL
- observacoes: TEXT NULL
- data_candidatura: TIMESTAMP
- status: ENUM('pendente', 'aprovada', 'reprovada', 'cancelada')
- observacao_admin: TEXT NULL
- data_avaliacao: TIMESTAMP NULL
```

## 🎨 Personalização

### Alterar Domínio
Para usar um domínio diferente, altere:
1. **Arquivo `qr_manager.php`**: URLs hardcoded
2. **Arquivo `.htaccess`**: RewriteRule
3. **Arquivo `inscricao_publica.php`**: URLs fetch

### Personalizar Formulário
O formulário pode ser personalizado editando:
- **Arquivo `qr_manager.php`**: Função `generateFormHTML()`
- **CSS**: No arquivo `inscricao_publica.php`

## 📱 Uso Móvel

O sistema é totalmente responsivo e otimizado para:
- **Smartphones**: Interface touch-friendly
- **Leitura de QR Code**: Apps nativos de câmera
- **Navegadores móveis**: Compatibilidade total

## 🔍 Monitoramento

### Logs de Acesso
- Monitorar acessos aos formulários públicos
- Rastrear conversões de candidaturas
- Análise de taxa de aprovação

### Relatórios
- **Candidaturas por evento**
- **Taxa de conversão**
- **Tempo médio de avaliação**

## ⚠️ Considerações de Segurança

1. **Rate Limiting**: Implementar limite de tentativas
2. **CAPTCHA**: Adicionar proteção contra bots
3. **Validação**: Sanitização completa de dados
4. **HTTPS**: Sempre usar conexão segura
5. **Logs**: Monitorar atividades suspeitas

## 🆘 Suporte

### Problemas Comuns

#### QR Code não carrega
- Verificar conexão com Google Charts API
- Verificar permissões de arquivo

#### Link não funciona
- Verificar configuração do `.htaccess`
- Verificar se o mod_rewrite está habilitado

#### Formulário não envia
- Verificar conectividade com banco de dados
- Verificar configurações CORS

### Configuração do Servidor

Para o sistema funcionar corretamente, o servidor deve ter:

```apache
# Habilitar mod_rewrite
RewriteEngine On

# Permissões necessárias
AllowOverride All
```

## 📈 Melhorias Futuras

- [ ] Sistema de notificações por email
- [ ] Dashboard de analytics
- [ ] Integração com WhatsApp
- [ ] Templates customizáveis
- [ ] Sistema de aprovação automática
- [ ] Relatórios avançados

---

**Desenvolvido para:** Sistema de Gerenciamento de Alunos em Ônibus
**Versão:** 1.0
**Data:** Agosto 2025</content>
<parameter name="filePath">c:\laragon\www\onibus\README_QR_SYSTEM.md
