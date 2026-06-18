<?php

namespace App\Solicitantes\Application\Services;

use App\Solicitantes\Domain\Entities\Solicitante;
use App\Solicitantes\Domain\Repositories\SolicitanteRepositoryInterface;

/**
 * Servicio de aplicación: orquesta los casos de uso de Solicitante.
 * Aquí irían reglas de negocio (ej: unicidad de email) si el dominio creciera.
 */
class SolicitanteService
{
    public function __construct(
        private readonly SolicitanteRepositoryInterface $repository,
    ) {
    }

    /**
     * @return Solicitante[]
     */
    public function listar(): array
    {
        return $this->repository->all();
    }

    public function buscar(string $id): ?Solicitante
    {
        return $this->repository->findById($id);
    }

    public function crear(array $data): Solicitante
    {
        return $this->repository->create($data);
    }

    public function actualizar(string $id, array $data): ?Solicitante
    {
        return $this->repository->update($id, $data);
    }

    public function eliminar(string $id): bool
    {
        return $this->repository->delete($id);
    }
}