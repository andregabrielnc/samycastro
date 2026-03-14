# Instalacao Completa - Servidor SamyCastro

## Contexto

Este servidor Ubuntu (recem-instalado) precisa ser configurado do zero para hospedar
o site **SamyCastro** (samycastro.vet) - site veterinario da Dra. Samla Cristie.

O repositorio do projeto esta em: `https://github.com/andregabrielnc/samycastro.git`

A stack e: **PHP 8.2 + Apache + MySQL 8.0 + Traefik (reverse proxy + SSL)**

Nao usar Coolify, Portainer ou qualquer painel. Tudo via Docker Compose + configs manuais.

---

## FASE 1 - SEGURANCA DO SERVIDOR (fazer ANTES de qualquer app)

### 1.1 Atualizar sistema

```bash
apt update && apt upgrade -y
apt install -y curl wget git unzip ufw fail2ban rsync
```

### 1.2 Criar usuario deploy (nao usar root para tudo)

```bash
adduser deploy
usermod -aG sudo deploy
usermod -aG docker deploy  # executar DEPOIS de instalar Docker
```

### 1.3 Configurar SSH seguro

Editar `/etc/ssh/sshd_config`:

```
PermitRootLogin prohibit-password
PasswordAuthentication no
PubkeyAuthentication yes
MaxAuthTries 3
ClientAliveInterval 300
ClientAliveCountMax 2
AllowUsers deploy root
```

Reiniciar: `systemctl restart sshd`

**IMPORTANTE**: garantir que a chave SSH do usuario esteja em `~/.ssh/authorized_keys` ANTES de desabilitar senha.

### 1.4 Firewall (UFW)

```bash
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp    # SSH
ufw allow 80/tcp    # HTTP (redirect para HTTPS)
ufw allow 443/tcp   # HTTPS
ufw allow 443/udp   # HTTP/3 QUIC (opcional)
ufw enable
```

**NAO abrir** portas de banco (3306), Redis, phpMyAdmin etc. Tudo fica interno no Docker.

### 1.5 Fail2Ban (protecao contra brute force SSH)

Criar `/etc/fail2ban/jail.local`:

```ini
[sshd]
enabled = true
port = 22
filter = sshd
logpath = /var/log/auth.log
maxretry = 3
bantime = 3600
findtime = 600
```

```bash
systemctl enable fail2ban
systemctl start fail2ban
```

### 1.6 Configuracoes de kernel (sysctl)

Criar `/etc/sysctl.d/99-security.conf`:

```
net.ipv4.tcp_syncookies = 1
net.ipv4.conf.all.rp_filter = 1
net.ipv4.conf.default.rp_filter = 1
net.ipv4.conf.all.accept_redirects = 0
net.ipv4.conf.default.accept_redirects = 0
net.ipv4.conf.all.send_redirects = 0
net.ipv4.conf.default.send_redirects = 0
net.ipv4.icmp_echo_ignore_broadcasts = 1
net.ipv4.conf.all.log_martians = 1
```

```bash
sysctl --system
```

### 1.7 Atualizacoes automaticas de seguranca

```bash
apt install -y unattended-upgrades
dpkg-reconfigure -plow unattended-upgrades
```

---

## FASE 2 - INSTALAR DOCKER

```bash
# Remover versoes antigas
apt remove -y docker docker-engine docker.io containerd runc 2>/dev/null

# Instalar Docker oficial
curl -fsSL https://get.docker.com | sh

# Habilitar e iniciar
systemctl enable docker
systemctl start docker

# Adicionar usuario deploy ao grupo docker
usermod -aG docker deploy
```

Verificar: `docker --version && docker compose version`

---

## FASE 3 - ESTRUTURA DE DIRETORIOS

```bash
mkdir -p /opt/infra          # Traefik + infra
mkdir -p /opt/apps/samycastro # App SamyCastro
mkdir -p /opt/webhook         # Webhook para deploy automatico
mkdir -p /opt/backups         # Backups automaticos
```

