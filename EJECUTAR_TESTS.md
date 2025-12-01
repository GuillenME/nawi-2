# Cómo Ejecutar los Tests

## Comandos Rápidos

### Ejecutar todos los tests
```bash
php artisan test
```

### Ejecutar tests específicos
```bash
# Solo tests de Feature
php artisan test --testsuite=Feature

# Solo tests de Unit
php artisan test --testsuite=Unit

# Un archivo específico
php artisan test tests/Feature/AuthTest.php

# Un test específico por nombre
php artisan test --filter test_usuario_puede_registrarse_como_pasajero
```

### Ejecutar con cobertura
```bash
php artisan test --coverage
```

## Tests Disponibles

### Tests de Autenticación API
- `tests/Feature/AuthTest.php` - Tests completos de autenticación vía API
  - Registro de pasajeros
  - Registro de taxistas
  - Login y logout
  - Validaciones

### Tests de Autenticación Web
- `tests/Feature/WebAuthTest.php` - Tests de autenticación web
  - Formularios de login
  - Redirecciones según rol
  - Logout web

### Tests de Modelos
- `tests/Unit/UsuarioModelTest.php` - Tests del modelo Usuario
  - Creación de usuarios
  - Relaciones (rol, pasajero, taxista)
  - Validaciones

### Tests Existentes
- `tests/Feature/ApkDownloadTest.php` - Tests de descarga de APK
- `tests/Feature/ExampleTest.php` - Test básico de ejemplo

## Configuración de Base de Datos

Antes de ejecutar los tests, asegúrate de tener configurada la base de datos de testing.

### Opción 1: SQLite en memoria (Recomendado)
Crea o edita `.env.testing`:
```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### Opción 2: MySQL
Crea una base de datos separada para testing:
```env
APP_ENV=testing
DB_CONNECTION=mysql
DB_DATABASE=nawi_test
DB_USERNAME=root
DB_PASSWORD=
```

## Solución de Problemas

### Error: "Class 'Database\Factories\UsuarioFactory' not found"
Ejecuta:
```bash
composer dump-autoload
```

### Error: "SQLSTATE[HY000] [1045] Access denied"
Verifica las credenciales de la base de datos en `.env.testing`

### Error: "No application encryption key has been specified"
Ejecuta:
```bash
php artisan key:generate
```

## Próximos Pasos

1. **Crear más tests**: Agrega tests para:
   - Controladores de viajes
   - Gestión de documentos
   - Calificaciones
   - Servicios externos

2. **Aumentar cobertura**: Apunta a al menos 70-80% de cobertura de código

3. **Tests de integración**: Crea tests que prueben flujos completos de usuario

4. **Tests de rendimiento**: Para endpoints críticos


