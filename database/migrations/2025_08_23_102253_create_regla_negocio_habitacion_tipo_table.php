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
        Schema::create('regla_negocio_habitacion_tipo', function (Blueprint $table) {
            $table->id();

            // Claves foráneas
            $table->foreignId('regla_negocio_id')
                  ->constrained('regla_negocios')
                  ->cascadeOnDelete();

            $table->foreignId('habitacion_tipo_id')
                  ->constrained('habitacion_tipos')
                  ->cascadeOnDelete();

            // Configuración específica para esta relación
            $table->decimal('precio_override', 8, 2)->nullable()
                  ->comment('Precio específico para este tipo de habitación (sobrescribe el general)');
            $table->boolean('activa')->default(true);

            $table->timestamps();

            // Índices y restricciones
            $table->unique(['regla_negocio_id', 'habitacion_tipo_id'], 'regla_tipo_unique');
            $table->index(['regla_negocio_id', 'activa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regla_negocio_habitacion_tipo');
    }
};
