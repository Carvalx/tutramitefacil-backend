<?php

namespace Database\Seeders;

use App\Solicitantes\Infrastructure\Persistence\EloquentSolicitante;
use App\Solicitudes\Infrastructure\Persistence\EloquentSolicitud;
use Illuminate\Database\Seeder;

class SolicitudSeeder extends Seeder
{
    public function run(): void
    {
        // Para cada Solicitante existente, crea entre 1 y 4 solicitudes.
        // Así garantizamos que la pantalla "detalle de solicitante con
        // sus solicitudes" del frontend tenga datos realistas que mostrar.
        EloquentSolicitante::all()->each(function (EloquentSolicitante $solicitante) {
            EloquentSolicitud::factory()
                ->count(rand(1, 4))
                ->for($solicitante, 'solicitante')
                ->create();
        });
    }
}