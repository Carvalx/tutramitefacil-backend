<?php

namespace Database\Factories;

use App\Solicitantes\Infrastructure\Persistence\EloquentSolicitante;
use App\Solicitudes\Domain\Enums\Estado;
use App\Solicitudes\Domain\Enums\TipoAyuda;
use App\Solicitudes\Infrastructure\Persistence\EloquentSolicitud;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitudFactory extends Factory
{
    protected $model = EloquentSolicitud::class;

    public function definition(): array
    {
        // Reutilizamos los enums de Domain para generar valores SIEMPRE
        // válidos (si añadimos un TipoAyuda nuevo, el factory lo recoge
        // automáticamente sin tocar este archivo).
        $tipoAyuda = $this->faker->randomElement(TipoAyuda::cases());
        $estado = $this->faker->randomElement(Estado::cases());

        $fechaSolicitud = $this->faker->dateTimeBetween('-6 months', 'now');

        // Solo asignamos fecha_resolucion si el estado es "final"
        // (Concedida o Denegada), igual que pasaría en la realidad.
        $fechaResolucion = $estado->esFinal()
            ? $this->faker->dateTimeBetween($fechaSolicitud, 'now')
            : null;

        return [
            // Por defecto, crea un Solicitante nuevo para cada Solicitud.
            // Esto se puede sobreescribir con ->for() en el seeder
            // para asociar varias solicitudes a un mismo solicitante.
            'solicitante_id' => EloquentSolicitante::factory(),
            'tipo_ayuda' => $tipoAyuda->value,
            'fecha_solicitud' => $fechaSolicitud,
            'fecha_resolucion' => $fechaResolucion,
            'importe_estimado' => $this->faker->randomFloat(2, 100, 5000),
            'estado' => $estado->value,
        ];
    }
}