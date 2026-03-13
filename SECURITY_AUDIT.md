# 🔒 Relatório de Auditoria de Segurança
## Site Veterinário - Dra. Samla Cristie

**Data:** 2026-03-13
**Auditor:** Claude Code
**Escopo:** Sistema de Autenticação Admin + Upload/Exibição de Imagens

---

## 📊 Resumo Executivo

### ✅ Pontos Positivos
1. **Autenticação segura** com `password_verify()` e `password_hash()`
2. **Session regeneration** após login (`session_regenerate_id(true)`)
3. **Prepared statements** em todas as queries (proteção SQL Injection)
4. **Escape de output** com função `e()` (proteção XSS)
5. **Validação de MIME type** no upload de imagens
6. **Validação de extensão de arquivo**
7. **Diagnóstico restrito** apenas para localhost

### ⚠️ Vulnerabilidades Encontradas

#### 🔴 CRÍTICAS

**1. Path Traversal no Upload de Imagens**
- **Localização:** `admin/articles.php:34-38` (e similar em outros arquivos admin)
- **Descrição:** O path do upload é relativo sem validação completa
- **Risco:** Possível escrita de arquivos fora do diretório uploads/
- **Impacto:** Alto - possível execução de código

**2. CSRF (Cross-Site Request Forgery)**
- **Localização:** Todos os formulários admin
- **Descrição:** Não há tokens CSRF em nenhum formulário
- **Risco:** Atacante pode executar ações em nome do admin autenticado
- **Impacto:** Alto - pode modificar/deletar conteúdo

#### 🟠 MÉDIAS

**3. Permissões Muito Abertas**
- **Localização:** `admin/articles.php:35`
- **Descrição:** `mkdir($uploadDir, 0777, true)` e `chmod($uploadDir, 0777)`
- **Risco:** Qualquer processo pode escrever no diretório
- **Impacto:** Médio - possível injeção de arquivos maliciosos

**4. Falta de Limite de Tamanho de Upload**
- **Localização:** Todos os uploads
- **Descrição:** Não há validação de tamanho máximo do arquivo
- **Risco:** DoS por upload de arquivos muito grandes
- **Impacto:** Médio - pode esgotar espaço em disco

**5. Informações Sensíveis em Diagnóstico**
- **Localização:** `admin/login.php:63-70`
- **Descrição:** Exibe informações do banco de dados (host, user, dbname)
- **Risco:** Information disclosure
- **Impacto:** Baixo/Médio - ajuda atacante a mapear o sistema

#### 🟡 BAIXAS

**6. Falta de Rate Limiting no Login**
- **Localização:** `admin/login.php`
- **Descrição:** Sem proteção contra brute force
- **Risco:** Tentativas ilimitadas de login
- **Impacto:** Baixo - mas pode ser combinado com senhas fracas

**7. Falta de Headers de Segurança**
- **Descrição:** Sem `Content-Security-Policy`, `X-Frame-Options`, etc.
- **Risco:** Vulnerável a clickjacking e XSS baseado em DOM
- **Impacto:** Baixo - mitigado pelo escape de output

**8. Session Fixation (Parcialmente Mitigado)**
- **Localização:** `config.php:8-10`
- **Descrição:** Session inicia antes da autenticação
- **Risco:** Baixo - já usa `session_regenerate_id(true)`
- **Impacto:** Muito Baixo

---

## 🔧 Recomendações de Correção

### Prioridade 1 - CRÍTICO

#### 1.1 Adicionar Proteção CSRF
```php
// Em config.php, adicionar:
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Em cada formulário, adicionar:
<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

// No processamento POST, verificar:
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    die('CSRF token inválido');
}
```

#### 1.2 Corrigir Upload de Imagens
```php
// Validação completa de upload
function secureUpload($file, $prefix = 'img') {
    $allowedMime = ['image/jpeg','image/png','image/gif','image/webp'];
    $allowedExt = ['jpg','jpeg','png','gif','webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Erro no upload'];
    }

    if ($file['size'] > $maxSize) {
        return ['error' => 'Arquivo muito grande (max 5MB)'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedMime)) {
        return ['error' => 'Tipo de arquivo não permitido'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        return ['error' => 'Extensão não permitida'];
    }

    // Nome seguro sem path traversal
    $newName = $prefix . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $uploadDir = realpath(__DIR__.'/../uploads');

    if (!$uploadDir || !is_dir($uploadDir)) {
        mkdir(__DIR__.'/../uploads', 0755, true);
        $uploadDir = realpath(__DIR__.'/../uploads');
    }

    $destination = $uploadDir . DIRECTORY_SEPARATOR . $newName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        chmod($destination, 0644); // Apenas leitura
        return ['success' => true, 'path' => 'uploads/' . $newName];
    }

    return ['error' => 'Falha ao mover arquivo'];
}
```

### Prioridade 2 - MÉDIO

#### 2.1 Corrigir Permissões
```php
// Substituir:
mkdir($uploadDir, 0777, true);
@chmod($uploadDir, 0777);

// Por:
mkdir($uploadDir, 0755, true);
@chmod($uploadDir, 0755);
// E arquivos com chmod 0644
```

#### 2.2 Adicionar Rate Limiting
```php
// Simples rate limit baseado em sessão
function checkLoginAttempts() {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_last_attempt'] = time();
    }

    // Reset após 15 minutos
    if (time() - $_SESSION['login_last_attempt'] > 900) {
        $_SESSION['login_attempts'] = 0;
    }

    if ($_SESSION['login_attempts'] >= 5) {
        $waitTime = 900 - (time() - $_SESSION['login_last_attempt']);
        if ($waitTime > 0) {
            return ['blocked' => true, 'wait' => ceil($waitTime / 60)];
        }
    }

    return ['blocked' => false];
}
```

### Prioridade 3 - BAIXO

#### 3.1 Adicionar Security Headers
```php
// No início de cada página admin:
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
```

#### 3.2 Melhorar Gerenciamento de Sessão
```php
// Em config.php, antes de session_start():
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Se HTTPS
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
```

---

## 📋 Checklist de Implementação

- [ ] Implementar tokens CSRF em todos os formulários
- [ ] Refatorar função de upload seguro
- [ ] Corrigir permissões de diretórios (0755) e arquivos (0644)
- [ ] Adicionar limite de tamanho de upload
- [ ] Implementar rate limiting no login
- [ ] Adicionar security headers
- [ ] Melhorar configuração de sessão
- [ ] Remover/restringir diagnóstico de banco de dados
- [ ] Testar todas as correções
- [ ] Deploy em produção

---

## 🧪 Testes Recomendados

1. **SQL Injection:** ✅ PASSOU (prepared statements)
2. **XSS:** ✅ PASSOU (escape com htmlspecialchars)
3. **CSRF:** ❌ FALHOU (sem proteção)
4. **File Upload:** ⚠️ PARCIAL (validação básica, mas permissões ruins)
5. **Authentication:** ✅ PASSOU (password_verify)
6. **Session Management:** ✅ PASSOU (session_regenerate_id)
7. **Path Traversal:** ⚠️ RISCO MÉDIO (uploadDir relativo)

---

## 📌 Conclusão

O sistema possui uma base sólida de segurança (prepared statements, password hashing, escape de output), mas precisa de melhorias críticas em:
- **Proteção CSRF** (prioridade máxima)
- **Upload seguro de arquivos**
- **Permissões de sistema de arquivos**

Implementando as correções de Prioridade 1, o sistema alcançará um nível de segurança adequado para produção.

**Score de Segurança Atual:** 6.5/10
**Score Esperado Após Correções:** 9/10
