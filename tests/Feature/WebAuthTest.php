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

class WebAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que se puede mostrar el formulario de login
     */
    public function test_puede_mostrar_formulario_login(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
                 ->assertViewIs('auth.login');
    }

    /**
     * Test que un usuario puede hacer login vía web
     */
    public function test_usuario_puede_hacer_login_web(): void
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

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($usuario);
    }

    /**
     * Test que login web redirige a dashboard de admin si es admin
     */
    public function test_login_web_redirige_admin_a_dashboard(): void
    {
        $rolAdmin = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'admin'
        ]);

        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Admin',
            'apellido' => 'User',
            'telefono' => '1234567890',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'id_rol' => $rolAdmin->id
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($usuario);
    }

    /**
     * Test que login web redirige a dashboard de taxista si es taxista
     */
    public function test_login_web_redirige_taxista_a_dashboard(): void
    {
        $rolTaxista = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'taxista'
        ]);

        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Taxista',
            'apellido' => 'User',
            'telefono' => '1234567890',
            'email' => 'taxista@example.com',
            'password' => Hash::make('password123'),
            'id_rol' => $rolTaxista->id
        ]);

        Taxista::create([
            'id' => Str::uuid(),
            'id_usuario' => $usuario->id
        ]);

        $response = $this->post('/login', [
            'email' => 'taxista@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/taxista/dashboard');
        $this->assertAuthenticatedAs($usuario);
    }

    /**
     * Test que login web falla con credenciales incorrectas
     */
    public function test_login_web_falla_con_credenciales_incorrectas(): void
    {
        $response = $this->post('/login', [
            'email' => 'noexiste@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test que un usuario puede hacer logout
     */
    public function test_usuario_puede_hacer_logout_web(): void
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

        $this->actingAs($usuario);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test que rutas protegidas requieren autenticación
     */
    public function test_rutas_protegidas_requieren_autenticacion_web(): void
    {
        $response = $this->get('/perfil');

        $response->assertRedirect('/login');
    }
}


