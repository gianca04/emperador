<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReglaNegocioService;
use App\Models\Habitacion;
use App\Models\ReglaNegocio;
use Carbon\Carbon;

class SimularReglasNegocio extends Command
{
    protected $signature = 'reglas:simular
                            {--habitacion= : ID de habitación específica}
                            {--horas=4 : Número de horas para simular}
                            {--fecha= : Fecha para simular (Y-m-d H:i)}
                            {--checkout= : Hora de checkout para penalización (H:i)}
                            {--monto-base=200 : Monto base para calcular penalizaciones}
                            {--resumen : Mostrar solo resumen general}
                            {--ejemplo-real : Ejecutar ejemplo de caso real con cliente}';

    protected $description = 'Simular aplicación de reglas de negocio para alquiler por horas y penalizaciones';

    public function handle()
    {
        $reglaNegocioService = app(ReglaNegocioService::class);

        if ($this->option('resumen')) {
            $this->mostrarResumenGeneral($reglaNegocioService);
            return 0;
        }

        if ($this->option('ejemplo-real')) {
            return $this->ejecutarEjemploReal($reglaNegocioService);
        }

        $this->info('🏨 SIMULADOR DE REGLAS DE NEGOCIO');
        $this->line('');

        // Mostrar resumen de reglas activas
        $resumen = $reglaNegocioService->obtenerResumenReglas();
        $this->info("📊 Reglas activas en el sistema: {$resumen['total_activas']}");
        $this->line("   • Alquiler por horas: {$resumen['por_tipo']['alquiler_horas']}");
        $this->line("   • Penalización checkout: {$resumen['por_tipo']['penalizacion_checkout']}");
        $this->line("   • Descuentos: {$resumen['por_tipo']['descuentos']}");
        $this->line("   • Recargos: {$resumen['por_tipo']['recargos']}");
        $this->line('');

        // Obtener habitación para simular
        $habitacion = $this->obtenerHabitacion();
        if (!$habitacion) {
            $this->error('No se encontraron habitaciones para simular');
            return 1;
        }

        $this->info("🏠 Simulando para: Habitación {$habitacion->numero} - {$habitacion->tipo?->name}");
        $this->line('');

        // Configurar fecha de simulación
        $fecha = $this->option('fecha')
            ? Carbon::createFromFormat('Y-m-d H:i', $this->option('fecha'))
            : now();

        $this->info("📅 Fecha de simulación: {$fecha->format('d/m/Y H:i')} ({$fecha->dayName})");
        $this->line('');

        // Simular alquiler por horas
        $this->simularAlquilerPorHoras($reglaNegocioService, $habitacion, $fecha);

        // Simular penalización de checkout
        if ($this->option('checkout')) {
            $this->simularPenalizacionCheckout($reglaNegocioService, $habitacion, $fecha);
        }

        return 0;
    }

