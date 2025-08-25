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
        Schema::create('regla_negocios', function (Blueprint $table) {
            $table->id();

            // Información básica de la regla
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['alquiler_horas', 'penalizacion_checkout', 'descuento', 'recargo'])
                  ->default('alquiler_horas');
            $table->boolean('activa')->default(true);

            // Configuración de alquiler por horas
            $table->integer('horas_minimas')->nullable()->comment('Mínimo de horas para aplicar la regla');
            $table->integer('horas_maximas')->nullable()->comment('Máximo de horas para aplicar la regla');
            $table->decimal('precio_por_hora', 8, 2)->nullable()->comment('Precio por hora en esta regla');
            $table->decimal('precio_fijo', 10, 2)->nullable()->comment('Precio fijo independiente de horas');

            // Configuración de checkout y penalizaciones
            $table->time('hora_checkout_limite')->default('12:00:00')->comment('Hora límite de checkout');
            $table->decimal('penalizacion_monto', 8, 2)->nullable()->comment('Monto de penalización');
            $table->enum('penalizacion_tipo', ['fijo', 'porcentaje', 'por_hora'])
                  ->default('fijo')->comment('Tipo de cálculo de penalización');

            // Configuración de aplicabilidad
            $table->enum('aplicabilidad_habitaciones', ['todas', 'tipos_especificos', 'habitaciones_especificas'])
                  ->default('todas')->comment('A qué habitaciones aplica esta regla');

            // Configuración temporal
            $table->date('fecha_inicio')->nullable()->comment('Fecha desde cuando aplica la regla');
            $table->date('fecha_fin')->nullable()->comment('Fecha hasta cuando aplica la regla');
            $table->json('dias_semana')->nullable()->comment('Días de la semana cuando aplica [1-7, donde 1=Lunes]');
            $table->time('hora_inicio')->nullable()->comment('Hora desde cuando aplica en el día');
            $table->time('hora_fin')->nullable()->comment('Hora hasta cuando aplica en el día');

            // Configuración de temporadas/eventos especiales
            $table->boolean('es_temporada_alta')->default(false);
            $table->boolean('es_fin_semana')->default(false);
            $table->boolean('es_feriado')->default(false);

            // Metadatos
            $table->integer('prioridad')->default(1)->comment('Prioridad de aplicación (mayor número = mayor prioridad)');
            $table->json('configuracion_adicional')->nullable()->comment('Configuraciones extra en JSON');

            // Auditoría
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['tipo', 'activa']);
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->index('prioridad');
            $table->index(['activa', 'tipo', 'prioridad']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regla_negocios');
    }
};