---

## FASE 4 - TRAEFIK (Reverse Proxy + SSL Automatico)

### 4.1 Criar rede Docker compartilhada

```bash
docker network create traefik-public
```

### 4.2 Arquivo `/opt/infra/docker-compose.yml`

```yaml
services:
  traefik:
    image: traefik:v3.6
    container_name: traefik
    restart: unless-stopped
    security_opt:
      - no-new-privileges:true
    ports:
      - "80:80"
      - "443:443"
      - "443:443/udp"
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock:ro
      - ./traefik.yml:/etc/traefik/traefik.yml:ro
      - ./acme.json:/acme.json
    networks:
      - traefik-public
    labels:
      - "traefik.enable=true"

networks:
  traefik-public:
    external: true
```

### 4.3 Arquivo `/opt/infra/traefik.yml`

```yaml
api:
  dashboard: false

entryPoints:
  http:
    address: ":80"
    http:
      redirections:
        entryPoint:
          to: https
          scheme: https
  https:
    address: ":443"
    http:
      tls:
        certResolver: letsencrypt
    http3: {}

providers:
  docker:
    endpoint: "unix:///var/run/docker.sock"
    exposedByDefault: false
    network: traefik-public

certificatesResolvers:
  letsencrypt:
    acme:
      email: contato@samycastro.vet
      storage: /acme.json
      httpChallenge:
        entryPoint: http

log:
  level: WARN
```

### 4.4 Criar arquivo de certificados e iniciar

```bash
touch /opt/infra/acme.json
chmod 600 /opt/infra/acme.json
cd /opt/infra && docker compose up -d
```

---

## FASE 5 - SAMYCASTRO (App Principal)

### 5.1 Clonar repositorio

```bash
cd /opt/apps/samycastro
git clone https://github.com/andregabrielnc/samycastro.git .
```

### 5.2 Criar `/opt/apps/samycastro/.env`

```env
DB_HOST=samycastro-db
DB_NAME=samlavet
DB_USER=samlavet_user
DB_PASS=GERAR_SENHA_FORTE_AQUI
MYSQL_ROOT_PASSWORD=GERAR_OUTRA_SENHA_FORTE_AQUI
DOMAIN=samycastro.vet
```

**IMPORTANTE**: gerar senhas fortes com `openssl rand -base64 24` para DB_PASS e MYSQL_ROOT_PASSWORD.

### 5.3 Criar `/opt/apps/samycastro/docker-compose.prod.yml`