    private function ejecutarEjemploReal(ReglaNegocioService $service)
    {
        $this->info('🎭 EJEMPLO DE CASO REAL: Cliente que cambia de modalidad');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('');

        // Habitación de ejemplo
        $habitacion = Habitacion::with('tipo')->first();
        if (!$habitacion) {
            $this->error('❌ No hay habitaciones en el sistema. Ejecuta primero los seeders.');
            return 1;
        }

        // Escenario: Viernes 15:00
        $fechaInicio = Carbon::parse('2025-08-22 15:00'); // Viernes

        $this->info("👤 Cliente: Juan Pérez");
        $this->info("🏠 Habitación: {$habitacion->numero} - {$habitacion->tipo?->name}");
        $this->info("📅 Fecha: {$fechaInicio->format('d/m/Y H:i')} ({$fechaInicio->locale('es')->dayName})");
        $this->line('');

        // Paso 1: Reserva inicial (4 horas)
        $this->info('🕒 PASO 1: Reserva inicial - 4 horas (15:00-19:00)');
        $this->line('─────────────────────────────────────────────────────');
        $calculo1 = $service->calcularPrecioAlquilerHoras($habitacion, 4, $fechaInicio);
        $this->mostrarDetalleCalculo($calculo1, 4);
        $totalPagado = $calculo1['precio_final'];

        $this->line('');
        $this->info("💰 Cliente paga: S/ {$totalPagado}");
        $this->line('');

        // Paso 2: Extensión (2 horas más)
        $this->info('🕕 PASO 2: Cliente decide quedarse 2 horas más (hasta 21:00)');
        $this->line('─────────────────────────────────────────────────────────────');
        $calculo2 = $service->calcularPrecioAlquilerHoras($habitacion, 6, $fechaInicio);
        $this->mostrarDetalleCalculo($calculo2, 6);
        $diferencia1 = $calculo2['precio_final'] - $totalPagado;
        $totalPagado = $calculo2['precio_final'];

        $this->line('');
        $this->info("💳 Cliente paga adicional: S/ {$diferencia1}");
        $this->info("💰 Total pagado hasta ahora: S/ {$totalPagado}");
        $this->line('');

        // Paso 3: Cambio a noche completa
        $this->info('🌙 PASO 3: Cliente decide quedarse toda la noche (hasta 12:00 del día siguiente)');
        $this->line('─────────────────────────────────────────────────────────────────────────────');
        $calculo3 = $service->calcularPrecioAlquilerHoras($habitacion, 21, $fechaInicio);
        $this->mostrarDetalleCalculo($calculo3, 21);
        $diferencia2 = $calculo3['precio_final'] - $totalPagado;

        $this->line('');
        $this->info("💳 Cliente paga adicional: S/ {$diferencia2}");
        $this->info("💰 Total final: S/ {$calculo3['precio_final']}");

        // Mostrar ahorro
        $precioSinReglas = 21 * 35; // 21 horas a tarifa normal
        $ahorro = $precioSinReglas - $calculo3['precio_final'];
        if ($ahorro > 0) {
            $this->line('');
            $this->info("🎉 ¡BENEFICIO PARA EL CLIENTE!");
            $this->line("   Sin reglas especiales: S/ {$precioSinReglas} (21h × S/35)");
            $this->line("   Con reglas aplicadas: S/ {$calculo3['precio_final']}");
            $this->line("   💰 Ahorro: S/ {$ahorro}");
        }

        $this->line('');
        $this->info('📊 RESUMEN DE TRANSACCIONES:');
        $this->table(
            ['Momento', 'Horas', 'Precio Total', 'Pago Adicional', 'Acumulado'],
            [
                ['15:00 - Inicial', '4h', "S/ {$calculo1['precio_final']}", "S/ {$calculo1['precio_final']}", "S/ {$calculo1['precio_final']}"],
                ['18:00 - Extensión', '6h', "S/ {$calculo2['precio_final']}", "S/ {$diferencia1}", "S/ {$calculo2['precio_final']}"],
                ['20:00 - Noche completa', '21h', "S/ {$calculo3['precio_final']}", "S/ {$diferencia2}", "S/ {$calculo3['precio_final']}"],
            ]
        );

        $this->line('');
        $this->info('💡 ANÁLISIS DEL CASO:');
        $this->line('   • El cliente obtiene flexibilidad para cambiar de modalidad');
        $this->line('   • Solo paga diferencias cuando extiende su estadía');
        $this->line('   • Las reglas de negocio optimizan los precios automáticamente');
        $this->line('   • El hotel maximiza ocupación y satisfacción del cliente');

        return 0;
    }

    private function obtenerHabitacion(): ?Habitacion
    {
        if ($this->option('habitacion')) {
            return Habitacion::with('tipo')->find($this->option('habitacion'));
        }

        // Tomar la primera habitación disponible
        return Habitacion::with('tipo')->first();
    }

    private function simularAlquilerPorHoras(ReglaNegocioService $service, Habitacion $habitacion, Carbon $fecha)
    {
        $this->info('💰 SIMULACIÓN ALQUILER POR HORAS');
        $this->line('');

        $horas = (int) $this->option('horas');

        // Verificar disponibilidad
        $validacion = $service->validarDisponibilidadAlquilerHoras($habitacion, $fecha, $horas);

        if (!$validacion['es_valido']) {
            $this->warn("❌ No es posible alquilar por {$horas} horas:");
            foreach ($validacion['errores'] as $error) {
                $this->line("   • {$error}");
            }
            $this->line('');
            return;
        }

        if (!empty($validacion['advertencias'])) {
            $this->warn('⚠️  Advertencias:');
            foreach ($validacion['advertencias'] as $advertencia) {
                $this->line("   • {$advertencia}");
            }
            $this->line('');
        }

        // Calcular precio
        $calculo = $service->calcularPrecioAlquilerHoras($habitacion, $horas, $fecha);
        $this->mostrarDetalleCalculo($calculo, $horas);

        $this->line('');

        // Mostrar simulación para diferentes rangos de horas
        $this->mostrarSimulacionRangos($service, $habitacion, $fecha);
    }

    private function simularPenalizacionCheckout(ReglaNegocioService $service, Habitacion $habitacion, Carbon $fecha)
    {
        $this->info('⏰ SIMULACIÓN PENALIZACIÓN CHECKOUT');
        $this->line('');

        $horaCheckout = Carbon::createFromFormat('H:i', $this->option('checkout'));
        $fechaCheckout = $fecha->copy()->setTime($horaCheckout->hour, $horaCheckout->minute);

        $montoReserva = (float) $this->option('monto-base');

        $penalizacion = $service->calcularPenalizacionCheckout($habitacion, $fechaCheckout, $montoReserva);

        if ($penalizacion['penalizacion_total'] > 0) {
            $this->table(
                ['Concepto', 'Valor'],
                [
                    ['Hora de Checkout', $fechaCheckout->format('H:i')],
                    ['Monto Reserva', 'S/ ' . number_format($montoReserva, 2)],
                    ['Penalización Total', 'S/ ' . number_format($penalizacion['penalizacion_total'], 2)],
                ]
            );

            if (!empty($penalizacion['detalles'])) {
                $this->info('📋 Detalles de penalizaciones:');
                foreach ($penalizacion['detalles'] as $detalle) {
                    $this->line("   • {$detalle['regla']}: S/ {$detalle['monto']} (Límite: {$detalle['hora_limite']})");
                }
            }
        } else {
            $this->info("✅ No hay penalización para checkout a las {$fechaCheckout->format('H:i')}");
        }

        $this->line('');
    }

