<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alquiler_persona', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alquiler_id')->constrained('alquileres');
            $table->foreignId('persona_id')->constrained();
            $table->boolean('es_titular')->default(false); // Determina si la persona es el titular
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alquiler_persona');
    }
};
