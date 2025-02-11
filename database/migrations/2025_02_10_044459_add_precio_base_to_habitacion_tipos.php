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
            $table->decimal('precio_base', 10, 2)->after('name'); // Precio en formato decimal
            $table->boolean('activa')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habitacion_tipos', function (Blueprint $table) {
            $table->dropColumn('precio_base');
            $table->dropColumn('activa');
            
        });
    }
};
