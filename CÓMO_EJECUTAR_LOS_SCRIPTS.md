# Cómo ejecutar los scripts

Este documento recoge los comandos y pasos más comunes para trabajar con este proyecto Laravel.

- Requisitos locales mínimos:
  - PHP 8.1+ (8.2 recomendado)
  - Composer
  - Node.js + npm
  - MySQL (si no usas Docker)

1) Preparar el entorno (.env)

```bash
# Copiar el .env de ejemplo
cp .env.example .env

# Generar APP_KEY
php artisan key:generate
```

Si trabajas con Docker Compose (recomendado), el archivo `docker-compose.yml` ya contiene servicios para `app` y `db`.

2) Instalar dependencias

```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
npm install
```

3) Construir assets de frontend

Para desarrollo (watch):
```bash
npm run dev
```

Para producción (build):
```bash
npm run build
```

4) Migraciones / semillas

```bash
php artisan migrate --force
# Si tienes seeders:
php artisan db:seed
```

5) Ejecutar tests

```bash
# Ejecuta todos los tests
./vendor/bin/phpunit
# o usando artisan
php artisan test
```

6) Ejecutar la aplicación

Local (sin Docker):
```bash
php artisan serve --host=127.0.0.1 --port=8000
# Luego abre http://127.0.0.1:8000
```

Con Docker Compose (recomendado):
```bash
docker compose up --build -d
# Ver logs:
docker compose logs -f app
```

7) Tareas comunes

- Crear enlace a storage (si la app necesita): `php artisan storage:link`
- Limpiar cachés: `php artisan config:cache && php artisan route:cache && php artisan view:cache`

Notas:
- Si el CI / workflow ejecuta tests sin base de datos, revisa `.github/workflows/tests.yml` que está preparado para ejecutar `php artisan test` en el runner.
- Ajusta las variables DB en `.env` si trabajas fuera de Docker.
