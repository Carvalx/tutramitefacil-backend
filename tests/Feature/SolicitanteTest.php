<?php

use App\Models\User;
use App\Solicitantes\Infrastructure\Persistence\EloquentSolicitante;

function obtenerTokenJWT(): string
{
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $response = test()->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    return $response->json('access_token');
}

test('cualquiera puede listar solicitantes sin autenticarse', function () {
    EloquentSolicitante::factory()->count(3)->create();

    $response = $this->getJson('/api/solicitantes');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('crear un solicitante requiere autenticacion', function () {
    $response = $this->postJson('/api/solicitantes', [
        'nombre' => 'Carlos',
        'apellidos' => 'Macero',
        'email' => 'carlos@test.com',
        'telefono' => '600000000',
        'comunidad_autonoma' => 'Comunidad Valenciana',
        'fecha_registro' => '2026-06-15',
    ]);

    $response->assertStatus(401);
});

test('un usuario autenticado puede crear un solicitante', function () {
    $token = obtenerTokenJWT();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/solicitantes', [
            'nombre' => 'Carlos',
            'apellidos' => 'Macero',
            'email' => 'carlos@test.com',
            'telefono' => '600000000',
            'comunidad_autonoma' => 'Comunidad Valenciana',
            'fecha_registro' => '2026-06-15',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.nombre', 'Carlos');

    $this->assertDatabaseHas('solicitantes', ['email' => 'carlos@test.com']);
});

test('eliminar un solicitante requiere autenticacion', function () {
    $solicitante = EloquentSolicitante::factory()->create();

    $response = $this->deleteJson("/api/solicitantes/{$solicitante->id}");

    $response->assertStatus(401);
});
