# Limpeza de Inconsistências - Sistema Ônibus

## Arquivos para Mover/Organizar

### 1. Arquivos de Debug/Teste (mover para pasta debug/)
```
diagnostico_candidaturas.php
diagnostico_visual.php
diag_ajax_eventos.php
teste_ajax.php
teste_conexao_db.php
teste_json.php
teste_sistema.php
test_query.php
test_query_presence.php
test_presence.php
test_dates.php
test_ajax.php
```

### 2. Arquivos de Resposta/Inspeção (deletar - são temporários)
```
response.bin
response.hex
response.txt
response_*.bin
response_*.hex
response_get*.bin
response_get*.hex
response_evt.*
headers_*.txt
inspect_bytes.ps1
inspect_last_bytes.ps1
```

### 3. Duplicatas para Remover (manter apenas na pasta principal)
Remover da pasta `arquivos_desnecessarios/`:
```
setup_qr.php (IDÊNTICO - pode ser removido da pasta arquivos_desnecessarios)
```

### 4. Duplicatas com Diferenças (revisar e consolidar)
Comparar versões e manter apenas a mais atual:
```
auto_alocar.php (versões diferentes)
install.php (versões diferentes)
debug.php (versões diferentes)
README.md (versões diferentes)
```

## Estrutura Proposta

```
onibus/
├── debug/                  # Arquivos de debug e teste
│   ├── diagnostico_*.php
│   ├── teste_*.php
│   └── test_*.php
├── arquivos_desnecessarios/ # Manter apenas arquivos realmente desnecessários
├── inscricao/              # Rota pública (OK)
└── [arquivos principais]   # Apenas arquivos de produção
```

## Comandos de Limpeza Sugeridos

1. **Criar pasta debug:**
```powershell
mkdir debug
```

2. **Mover arquivos de debug:**
```powershell
mv diagnostico_*.php debug/
mv teste_*.php debug/
mv test_*.php debug/
mv diag_*.php debug/
```

3. **Remover arquivos temporários:**
```powershell
rm response*.*, headers*.txt, inspect_*.ps1
```

4. **Revisar duplicatas:**
```powershell
# Compare e mantenha apenas uma versão de cada
Compare-Object (Get-Content auto_alocar.php) (Get-Content arquivos_desnecessarios/auto_alocar.php)
```

## Links de Navegação para Atualizar

Após mover arquivos de debug, atualizar links em:
- `diagnostico_visual.php` (se mantido)
- `navbar.php` (se houver referências)
- `index.php` (se houver referências)
