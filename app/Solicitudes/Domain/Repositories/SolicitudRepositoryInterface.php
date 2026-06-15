<?php

namespace App\Solicitudes\Domain\Repositories;

use App\Solicitudes\Domain\Entities\Solicitud;

interface SolicitudRepositoryInterface
{
    /**
     * @return Solicitud[]
     */
    public function all(): array;

    public function findById(string $id): ?Solicitud;

    /**
     * Devuelve todas las solicitudes de un solicitante concreto.
     * Necesario para la pantalla "detalle de solicitante con sus solicitudes".
     *
     * @return Solicitud[]
     */
    public function findBySolicitanteId(string $solicitanteId): array;

    public function create(array $data): Solicitud;

    public function update(string $id, array $data): ?Solicitud;

    public function delete(string $id): bool;
}