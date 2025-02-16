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
        Schema::create('alquiler_caracteristica', function (Blueprint $table) {
            $table->id();
        
            // Crea la clave foránea haciendo referencia a 'alquileres'
            $table->foreignId('alquiler_id')
                  ->nullable()
                  ->constrained('alquileres')
                  ->nullOnDelete();
        
            // Otra clave foránea para 'caracteristicas'
            $table->foreignId('caracteristica_id')
                  ->constrained()
                  ->onDelete('cascade');
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alquiler_caracteristica');
    }
};
