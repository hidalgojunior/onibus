
# Sistema de Gerenciamento de Alunos em Ônibus para Eventos

## 🆕 Novas Funcionalidades Implementadas (Agosto 2025)

### 🎯 Sistema Completo de Gerenciamento de Eventos e Ônibus

#### 📅 Gerenciamento de Eventos
- **Cadastrar Eventos**: Crie eventos com nome, período, local e descrição
- **Visualizar Estatísticas**: Veja total de alunos e ônibus por evento
- **Editar/Remover Eventos**: Gerencie eventos existentes com segurança
- **Integração Completa**: Eventos conectam alunos, ônibus e alocações

#### 🚌 Gerenciamento de Ônibus
- **Cadastrar Ônibus**: Registre ônibus, vans ou micro-ônibus por evento
- **Definir Capacidades**: Configure capacidade máxima e dias de reserva
- **Organização por Evento**: Cada ônibus é vinculado a um evento específico
- **Controle Total**: Edite ou remova ônibus conforme necessário

#### ⚡ Alocação Automática Inteligente
- **Baseada na Ordem de Inscrição**: Alunos são alocados por ordem de chegada (data_inscricao)
- **Distribuição Equilibrada**: Sistema distribui alunos igualmente entre ônibus disponíveis
- **Preview Antes da Alocação**: Visualize como ficará a distribuição antes de confirmar
- **Limpeza e Reprocessamento**: Permite limpar alocações atuais e refazer distribuição
- **Respeito à Capacidade**: Sistema respeita limite máximo de cada veículo

### 🔄 Novo Fluxo de Uso Recomendado

1. **📅 Criar Evento** (`eventos.php`)
   - Defina nome, datas, local e descrição do evento

2. **🚌 Cadastrar Ônibus** (`onibus.php`)
   - Registre todos os veículos disponíveis para o evento
   - Defina capacidades e períodos de reserva

3. **👥 Importar Alunos** (`import_students.php`)
   - Importe alunos em massa (sistema registra automaticamente data de inscrição)
   - Cada aluno importado recebe timestamp de inscrição

4. **⚡ Alocação Automática** (`alocacao.php`)
   - Sistema aloca automaticamente baseado na ordem de inscrição
   - Primeiro aluno inscrito = primeiro alocado
   - Distribuição igualitária entre ônibus disponíveis

5. **📋 Controle de Presença** (`presence.php`)
   - Marque alunos que embarcaram/desembarcaram
   - Sistema já sabe qual ônibus cada aluno foi alocado

6. **📊 Relatórios** (`daily_report.php`)
   - Visualize presenças por ônibus e data
   - Estatísticas completas de embarque/retorno

### 🗄️ Estrutura do Banco de Dados Atualizada

#### Novas Tabelas:
- **`eventos`**: Armazena informações dos eventos
- **`onibus`**: Cadastro de veículos por evento
- **`alocacoes_onibus`**: Liga alunos a ônibus específicos

#### Tabelas Atualizadas:
- **`alunos`**: Adicionada coluna `evento_id` e `data_inscricao`
- **Compatibilidade**: Mantém todas as funcionalidades anteriores

### 🚀 Como Atualizar o Sistema

1. **Execute o Script de Atualização**:
   ```bash
   # Acesse no navegador:
   http://localhost/onibus/update_database_new.php
   ```

2. **O que o script faz**:
   - ✅ Cria tabelas `eventos`, `onibus`, `alocacoes_onibus`
   - ✅ Adiciona colunas necessárias à tabela `alunos`
   - ✅ Cria evento e ônibus de exemplo
   - ✅ Verifica estrutura completa do banco

3. **Acesse as novas funcionalidades**:
   - **Eventos**: `eventos.php`
   - **Ônibus**: `onibus.php`
   - **Alocação Automática**: `alocacao.php`

### 📱 Interface Melhorada

- **Menu Reorganizado**: Novo dropdown "Gerenciamento" com Eventos, Ônibus e Alocação
- **Responsividade**: Todas as páginas funcionam perfeitamente em mobile
- **Feedback Visual**: Loading states, mensagens de sucesso/erro
- **Modais Interativos**: Confirmações seguras para ações críticas

### 🔒 Segurança e Integridade

- **Prepared Statements**: Todas as consultas usam prepared statements
- **Foreign Keys**: Relacionamentos íntegros entre tabelas
- **Transações**: Operações críticas usam transações para consistência
- **Validações**: Dados validados tanto no frontend quanto backend

---

## Objetivo

Desenvolver um sistema para gerenciar o embarque e retorno de alunos em ônibus escolares utilizados em eventos institucionais, com foco no controle de presença, alocação de alunos, geração de listas (online e física) e autorizações.

