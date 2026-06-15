<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla 'solicitudes'.
     *
     * TipoAyuda y Estado son ENUMS DE DOMINIO (PHP), no $table->enum().
     * En la BD los guardamos como string: así, si añadimos un nuevo
     * tipo de ayuda en el futuro, NO hace falta una migración para
     * alterar un ENUM de MySQL (que es costoso en tablas grandes).
     * La validación de qué valores son válidos vive en el enum de PHP.
     */
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // FK hacia solicitantes.
            $table->uuid('solicitante_id');
            $table->foreign('solicitante_id')
                ->references('id')->on('solicitantes')
                ->onDelete('cascade');

            // TipoAyuda: Alquiler, ChequeBebe, IngresoMinimoVital, BonoCulturalJoven
            $table->string('tipo_ayuda');

            $table->date('fecha_solicitud');

            // Nullable: una solicitud recién creada no tiene fecha de resolución aún.
            $table->date('fecha_resolucion')->nullable();

            // decimal(10,2): hasta 99,999,999.99 — suficiente para ayudas sociales.
            $table->decimal('importe_estimado', 10, 2);

            // Estado: Pendiente, EnRevision, Concedida, Denegada.
            // Default 'Pendiente' porque toda solicitud nace en ese estado.
            $table->string('estado')->default('Pendiente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};