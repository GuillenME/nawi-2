<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'nombre' => fake()->randomElement(['admin', 'pasajero', 'taxista']),
        ];
    }

    /**
     * Indicate that the role is admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => 'admin',
        ]);
    }

    /**
     * Indicate that the role is pasajero.
     */
    public function pasajero(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => 'pasajero',
        ]);
    }

    /**
     * Indicate that the role is taxista.
     */
    public function taxista(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => 'taxista',
        ]);
    }
}