## Funcionalidades Principais

- Cadastro de alunos, responsáveis, ônibus e eventos
- Alocação de alunos em diferentes ônibus para eventos específicos
- Importação em massa de alunos (copiar e colar)
- Controle de presença dos alunos, com registro de embarque e retorno
- Geração de listas de presença online e físicas (para impressão e controle manual)
- Identificação de alunos que não retornaram no ônibus e notificação ao responsável pelo ônibus
- Geração de formulários de autorização de saída e de uso de imagem
- No primeiro acesso do aluno, preenchimento de cadastro completo, utilizando RM e senha como identificadores
- Geração de formulário de presença para cada ônibus disponibilizado
- Possibilidade de verificar quantos alunos embarcaram e quantos não retornaram
- Autorização de saída da escola inclui autorização de uso de imagem, gerada para todos os eventos em que o aluno participar
- Cadastro do aluno solicita data de aniversário e permissão para receber notificações por WhatsApp
- Envio automático de mensagens via WhatsApp para alunos autorizados, em lotes de 20 a cada 5 segundos, quando o embarque for autorizado

## Fluxo de Uso

1. Cadastro/importação dos alunos e responsáveis
2. Cadastro dos ônibus e eventos
3. Alocação dos alunos nos ônibus para cada evento
4. Geração de listas de presença (online e física) para embarque e retorno
5. Controle de presença no embarque e no retorno
6. Registro e comunicação de alunos que não retornaram no ônibus
7. Geração e armazenamento de autorizações e formulários necessários
8. Inclusão automática de autorização de uso de imagem em todas as autorizações de saída
9. Envio de notificações por WhatsApp para os alunos autorizados, em lotes, no momento do embarque

## Tecnologias

- **Backend:** PHP
- **Frontend:** Bootstrap (framework CSS)
- **Banco de Dados:** MySQL (base chamada `onibus`)

## Como Usar o Sistema

1. **Página Inicial**:
   - Acesse `index.php` para ver o painel principal
   - O sistema detecta automaticamente o ambiente (local/online)

2. **Configuração Inicial**:
   - Use os links na página inicial para instalar ou atualizar o banco
   - O sistema detecta automaticamente se está rodando localmente ou no servidor remoto
   - Para desenvolvimento local: usa `localhost`, usuário `root`, senha em branco
   - Para produção: usa o servidor remoto com as credenciais configuradas

3. **Instalação do Banco**:
   - Acesse `install.php` para criar o banco de dados
   - Ou use `update_database.php` se precisar atualizar tabelas existentes
   - Use `update_autorizacoes.php` para adicionar suporte às autorizações

4. **Importação de Alunos**:
   - Acesse `import_students.php`
   - Cole os dados no formato: `Nome - Série - Curso - Telefone`
   - Clique em "Importar Alunos"

5. **Controle de Presença**:
   - Acesse `presence.php`
   - Clique nos botões vermelhos (Não Embarcou) para marcar como Embarcou (verde)
   - Clique nos botões verdes (Embarcou) para remover o embarque (vermelho)
   - O sistema registra automaticamente no ônibus 1 para o evento atual

6. **Controle de Retorno**:
   - Acesse `return_control.php`
   - Selecione data e ônibus
   - Marque apenas os alunos que retornaram
   - Sistema registra o retorno dos alunos

7. **Geração de Autorizações**:
   - Acesse `autorizacoes.php`
   - Selecione um aluno e o tipo de autorização
   - Preencha os dados necessários
   - Gere e imprima a autorização

8. **Listar Autorizações**:
   - Acesse `listar_autorizacoes.php`
   - Visualize todas as autorizações já geradas
   - Filtre por aluno ou tipo de autorização

9. **Relatórios Diários**:
   - Acesse `daily_report.php`
   - Selecione a data e o ônibus
   - Visualize quem embarcou e quem não embarcou
   - Veja estatísticas e imprima o relatório

## Funcionalidades Principais

- Cadastro de alunos, responsáveis, ônibus e eventos
- Alocação de alunos em diferentes ônibus para eventos específicos
- Importação em massa de alunos (copiar e colar)
- Controle de presença dos alunos, com registro de embarque e retorno
- Geração de listas de presença online e físicas (para impressão e controle manual)
- Identificação de alunos que não retornaram no ônibus e notificação ao responsável pelo ônibus
- Geração de formulários de autorização de saída e de uso de imagem
- No primeiro acesso do aluno, preenchimento de cadastro completo, utilizando RM e senha como identificadores
- Geração de formulário de presença para cada ônibus disponibilizado
- Possibilidade de verificar quantos alunos embarcaram e quantos não retornaram
- Autorização de saída da escola inclui autorização de uso de imagem, gerada para todos os eventos em que o aluno participar
- Cadastro do aluno solicita data de aniversário e permissão para receber notificações por WhatsApp
- Envio automático de mensagens via WhatsApp para alunos autorizados, em lotes de 20 a cada 5 segundos, quando o embarque for autorizado

