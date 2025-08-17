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
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique()->index();
            $table->enum('estado', ['Disponible', 'Ocupada', 'Limpiar', 'Deshabilitada', 'Mantenimiento'])->default('Disponible');
            $table->text('descripcion')->nullable();
            $table->foreignId('habitacion_tipo_id')->constrained()->onDelete('cascade');
            $table->text('notas')->nullable();
            $table->integer('capacidad')->default(1);
            $table->enum('ubicacion', ['Segundo Piso', 'Tercer Piso', 'Cuarto Piso', 'Quinto Piso', 'Mantenimiento'])->nullable();
            $table->decimal('precio_base', 10, 2)->default(0);
            $table->decimal('precio_final', 10, 2)->default(0);
            $table->timestamp('ultima_limpieza')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitaciones');
    }
};
