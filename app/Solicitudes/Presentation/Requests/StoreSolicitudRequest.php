<?php

namespace App\Solicitudes\Presentation\Requests;

use App\Solicitudes\Domain\Enums\Estado;
use App\Solicitudes\Domain\Enums\TipoAyuda;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'solicitante_id' => ['required', 'uuid', 'exists:solicitantes,id'],

            // Rule::enum() valida que el valor recibido sea uno de los
            // "cases" del enum TipoAyuda. Si el frontend envía
            // 'tipo_ayuda' => 'Vacaciones' (inválido), la validación falla
            // ANTES de llegar al enum de PHP.
            'tipo_ayuda' => ['required', Rule::enum(TipoAyuda::class)],

            'fecha_solicitud' => ['required', 'date'],
            'fecha_resolucion' => ['nullable', 'date'],
            'importe_estimado' => ['required', 'numeric', 'min:0'],

            // 'estado' es opcional al crear (por defecto 'Pendiente'
            // según la migración), pero si se envía, debe ser válido.
            'estado' => ['sometimes', Rule::enum(Estado::class)],
        ];
    }
}