```yaml
services:
  web:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: samycastro-web
    restart: unless-stopped
    expose:
      - "80"
    environment:
      - DB_HOST=${DB_HOST}
      - DB_NAME=${DB_NAME}
      - DB_USER=${DB_USER}
      - DB_PASS=${DB_PASS}
    volumes:
      - app-data:/var/www/html
      - uploads-data:/var/www/html/uploads
    depends_on:
      db:
        condition: service_healthy
    networks:
      - samycastro-internal
      - traefik-public
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.samycastro.rule=Host(`${DOMAIN}`)"
      - "traefik.http.routers.samycastro.entryPoints=https"
      - "traefik.http.routers.samycastro.tls=true"
      - "traefik.http.routers.samycastro.tls.certresolver=letsencrypt"
      - "traefik.http.routers.samycastro.middlewares=samycastro-headers,samycastro-gzip"
      - "traefik.http.middlewares.samycastro-gzip.compress=true"
      - "traefik.http.middlewares.samycastro-headers.headers.stsSeconds=31536000"
      - "traefik.http.middlewares.samycastro-headers.headers.stsIncludeSubdomains=true"
      - "traefik.http.middlewares.samycastro-headers.headers.forceSTSHeader=true"
      - "traefik.http.middlewares.samycastro-headers.headers.frameDeny=true"
      - "traefik.http.middlewares.samycastro-headers.headers.contentTypeNosniff=true"
      - "traefik.http.middlewares.samycastro-headers.headers.browserXssFilter=true"
      - "traefik.http.middlewares.samycastro-headers.headers.referrerPolicy=strict-origin-when-cross-origin"
      - "traefik.http.services.samycastro.loadbalancer.server.port=80"

  db:
    image: mysql:8.0
    container_name: samycastro-db
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
      MYSQL_DATABASE: ${DB_NAME}
      MYSQL_USER: ${DB_USER}
      MYSQL_PASSWORD: ${DB_PASS}
    volumes:
      - mysql-data:/var/lib/mysql
      - ./init-db.sql:/docker-entrypoint-initdb.d/init-db.sql:ro
    expose:
      - "3306"
    networks:
      - samycastro-internal
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost", "-u", "root", "-p${MYSQL_ROOT_PASSWORD}"]
      interval: 10s
      timeout: 5s
      retries: 5

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: samycastro-phpmyadmin
    restart: unless-stopped
    environment:
      PMA_HOST: samycastro-db
      PMA_PORT: 3306
      PMA_USER: ${DB_USER}
      PMA_PASSWORD: ${DB_PASS}
    expose:
      - "80"
    depends_on:
      - db
    networks:
      - samycastro-internal
      - traefik-public
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.phpmyadmin.rule=Host(`pma.${DOMAIN}`)"
      - "traefik.http.routers.phpmyadmin.entryPoints=https"
      - "traefik.http.routers.phpmyadmin.tls=true"
      - "traefik.http.routers.phpmyadmin.tls.certresolver=letsencrypt"
      - "traefik.http.routers.phpmyadmin.middlewares=phpmyadmin-auth"
      - "traefik.http.middlewares.phpmyadmin-auth.basicauth.users=admin:$$apr1$$GERAR_HASH"
      - "traefik.http.services.phpmyadmin.loadbalancer.server.port=80"

networks:
  samycastro-internal:
    driver: bridge
  traefik-public:
    external: true

volumes:
  mysql-data:
  uploads-data:
  app-data:
```

**NOTA sobre phpMyAdmin**: gerar hash de senha com:
```bash
htpasswd -nb admin SENHA_AQUI
```
E substituir `admin:$$apr1$$GERAR_HASH` pelo resultado (dobrando os `$` para `$$`).

Se preferir NAO expor o phpMyAdmin publicamente (mais seguro), remover os labels traefik
e as redes traefik-public do servico phpmyadmin. Acessar apenas via SSH tunnel:
```bash
ssh -L 8080:localhost:80 deploy@IP_SERVIDOR
# depois: docker exec para acessar ou expor porta local temporariamente
```

### 5.4 Iniciar a app

```bash
cd /opt/apps/samycastro
docker compose -f docker-compose.prod.yml up -d --build
```

### 5.5 Popular banco de dados (apenas na primeira vez)

O `init-db.sql` sera executado automaticamente pelo MySQL na primeira inicializacao.
Se precisar de dados iniciais (seed), executar:

```bash
docker exec samycastro-web php /var/www/html/seed-data.php
```

### 5.6 Corrigir permissoes do uploads

```bash
docker exec samycastro-web chown -R www-data:www-data /var/www/html/uploads
docker exec samycastro-web chmod 775 /var/www/html/uploads
```

---

## FASE 6 - DEPLOY AUTOMATICO VIA GITHUB WEBHOOK

### 6.1 Gerar secret para o webhook

```bash
WEBHOOK_SECRET=$(openssl rand -hex 20)
echo "Guardar este secret: $WEBHOOK_SECRET"
echo "$WEBHOOK_SECRET" > /opt/webhook/.secret
chmod 600 /opt/webhook/.secret
```

### 6.2 Criar `/opt/webhook/deploy.sh`

