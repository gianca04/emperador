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
        Schema::table('habitacion_tipos', function (Blueprint $table) {
            $table->integer('capacidad')->default(1); // Añadimos capacidad
        });

        Schema::table('habitaciones', function (Blueprint $table) {
            $table->dropColumn('capacidad'); // Eliminamos capacidad de habitaciones
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habitaciones', function (Blueprint $table) {
            $table->integer('capacidad')->default(1); // Restauramos capacidad en habitaciones
        });
        Schema::table('habitacion_tipos', function (Blueprint $table) {
            $table->dropColumn('capacidad'); // Eliminamos capacidad de habitacion_tipos
        });
    }
};