## Fluxo de Uso

1. Cadastro/importação dos alunos e responsáveis
2. Cadastro dos ônibus e eventos
3. Alocação dos alunos nos ônibus para cada evento
4. Geração de listas de presença (online e física) para embarque e retorno
5. Controle de presença no embarque e no retorno
6. Registro e comunicação de alunos que não retornaram no ônibus
7. Geração e armazenamento de autorizações e formulários necessários
8. Inclusão automática de autorização de uso de imagem em todas as autorizações de saída
9. Envio de notificações por WhatsApp para os alunos autorizados, em lotes, no momento do embarque

## Tecnologias

- **Backend:** PHP
- **Frontend:** Bootstrap (framework CSS)
- **Banco de Dados:** MySQL (base chamada `onibus`)

## Como Usar o Sistema

1. **Página Inicial**:
   - Acesse `index.php` para ver o painel principal
   - O sistema detecta automaticamente o ambiente (local/online)

2. **Configuração Inicial**:
   - Use os links na página inicial para instalar ou atualizar o banco
   - O sistema detecta automaticamente se está rodando localmente ou no servidor remoto
   - Para desenvolvimento local: usa `localhost`, usuário `root`, senha em branco
   - Para produção: usa o servidor remoto com as credenciais configuradas

3. **Instalação do Banco**:
   - Acesse `install.php` para criar o banco de dados
   - Ou use `update_database.php` se precisar atualizar tabelas existentes
   - Use `update_autorizacoes.php` para adicionar suporte às autorizações

4. **Importação de Alunos**:
   - Acesse `import_students.php`
   - Cole os dados no formato: `Nome - Série - Curso - Telefone`
   - Clique em "Importar Alunos"

5. **Controle de Presença**:
   - Acesse `presence.php`
   - Marque os alunos que embarcaram
   - O sistema registra automaticamente no ônibus 1 para o evento atual

## Funcionalidades Implementadas

- ✅ Detecção automática de ambiente (local/online)
- ✅ Instalação do banco de dados
- ✅ Importação de alunos em massa
- ✅ Controle de presença de embarque
- ✅ Controle de retorno dos alunos
- ✅ Alocação automática no ônibus
- ✅ Relatório diário de presença por ônibus
- ✅ Geração de autorizações de saída e uso de imagem
- ✅ Listagem e visualização de autorizações geradas
- ✅ Menu responsivo personalizado em todas as páginas
- ✅ Interface responsiva com Bootstrap 5.3.0
- ✅ Botões dinâmicos com feedback visual
- ✅ Otimização da interface (coluna série removida)
- ✅ Correção de conflitos de funções
- ✅ Estatísticas de embarque (total, embarcaram, não embarcaram)
- ✅ Tratamento de formatos especiais de nome

## Arquivos Criados

- `index.php`: Página inicial do sistema com navegação
- `config.php`: Arquivo de configuração de conexão com detecção automática de ambiente (local/online)
- `install_database.sql`: Script SQL para criação das tabelas
- `install.php`: Instalador visual do banco de dados com Bootstrap
- `import_students.php`: Página para importar alunos em massa
- `presence.php`: Formulário para controle de presença de embarque
- `return_control.php`: Formulário para controle de retorno dos alunos
- `navbar.php`: Menu responsivo personalizado usado em todas as páginas
- `autorizacoes.php`: Página para geração de autorizações de saída e uso de imagem
- `listar_autorizacoes.php`: Página para listar e visualizar autorizações geradas
- `get_autorizacao.php`: Script AJAX para buscar conteúdo de autorizações
- `update_autorizacoes.php`: Página para atualizar o banco com suporte às autorizações
- `daily_report.php`: Relatório diário de presença por ônibus
- `update_database.php`: Página para atualizar o tamanho do campo telefone

---

## Prompts e Respostas para Cada Etapa do Projeto

### 1. Cadastro/Importação de Alunos e Responsáveis
- Como será feita a importação em massa dos alunos? (Formato, campos obrigatórios, validação)
- Quais dados dos responsáveis devem ser obrigatórios?
- Como será feita a associação entre aluno e responsável?

