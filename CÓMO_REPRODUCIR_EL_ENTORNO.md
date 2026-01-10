# Cómo reproducir el entorno

Este apartado explica cómo levantar el entorno de desarrollo/QA usando Docker Compose y también una alternativa para levantamiento local manual.

1) Usando Docker Compose (rápido y replicable)

Requisitos: Docker y Docker Compose instalados.

```bash
# Desde la raíz del proyecto
docker compose up --build -d

# Comprobar que los contenedores estén en marcha
docker compose ps

# Ver logs del contenedor principal (app)
docker compose logs -f app
```

Servicios incluidos (según `docker-compose.yml`):
- `app` : contenedor de la aplicación (PHP + Apache). Mapea puerto `8000:80`.
- `db` : MySQL.
- `prometheus`, `grafana`, `node_exporter` : opcionales para monitoreo (ver sección de monitoreo).

Variables importantes en `.env` (si usas Docker, asegúrate de preparar `.env` antes de levantar):
- `APP_KEY` : generada con `php artisan key:generate` o exportada antes.
- `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` : deben coincidir con la configuración de `docker-compose.yml` o el host que uses.

2) Levantamiento local (sin Docker)

Instala dependencias locales y la base de datos MySQL.

```bash
composer install
npm install
npm run dev # o build

# Configura .env con credenciales de tu MySQL local
php artisan key:generate
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000
```

3) Preparar la base de datos

El proyecto contiene migraciones en `database/migrations`. Para preparar la BD ejecuta:

```bash
php artisan migrate
# si quieres poblar datos de ejemplo
php artisan db:seed
```

4) Recomendaciones

- Asigna permisos a `storage` y `bootstrap/cache` si trabajas en Linux:
```bash
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
```
- Usa `docker compose exec app bash` para acceder al contenedor y ejecutar comandos artisan/composer desde dentro.