```bash
#!/bin/bash
REPO_DIR="/opt/apps/samycastro"

echo "[$(date)] Deploy iniciado..."

cd "$REPO_DIR" || exit 1
git pull origin main 2>&1

if [ $? -ne 0 ]; then
    echo "[$(date)] ERRO: git pull falhou"
    exit 1
fi

# Rebuild e restart do container web (sem downtime no DB)
docker compose -f docker-compose.prod.yml build web
docker compose -f docker-compose.prod.yml up -d web

# Garantir permissoes do uploads
docker exec samycastro-web chown -R www-data:www-data /var/www/html/uploads
docker exec samycastro-web chmod 775 /var/www/html/uploads

echo "[$(date)] Deploy concluido!"
```

```bash
chmod +x /opt/webhook/deploy.sh
```

### 6.3 Criar `/opt/webhook/hooks.json`

```json
[
  {
    "id": "deploy-samycastro",
    "execute-command": "/etc/webhook/deploy.sh",
    "command-working-directory": "/opt/apps/samycastro",
    "response-message": "Deploy iniciado!",
    "trigger-rule": {
      "and": [
        {
          "match": {
            "type": "payload-hmac-sha256",
            "secret": "COLOCAR_WEBHOOK_SECRET_AQUI",
            "parameter": {
              "source": "header",
              "name": "X-Hub-Signature-256"
            }
          }
        },
        {
          "match": {
            "type": "value",
            "value": "refs/heads/main",
            "parameter": {
              "source": "payload",
              "name": "ref"
            }
          }
        }
      ]
    }
  }
]
```

Substituir `COLOCAR_WEBHOOK_SECRET_AQUI` pelo secret gerado em 6.1.

### 6.4 Criar `/opt/webhook/Dockerfile`

```dockerfile
FROM almir/webhook:latest
COPY hooks.json /etc/webhook/hooks.json
COPY deploy.sh /etc/webhook/deploy.sh
RUN chmod +x /etc/webhook/deploy.sh
```

### 6.5 Criar `/opt/webhook/docker-compose.yml`

```yaml
services:
  webhook:
    build: .
    container_name: samycastro-webhook
    restart: unless-stopped
    volumes:
      - /opt/apps/samycastro:/opt/apps/samycastro
      - /var/run/docker.sock:/var/run/docker.sock
    expose:
      - "9000"
    networks:
      - traefik-public
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.webhook.rule=Host(`samycastro.vet`) && PathPrefix(`/hooks/`)"
      - "traefik.http.routers.webhook.entryPoints=https"
      - "traefik.http.routers.webhook.tls=true"
      - "traefik.http.routers.webhook.tls.certresolver=letsencrypt"
      - "traefik.http.services.webhook.loadbalancer.server.port=9000"

networks:
  traefik-public:
    external: true
```

```bash
cd /opt/webhook && docker compose up -d --build
```

### 6.6 Configurar webhook no GitHub

1. Ir em `https://github.com/andregabrielnc/samycastro/settings/hooks`
2. Add webhook:
   - Payload URL: `https://samycastro.vet/hooks/deploy-samycastro`
   - Content type: `application/json`
   - Secret: o valor gerado em 6.1
   - Events: `Just the push event`

---

## FASE 7 - BACKUPS AUTOMATICOS

### 7.1 Criar `/opt/backups/backup.sh`

```bash
#!/bin/bash
BACKUP_DIR="/opt/backups"
DATE=$(date +%Y%m%d_%H%M%S)
KEEP_DAYS=7

# Backup MySQL
docker exec samycastro-db mysqldump -u root -p"$(cat /opt/apps/samycastro/.env | grep MYSQL_ROOT_PASSWORD | cut -d= -f2)" samlavet > "$BACKUP_DIR/db_${DATE}.sql"
gzip "$BACKUP_DIR/db_${DATE}.sql"

# Backup uploads
tar -czf "$BACKUP_DIR/uploads_${DATE}.tar.gz" -C /var/lib/docker/volumes/ $(docker volume inspect samycastro_uploads-data --format '{{.Name}}')/_data/ 2>/dev/null

# Limpar backups antigos
find "$BACKUP_DIR" -name "db_*.sql.gz" -mtime +$KEEP_DAYS -delete
find "$BACKUP_DIR" -name "uploads_*.tar.gz" -mtime +$KEEP_DAYS -delete

echo "[$(date)] Backup concluido: db_${DATE}.sql.gz"
```

