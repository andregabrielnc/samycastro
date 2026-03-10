#!/bin/bash
# SamyCastro Deployment Helper Script

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_header() {
    echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}! $1${NC}"
}

# Check if Docker is installed
check_docker() {
    print_header "Verificando Docker..."
    if ! command -v docker &> /dev/null; then
        print_error "Docker não está instalado. Instale em https://docs.docker.com/install/"
        exit 1
    fi
    print_success "Docker encontrado: $(docker --version)"
}

# Check if .env exists
check_env() {
    print_header "Verificando configuração de ambiente..."
    if [ ! -f .env ]; then
        print_warning ".env não encontrado. Copiando de .env.example..."
        cp .env.example .env
        print_warning "Edite .env com suas configurações antes de continuar!"
        exit 1
    fi
    print_success ".env configurado"
}

# Build and run development environment
dev_setup() {
    print_header "Iniciando ambiente de desenvolvimento..."
    check_env
    
    print_warning "Iniciando containers..."
    docker-compose -f docker-compose.dev.yml up -d
    
    print_success "Ambiente de desenvolvimento iniciado!"
    print_success "Acesse: http://localhost"
    print_success "PHPMyAdmin: http://localhost:8080"
    print_success "Para parar: docker-compose -f docker-compose.dev.yml down"
}

# Build for production
build_production() {
    print_header "Construindo imagem para produção..."
    check_env
    
    docker build -t samycastro:latest .
    print_success "Imagem construída: samycastro:latest"
    
    echo -e "${YELLOW}Próximos passos:${NC}"
    echo "1. Publique a imagem no Docker Hub:"
    echo "   docker login"
    echo "   docker tag samycastro:latest seu-usuario/samycastro:latest"
    echo "   docker push seu-usuario/samycastro:latest"
    echo ""
    echo "2. Ou use localmente com docker-compose.coolify.yml"
}

# Stop all containers
stop_containers() {
    print_header "Parando containers..."
    docker-compose down
    docker-compose -f docker-compose.dev.yml down 2>/dev/null || true
    print_success "Containers parados"
}

# Clean up
cleanup() {
    print_header "Limpando..."
    docker system prune -f
    print_success "Limpeza concluída"
}

# Default: show usage
usage() {
    echo "🐳 SamyCastro Docker Helper"
    echo ""
    echo "Uso: ./deploy.sh [comando]"
    echo ""
    echo "Comandos:"
    echo "  dev              Inicia ambiente de desenvolvimento com Docker Compose"
    echo "  build            Constrói imagem Docker para produção"
    echo "  stop             Para todos os containers"
    echo "  clean            Remove containers e imagens não usadas"
    echo "  help             Mostra esta mensagem"
    echo ""
    echo "Exemplos:"
    echo "  ./deploy.sh dev"
    echo "  ./deploy.sh build"
}

# Main
if [ -z "$1" ]; then
    usage
    exit 0
fi

case "$1" in
    dev)
        check_docker
        dev_setup
        ;;
    build)
        check_docker
        build_production
        ;;
    stop)
        stop_containers
        ;;
    clean)
        cleanup
        ;;
    help)
        usage
        ;;
    *)
        print_error "Comando desconhecido: $1"
        echo ""
        usage
        exit 1
        ;;
esac
