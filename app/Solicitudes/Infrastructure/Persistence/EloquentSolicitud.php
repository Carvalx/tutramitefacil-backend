<?php

namespace App\Solicitudes\Infrastructure\Persistence;

use App\Solicitudes\Domain\Enums\Estado;
use App\Solicitantes\Infrastructure\Persistence\EloquentSolicitante;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EloquentSolicitud extends Model
{
    use HasUuids;

    protected $table = 'solicitudes';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $attributes = [
        'estado' => Estado::Pendiente->value,
    ];

    protected $fillable = [
        'id',
        'solicitante_id',
        'tipo_ayuda',
        'fecha_solicitud',
        'fecha_resolucion',
        'importe_estimado',
        'estado',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_resolucion' => 'date',
        'importe_estimado' => 'decimal:2',
    ];

    /**
     * Relación Eloquent hacia Solicitante.
     * Detalle de infraestructura: útil para queries eficientes
     * (eager loading), pero la Entity de Domain (Solicitud) NO
     * conoce esta relación, solo el solicitanteId (string).
     */
    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(EloquentSolicitante::class, 'solicitante_id');
    }
}