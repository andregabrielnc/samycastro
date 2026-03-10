# 🐳 Docker & Containerização - SamyCastro

Guia completo para containerizar e fazer deploy da aplicação SamyCastro.

---

## 📁 Arquivos Docker Inclusos

### 1. **Dockerfile**
- Imagem base: `php:8.2-apache`
- Instala extensões necessárias: PDO, MySQL, GD, ZIP
- Otimizado para produção
- Health check automático

### 2. **docker-compose.yml** (Desenvolvimento local)
```bash
docker-compose up -d
```
- Web service em http://localhost
- MySQL em localhost:3306
- PHPMyAdmin em http://localhost:8080

### 3. **docker-compose.dev.yml** (Desenvolvimento com hot-reload)
```bash
docker-compose -f docker-compose.dev.yml up -d
```
- Volume bind para código ao vivo
- Ideal para desenvolvimento
- Mudanças em arquivos PHP refletem instantaneamente

### 4. **docker-compose.coolify.yml** (Produção - Coolify)
- Usa `docker-compose.dev.yml` como base
- Build automático do Dockerfile
- Volumes persistentes para MySQL e uploads
- Sem portas expostas (Coolify cuida disso)

---

## 🚀 Quickstart

### Desenvolvimento Local (Windows)

**PowerShell:**
```powershell
# Dar permissão ao script
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# Iniciar com um comando
.\deploy.ps1 dev
```

**Ou manualmente:**
```bash
# Copiar .env
copy .env.example .env

# Editar .env conforme necessário
notepad .env

# Iniciar containers
docker-compose -f docker-compose.dev.yml up -d

# Acessar
- http://localhost (app)
- http://localhost:8080 (PHPMyAdmin - user: samlavet_user)
```

### Desenvolvimento Local (Linux/Mac)

```bash
# Dar permissão
chmod +x deploy.sh

# Iniciar
./deploy.sh dev

# Ou manualmente
cp .env.example .env
nano .env  # editar conforme necessário
docker-compose -f docker-compose.dev.yml up -d
```

---

## 🌐 Deploy no Coolify

### Passo 1: Push para GitHub
```bash
git add .
git commit -m "Containerization ready for Coolify"
git push origin main
```

### Passo 2: Criar Service no Coolify
1. Vá para **Applications** → **New Application**
2. Selecione **Docker Compose**
3. Conecte seu repositório GitHub
4. Aponte para: `docker-compose.coolify.yml`

### Passo 3: Configurar Variáveis
```env
DB_NAME=samlavet
DB_USER=samlavet_user
DB_PASS=SenhaSegura123!
MYSQL_ROOT_PASSWORD=SenhaRootSegura123!
```

### Passo 4: Deploy
- Clique em **Deploy**
- Aguarde o build (5-10 min na primeira vez)
- Acesse: `https://seu-dominio.com`

### Passo 5: Instalação
1. Acesse: `https://seu-dominio.com/install.php`
2. Configure banco de dados:
   - Host: `db`
   - Nome: `samlavet`
   - Usuário: `samlavet_user`
   - Senha: (a que configurou em DB_PASS)

### Passo 6: Pós-Instalação
```bash
# Delete install.php
git rm install.php
git commit -m "Remove install.php"
git push

# Coolify fará redeploy automaticamente
```

---

## 📊 Arquitetura

```
┌─────────────────────────────────────────────────────────┐
│                   Seu Servidor (Coolify)               │
├──────────────────────┬──────────────────────────────────┤
│                      │                                  │
│  ┌─────────────────┐ │  ┌──────────────────────────┐   │
│  │  Web Container  │ │  │  Database Container      │   │
│  │                 │ │  │  (MySQL 8.0)             │   │
│  │ PHP 8.2         │ │  │                          │   │
│  │ Apache          │ │  │ init-db.sql              │   │
│  │ Extensões MySQL │ │  │ Volumes persistentes     │   │
│  │ Port 80         │ │  │ Health check             │   │
│  └────────┬────────┘ │  └──────────────────────────┘   │
│           │          │           ▲                     │
│           └──────────┼───────────┘                     │
│                      │ (Comunicação interna)          │
│  ┌─────────────────┐ │                                │
│  │   PHPMyAdmin    │ │ (Opcional - Gerenciar BD)     │
│  │   Port 80       │ │                                │
│  └─────────────────┘ │                                │
│                      │                                │
└──────────────────────┴──────────────────────────────────┘
         ▲
         │ HTTPS (Coolify cuida)
         │
    Usuários Internet
```

