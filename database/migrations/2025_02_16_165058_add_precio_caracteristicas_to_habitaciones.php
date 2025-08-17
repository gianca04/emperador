<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('habitaciones', function (Blueprint $table) {
            $table->decimal('precio_caracteristicas', 10, 2)
                ->after('precio_base')
                ->default(0.00)
                ->comment('Precio total de las características adicionales');
        });
    }

    public function down(): void
    {
        Schema::table('habitaciones', function (Blueprint $table) {
            $table->dropColumn('precio_caracteristicas');
        });
    }
};

