<?php

namespace App\Solicitudes\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job: ProcesarSolicitudJob.
 *
 * Se encola cuando:
 * 1. Se CREA una nueva solicitud (notificar al solicitante + verificar
 *    requisitos con "la administración").
 * 2. Se CAMBIA el estado de una solicitud (notificar el nuevo estado).
 *
 * Este Job es procesado de forma ASÍNCRONA por un worker de Horizon
 * (no bloquea la respuesta HTTP al usuario).
 *
 * NOTA IMPORTANTE: este Job recibe datos primitivos (strings, no la
 * Entity de Domain ni el modelo Eloquent), porque los Jobs se serializan
 * para guardarse en Redis. Una Entity de Domain (con enums, etc.) podría
 * no serializar/deserializar limpiamente, así que pasamos solo lo
 * estrictamente necesario como tipos simples.
 */
class ProcesarSolicitudJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $solicitudId,
        public readonly string $solicitanteId,
        public readonly string $tipoAyuda,
        public readonly string $estado,
        public readonly string $accion, // 'creada' | 'cambio_estado'
    ) {
    }

    /**
     * Execute the job.
     *
     * En un sistema real, aquí iría: enviar email al solicitante
     * (Mail::to(...)->send(...)) y/o llamar a una API externa de
     * verificación. Para esta prueba, lo simulamos con un log,
     * que es perfectamente válido para demostrar el flujo asíncrono
     * en Horizon (verás el Job aparecer y completarse en el dashboard).
     */
    public function handle(): void
    {
        Log::info('ProcesarSolicitudJob ejecutado', [
            'solicitud_id' => $this->solicitudId,
            'solicitante_id' => $this->solicitanteId,
            'tipo_ayuda' => $this->tipoAyuda,
            'estado' => $this->estado,
            'accion' => $this->accion,
        ]);

        // Simula trabajo real (ej: llamada a API externa, envío de email).
        // sleep(1) es opcional, ayuda a VER el Job "en proceso" en Horizon
        // en vez de que termine instantáneamente.
        sleep(1);

        Log::info('ProcesarSolicitudJob completado', [
            'solicitud_id' => $this->solicitudId,
        ]);
    }
}