---

## 🛠️ Comandos Úteis

### Docker Compose
```bash
# Iniciar em background
docker-compose -f docker-compose.dev.yml up -d

# Ver status
docker-compose -f docker-compose.dev.yml ps

# Ver logs
docker-compose -f docker-compose.dev.yml logs -f web

# Parar
docker-compose -f docker-compose.dev.yml down

# Remover volumes (cuidado! deleta dados)
docker-compose -f docker-compose.dev.yml down -v
```

### Docker CLI
```bash
# Listar imagens
docker images

# Listar containers rodando
docker ps

# Acessar container em execução
docker exec -it samycastro-web-dev bash

# Ver logs em tempo real
docker logs -f samycastro-web-dev

# Limpar sistema
docker system prune -a
```

---

## 🔧 Troubleshooting

### ❌ "Erro de conexão com MySQL"
```bash
# Aguarde 30 segundos para o MySQL iniciar
# Verifique se DB_HOST=db nas variáveis
docker-compose -f docker-compose.dev.yml logs db
```

### ❌ "Erro 403 ou 404 no navegador"
```bash
# Verifique permissões
docker-compose -f docker-compose.dev.yml logs web

# Reinicie
docker-compose -f docker-compose.dev.yml restart web
```

### ❌ "Uploads não funcionam"
```bash
# Verifique permissões do volume
docker exec samycastro-web-dev ls -la /var/www/html/uploads

# Deve ser 777 e owner www-data
```

### ❌ "PHPMyAdmin não conecta"
```bash
# Verifique variáveis
docker-compose -f docker-compose.dev.yml ps

# Verifique logs
docker-compose -f docker-compose.dev.yml logs phpmyadmin
```

---

## 🔐 Segurança

### Para Produção (Coolify):

1. ✅ **Alterar senhas padrão**
   - `DB_PASS` → Senha forte
   - `MYSQL_ROOT_PASSWORD` → Senha forte
   - Admin password → Senha forte

2. ✅ **Deletar install.php**
   ```bash
   git rm install.php
   git commit -m "Remove install script"
   git push
   ```

3. ✅ **Desabilitar PHPMyAdmin** (comentar no docker-compose.coolify.yml)
   ```yaml
   # phpmyadmin:
   #   image: phpmyadmin/phpmyadmin
   #   ...
   ```

4. ✅ **Habilitar HTTPS** (Coolify cuida automaticamente)

5. ✅ **Configurar backups** (Settings → Backups no Coolify)

---

## 📝 Arquivo .env

Copiar de `.env.example` e editar:

```env
# Banco de dados
DB_NAME=samlavet
DB_USER=samlavet_user
DB_PASS=SuaSenhaSegura123!
MYSQL_ROOT_PASSWORD=SenhaRootSegura123!

# Opcional
SITE_NAME=Dra. Samla Cristie
```

**IMPORTANTE:** Nunca commitar `.env` com senhas reais!

---

## 📚 Recursos Adicionais

- **Documentação Docker**: https://docs.docker.com/
- **Documentação Docker Compose**: https://docs.docker.com/compose/
- **Coolify Docs**: https://other.coolify.io/
- **PHP Docker**: https://hub.docker.com/_/php

---

## ✅ Checklist de Deploy

- [ ] `.env` configurado com senhas seguras
- [ ] `init-db.sql` presente no repositório
- [ ] `docker-compose.coolify.yml` apontando para o Dockerfile correto
- [ ] Dockerfile otimizado para produção
- [ ] `.dockerignore` configurado
- [ ] GitHub repositório atualizado
- [ ] Coolify conectado ao GitHub
- [ ] Variáveis de ambiente no Coolify
- [ ] Deploy concluído
- [ ] `install.php` deletado pós-instalação
- [ ] HTTPS funcionando
- [ ] Backups configurados

---

**Sucesso com seu deployment! 🎉**
