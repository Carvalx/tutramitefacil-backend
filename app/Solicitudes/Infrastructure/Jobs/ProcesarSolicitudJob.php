<?php

namespace App\Solicitudes\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job asíncrono encolado al crear/cambiar estado de una Solicitud (via Horizon + Redis).
 * Recibe tipos primitivos —no la Entity— porque los Jobs se serializan en Redis y un enum o DateTimeImmutable podría no rehidratarse limpiamente.
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
     * En producción aquí iría: Mail::to(...) + llamada a API de verificación.
     * El sleep(1) permite ver el Job "en proceso" en el dashboard de Horizon en vez de que termine instantáneamente.
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

        sleep(1);

        Log::info('ProcesarSolicitudJob completado', [
            'solicitud_id' => $this->solicitudId,
        ]);
    }
}