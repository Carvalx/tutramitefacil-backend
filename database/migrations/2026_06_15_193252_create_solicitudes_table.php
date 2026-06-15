<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('solicitante_id');
            $table->foreign('solicitante_id')
                ->references('id')
                ->on('solicitantes')
                ->cascadeOnDelete();

            $table->string('tipo_ayuda');
            $table->date('fecha_solicitud');
            $table->date('fecha_resolucion')->nullable();
            $table->decimal('importe_estimado', 10, 2);
            $table->string('estado')->default('Pendiente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
