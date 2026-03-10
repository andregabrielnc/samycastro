# 🚀 Guia de Deployment no Coolify

## 📋 Pré-requisitos

- Conta ativa no Coolify
- Acesso ao repositório GitHub (com as alterações pushed)
- Imagens Docker: MySQL 8.0, PHP 8.2-Apache

---

## 🔧 Passos para Deploy no Coolify

### 1️⃣ Conectar o Repositório GitHub

1. No Coolify, vá para **Applications** → **New Application**
2. Selecione **Docker Compose**
3. Conecte seu repositório GitHub: `https://github.com/andregabrielnc/samycastro.git`
4. Selecione a branch principal (geralmente `main` ou `master`)

### 2️⃣ Configurar Docker Compose File

1. Aponte para o arquivo: `docker-compose.coolify.yml`
2. Este arquivo contém:
   - **Web Service**: Build automático usando o Dockerfile
   - **MySQL Database**: Inicialização automática com `init-db.sql`
   - **PHPMyAdmin**: Para gerenciar o banco (opcional)

### 3️⃣ Configurar Variáveis de Ambiente

**IMPORTANTE**: Altere as senhas padrão para valores seguros!

```
DB_NAME=samlavet
DB_USER=samlavet_user
DB_PASS=SuaSenhaSeguraAqui123!
MYSQL_ROOT_PASSWORD=SenhaRootSeguraAqui123!
```

### 4️⃣ Configurar Volumes Persistentes

Com o docker-compose.coolify.yml, os volumes são:
- `mysql-data`: Persiste dados do MySQL
- `uploads`: Diretório de uploads da aplicação

### 5️⃣ Deploy

1. Clique em **Deploy**
2. Aguarde o build da imagem Docker (primeira vez pode levar ~5-10 minutos)
3. Aguarde os containers iniciarem
4. Acesse: `https://seu-dominio.com`

### 6️⃣ Instalação Inicial

1. Acesse: `https://seu-dominio.com/install.php`
2. Preencha o formulário:
   - **Host**: `db` (nome do serviço MySQL)
   - **Nome do Banco**: `samlavet`
   - **Usuário MySQL**: `samlavet_user`
   - **Senha MySQL**: A senha que configurou em `DB_PASS`
   - **Usuário Admin**: `admin`
   - **Senha Admin**: Sua senha segura

3. Clique em **Instalar**

### 7️⃣ Pós-Instalação (IMPORTANTE - Segurança)

1. Delete o arquivo `install.php` do repositório ou renomeie para `install.php.bak`
2. Faça commit e push: 
   ```bash
   git rm install.php
   git commit -m "Remove install.php after deployment"
   git push
   ```
3. O Coolify vai fazer redeploy automaticamente

---

## 🔗 Acessar Painel Admin

**URL**: `https://seu-dominio.com/admin/`

Use as credenciais que definiu durante a instalação.

---

## 🛠️ Troubleshooting

### Erro de Conexão com MySQL
- Certifique-se que `DB_HOST=db` nas variáveis de ambiente
- Aguarde o MySQL iniciar (pode levar alguns segundos)

### Erro de Permissão em `/uploads`
- Os uploads devem estar com permissão 777
- Isto está configurado automaticamente no Dockerfile

### Database não inicializa
- Verifique se `init-db.sql` está no repositório
- Confirme que o arquivo está no caminho correto

---

## 📦 Local com Docker Compose (Desenvolvimento)

Para testar localmente antes de fazer deploy:

```bash
# Copiar variáveis de ambiente
cp .env.example .env

# Editar .env com suas credenciais
nano .env

# Iniciar containers
docker-compose up -d

# Acessar
http://localhost
http://localhost:8080  # PHPMyAdmin
```

---

## 🚀 Deploy Automático do Coolify

A cada commit no repositório GitHub:
1. Coolify detecta a mudança
2. Faz novo build da imagem Docker
3. Redeploy automático

Se quiser desabilitar, configure em **Coolify Settings** → **Webhooks**.

---

## 📝 Resumo da Arquitetura

```
┌─────────────────────────────────────────────┐
│         Coolify Container                   │
├─────────────────────────────────────────────┤
│                                             │
│  ┌─────────────────┐   ┌──────────────────┐ │
│  │   Web Service   │   │ MySQL Container  │ │
│  │  (PHP + Apache) │   │  (MySQL 8.0)     │ │
│  │                 │   │                  │ │
│  │ - Builds do     │   │ - Inicializa com │ │
│  │   Dockerfile    │   │   init-db.sql    │ │
│  │ - Port 80       │   │ - Porta 3306     │ │
│  │ - /uploads      │   │ - mysql-data vol │ │
│  └─────────────────┘   └──────────────────┘ │
│                                             │
│  ┌──────────────────────────────────────┐  │
│  │  PHPMyAdmin (Optional)               │  │
│  │  - Gerenciar BD via Web              │  │
│  └──────────────────────────────────────┘  │
│                                             │
└─────────────────────────────────────────────┘
```

---

## 🔐 Checklist de Segurança

- [ ] Alterar senhas padrão do MySQL
- [ ] Alterar senha do admin
- [ ] Deletar `install.php` após instalação
- [ ] Desabilitar PHPMyAdmin em produção (comentar no docker-compose.coolify.yml)
- [ ] HTTPS habilitado no Coolify
- [ ] Backups automáticos configurados

---

**Sucesso com o deploy! 🎉**
