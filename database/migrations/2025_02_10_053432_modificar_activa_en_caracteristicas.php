<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('caracteristicas', function (Blueprint $table) {
            $table->boolean('activa')->default(true)->change();
        });
    }

    public function down(): void
    {
        Schema::table('caracteristicas', function (Blueprint $table) {
            $table->string('activa')->default('1')->change(); // Si necesitas revertir
        });
    }
};
