<?php

namespace App\Solicitantes\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request: valida los datos al CREAR un Solicitante.
 *
 * Las Form Requests son la forma "Laravel" de separar la validación
 * del Controller. Si la validación falla, Laravel devuelve automáticamente
 * una respuesta 422 con los errores, sin que el Controller tenga que
 * hacer nada extra.
 */
class StoreSolicitanteRequest extends FormRequest
{
    /**
     * authorize(): controla si el usuario tiene PERMISO para hacer esta
     * petición (autorización, no autenticación). Por ahora devolvemos
     * true para todos; cuando añadamos JWT, aquí podríamos comprobar
     * roles si hiciera falta. La autenticación (JWT) la pondremos
     * a nivel de middleware en las rutas, no aquí.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * rules(): reglas de validación para cada campo.
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            // unique:solicitantes,email -> no puede repetirse el email
            // en la tabla 'solicitantes'.
            'email' => ['required', 'email', 'max:255', 'unique:solicitantes,email'],
            'telefono' => ['required', 'string', 'max:20'],
            'comunidad_autonoma' => ['required', 'string', 'max:255'],
            'fecha_registro' => ['required', 'date'],
        ];
    }
}