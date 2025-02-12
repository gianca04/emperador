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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->enum('tipo_documento', ['DNI','CARNET EXT', 'PASAPORTE', 'OTROS']);
            $table->string('numero_documento', 15);
            $table->string('email')->unique()->nullable();
            $table->string('telefono', 9)->nullable();
            $table->string('telefono_secundario', 9)->nullable();
            $table->timestamps();
        });
    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
