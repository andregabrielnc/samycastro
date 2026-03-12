#!/bin/bash
# Script para buildar e publicar a imagem Docker
# Execute este script localmente onde o Docker está instalado

echo "🐳 Building Docker image..."
docker build -t andregabrielnc/samycastro:latest .

echo "📦 Pushing to Docker Hub..."
docker push andregabrielnc/samycastro:latest

echo "✅ Done! Image published to andregabrielnc/samycastro:latest"
echo ""
echo "To use in Coolify, update docker-compose.coolify.yml with:"
echo "  image: andregabrielnc/samycastro:latest"
