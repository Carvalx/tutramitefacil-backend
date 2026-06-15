<?php

namespace App\Solicitudes\Presentation\Requests;

use App\Solicitudes\Domain\Enums\Estado;
use App\Solicitudes\Domain\Enums\TipoAyuda;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'solicitante_id' => ['sometimes', 'required', 'uuid', 'exists:solicitantes,id'],
            'tipo_ayuda' => ['sometimes', 'required', Rule::enum(TipoAyuda::class)],
            'fecha_solicitud' => ['sometimes', 'required', 'date'],
            'fecha_resolucion' => ['sometimes', 'nullable', 'date'],
            'importe_estimado' => ['sometimes', 'required', 'numeric', 'min:0'],
            'estado' => ['sometimes', 'required', Rule::enum(Estado::class)],
        ];
    }
}