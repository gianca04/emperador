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
        Schema::create('habitacion_tipo_caracteristica', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('habitacion_tipo_id')->constrained()->onDelete('cascade');
            $table->foreignId('caracteristica_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitacion_tipo_caracteristica');
    }
};
