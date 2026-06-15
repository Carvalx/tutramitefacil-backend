<?php

namespace App\Solicitudes\Application\Services;

use App\Solicitudes\Domain\Entities\Solicitud;
use App\Solicitudes\Domain\Repositories\SolicitudRepositoryInterface;
use App\Solicitudes\Infrastructure\Jobs\ProcesarSolicitudJob;

class SolicitudService
{
    public function __construct(
        private readonly SolicitudRepositoryInterface $repository,
    ) {
    }

    /**
     * @return Solicitud[]
     */
    public function listar(): array
    {
        return $this->repository->all();
    }

    public function buscar(string $id): ?Solicitud
    {
        return $this->repository->findById($id);
    }

    /**
     * @return Solicitud[]
     */
    public function porSolicitante(string $solicitanteId): array
    {
        return $this->repository->findBySolicitanteId($solicitanteId);
    }

    /**
     * Crea una solicitud y encola un Job para procesarla de forma asíncrona
     * (notificación al solicitante + verificación de requisitos).
     */
    public function crear(array $data): Solicitud
    {
        $solicitud = $this->repository->create($data);

        ProcesarSolicitudJob::dispatch(
            solicitudId: $solicitud->id(),
            solicitanteId: $solicitud->solicitanteId(),
            tipoAyuda: $solicitud->tipoAyuda()->value,
            estado: $solicitud->estado()->value,
            accion: 'creada',
        );

        return $solicitud;
    }

    /**
     * Actualiza una solicitud. Si el campo 'estado' cambió respecto al
     * valor anterior, encola un Job para notificar el cambio.
     */
    public function actualizar(string $id, array $data): ?Solicitud
    {
        $anterior = $this->repository->findById($id);

        if (! $anterior) {
            return null;
        }

        $actualizada = $this->repository->update($id, $data);

        if ($actualizada && $actualizada->estado() !== $anterior->estado()) {
            ProcesarSolicitudJob::dispatch(
                solicitudId: $actualizada->id(),
                solicitanteId: $actualizada->solicitanteId(),
                tipoAyuda: $actualizada->tipoAyuda()->value,
                estado: $actualizada->estado()->value,
                accion: 'cambio_estado',
            );
        }

        return $actualizada;
    }

    public function eliminar(string $id): bool
    {
        return $this->repository->delete($id);
    }
}