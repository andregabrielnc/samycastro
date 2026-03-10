# 🚀 Guia de Setup Automático - SamyCastro

## ✅ O Que Mudou

Agora o setup é **automático e sem necessidade de instalar nada na primeira vez!**

- Admin será criado automaticamente com:
  - **Usuário**: `admin`
  - **Senha**: `Ag570411@2026`

---

## 📋 Passo a Passo no Coolify

### 1️⃣ Deploy (igual antes)
- Crie um novo Service Docker Compose no Coolify
- Use `docker-compose.coolify.yml`
- Configure as variáveis de ambiente (mesmas de antes)

### 2️⃣ Após Deploy (NOVO!)
Quando o deploy estiver completo:

1. **Acesse a aplicação**: `https://seu-dominio.com/`
2. **Acesse auto-setup**: `https://seu-dominio.com/auto-setup.php`
3. O script vai:
   - Criar as tabelas no MySQL
   - Criar usuário admin com a senha pré-configurada
   - Criar configurações padrão do site

### 3️⃣ Fazer Login
1. Vá para `https://seu-dominio.com/admin/`
2. Login com:
   - Usuário: `admin`
   - Senha: `Ag570411@2026`

### 4️⃣ Limpeza (IMPORTANTE!)
**Depois de entrar no admin pela primeira vez:**

1. Delete ou renomeie os arquivos de setup:
   - `auto-setup.php` → delete
   - `setup.php` → delete (se ainda existir)
   - `install.php` → delete (se ainda existir)

Via terminal:
```bash
git rm auto-setup.php setup.php install.php
git commit -m "Remove setup files after installation"
git push

# Coolify vai fazer redeploy automaticamente
```

---

## 🔧 Arquivo `auto-setup.php`

Este arquivo:
- ✅ É executado **uma única vez** (depois cria um `.auto-setup-done` lock)
- ✅ Cria admin user automaticamente
- ✅ Cria configurações padrão do site
- ✅ É seguro deixar no repositório na primeira vez

**MAS**, após o setup, **DEVE SER DELETADO** por segurança!

---

## 🛡️ Segurança

| Arquivo | Antes | Depois |
|---------|-------|--------|
| `auto-setup.php` | ✅ Necessário | ❌ Delete |
| `setup.php` | ℹ️ Alternativo | ❌ Delete |
| `install.php` | ❌ Nunca use | ❌ Delete |

---

## 💾 Se Algo Deu Errado

### Senha não funciona?
```sql
-- Execute no PHPMyAdmin:
DELETE FROM admin_users WHERE username = 'admin';
-- Depois acesse /auto-setup.php novamente
```

### Não consegue acessar /auto-setup.php?
```sql
-- No PHPMyAdmin, delete o lock file no banco:
-- (não existe no banco, é um arquivo .auto-setup-done)
-- Simplesmente delete e recrie o arquivo auto-setup.php
```

### Redeploy do Coolify não funcionou?
1. Vá em **Settings** → **Webhooks**
2. Dispare webhook manualmente
3. Aguarde o build

---

## 📝 Resumo da Primeira Vez

```
1. Deploy Coolify concluído
        ↓
2. Acessa /auto-setup.php
        ↓
3. Script cria admin automaticamente
        ↓
4. Faz login em /admin/
        ↓
5. Delete auto-setup.php e setup.php
        ↓
6. Git push → Coolify redeploy
        ↓
7. ✅ Pronto para usar!
```

---

## ❓ FAQ

**P: Posso mudar a senha padrão?**
R: Sim, após o login, vá em **Admin** → **Configurações** → altere sua senha.

**P: Posso deixar auto-setup.php no repositório?**
R: Não! Depois do setup, delete. Ele fica inacessível após executado (lock), mas é risco de segurança.

**P: Como eu sei que o setup foi executado?**
R: Você será redirecionado para `/admin/` e verá a mensagem de sucesso.

**P: E se eu não conseguir acessar /admin/?
R: Verifique se está logado. Se não, acesse novamente `/auto-setup.php`.

---

## 🎉 Pronto!

Agora seu SamyCastro está pronto para usar no Coolify sem necessidade de instalação manual!

**Próximos passos:**
- Configure settings do site em `/admin/`
- Adicione serviços, artigos, equipe
- Configure WhatsApp e contatos
- Customize a homepage

---

**Dúvidas? Consulte [DOCKER_GUIDE.md](DOCKER_GUIDE.md) ou [DEPLOYMENT_COOLIFY.md](DEPLOYMENT_COOLIFY.md)**
