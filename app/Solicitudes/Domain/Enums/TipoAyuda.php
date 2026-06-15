<?php

namespace App\Solicitudes\Domain\Enums;

/**
 * Enum de Domain: TipoAyuda.
 *
 * Esto es un "Value Object" en forma de enum nativo de PHP (8.1+).
 * Representa los ÚNICOS valores válidos para el tipo de ayuda,
 * según el enunciado de la prueba.
 *
 * Ventaja sobre un ENUM de SQL: si añadimos un nuevo tipo de ayuda,
 * solo tocamos este archivo PHP (un "case" más), sin migraciones
 * que alteren la tabla. La BD sigue siendo un string normal.
 *
 * "backed enum" (: string) significa que cada caso tiene un valor
 * string asociado, que es lo que se guarda en la BD.
 */
enum TipoAyuda: string
{
    case Alquiler = 'Alquiler';
    case ChequeBebe = 'ChequeBebe';
    case IngresoMinimoVital = 'IngresoMinimoVital';
    case BonoCulturalJoven = 'BonoCulturalJoven';

    /**
     * Etiqueta legible para mostrar al usuario (frontend).
     * Esto es típico de DDD: el enum no es solo "datos",
     * también puede tener comportamiento/lógica asociada.
     */
    public function label(): string
    {
        return match ($this) {
            self::Alquiler => 'Ayuda al Alquiler',
            self::ChequeBebe => 'Cheque Bebé',
            self::IngresoMinimoVital => 'Ingreso Mínimo Vital',
            self::BonoCulturalJoven => 'Bono Cultural Joven',
        };
    }
}