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
        Schema::create('alquileres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habitacion_id')->constrained('habitaciones');
            $table->enum('tipo_alquiler', ['HORAS', 'DIAS'])->default('HORAS');
            
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            
            $table->integer('horas')->nullable(); // Solo se usa si es por horas
            
            $table->decimal('monto_total', 10, 2)->default(0);
            $table->dateTime('checkin_at')->nullable();
            $table->dateTime('checkout_at')->nullable();
            $table->enum('estado', ['pendiente', 'en_curso', 'finalizado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alquileres');
    }
};
