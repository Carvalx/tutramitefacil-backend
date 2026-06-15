<?php

namespace App\Solicitudes\Presentation\Resources;

use App\Solicitudes\Domain\Entities\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Solicitud
 */
class SolicitudResource extends JsonResource
{
    public function __construct(Solicitud $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id(),
            'solicitante_id' => $this->resource->solicitanteId(),

            // Devolvemos tanto el "valor crudo" del enum (->value, ej: 'ChequeBebe')
            // como la etiqueta legible (->label(), ej: 'Cheque Bebé').
            'tipo_ayuda' => $this->resource->tipoAyuda()->value,
            'tipo_ayuda_label' => $this->resource->tipoAyuda()->label(),

            'fecha_solicitud' => $this->resource->fechaSolicitud()->format('Y-m-d'),
            'fecha_resolucion' => $this->resource->fechaResolucion()?->format('Y-m-d'),
            'importe_estimado' => $this->resource->importeEstimado(),

            'estado' => $this->resource->estado()->value,
            'estado_label' => $this->resource->estado()->label(),
        ];
    }
}