```bash
chmod +x /opt/backups/backup.sh
```

### 7.2 Agendar via cron (diario as 3h da manha)

```bash
crontab -e
# Adicionar:
0 3 * * * /opt/backups/backup.sh >> /opt/backups/backup.log 2>&1
```

---

## FASE 8 - MONITORAMENTO BASICO

### 8.1 Health check via cron

Criar `/opt/infra/healthcheck.sh`:

```bash
#!/bin/bash
DOMAIN="samycastro.vet"
STATUS=$(curl -s -o /dev/null -w "%{http_code}" "https://${DOMAIN}" --max-time 10)

if [ "$STATUS" != "200" ]; then
    echo "[$(date)] ALERTA: ${DOMAIN} retornou HTTP ${STATUS}" >> /opt/infra/health.log
    # Tentar restart automatico
    cd /opt/apps/samycastro && docker compose -f docker-compose.prod.yml restart web
fi
```

```bash
chmod +x /opt/infra/healthcheck.sh
# Agendar a cada 5 minutos
crontab -e
# Adicionar:
*/5 * * * * /opt/infra/healthcheck.sh
```

---

## FASE 9 - DNS

Configurar no provedor de DNS do dominio `samycastro.vet`:

| Tipo | Nome | Valor |
|------|------|-------|
| A | @ | IP_DO_SERVIDOR |
| A | pma | IP_DO_SERVIDOR |
| A | www | IP_DO_SERVIDOR |

O Traefik cuida do SSL automaticamente via Let's Encrypt.

---

## RESUMO DA ESTRUTURA FINAL

```
/opt/
  infra/
    docker-compose.yml    # Traefik
    traefik.yml           # Config do Traefik
    acme.json             # Certificados SSL
    healthcheck.sh        # Monitoramento
  apps/
    samycastro/
      .env                # Variaveis de ambiente (senhas)
      docker-compose.prod.yml
      Dockerfile
      init-db.sql
      index.php, config.php, etc...
      admin/
      uploads/
  webhook/
    docker-compose.yml
    Dockerfile
    hooks.json
    deploy.sh
    .secret
  backups/
    backup.sh
    backup.log
    db_*.sql.gz
    uploads_*.tar.gz
```

## ORDEM DE EXECUCAO

1. Fase 1 (seguranca) - PRIMEIRO
2. Fase 2 (Docker)
3. Fase 3 (diretorios)
4. Fase 4 (Traefik)
5. Fase 9 (DNS - pode levar tempo para propagar)
6. Fase 5 (SamyCastro app)
7. Fase 6 (webhook deploy)
8. Fase 7 (backups)
9. Fase 8 (monitoramento)

## CHECKLIST POS-INSTALACAO

- [ ] `ufw status` mostra apenas portas 22, 80, 443
- [ ] `fail2ban-client status sshd` mostra jail ativo
- [ ] `curl -I https://samycastro.vet` retorna 200 com headers de seguranca (HSTS, X-Frame-Options)
- [ ] SSL nota A+ no https://www.ssllabs.com/ssltest/
- [ ] Upload de imagem via admin funciona
- [ ] Push no GitHub dispara deploy automatico
- [ ] Backup diario rodando (`ls /opt/backups/`)
- [ ] phpMyAdmin acessivel apenas via pma.samycastro.vet com basic auth (ou via SSH tunnel)
- [ ] Portas 3306, 6379, 9000 NAO acessiveis externamente
