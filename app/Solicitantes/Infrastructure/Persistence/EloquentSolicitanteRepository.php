<?php

namespace App\Solicitantes\Infrastructure\Persistence;

use App\Solicitantes\Domain\Entities\Solicitante;
use App\Solicitantes\Domain\Repositories\SolicitanteRepositoryInterface;
use DateTimeImmutable;

/**
 * Implementación concreta (Eloquent + MySQL) de SolicitanteRepositoryInterface.
 *
 * Esta clase hace de "traductor" en ambas direcciones:
 * - Lee de MySQL vía EloquentSolicitante -> convierte a Solicitante (Domain Entity)
 * - Recibe datos -> los guarda en MySQL vía EloquentSolicitante
 *
 * Application/Services NUNCA debería ver un EloquentSolicitante directamente,
 * solo Solicitante (la Entity de Domain). Eso es lo que hace este repositorio.
 */
class EloquentSolicitanteRepository implements SolicitanteRepositoryInterface
{
    public function all(): array
    {
        return EloquentSolicitante::all()
            ->map(fn (EloquentSolicitante $model) => $this->toEntity($model))
            ->all();
    }

    public function findById(string $id): ?Solicitante
    {
        $model = EloquentSolicitante::find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function create(array $data): Solicitante
    {
        $model = EloquentSolicitante::create($data);

        return $this->toEntity($model);
    }

    public function update(string $id, array $data): ?Solicitante
    {
        $model = EloquentSolicitante::find($id);

        if (! $model) {
            return null;
        }

        $model->update($data);

        return $this->toEntity($model);
    }

    public function delete(string $id): bool
    {
        $model = EloquentSolicitante::find($id);

        if (! $model) {
            return false;
        }

        return (bool) $model->delete();
    }

    /**
     * Convierte un modelo Eloquent (Infrastructure) en una Entity
     * de Domain (Solicitante). Este es el "punto de traducción"
     * entre las dos capas.
     */
    private function toEntity(EloquentSolicitante $model): Solicitante
    {
        return new Solicitante(
            id: $model->id,
            nombre: $model->nombre,
            apellidos: $model->apellidos,
            email: $model->email,
            telefono: $model->telefono,
            comunidadAutonoma: $model->comunidad_autonoma,
            fechaRegistro: new DateTimeImmutable($model->fecha_registro->toDateString()),
        );
    }
}