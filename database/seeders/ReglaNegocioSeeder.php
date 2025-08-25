<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReglaNegocio;
use App\Models\HabitacionTipo;

class ReglaNegocioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Regla de alquiler por horas estándar
        $reglaAlquilerStandard = ReglaNegocio::create([
            'nombre' => 'Alquiler por Horas - Estándar',
            'descripcion' => 'Tarifa estándar para alquiler por horas durante días laborables',
            'tipo' => ReglaNegocio::TIPO_ALQUILER_HORAS,
            'activa' => true,
            'horas_minimas' => 2,
            'horas_maximas' => 12,
            'precio_por_hora' => 25.00,
            'aplicabilidad_habitaciones' => ReglaNegocio::APLICABILIDAD_TODAS,
            'dias_semana' => [1, 2, 3, 4, 5], // Lunes a Viernes
            'hora_inicio' => '08:00:00',
            'hora_fin' => '18:00:00',
            'prioridad' => 1,
            'es_temporada_alta' => false,
            'es_fin_semana' => false,
            'es_feriado' => false,
        ]);

        // Regla de alquiler por horas - fin de semana
        $reglaAlquilerFinSemana = ReglaNegocio::create([
            'nombre' => 'Alquiler por Horas - Fin de Semana',
            'descripcion' => 'Tarifa especial para alquiler por horas durante fines de semana',
            'tipo' => ReglaNegocio::TIPO_ALQUILER_HORAS,
            'activa' => true,
            'horas_minimas' => 3,
            'horas_maximas' => 24,
            'precio_por_hora' => 35.00,
            'aplicabilidad_habitaciones' => ReglaNegocio::APLICABILIDAD_TODAS,
            'dias_semana' => [6, 0], // Sábado y Domingo
            'prioridad' => 5,
            'es_temporada_alta' => false,
            'es_fin_semana' => true,
            'es_feriado' => false,
        ]);

        // Regla de alquiler nocturno
        $reglaAlquilerNocturno = ReglaNegocio::create([
            'nombre' => 'Alquiler por Horas - Nocturno',
            'descripcion' => 'Tarifa especial para alquiler nocturno (18:00 - 08:00)',
            'tipo' => ReglaNegocio::TIPO_ALQUILER_HORAS,
            'activa' => true,
            'horas_minimas' => 4,
            'horas_maximas' => 14,
            'precio_por_hora' => 40.00,
            'aplicabilidad_habitaciones' => ReglaNegocio::APLICABILIDAD_TODAS,
            'hora_inicio' => '18:00:00',
            'hora_fin' => '08:00:00',
            'prioridad' => 7,
            'es_temporada_alta' => false,
            'es_fin_semana' => false,
            'es_feriado' => false,
        ]);

        // Regla de penalización por checkout tardío
        $reglaPenalizacionCheckout = ReglaNegocio::create([
            'nombre' => 'Penalización Checkout Tardío',
            'descripcion' => 'Penalización por hacer checkout después de las 12:00 PM',
            'tipo' => ReglaNegocio::TIPO_PENALIZACION_CHECKOUT,
            'activa' => true,
            'hora_checkout_limite' => '12:00:00',
            'penalizacion_tipo' => ReglaNegocio::PENALIZACION_FIJO,
            'penalizacion_monto' => 50.00,
            'aplicabilidad_habitaciones' => ReglaNegocio::APLICABILIDAD_TODAS,
            'prioridad' => 10,
        ]);

        // Regla de penalización por checkout muy tardío
        $reglaPenalizacionCheckoutTardio = ReglaNegocio::create([
            'nombre' => 'Penalización Checkout Muy Tardío',
            'descripcion' => 'Penalización por hora adicional después de las 15:00 PM',
            'tipo' => ReglaNegocio::TIPO_PENALIZACION_CHECKOUT,
            'activa' => true,
            'hora_checkout_limite' => '15:00:00',
            'penalizacion_tipo' => ReglaNegocio::PENALIZACION_POR_HORA,
            'penalizacion_monto' => 30.00,
            'aplicabilidad_habitaciones' => ReglaNegocio::APLICABILIDAD_TODAS,
            'prioridad' => 15,
        ]);

        // Regla especial para habitaciones VIP
        $reglaVIP = ReglaNegocio::create([
            'nombre' => 'Alquiler VIP - Temporada Alta',
            'descripcion' => 'Tarifa premium para habitaciones VIP durante temporada alta',
            'tipo' => ReglaNegocio::TIPO_ALQUILER_HORAS,
            'activa' => true,
            'horas_minimas' => 2,
            'horas_maximas' => 8,
            'precio_por_hora' => 80.00,
            'aplicabilidad_habitaciones' => ReglaNegocio::APLICABILIDAD_TIPOS_ESPECIFICOS,
            'prioridad' => 20,
            'es_temporada_alta' => true,
            'es_fin_semana' => false,
            'es_feriado' => false,
        ]);

        // Regla de descuento para estancias largas
        $reglaDescuentoLargo = ReglaNegocio::create([
            'nombre' => 'Descuento Estancia Larga',
            'descripcion' => 'Descuento del 15% para alquileres de más de 8 horas',
            'tipo' => ReglaNegocio::TIPO_DESCUENTO,
            'activa' => true,
            'horas_minimas' => 8,
            'penalizacion_tipo' => ReglaNegocio::PENALIZACION_PORCENTAJE,
            'penalizacion_monto' => 15.00, // 15% de descuento
            'aplicabilidad_habitaciones' => ReglaNegocio::APLICABILIDAD_TODAS,
            'prioridad' => 3,
        ]);

        // Asociar la regla VIP con tipos de habitación específicos (si existen)
        $tiposVIP = HabitacionTipo::whereIn('name', ['Suite', 'VIP', 'Deluxe', 'Premium'])->get();
        if ($tiposVIP->count() > 0) {
            $reglaVIP->tiposHabitacion()->attach($tiposVIP->pluck('id'));
        }

        $this->command->info('✅ Se crearon ' . ReglaNegocio::count() . ' reglas de negocio de ejemplo');
        $this->command->info('📋 Tipos de reglas creadas:');
        $this->command->info('   • Alquiler por horas estándar (días laborables)');
        $this->command->info('   • Alquiler por horas fin de semana');
        $this->command->info('   • Alquiler nocturno');
        $this->command->info('   • Penalización checkout tardío');
        $this->command->info('   • Penalización checkout muy tardío');
        $this->command->info('   • Tarifa VIP temporada alta');
        $this->command->info('   • Descuento estancia larga');
    }
}
