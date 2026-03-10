# SamyCastro Deployment Helper Script for Windows
# Usage: .\deploy.ps1 [command]

param(
    [Parameter(Position=0)]
    [ValidateSet("dev", "build", "stop", "clean", "help", "")]
    [string]$Command = ""
)

# Colors for output
function Write-Header {
    param([string]$Text)
    Write-Host "`n═══════════════════════════════════════════════════════" -ForegroundColor Cyan
    Write-Host $Text -ForegroundColor Cyan
    Write-Host "═══════════════════════════════════════════════════════`n" -ForegroundColor Cyan
}

function Write-Success {
    param([string]$Text)
    Write-Host "✓ $Text" -ForegroundColor Green
}

function Write-Error-Custom {
    param([string]$Text)
    Write-Host "✗ $Text" -ForegroundColor Red
}

function Write-Warning-Custom {
    param([string]$Text)
    Write-Host "! $Text" -ForegroundColor Yellow
}

# Check if Docker is installed
function Check-Docker {
    Write-Header "Verificando Docker..."
    
    $docker = Get-Command docker -ErrorAction SilentlyContinue
    if ($null -eq $docker) {
        Write-Error-Custom "Docker não está instalado."
        Write-Host "Instale em: https://docs.docker.com/install/" -ForegroundColor Yellow
        exit 1
    }
    
    $version = docker --version
    Write-Success "Docker encontrado: $version"
}

# Check if .env exists
function Check-Env {
    Write-Header "Verificando configuração de ambiente..."
    
    if ((Test-Path ".env") -eq $false) {
        Write-Warning-Custom ".env não encontrado. Copiando de .env.example..."
        
        if ((Test-Path ".env.example") -eq $true) {
            Copy-Item ".env.example" ".env"
            Write-Warning-Custom "Edite .env com suas configurações antes de continuar!"
            Start-Process notepad .env
            exit 0
        } else {
            Write-Error-Custom "Arquivo .env.example não encontrado!"
            exit 1
        }
    }
    
    Write-Success ".env configurado"
}

# Development setup
function Dev-Setup {
    Write-Header "Iniciando ambiente de desenvolvimento..."
    Check-Env
    
    Write-Warning-Custom "Iniciando containers..."
    docker-compose -f docker-compose.dev.yml up -d
    
    Write-Host ""
    Write-Success "Ambiente de desenvolvimento iniciado!"
    Write-Host ""
    Write-Host "Acesse:" -ForegroundColor Cyan
    Write-Host "  • Aplicação: http://localhost" -ForegroundColor White
    Write-Host "  • PHPMyAdmin: http://localhost:8080" -ForegroundColor White
    Write-Host ""
    Write-Host "Para parar os containers:" -ForegroundColor Cyan
    Write-Host "  docker-compose -f docker-compose.dev.yml down" -ForegroundColor White
    Write-Host ""
}

# Build for production
function Build-Production {
    Write-Header "Construindo imagem para produção..."
    Check-Env
    
    docker build -t samycastro:latest .
    
    Write-Success "Imagem construída: samycastro:latest"
    Write-Host ""
    Write-Host "Próximos passos:" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "1. Publique a imagem no Docker Hub:" -ForegroundColor White
    Write-Host "   docker login" -ForegroundColor Gray
    Write-Host "   docker tag samycastro:latest seu-usuario/samycastro:latest" -ForegroundColor Gray
    Write-Host "   docker push seu-usuario/samycastro:latest" -ForegroundColor Gray
    Write-Host ""
    Write-Host "2. Ou use localmente com docker-compose.coolify.yml" -ForegroundColor White
    Write-Host ""
}

# Stop containers
function Stop-Containers {
    Write-Header "Parando containers..."
    
    docker-compose down 2>$null
    docker-compose -f docker-compose.dev.yml down 2>$null
    
    Write-Success "Containers parados"
}

# Clean up
function Cleanup-Docker {
    Write-Header "Limpando sistema Docker..."
    
    docker system prune -f | Out-Null
    
    Write-Success "Limpeza concluída"
}

# Show usage
function Show-Usage {
    Write-Host "🐳 SamyCastro Docker Helper for Windows`n" -ForegroundColor Cyan
    Write-Host "Uso: .\deploy.ps1 [comando]`n" -ForegroundColor Yellow
    
    Write-Host "Comandos:" -ForegroundColor Cyan
    Write-Host "  dev              Inicia ambiente de desenvolvimento com Docker Compose" -ForegroundColor White
    Write-Host "  build            Constrói imagem Docker para produção" -ForegroundColor White
    Write-Host "  stop             Para todos os containers" -ForegroundColor White
    Write-Host "  clean            Remove containers e imagens não usadas" -ForegroundColor White
    Write-Host "  help             Mostra esta mensagem" -ForegroundColor White
    Write-Host ""
    
    Write-Host "Exemplos:" -ForegroundColor Cyan
    Write-Host "  .\deploy.ps1 dev" -ForegroundColor Gray
    Write-Host "  .\deploy.ps1 build" -ForegroundColor Gray
    Write-Host ""
}

# Main
switch ($Command.ToLower()) {
    "dev" {
        Check-Docker
        Dev-Setup
    }
    "build" {
        Check-Docker
        Build-Production
    }
    "stop" {
        Stop-Containers
    }
    "clean" {
        Cleanup-Docker
    }
    "help" {
        Show-Usage
    }
    "" {
        Show-Usage
    }
    default {
        Write-Error-Custom "Comando desconhecido: $Command"
        Write-Host ""
        Show-Usage
        exit 1
    }
}
