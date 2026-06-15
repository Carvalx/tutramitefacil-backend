<?php

namespace App\Solicitudes\Infrastructure\Persistence;

use App\Solicitudes\Domain\Entities\Solicitud;
use App\Solicitudes\Domain\Enums\Estado;
use App\Solicitudes\Domain\Enums\TipoAyuda;
use App\Solicitudes\Domain\Repositories\SolicitudRepositoryInterface;
use DateTimeImmutable;

class EloquentSolicitudRepository implements SolicitudRepositoryInterface
{
    public function all(): array
    {
        return EloquentSolicitud::all()
            ->map(fn (EloquentSolicitud $model) => $this->toEntity($model))
            ->all();
    }

    public function findById(string $id): ?Solicitud
    {
        $model = EloquentSolicitud::find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function findBySolicitanteId(string $solicitanteId): array
    {
        return EloquentSolicitud::where('solicitante_id', $solicitanteId)
            ->get()
            ->map(fn (EloquentSolicitud $model) => $this->toEntity($model))
            ->all();
    }

    public function create(array $data): Solicitud
    {
        $model = EloquentSolicitud::create($data);

        return $this->toEntity($model);
    }

    public function update(string $id, array $data): ?Solicitud
    {
        $model = EloquentSolicitud::find($id);

        if (! $model) {
            return null;
        }

        $model->update($data);

        return $this->toEntity($model);
    }

    public function delete(string $id): bool
    {
        $model = EloquentSolicitud::find($id);

        if (! $model) {
            return false;
        }

        return (bool) $model->delete();
    }

    /**
     * Traduce EloquentSolicitud -> Solicitud (Domain Entity).
     *
     * Aquí el string de la BD ('Alquiler', 'Pendiente', etc.) se
     * convierte en el enum de PHP correspondiente, usando
     * TipoAyuda::from() / Estado::from(). Si el valor en BD no
     * coincidiera con ningún "case" del enum, esto lanzaría
     * un ValueError -> es una validación de integridad "gratis".
     */
    private function toEntity(EloquentSolicitud $model): Solicitud
    {
        return new Solicitud(
            id: $model->id,
            solicitanteId: $model->solicitante_id,
            tipoAyuda: TipoAyuda::from($model->tipo_ayuda),
            fechaSolicitud: new DateTimeImmutable($model->fecha_solicitud->toDateString()),
            fechaResolucion: $model->fecha_resolucion
                ? new DateTimeImmutable($model->fecha_resolucion->toDateString())
                : null,
            importeEstimado: (float) $model->importe_estimado,
            estado: Estado::from($model->estado),
        );
    }
}