<?php

namespace App\Solicitudes\Domain\Enums;

/**
 * Enum de Domain: Estado de una Solicitud.
 *
 * Representa el ciclo de vida de una solicitud de ayuda.
 * Toda solicitud nace en 'Pendiente' (ver default en la migración).
 */
enum Estado: string
{
    case Pendiente = 'Pendiente';
    case EnRevision = 'EnRevision';
    case Concedida = 'Concedida';
    case Denegada = 'Denegada';

    public function label(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::EnRevision => 'En Revisión',
            self::Concedida => 'Concedida',
            self::Denegada => 'Denegada',
        };
    }

    /**
     * Regla de negocio: ¿es este un estado "final" (ya resuelto)?
     * Esto es justo el tipo de lógica que vive bien en un enum de Domain.
     * La usaremos más adelante para decidir si encolar el Job de
     * "verificación" (solo si el estado cambia A un estado final).
     */
    public function esFinal(): bool
    {
        return match ($this) {
            self::Concedida, self::Denegada => true,
            self::Pendiente, self::EnRevision => false,
        };
    }
}