**Respostas:**
- A importação em massa será feita por um arquivo texto, com cada linha contendo: Nome do aluno - Série - Curso - Telefone.
- No cadastro em lote, não haverá dados de responsáveis. No cadastro individual, será obrigatório informar nome, e-mail e telefone do responsável.

### 2. Cadastro de Ônibus e Eventos
- Quais informações são necessárias para cadastrar um ônibus? (placa, capacidade, motorista, etc.)
- Quais informações são necessárias para cadastrar um evento? (nome, datas, local, etc.)

**Respostas:**
- Ônibus terão um número identificador, e será possível imprimir uma placa em papel A4 (paisagem, Arial, letras maiúsculas) com o número do ônibus, nome do evento e um QR Code.
- O QR Code será gerado para cada dia do evento, permitindo o registro de presença do aluno naquele dia específico.
- Além de ônibus, poderão ser cadastrados vans ou carros, sendo obrigatório informar a capacidade do veículo e impedindo o embarque de mais pessoas do que o permitido.
- No cadastro do veículo, será necessário informar para qual evento ele será utilizado e para quantos dias estará reservado.

### 3. Alocação de Alunos nos Ônibus para Cada Evento
- Como será feita a alocação dos alunos nos ônibus? (manual, automática, critérios)
- Será possível editar a alocação após o cadastro?

**Respostas:**
- A alocação será feita a partir do cadastro do aluno no ônibus, evento a evento.
- O evento será cadastrado (ex: Bootcamp Jovem Programador) e, em seguida, o acesso ao ônibus para o evento será feito individualmente, aluno por aluno.
- Após a validação dos ônibus para o evento, será possível elencar quem ficará em cada ônibus.

### 4. Geração de Listas de Presença (Online e Física)
- Qual o formato desejado para as listas físicas? (PDF, impressão direta, etc.)
- Quais informações devem constar nas listas?

### 5. Controle de Presença no Embarque e no Retorno
- Como será feito o registro de presença? (digital, manual, ambos)
- Como identificar alunos que não retornaram?

### 6. Registro e Comunicação de Alunos que Não Retornaram
- Como será feita a comunicação ao responsável pelo ônibus?
- Haverá registro de justificativa para não retorno?

### 7. Geração e Armazenamento de Autorizações e Formulários
- Como será feita a geração dos documentos? (modelo fixo, editável)
- Como será feito o armazenamento e consulta das autorizações?

### 8. Inclusão Automática de Autorização de Uso de Imagem
- O texto da autorização de uso de imagem será padrão para todos os eventos?
- Como será feito o aceite pelo responsável?

### 9. Envio de Notificações por WhatsApp
- Qual serviço será utilizado para envio das mensagens?
- Como será feito o controle dos lotes e do tempo de envio?
- O aluno pode optar por não receber notificações a qualquer momento?

---

## Mudanças Recentes (27/08/2025)

### ✅ Melhorias Implementadas:
- **Botões Dinâmicos no Controle de Presença**: Os botões agora são vermelhos para "Não Embarcou" e verdes para "Embarcou", permitindo alternar facilmente entre os estados
- **Configuração Sempre Online**: O sistema foi configurado para usar sempre o banco de dados online (177.153.208.104), removendo a detecção automática de ambiente
- **Correção de Erros de Banco**: Corrigidos os erros de colunas inexistentes nas tabelas de autorizações
- **Atualização Automática do Schema**: Criado sistema para atualizar automaticamente o banco de dados com novas colunas quando necessário

### 🔧 Configuração Atual:
- **Banco de Dados**: Sempre online em 177.153.208.104
- **Usuário**: onibus
- **Senha**: Devisate@2025@
- **Banco**: onibus

### 🎯 Como Usar os Novos Botões:
- **Vermelho (Não Embarcou)**: Clique para marcar o aluno como embarcado
- **Verde (Embarcou)**: Clique para remover o embarque do aluno
- **Salvamento Automático**: As mudanças são salvas quando você clica em "Salvar Presença"

### 🧭 **Novo Menu Responsivo:**
- **Implementado**: Menu personalizado em todas as páginas
- **Responsivo**: Adapta-se a desktop e mobile
- **Organizado**: Links agrupados por categoria (Configuração, Controle, Relatórios, Autorizações)
- **Visual**: Tema dark blue com ícones FontAwesome
- **Navegação**: Fácil acesso a todas as funcionalidades do sistema

### 📊 **Otimização da Interface:**
- **Controle de Embarque**: Coluna "Série" removida para interface mais limpa
- **Correção de Erros**: Resolvido problema de redeclaração de funções
- **Performance**: Includes otimizados para evitar conflitos
