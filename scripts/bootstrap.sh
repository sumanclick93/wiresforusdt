#!/usr/bin/env bash
set -euo pipefail

if ! command -v docker >/dev/null 2>&1; then
  echo "Docker is not installed or not in PATH. Install Docker Desktop and retry."
  exit 1
fi

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"

echo "Scaffolding Laravel into $ROOT_DIR/laravel using Composer container..."
docker run --rm -u "$(id -u):$(id -g)" -v "$ROOT_DIR/laravel":/app -w /app composer:2 create-project laravel/laravel .

echo "Building and starting containers..."
docker compose up -d --build

echo "Done. Visit http://localhost:8080"
