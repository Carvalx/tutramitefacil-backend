<?php

namespace App\Solicitantes\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent de Solicitante.
 *
 * IMPORTANTE: esto NO es la "Entity" de Domain. Esto es el "traductor"
 * técnico que sabe hablar con la tabla 'solicitantes' de MySQL.
 * La Entity de Domain (Domain/Entities/Solicitante.php) es una clase
 * PHP pura, sin esto.
 *
 * ¿Por qué vive en Infrastructure y no en app/Models?
 * Porque Eloquent es un detalle de IMPLEMENTACIÓN (cómo guardamos los
 * datos), no parte del negocio. Si un día cambiáramos de ORM, esta
 * clase cambiaría, pero la Entity de Domain no tendría por qué hacerlo.
 */
class EloquentSolicitante extends Model
{
    /**
     * HasUuids: trait de Laravel 11 que hace dos cosas automáticamente:
     * 1. Genera un UUID al crear un registro (si no se especifica 'id').
     * 2. Le dice a Eloquent que la PK no es un entero autoincremental.
     */
    use HasUuids, HasFactory;

    /**
     * Nombre de la tabla. Por convención Eloquent buscaría 'eloquent_solicitantes'
     * (snake_case del nombre de la clase), así que lo indicamos explícitamente.
     */
    protected $table = 'solicitantes';

    /**
     * $incrementing = false: la PK (id) NO es autoincremental,
     * es un UUID generado por HasUuids.
     */
    public $incrementing = false;

    /**
     * Tipo de la PK: string (el UUID se almacena como CHAR(36)/string).
     */
    protected $keyType = 'string';

    /**
     * $fillable: campos que se pueden asignar masivamente
     * (ej: EloquentSolicitante::create([...])).
     * Es una medida de seguridad: solo estos campos se aceptan
     * desde arrays externos (evita "mass assignment" de columnas
     * sensibles que no deberían poder rellenarse así).
     */
    protected $fillable = [
        'id',
        'nombre',
        'apellidos',
        'email',
        'telefono',
        'comunidad_autonoma',
        'fecha_registro',
    ];

    /**
     * $casts: conversiones automáticas de tipos.
     * 'fecha_registro' => 'date' hace que Eloquent la trate como
     * objeto Carbon (fecha) en PHP, aunque en BD sea un DATE.
     */
    protected $casts = [
        'fecha_registro' => 'date',
    ];

    /**
     * Indica explícitamente qué Factory usar.
     *
     * Necesario porque este modelo vive en una ruta no estándar
     * (App\Solicitantes\Infrastructure\Persistence\...), así que
     * Laravel no puede adivinar el nombre del factory por convención.
     */
    protected static function newFactory()
    {
        return \Database\Factories\SolicitanteFactory::new();
    }

}