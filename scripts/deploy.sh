#!/bin/bash

echo "Deploying Laravel App on Codespaces..."

# Stop old containers
docker compose down || true

# Rebuild image (pull if needed)
docker compose build --no-cache

# Start containers
docker compose up -d

echo "Deployment completed."

# Show running containers
docker ps
