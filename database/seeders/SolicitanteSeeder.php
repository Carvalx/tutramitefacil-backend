<?php

namespace Database\Seeders;

use App\Solicitantes\Infrastructure\Persistence\EloquentSolicitante;
use Illuminate\Database\Seeder;

class SolicitanteSeeder extends Seeder
{
    public function run(): void
    {
        EloquentSolicitante::factory()->count(15)->create();
    }
}