<?php

namespace App\Solicitantes\Presentation\Resources;

use App\Solicitantes\Domain\Entities\Solicitante;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource: define el formato JSON que se devuelve al cliente.
 *
 * Normalmente un JsonResource de Laravel envuelve un modelo Eloquent,
 * pero aquí lo construimos a partir de la Entity de Domain (Solicitante),
 * que es justo el punto: Presentation no debería ni "ver" Eloquent.
 *
 * @mixin Solicitante
 */
class SolicitanteResource extends JsonResource
{
    /**
     * @param Solicitante $resource
     */
    public function __construct(Solicitante $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id(),
            'nombre' => $this->resource->nombre(),
            'apellidos' => $this->resource->apellidos(),
            'nombre_completo' => $this->resource->nombreCompleto(),
            'email' => $this->resource->email(),
            'telefono' => $this->resource->telefono(),
            'comunidad_autonoma' => $this->resource->comunidadAutonoma(),
            'fecha_registro' => $this->resource->fechaRegistro()->format('Y-m-d'),
        ];
    }
}