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
        Schema::create('regla_negocio_habitacion', function (Blueprint $table) {
            $table->id();

            // Claves foráneas
            $table->foreignId('regla_negocio_id')
                  ->constrained('regla_negocios')
                  ->cascadeOnDelete();

            $table->foreignId('habitacion_id')
                  ->constrained('habitaciones')
                  ->cascadeOnDelete();

            // Configuración específica para esta habitación
            $table->decimal('precio_override', 8, 2)->nullable()
                  ->comment('Precio específico para esta habitación (sobrescribe el general y del tipo)');
            $table->boolean('activa')->default(true);

            $table->timestamps();

            // Índices y restricciones
            $table->unique(['regla_negocio_id', 'habitacion_id'], 'regla_habitacion_unique');
            $table->index(['regla_negocio_id', 'activa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regla_negocio_habitacion');
    }
};
