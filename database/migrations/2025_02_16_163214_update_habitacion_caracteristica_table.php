<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('habitacion_caracteristica', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('habitacion_id')->nullable();
            $table->unsignedBigInteger('caracteristica_id')->nullable();
            $table->timestamps();

            $table->foreign('habitacion_id')
                ->references('id')
                ->on('habitaciones')
                ->onDelete('set null');

            $table->foreign('caracteristica_id')
                ->references('id')
                ->on('caracteristicas')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habitacion_caracteristica');
    }
};
