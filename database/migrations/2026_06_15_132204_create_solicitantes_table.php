<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración: crea la tabla 'solicitantes'.
     *
     * Usamos UUID como clave primaria (en vez del 'id' autoincremental
     * por defecto de Laravel) porque el enunciado lo pide explícitamente.
     * $table->uuid('id')->primary() crea una columna CHAR(36) que
     * almacena el UUID como string, y la marca como PK.
     */
    public function up(): void
    {
        Schema::create('solicitantes', function (Blueprint $table) {
            // Clave primaria UUID (en vez de bigint autoincremental)
            $table->uuid('id')->primary();

            // Datos personales del solicitante
            $table->string('nombre');
            $table->string('apellidos');

            // unique(): no puede haber dos solicitantes con el mismo email
            $table->string('email')->unique();

            $table->string('telefono');

            // Comunidad Autónoma: string libre por ahora.
            $table->string('comunidad_autonoma');

            // FechaRegistro: fecha en la que el solicitante se registró
            // en la plataforma (campo propio definido en el enunciado,
            // distinto de created_at que es la fecha de creación del registro en BD).
            $table->date('fecha_registro');

            // created_at / updated_at automáticos de Laravel
            $table->timestamps();
        });
    }

    /**
     * Revierte la migración: elimina la tabla 'solicitantes'.
     * Se ejecuta con 'php artisan migrate:rollback'.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitantes');
    }
};