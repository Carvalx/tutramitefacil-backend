<?php

namespace App\Solicitantes\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request: valida los datos al ACTUALIZAR un Solicitante.
 *
 * Diferencia con Store: usamos 'sometimes' en los campos, lo que significa
 * "valida este campo SOLO SI viene en la petición". Esto permite
 * actualizaciones parciales (PATCH-like), aunque la ruta sea PUT.
 *
 * Para 'email', excluimos el propio registro de la regla 'unique'
 * usando el {id} de la ruta -> así el usuario puede "actualizar"
 * su propio email sin que choque con su email actual.
 */
class UpdateSolicitanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // route('solicitante') obtiene el parámetro {solicitante} de la ruta
        // (el UUID), para excluirlo de la comprobación 'unique'.
        $id = $this->route('solicitante');

        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'apellidos' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', "unique:solicitantes,email,{$id}"],
            'telefono' => ['sometimes', 'required', 'string', 'max:20'],
            'comunidad_autonoma' => ['sometimes', 'required', 'string', 'max:255'],
            'fecha_registro' => ['sometimes', 'required', 'date'],
        ];
    }
}