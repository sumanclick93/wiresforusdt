# Laravel Docker Setup

This workspace contains a Docker setup to run a Laravel application.

Quick steps:

1. Scaffold Laravel into `./laravel` using the Composer image (internet required):

```bash
docker run --rm -u $(id -u):$(id -g) -v "$PWD/laravel":/app -w /app composer:2 create-project laravel/laravel .
```

2. Build and start containers:

```bash
docker compose up -d --build
```

3. Visit http://localhost:8080

Notes:
- Database: MySQL at `localhost:3306` with user `laravel` / `secret`.
- If Docker Desktop is not installed, install it from https://www.docker.com/get-started
