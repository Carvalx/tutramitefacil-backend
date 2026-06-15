<?php

namespace App\Solicitudes\Application\Services;

use App\Solicitudes\Domain\Entities\Solicitud;
use App\Solicitudes\Domain\Repositories\SolicitudRepositoryInterface;

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

    public function crear(array $data): Solicitud
    {
        return $this->repository->create($data);
    }

    public function actualizar(string $id, array $data): ?Solicitud
    {
        return $this->repository->update($id, $data);
    }

    public function eliminar(string $id): bool
    {
        return $this->repository->delete($id);
    }
}