<?php

namespace Database\Factories;

use App\Solicitantes\Infrastructure\Persistence\EloquentSolicitante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory de EloquentSolicitante.
 *
 * Genera datos falsos pero realistas usando Faker (ya en español,
 * gracias a APP_FAKER_LOCALE=es_ES en el .env).
 */
class SolicitanteFactory extends Factory
{
    protected $model = EloquentSolicitante::class;

    public function definition(): array
    {
        $comunidades = [
            'Andalucía', 'Aragón', 'Asturias', 'Baleares', 'Canarias',
            'Cantabria', 'Castilla-La Mancha', 'Castilla y León',
            'Cataluña', 'Comunidad Valenciana', 'Extremadura', 'Galicia',
            'Madrid', 'Murcia', 'Navarra', 'País Vasco', 'La Rioja',
        ];

        return [
            'nombre' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName().' '.$this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => '6'.$this->faker->numerify('########'),
            'comunidad_autonoma' => $this->faker->randomElement($comunidades),
            'fecha_registro' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}