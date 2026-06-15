<?php

namespace App\Solicitantes\Domain\Entities;

use DateTimeImmutable;

/**
 * Entity de Domain: Solicitante.
 *
 * Esta clase representa "qué es un Solicitante" en términos de negocio,
 * SIN saber nada de Eloquent, MySQL, HTTP, ni Laravel.
 *
 * Es una clase PHP normal y corriente. Podríamos copiar este archivo
 * a un proyecto sin Laravel y seguiría funcionando igual.
 *
 * Aquí es donde, en un proyecto más grande, pondríamos REGLAS DE NEGOCIO,
 * por ejemplo: "el email debe tener un formato válido", "la fecha de
 * registro no puede ser futura", etc. Por ahora la mantenemos simple
 * (un constructor + getters), pero la estructura está lista para crecer.
 */
final class Solicitante
{
    public function __construct(
        private readonly string $id,
        private readonly string $nombre,
        private readonly string $apellidos,
        private readonly string $email,
        private readonly string $telefono,
        private readonly string $comunidadAutonoma,
        private readonly DateTimeImmutable $fechaRegistro,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function apellidos(): string
    {
        return $this->apellidos;
    }

    public function nombreCompleto(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function email(): string
    {
        return $this->email;
    }

    public function telefono(): string
    {
        return $this->telefono;
    }

    public function comunidadAutonoma(): string
    {
        return $this->comunidadAutonoma;
    }

    public function fechaRegistro(): DateTimeImmutable
    {
        return $this->fechaRegistro;
    }
}