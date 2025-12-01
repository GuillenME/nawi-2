# Guía de Testing - NAWI

Esta guía te ayudará a testear tu aplicación Laravel de manera efectiva.

## 📋 Tabla de Contenidos

1. [Configuración Inicial](#configuración-inicial)
2. [Ejecutar Tests](#ejecutar-tests)
3. [Estructura de Tests](#estructura-de-tests)
4. [Tipos de Tests](#tipos-de-tests)
5. [Ejemplos Prácticos](#ejemplos-prácticos)
6. [Mejores Prácticas](#mejores-prácticas)

## 🔧 Configuración Inicial

### 1. Configurar Base de Datos para Testing

Edita tu archivo `.env.testing` o crea uno nuevo:

```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

O si prefieres usar MySQL:

```env
APP_ENV=testing
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nawi_test
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Configurar PHPUnit

El archivo `phpunit.xml` ya está configurado. Asegúrate de que las variables de entorno estén correctas.

## 🚀 Ejecutar Tests

### Ejecutar todos los tests

```bash
php artisan test
```

O usando PHPUnit directamente:

```bash
vendor/bin/phpunit
```

### Ejecutar tests específicos

```bash
# Todos los tests de Feature
php artisan test --testsuite=Feature

# Todos los tests de Unit
php artisan test --testsuite=Unit

# Un archivo específico
php artisan test tests/Feature/AuthTest.php

# Un test específico
php artisan test --filter test_user_can_login
```

### Ejecutar con cobertura de código

```bash
php artisan test --coverage
```

## 📁 Estructura de Tests

```
tests/
├── Feature/          # Tests de integración (HTTP, rutas, controladores)
│   ├── AuthTest.php
│   ├── ViajeTest.php
│   └── ...
├── Unit/             # Tests unitarios (modelos, servicios, helpers)
│   ├── UsuarioTest.php
│   └── ...
├── TestCase.php      # Clase base para todos los tests
└── CreatesApplication.php
```

## 🧪 Tipos de Tests

### 1. Tests de Feature (Integración)

Prueban funcionalidades completas desde la perspectiva del usuario:
- Peticiones HTTP
- Rutas y controladores
- Middleware
- Autenticación

### 2. Tests de Unit (Unitarios)

Prueban componentes individuales:
- Modelos y relaciones
- Servicios
- Helpers
- Validaciones

## 📝 Ejemplos Prácticos

### Test de Autenticación API

```php
public function test_usuario_puede_registrarse_como_pasajero()
{
    $response = $this->postJson('/api/register/pasajero', [
        'nombre' => 'Juan',
        'apellido' => 'Pérez',
        'telefono' => '1234567890',
        'email' => 'juan@example.com',
        'password' => 'password123'
    ]);

    $response->assertStatus(201)
             ->assertJson(['success' => true]);
}
```

### Test de Autenticación Web

```php
public function test_usuario_puede_hacer_login()
{
    $usuario = Usuario::factory()->create([
        'password' => Hash::make('password123')
    ]);

    $response = $this->post('/login', [
        'email' => $usuario->email,
        'password' => 'password123'
    ]);

    $response->assertRedirect();
    $this->assertAuthenticatedAs($usuario);
}
```

### Test de Rutas Protegidas

```php
public function test_ruta_protegida_requiere_autenticacion()
{
    $response = $this->getJson('/api/user');
    
    $response->assertStatus(401);
}

public function test_usuario_autenticado_puede_acceder_a_ruta_protegida()
{
    $usuario = Usuario::factory()->create();
    $token = $usuario->createToken('test')->accessToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->getJson('/api/user');

    $response->assertStatus(200);
}
```

### Test de Modelos

```php
public function test_usuario_tiene_relacion_con_rol()
{
    $usuario = Usuario::factory()->create();
    
    $this->assertInstanceOf(Role::class, $usuario->rol);
}

public function test_usuario_puede_tener_taxista()
{
    $usuario = Usuario::factory()->create();
    $taxista = Taxista::factory()->create(['id_usuario' => $usuario->id]);
    
    $this->assertInstanceOf(Taxista::class, $usuario->taxista);
}
```

### Test de Validación

```php
public function test_registro_requiere_email_unico()
{
    Usuario::factory()->create(['email' => 'test@example.com']);

    $response = $this->postJson('/api/register/pasajero', [
        'nombre' => 'Juan',
        'apellido' => 'Pérez',
        'telefono' => '1234567890',
        'email' => 'test@example.com',
        'password' => 'password123'
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email']);
}
```

## ✅ Mejores Prácticas

1. **Usa RefreshDatabase**: Para tests que modifican la base de datos
   ```php
   use Illuminate\Foundation\Testing\RefreshDatabase;
   
   class MiTest extends TestCase
   {
       use RefreshDatabase;
   }
   ```

2. **Usa Factories**: Para crear datos de prueba de manera consistente
   ```php
   $usuario = Usuario::factory()->create();
   ```

3. **Nombres descriptivos**: Los nombres de los tests deben describir qué están probando
   ```php
   // ✅ Bueno
   public function test_usuario_puede_crear_viaje()
   
   // ❌ Malo
   public function test1()
   ```

4. **Arrange-Act-Assert**: Estructura tus tests en tres partes
   ```php
   public function test_ejemplo()
   {
       // Arrange: Preparar datos
       $usuario = Usuario::factory()->create();
       
       // Act: Ejecutar acción
       $response = $this->actingAs($usuario)->get('/perfil');
       
       // Assert: Verificar resultado
       $response->assertStatus(200);
   }
   ```

5. **Tests independientes**: Cada test debe poder ejecutarse de forma independiente

6. **Mock de servicios externos**: Si usas APIs externas, usa mocks
   ```php
   Http::fake([
       'api.externa.com/*' => Http::response(['data' => 'test'], 200)
   ]);
   ```

## 🎯 Áreas Importantes para Testear

### Autenticación
- ✅ Registro de pasajeros
- ✅ Registro de taxistas
- ✅ Login (API y Web)
- ✅ Logout
- ✅ Recuperación de contraseña
- ✅ Rutas protegidas

### Funcionalidades Core
- ✅ Creación de viajes
- ✅ Aceptación de viajes
- ✅ Calificación de viajes
- ✅ Gestión de documentos (matrículas, licencias)
- ✅ Perfil de usuario

### Validaciones
- ✅ Validación de datos de entrada
- ✅ Validación de permisos
- ✅ Validación de estados de viaje

## 📊 Cobertura de Código

Para ver la cobertura de código:

```bash
php artisan test --coverage
```

O con un umbral mínimo:

```bash
php artisan test --coverage --min=80
```

## 🔍 Debugging Tests

Si un test falla, puedes usar:

```php
// Ver la respuesta completa
$response->dump();

// Ver solo los headers
$response->dumpHeaders();

// Ver solo el contenido
$response->dumpSession();
```

## 📚 Recursos Adicionales

- [Documentación de Laravel Testing](https://laravel.com/docs/10.x/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel Testing Best Practices](https://laravel.com/docs/10.x/testing#testing-apis)


