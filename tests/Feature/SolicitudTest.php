<?php

use App\Models\User;
use App\Solicitantes\Infrastructure\Persistence\EloquentSolicitante;
use App\Solicitudes\Infrastructure\Jobs\ProcesarSolicitudJob;
use App\Solicitudes\Infrastructure\Persistence\EloquentSolicitud;
use Illuminate\Support\Facades\Queue;

function obtenerTokenJWTSolicitud(): string
{
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $response = test()->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    return $response->json('access_token');
}

test('cualquiera puede listar solicitudes sin autenticarse', function () {
    EloquentSolicitud::factory()->count(2)->create();

    $response = $this->getJson('/api/solicitudes');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('crear una solicitud requiere autenticacion', function () {
    $solicitante = EloquentSolicitante::factory()->create();

    $response = $this->postJson('/api/solicitudes', [
        'solicitante_id' => $solicitante->id,
        'tipo_ayuda' => 'Alquiler',
        'fecha_solicitud' => '2026-06-15',
        'importe_estimado' => 500,
    ]);

    $response->assertStatus(401);
});

test('crear una solicitud despacha el ProcesarSolicitudJob', function () {
    Queue::fake();

    $token = obtenerTokenJWTSolicitud();
    $solicitante = EloquentSolicitante::factory()->create();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/solicitudes', [
            'solicitante_id' => $solicitante->id,
            'tipo_ayuda' => 'Alquiler',
            'fecha_solicitud' => '2026-06-15',
            'importe_estimado' => 500,
        ]);

    $response->assertStatus(201);

    Queue::assertPushed(ProcesarSolicitudJob::class, function ($job) use ($solicitante) {
        return $job->solicitanteId === $solicitante->id
            && $job->accion === 'creada';
    });
});

test('cambiar el estado de una solicitud despacha el Job con accion cambio_estado', function () {
    Queue::fake();

    $token = obtenerTokenJWTSolicitud();
    $solicitud = EloquentSolicitud::factory()->create(['estado' => 'Pendiente']);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/solicitudes/{$solicitud->id}", [
            'estado' => 'Concedida',
        ]);

    $response->assertStatus(200);

    Queue::assertPushed(ProcesarSolicitudJob::class, function ($job) {
        return $job->accion === 'cambio_estado' && $job->estado === 'Concedida';
    });
});
