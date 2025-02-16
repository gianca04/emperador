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
            $table->decimal('precio_caracteristicas', 10, 2)->after('precio_base')->nullable();
            $table->decimal('precio_final', 10, 2)->after('precio_caracteristicas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habitacion_tipos', function (Blueprint $table) {
            Schema::table('habitacion_tipos', function (Blueprint $table) {
                $table->dropColumn(['precio_caracteristicas', 'precio_final']);
            });
        });
    }
};
