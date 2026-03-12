# Site Veterinário - Dra. Samla Cristie

Sistema completo de gerenciamento de conteúdo (CMS) para clínica veterinária com PHP e MySQL.

## 🚀 Deploy no Coolify

### Passo a Passo

1. **No Coolify, crie um novo Service:**
   - Clique em "+ Add" → "Service"
   - Escolha "Docker Compose"

2. **Cole o conteúdo do arquivo `docker-compose.coolify.yml`:**
   - Abra o arquivo `docker-compose.coolify.yml` no repositório
   - Copie TODO o conteúdo
   - Cole no campo "Docker Compose file" do Coolify

3. **Configure as variáveis de ambiente no Coolify:**
   ```
   DB_NAME=samlavet
   DB_USER=samlavet_user
   DB_PASS=SuaSenhaSegura123!
   MYSQL_ROOT_PASSWORD=SenhaRootSegura123!
   ```
   **IMPORTANTE:** Altere as senhas acima para valores seguros!

4. **Salve e faça Deploy:**
   - Clique em "Save"
   - Aguarde o deploy completar (pode levar alguns minutos na primeira vez)

5. **Acesse o instalador:**
   - Após o deploy, acesse: `https://seu-dominio.com/install.php`
   - Preencha o formulário com:
     - **Host**: `db`
     - **Nome do Banco**: `samlavet`
     - **Usuário MySQL**: `samlavet_user`
     - **Senha MySQL**: a senha que você configurou em `DB_PASS`
     - **Usuário Admin**: `admin`
     - **Senha Admin**: uma senha segura de sua escolha

6. **Acesse o painel administrativo:**
   - URL: `https://seu-dominio.com/admin/`
   - Use as credenciais de admin que você definiu

7. **IMPORTANTE - Segurança:**
   - Delete ou renomeie o arquivo `install.php` após a instalação

### 🐳 Deploy Local com Docker

```bash
# Clone o repositório
git clone https://github.com/andregabrielnc/samycastro.git
cd samycastro

# Copie o arquivo de exemplo
cp .env.example .env

# Edite o arquivo .env com suas credenciais
nano .env

# Inicie os containers
docker-compose up -d

# Acesse no navegador
# Site: http://localhost
# Admin: http://localhost/admin/
# phpMyAdmin: http://localhost:8080
```

## 📦 Estrutura do Projeto

```
samycastro/
├── admin/              # Painel administrativo
├── uploads/            # Arquivos enviados (imagens)
├── config.php          # Configuração do banco de dados
├── install.php         # Instalador do sistema
├── index.php           # Página inicial
├── Dockerfile          # Configuração do container PHP
├── docker-compose.yml  # Orquestração dos containers
└── init-db.sql         # Script de inicialização do banco
```

## 🔧 Serviços Docker

- **web**: Apache + PHP 8.2 (porta 80)
- **db**: MySQL 8.0 (porta 3306)
- **phpmyadmin**: Interface web para MySQL (porta 8080)

## 🔒 Segurança

**IMPORTANTE:** Após a instalação:
1. Delete ou renomeie o arquivo `install.php`
2. Altere as senhas padrão
3. Configure backups regulares do banco de dados
4. Use HTTPS em produção

## 📝 Funcionalidades

- ✅ Gerenciamento de serviços veterinários
- ✅ Blog com artigos
- ✅ Equipe e depoimentos
- ✅ FAQ personalizado
- ✅ Clientes/Parceiros
- ✅ Configurações do site
- ✅ Upload de imagens
- ✅ Painel administrativo completo

## 🛠️ Tecnologias

- PHP 8.2
- MySQL 8.0
- Apache 2.4
- Docker & Docker Compose
- Bootstrap 5
- Font Awesome

## 📞 Suporte

Para dúvidas ou problemas, entre em contato através do GitHub Issues.

## 📄 Licença

Projeto desenvolvido para uso da Dra. Samla Cristie - CRMV-GO 14064-VP
