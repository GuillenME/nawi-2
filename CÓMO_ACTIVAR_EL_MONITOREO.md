# Cómo activar el monitoreo

El proyecto ya incluye componentes y hooks para monitoreo con Prometheus y visualización con Grafana. A continuación se explica cómo activarlos y comprobar que funcionan.

## 1) Qué incluye el repositorio

- `docker-compose.yml` incluye servicios: `prometheus`, `grafana`, `node_exporter` y el servicio `app`.
- `prometheus.yml` con targets por defecto apuntando a `app:80` y `node_exporter:9100`.
- Ruta de métricas en la app: `/metrics` (ver `routes/web.php` que registra `MetricsController::export`).
- Middleware `App\Http\Middleware\PrometheusMetrics` e `App\PrometheusExporter` para exponer contadores.

## 2) Requisitos

- Docker + Docker Compose (recomendado)
- Si no usas Docker, necesitas instalar Prometheus y Grafana y configurar `prometheus.yml` para apuntar a tu app.

## 3) Levantar todo con Docker (rápido)

```bash
# Levanta app + db + prometheus + grafana + node_exporter
docker compose up --build -d

# Accede a Prometheus
# http://localhost:9090
# Accede a Grafana
# http://localhost:3000  (user: admin, password: admin por defecto en docker-compose)
```

## 4) Verificar endpoint de métricas

En el host donde corre la aplicación, pide:

```bash
curl http://localhost:8000/metrics
```

Deberías obtener output en texto con métricas (formato Prometheus). Si está en blanco o da 500, revisa logs del contenedor `app`.

## 5) Configurar almacenamiento de métricas en la app

El exporter interno puede usar almacenamiento en memoria o APCu según la variable `PROMETHEUS_STORAGE`.

- `PROMETHEUS_STORAGE=memory` (por defecto): datos en memoria (no persistente)
- `PROMETHEUS_STORAGE=apc` : requiere extensión `apcu` habilitada en PHP y persistencia a través de APCu.

Para habilitar APCu en Docker, el repo contiene `docker/apcu.ini`. Ajusta la imagen del Dockerfile o el contenedor `app` para cargar esa configuración si deseas usar APCu.

## 6) Prometheus config (si necesitas ajustarla)

`prometheus.yml` contiene:

```yaml
scrape_configs:
  - job_name: 'app'
    metrics_path: '/metrics'
    static_configs:
      - targets: ['app:80']

  - job_name: 'node'
    static_configs:
      - targets: ['node_exporter:9100']
```

Si ejecutas la aplicación fuera de Docker, actualiza `targets` por la IP/puerto accesible desde Prometheus.

## 7) Añadir dashboard en Grafana

- En Grafana (`http://localhost:3000`) crea un Data Source de tipo Prometheus apuntando a `http://prometheus:9090` (si usas Docker) o `http://localhost:9090` si ejecutas localmente.
- Importa o crea dashboards que usen las métricas expuestas (p. ej. `app_http_requests_total` creado por el middleware).

## 8) Comprobación rápida

```bash
# 1) Ver métricas desde container app
docker compose exec app curl -s http://localhost/metrics | head -n 50

# 2) Comprobar que Prometheus está listo
docker compose exec prometheus curl -s http://localhost:9090/-/ready

# 3) Revisar logs
docker compose logs -f prometheus grafana app
```

## 9) Buenas prácticas

- En entornos productivos usa `PROMETHEUS_STORAGE=apc` y habilita APCu o configura un storage remoto (si se requiere).
- Asegura que `/metrics` esté protegido si expone datos sensibles, o limita el acceso a la red interna de monitoreo.

Si quieres, puedo añadir una página de ejemplo de dashboard JSON para importar en Grafana o ajustar `docker-compose.yml` para habilitar APCu automáticamente. ¿Quieres que lo haga?
