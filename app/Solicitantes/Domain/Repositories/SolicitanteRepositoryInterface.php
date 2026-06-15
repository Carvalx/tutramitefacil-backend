<?php

namespace App\Solicitantes\Domain\Repositories;

use App\Solicitantes\Domain\Entities\Solicitante;

/**
 * Interfaz del repositorio de Solicitantes.
 *
 * Esta interfaz vive en Domain porque define QUÉ operaciones necesita
 * el negocio (guardar, buscar, listar, borrar un Solicitante), pero
 * NO CÓMO se hacen. El "cómo" (Eloquent + MySQL) lo implementa
 * Infrastructure/Persistence/EloquentSolicitanteRepository.php.
 *
 * Ventaja: Application/Services solo conoce esta interfaz, nunca
 * Eloquent directamente. Si cambiáramos de MySQL a MongoDB, solo
 * tocaríamos la implementación, no el resto del código.
 */
interface SolicitanteRepositoryInterface
{
    /**
     * Devuelve todos los solicitantes.
     *
     * @return Solicitante[]
     */
    public function all(): array;

    /**
     * Busca un solicitante por su UUID.
     * Devuelve null si no existe.
     */
    public function findById(string $id): ?Solicitante;

    /**
     * Crea un nuevo solicitante y devuelve la Entity creada
     * (con su UUID ya generado).
     */
    public function create(array $data): Solicitante;

    /**
     * Actualiza un solicitante existente.
     * Devuelve la Entity actualizada, o null si no existía.
     */
    public function update(string $id, array $data): ?Solicitante;

    /**
     * Elimina un solicitante por su UUID.
     * Devuelve true si se eliminó, false si no existía.
     */
    public function delete(string $id): bool;
}