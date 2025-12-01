<?php

namespace Tests\Feature;

use App\Models\Usuario;
use App\Models\Pasajero;
use App\Models\Taxista;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que un usuario puede registrarse como pasajero vía API
     */
    public function test_usuario_puede_registrarse_como_pasajero(): void
    {
        // Asegurar que existe el rol de pasajero
        $rolPasajero = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'pasajero'
        ]);

        $datosRegistro = [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'telefono' => '1234567890',
            'email' => 'juan@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/register/pasajero', $datosRegistro);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'tipo' => 'pasajero'
                     ]
                 ]);

        // Verificar que el usuario fue creado
        $this->assertDatabaseHas('usuarios', [
            'email' => 'juan@example.com',
            'nombre' => 'Juan',
            'apellido' => 'Pérez'
        ]);

        // Verificar que se creó el registro de pasajero
        $usuario = Usuario::where('email', 'juan@example.com')->first();
        $this->assertNotNull($usuario->pasajero);
    }

    /**
     * Test que un usuario puede registrarse como taxista vía API
     */
    public function test_usuario_puede_registrarse_como_taxista(): void
    {
        // Asegurar que existe el rol de taxista
        $rolTaxista = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'taxista'
        ]);

        $datosRegistro = [
            'nombre' => 'Carlos',
            'apellido' => 'González',
            'telefono' => '9876543210',
            'email' => 'carlos@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/register/taxista', $datosRegistro);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'tipo' => 'taxista'
                     ]
                 ]);

        // Verificar que el usuario fue creado
        $this->assertDatabaseHas('usuarios', [
            'email' => 'carlos@example.com'
        ]);

        // Verificar que se creó el registro de taxista
        $usuario = Usuario::where('email', 'carlos@example.com')->first();
        $this->assertNotNull($usuario->taxista);
    }

    /**
     * Test que el registro requiere email único
     */
    public function test_registro_requiere_email_unico(): void
    {
        $rolPasajero = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'pasajero'
        ]);

        // Crear un usuario existente
        Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Usuario',
            'apellido' => 'Existente',
            'telefono' => '1111111111',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'id_rol' => $rolPasajero->id
        ]);

        $response = $this->postJson('/api/register/pasajero', [
            'nombre' => 'Nuevo',
            'apellido' => 'Usuario',
            'telefono' => '2222222222',
            'email' => 'test@example.com', // Email duplicado
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test que un usuario puede hacer login vía API
     */
    public function test_usuario_puede_hacer_login_api(): void
    {
        $rolPasajero = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'pasajero'
        ]);

        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Test',
            'apellido' => 'User',
            'telefono' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'id_rol' => $rolPasajero->id
        ]);

        Pasajero::create([
            'id' => Str::uuid(),
            'id_usuario' => $usuario->id
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'tipo' => 'pasajero'
                     ]
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'access_token',
                         'token_type',
                         'usuario'
                     ]
                 ]);
    }

    /**
     * Test que login falla con credenciales incorrectas
     */
    public function test_login_falla_con_credenciales_incorrectas(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'noexiste@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Credenciales inválidas'
                 ]);
    }

    /**
     * Test que un usuario autenticado puede acceder a rutas protegidas
     */
    public function test_usuario_autenticado_puede_acceder_a_rutas_protegidas(): void
    {
        $rolPasajero = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'pasajero'
        ]);

        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Test',
            'apellido' => 'User',
            'telefono' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'id_rol' => $rolPasajero->id
        ]);

        $token = $usuario->createToken('API Token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'usuario'
                     ]
                 ]);
    }

    /**
     * Test que rutas protegidas requieren autenticación
     */
    public function test_rutas_protegidas_requieren_autenticacion(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    /**
     * Test que un usuario puede hacer logout
     */
    public function test_usuario_puede_hacer_logout(): void
    {
        $rolPasajero = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'pasajero'
        ]);

        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Test',
            'apellido' => 'User',
            'telefono' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'id_rol' => $rolPasajero->id
        ]);

        $token = $usuario->createToken('API Token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Sesión cerrada exitosamente'
                 ]);
    }
}


