<?php

namespace App\Solicitudes\Domain\Entities;

use App\Solicitudes\Domain\Enums\Estado;
use App\Solicitudes\Domain\Enums\TipoAyuda;
use DateTimeImmutable;

/**
 * Entity de Domain: Solicitud.
 *
 * Igual que Solicitante: clase PHP pura, sin Eloquent.
 * Nota cómo TipoAyuda y Estado son los ENUMS de Domain (no strings),
 * lo que garantiza que un objeto Solicitud SIEMPRE tiene un tipo
 * y estado VÁLIDOS (PHP no te deja instanciar un enum con un valor
 * que no exista como "case").
 */
final class Solicitud
{
    public function __construct(
        private readonly string $id,
        private readonly string $solicitanteId,
        private readonly TipoAyuda $tipoAyuda,
        private readonly DateTimeImmutable $fechaSolicitud,
        private readonly ?DateTimeImmutable $fechaResolucion,
        private readonly float $importeEstimado,
        private readonly Estado $estado,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function solicitanteId(): string
    {
        return $this->solicitanteId;
    }

    public function tipoAyuda(): TipoAyuda
    {
        return $this->tipoAyuda;
    }

    public function fechaSolicitud(): DateTimeImmutable
    {
        return $this->fechaSolicitud;
    }

    public function fechaResolucion(): ?DateTimeImmutable
    {
        return $this->fechaResolucion;
    }

    public function importeEstimado(): float
    {
        return $this->importeEstimado;
    }

    public function estado(): Estado
    {
        return $this->estado;
    }
}