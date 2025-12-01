<?php

namespace Database\Factories;

use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Usuario::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'id_rol' => function () {
                return Role::factory()->create()->id;
            },
        ];
    }

    /**
     * Indicate that the user should be a pasajero.
     */
    public function pasajero(): static
    {
        return $this->state(function (array $attributes) {
            $rolPasajero = Role::firstOrCreate(
                ['nombre' => 'pasajero'],
                ['id' => Str::uuid()]
            );

            return [
                'id_rol' => $rolPasajero->id,
            ];
        });
    }

    /**
     * Indicate that the user should be a taxista.
     */
    public function taxista(): static
    {
        return $this->state(function (array $attributes) {
            $rolTaxista = Role::firstOrCreate(
                ['nombre' => 'taxista'],
                ['id' => Str::uuid()]
            );

            return [
                'id_rol' => $rolTaxista->id,
            ];
        });
    }

    /**
     * Indicate that the user should be an admin.
     */
    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            $rolAdmin = Role::firstOrCreate(
                ['nombre' => 'admin'],
                ['id' => Str::uuid()]
            );

            return [
                'id_rol' => $rolAdmin->id,
            ];
        });
    }
}


