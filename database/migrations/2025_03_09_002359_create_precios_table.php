<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('precios', function (Blueprint $table) {
            $table->id(); 
            $table->decimal('precio_por_hora', 10, 2)->nullable()->index(); // Índice para búsquedas rápidas
            $table->decimal('precio_por_mora', 10, 2)->nullable()->index(); // Índice para mora
            $table->decimal('precio_hora_adicional', 10, 2)->nullable()->index(); // Índice para hora adicional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precios');
    }
};
