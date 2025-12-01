<?php

namespace Database\Factories;

use App\Models\Taxista;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Taxista>
 */
class TaxistaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Taxista::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'id_usuario' => Usuario::factory()->taxista()->create()->id,
            'id_matricula' => null,
            'id_licencia' => null,
        ];
    }
}


