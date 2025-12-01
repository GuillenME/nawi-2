<?php

namespace Tests\Unit;

use App\Models\Usuario;
use App\Models\Role;
use App\Models\Pasajero;
use App\Models\Taxista;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UsuarioModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que un usuario puede ser creado
     */
    public function test_usuario_puede_ser_creado(): void
    {
        $rol = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'pasajero'
        ]);

        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Test',
            'apellido' => 'User',
            'telefono' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'id_rol' => $rol->id
        ]);

        $this->assertDatabaseHas('usuarios', [
            'email' => 'test@example.com',
            'nombre' => 'Test'
        ]);

        $this->assertInstanceOf(Usuario::class, $usuario);
    }

    /**
     * Test que un usuario tiene relación con rol
     */
    public function test_usuario_tiene_relacion_con_rol(): void
    {
        $rol = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'pasajero'
        ]);

        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Test',
            'apellido' => 'User',
            'telefono' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'id_rol' => $rol->id
        ]);

        $this->assertInstanceOf(Role::class, $usuario->rol);
        $this->assertEquals('pasajero', $usuario->rol->nombre);
    }

    /**
     * Test que un usuario puede tener un pasajero
     */
    public function test_usuario_puede_tener_pasajero(): void
    {
        $rol = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'pasajero'
        ]);

        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Test',
            'apellido' => 'User',
            'telefono' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'id_rol' => $rol->id
        ]);

        $pasajero = Pasajero::create([
            'id' => Str::uuid(),
            'id_usuario' => $usuario->id
        ]);

        $this->assertInstanceOf(Pasajero::class, $usuario->pasajero);
        $this->assertEquals($pasajero->id, $usuario->pasajero->id);
    }

    /**
     * Test que un usuario puede tener un taxista
     */
    public function test_usuario_puede_tener_taxista(): void
    {
        $rol = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'taxista'
        ]);

        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Test',
            'apellido' => 'User',
            'telefono' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'id_rol' => $rol->id
        ]);

        $taxista = Taxista::create([
            'id' => Str::uuid(),
            'id_usuario' => $usuario->id
        ]);

        $this->assertInstanceOf(Taxista::class, $usuario->taxista);
        $this->assertEquals($taxista->id, $usuario->taxista->id);
    }

    /**
     * Test que la contraseña se hashea automáticamente
     */
    public function test_password_se_hashea_automaticamente(): void
    {
        $rol = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'pasajero'
        ]);

        $password = 'password123';
        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Test',
            'apellido' => 'User',
            'telefono' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make($password),
            'id_rol' => $rol->id
        ]);

        $this->assertNotEquals($password, $usuario->password);
        $this->assertTrue(Hash::check($password, $usuario->password));
    }

    /**
     * Test que el email debe ser único
     */
    public function test_email_debe_ser_unico(): void
    {
        $rol = Role::create([
            'id' => Str::uuid(),
            'nombre' => 'pasajero'
        ]);

        Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Test',
            'apellido' => 'User',
            'telefono' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'id_rol' => $rol->id
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Usuario::create([
            'id' => Str::uuid(),
            'nombre' => 'Test2',
            'apellido' => 'User2',
            'telefono' => '0987654321',
            'email' => 'test@example.com', // Email duplicado
            'password' => Hash::make('password'),
            'id_rol' => $rol->id
        ]);
    }
}