    private function mostrarDetalleCalculo(array $calculo, int $horas)
    {
        $precioPorHora = $horas > 0 ? $calculo['precio_final'] / $horas : 0;

        $this->table(
            ['Concepto', 'Monto'],
            [
                ['Precio Base', 'S/ ' . number_format($calculo['precio_base'], 2)],
                ['Descuentos', 'S/ ' . number_format($calculo['descuentos'], 2)],
                ['Recargos', 'S/ ' . number_format($calculo['recargos'], 2)],
                ['PRECIO FINAL', 'S/ ' . number_format($calculo['precio_final'], 2)],
                ['Precio por hora', 'S/ ' . number_format($precioPorHora, 2)],
            ]
        );

        if (!empty($calculo['reglas_aplicadas'])) {
            $this->info('📋 Reglas aplicadas:');
            foreach ($calculo['reglas_aplicadas'] as $regla) {
                $this->line("   • {$regla->nombre} (Prioridad: {$regla->prioridad})");
            }
        }
    }

    private function mostrarSimulacionRangos(ReglaNegocioService $service, Habitacion $habitacion, Carbon $fecha)
    {
        $this->info('📊 SIMULACIÓN PARA DIFERENTES RANGOS DE HORAS');
        $this->line('');

        $simulaciones = $service->simularCostosAlquiler($habitacion, [2, 4, 6, 8, 12, 24], $fecha);

        if (empty($simulaciones)) {
            $this->warn('No hay reglas aplicables para ningún rango de horas');
            return;
        }

        $rows = [];
        foreach ($simulaciones as $sim) {
            $extras = [];
            if ($sim['tiene_descuentos']) $extras[] = 'Descuento';
            if ($sim['tiene_recargos']) $extras[] = 'Recargo';

            $rows[] = [
                $sim['horas'] . ' h',
                'S/ ' . number_format($sim['precio_total'], 2),
                'S/ ' . number_format($sim['precio_por_hora'], 2),
                $sim['reglas_aplicadas'],
                implode(', ', $extras) ?: '-'
            ];
        }

        $this->table(
            ['Horas', 'Precio Total', 'Precio/Hora', 'Reglas', 'Extras'],
            $rows
        );
    }

    private function mostrarResumenGeneral(ReglaNegocioService $service)
    {
        $this->info('📊 RESUMEN GENERAL DE REGLAS DE NEGOCIO');
        $this->line('');

        $resumen = $service->obtenerResumenReglas();

        // Mostrar estadísticas generales
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total de reglas activas', $resumen['total_activas']],
                ['Alquiler por horas', $resumen['por_tipo']['alquiler_horas']],
                ['Penalización checkout', $resumen['por_tipo']['penalizacion_checkout']],
                ['Descuentos', $resumen['por_tipo']['descuentos']],
                ['Recargos', $resumen['por_tipo']['recargos']],
            ]
        );

        $this->line('');

        // Mostrar reglas por aplicabilidad
        $this->table(
            ['Aplicabilidad', 'Cantidad'],
            [
                ['Todas las habitaciones', $resumen['por_aplicabilidad']['todas']],
                ['Tipos específicos', $resumen['por_aplicabilidad']['tipos_especificos']],
                ['Habitaciones específicas', $resumen['por_aplicabilidad']['habitaciones_especificas']],
            ]
        );

        $this->line('');

        // Mostrar reglas activas detalladas
        $reglas = ReglaNegocio::activas()->orderBy('tipo')->orderBy('prioridad', 'desc')->get();

        if ($reglas->count() > 0) {
            $this->info('📋 REGLAS ACTIVAS DETALLADAS');
            $this->line('');

            $rows = [];
            foreach ($reglas as $regla) {
                $rows[] = [
                    $regla->nombre,
                    match($regla->tipo) {
                        'alquiler_horas' => 'Alquiler/H',
                        'penalizacion_checkout' => 'Penaliz.',
                        'descuento' => 'Descuento',
                        'recargo' => 'Recargo',
                        default => $regla->tipo
                    },
                    $regla->prioridad,
                    match($regla->aplicabilidad_habitaciones) {
                        'todas' => 'Todas',
                        'tipos_especificos' => 'Tipos',
                        'habitaciones_especificas' => 'Específicas',
                        default => $regla->aplicabilidad_habitaciones
                    },
                    $regla->activa ? '✅' : '❌'
                ];
            }

            $this->table(
                ['Nombre', 'Tipo', 'Prior.', 'Aplica a', 'Estado'],
                $rows
            );
        }
    }
}
