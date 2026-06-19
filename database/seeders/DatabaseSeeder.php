<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario para poder acceder
        User::firstOrCreate(
            ['email' => 'demo@tutramitefacil.com'],
            [
                'name' => 'Demo TuTramiteFácil',
                'password' => bcrypt('demo1234'),
            ]
        );

        $this->call([
            SolicitanteSeeder::class,
            SolicitudSeeder::class,
        ]);
    }
}