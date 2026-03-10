# 🔧 Solução de Erros de Conexão - SamyCastro

## ❌ Erro: "Erro ao conectar ao banco de dados"

Se você vê uma mensagem de erro ao tentar fazer login no `/admin/login.php`, siga estas instruções:

---

## 🆘 Passos de Diagnóstico

### 1️⃣ Ver Diagnóstico Automático

Acesse: `https://samycastro.vet/admin/login.php?diagnostics=1`

Você verá:
- ✅ Variáveis de banco de dados configuradas
- ✅ Teste de conexão com MySQL
- ✅ Status das extensões PHP

### 2️⃣ Ver Diagnóstico Detalhado

Acesse: `https://samycastro.vet/admin/debug.php`

Este relatório mostra:
- PHP version e extensões
- Configuração do banco de dados
- Teste de conexão ao servidor MySQL
- Lista de tabelas no banco
- Número de usuários admin existentes

---

## 🐛 Problemas Comuns e Soluções

### ❌ "Database connection refused"

**Possíveis Causas:**
1. Banco de dados não está rodando
2. Host do banco (`DB_HOST`) errado
3. Credenciais de acesso erradas

**Soluções:**
```bash
# 1. Verificar se containers estão rodando
docker-compose ps

# 2. Verificar nome do container
docker ps | grep mysql

# 3. Verificar se o MySQL está saudável
docker logs samycastro-db

# 4. Testar conexão manualmente
docker exec samycastro-db mysql -u samlavet_user -p samlavet -e "SELECT 1"
```

**Para Coolify:**
- Certifique-se de que `DB_HOST=db` nas variáveis de ambiente
- Verifique o status dos containers em **Settings** → **Logs**

---

### ❌ "No admin users found"

**Significa:** O banco de dados existe, mas não há usuário admin.

**Soluções:**

**Opção 1: Recuperar script de setup**
```bash
# Restaurar do histórico git
git checkout HEAD~1 auto-setup.php

# Fazer push
git add auto-setup.php
git commit -m "Restore auto-setup.php"
git push

# Coolify vai redeploy automaticamente
# Depois acesse /auto-setup.php
```

**Opção 2: Criar admin manualmente no PHPMyAdmin**
```sql
-- Acesse PHPMyAdmin em http://seu-dominio:8080
-- Use credenciais: samlavet_user / sua-senha

INSERT INTO admin_users (username, password, name) 
VALUES ('admin', '$2y$10$...hash_da_senha...', 'Administrador');
```

---

### ❌ "PDO MySQL extension not loaded"

**Significa:** PHP não tem extensão para MySQL.

**Soluções:**

1. **Se está usando Docker em desenvolvimento:**
   ```bash
   docker-compose -f docker-compose.dev.yml down
   docker-compose -f docker-compose.dev.yml build --no-cache
   docker-compose -f docker-compose.dev.yml up -d
   ```

2. **Se está no Coolify:**
   - Vá em **Settings** → **Redeploy**
   - Limpe cache: **Settings** → **Clear Cache**
   - Faça rebuild: **Re-deploy**

---

### ⚠️ "Connection timeout"

**Significa:** O MySQL demora para responder.

**Soluções:**

1. Aguarde o MySQL inicializar (pode levar 30 segundos)
2. Recarregue a página após alguns segundos
3. Verifique os logs: `docker logs samycastro-db`

---

## 🔍 Verificação de Variáveis de Ambiente

### No Docker (Local):

Verifique seu `.env`:
```bash
cat .env
```

Deve ter:
```
DB_HOST=db
DB_NAME=samlavet
DB_USER=samlavet_user
DB_PASS=sua-senha-segura
MYSQL_ROOT_PASSWORD=sua-senha-root
```

### No Coolify:

1. Vá em seu Service
2. **Settings** → **Environment Variables**
3. Verifique se todos estão configurados corretamente

---

## 📊 Checklist de Debug

- [ ] Tentar acesso em `/admin/login.php?diagnostics=1`
- [ ] Verificar diagnóstico em `/admin/debug.php`
- [ ] Confirmar que containers estão rodando: `docker ps`
- [ ] Confirmar variáveis de ambiente estão corretas
- [ ] Verificar logs: `docker logs samycastro-db`
- [ ] Testar conexão manualmente ao MySQL
- [ ] Verificar se tabelas foram criadas no banco
- [ ] Verificar se existe usuário admin no banco

---

## 🆘 SE NADA FUNCIONAR

### Opção 1: Reset completo (local)

```bash
# Parar containers
docker-compose down -v

# Copiar .env
cp .env.example .env

# Editar .env com suas senhas
nano .env

# Reconstruir
docker-compose up -d

# Aguardar inicialização
sleep 30

# Acessar /auto-setup.php para configurar
```

### Opção 2: Reset no Coolify

1. Vá em seu Service → **Settings** → **Delete Service**
2. Crie novo Service do zero
3. Copie o `docker-compose.coolify.yml`
4. Configure variáveis

---

## 📝 Logs para Investigação

### Logs do PHP/Apache:
```bash
docker logs samycastro-web
```

### Logs do MySQL:
```bash
docker logs samycastro-db
```

### Logs de Erro do MySQL:
```bash
docker exec samycastro-db tail -100 /var/log/mysql/error.log
```

---

## 💬 Informações para Suporte

Se precisar de ajuda, forneça:

1. Output de: `https://seu-dominio/admin/debug.php`
2. Output de: `docker logs samycastro-db` (últimas 20 linhas)
3. Output de: `docker logs samycastro-web` (últimas 20 linhas)
4. Seu `docker-compose.coolify.yml` (sem senhas)
5. Suas variáveis de ambiente no Coolify (sem senhas)

---

**Sucesso! Você vai conseguir! 💪**
