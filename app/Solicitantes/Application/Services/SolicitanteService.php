<?php

namespace App\Solicitantes\Application\Services;

use App\Solicitantes\Domain\Entities\Solicitante;
use App\Solicitantes\Domain\Repositories\SolicitanteRepositoryInterface;

/**
 * Application Service: orquesta los "casos de uso" de Solicitante.
 *
 * Esta clase es el punto de entrada desde Presentation (Controllers)
 * hacia el negocio. Por ahora cada método es casi un "paso directo"
 * al Repository, pero aquí es donde, si el negocio creciera, irían
 * cosas como: "antes de crear, comprobar que no exista otro solicitante
 * con el mismo email" (validaciones de negocio que no son solo
 * de formato, sino de REGLAS).
 *
 * Importante: este Service depende de la INTERFAZ del repositorio
 * (SolicitanteRepositoryInterface), no de la implementación Eloquent.
 * Laravel inyecta automáticamente EloquentSolicitanteRepository gracias
 * al binding que hicimos en AppServiceProvider.
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