<?php

namespace App\Services;

use App\Models\ReglaNegocio;
use App\Models\Habitacion;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReglaNegocioService
{
    /**
     * Calcular precio de alquiler por horas para una habitación
     */
    public function calcularPrecioAlquilerHoras(
        Habitacion $habitacion,
        int $horas,
        Carbon $fechaInicio = null
    ): array {
        $fechaInicio = $fechaInicio ?? now();

        // Obtener reglas aplicables para alquiler por horas
        $reglas = ReglaNegocio::obtenerReglasAplicables(
            $habitacion,
            ReglaNegocio::TIPO_ALQUILER_HORAS,
            $fechaInicio
        );

        $resultados = [
            'precio_base' => 0,
            'descuentos' => 0,
            'recargos' => 0,
            'precio_final' => 0,
            'reglas_aplicadas' => [],
            'detalles' => []
        ];

        foreach ($reglas as $regla) {
            $precio = $regla->calcularPrecioPorHoras($horas, $habitacion);

            if ($precio > 0) {
                if ($regla->tipo === ReglaNegocio::TIPO_ALQUILER_HORAS) {
                    // Usar la regla con mayor prioridad para precio base
                    if ($precio > $resultados['precio_base']) {
                        $resultados['precio_base'] = $precio;
                        array_unshift($resultados['reglas_aplicadas'], $regla);
                    }
                } elseif ($regla->tipo === ReglaNegocio::TIPO_DESCUENTO) {
                    $descuento = $this->calcularDescuento($resultados['precio_base'], $regla);
                    $resultados['descuentos'] += $descuento;
                    $resultados['reglas_aplicadas'][] = $regla;
                } elseif ($regla->tipo === ReglaNegocio::TIPO_RECARGO) {
                    $recargo = $this->calcularRecargo($resultados['precio_base'], $regla);
                    $resultados['recargos'] += $recargo;
                    $resultados['reglas_aplicadas'][] = $regla;
                }

                $resultados['detalles'][] = [
                    'regla' => $regla->nombre,
                    'tipo' => $regla->tipo,
                    'monto' => $precio,
                    'descripcion' => $regla->descripcion
                ];
            }
        }

        $resultados['precio_final'] = max(0, $resultados['precio_base'] + $resultados['recargos'] - $resultados['descuentos']);

        return $resultados;
    }

    /**
     * Calcular penalización por checkout tardío
     */
    public function calcularPenalizacionCheckout(
        Habitacion $habitacion,
        Carbon $horaCheckout,
        float $montoReserva = 0
    ): array {
        // Obtener reglas de penalización aplicables
        $reglas = ReglaNegocio::obtenerReglasAplicables(
            $habitacion,
            ReglaNegocio::TIPO_PENALIZACION_CHECKOUT,
            $horaCheckout
        );

        $resultados = [
            'penalizacion_total' => 0,
            'reglas_aplicadas' => [],
            'detalles' => []
        ];

        foreach ($reglas as $regla) {
            $penalizacion = $regla->calcularPenalizacionCheckout($horaCheckout, $montoReserva);

            if ($penalizacion > 0) {
                $resultados['penalizacion_total'] += $penalizacion;
                $resultados['reglas_aplicadas'][] = $regla;

                $resultados['detalles'][] = [
                    'regla' => $regla->nombre,
                    'hora_limite' => $regla->hora_checkout_limite->format('H:i'),
                    'hora_checkout' => $horaCheckout->format('H:i'),
                    'monto' => $penalizacion,
                    'tipo_penalizacion' => $regla->penalizacion_tipo,
                    'descripcion' => $regla->descripcion
                ];
            }
        }

        return $resultados;
    }

    /**
     * Obtener todas las reglas aplicables para una habitación en una fecha
     */
    public function obtenerReglasAplicablesParaHabitacion(
        Habitacion $habitacion,
        Carbon $fecha = null
    ): Collection {
        $fecha = $fecha ?? now();

        return ReglaNegocio::activas()
            ->aplicablesEnFecha($fecha)
            ->aplicablesEnDiaSemana($fecha->dayOfWeek)
            ->aplicablesEnHora($fecha->format('H:i:s'))
            ->orderBy('prioridad', 'desc')
            ->get()
            ->filter(fn($regla) => $regla->aplicaParaHabitacion($habitacion));
    }

    /**
     * Verificar si una habitación tiene reglas de alquiler por horas disponibles
     */
    public function tieneAlquilerPorHoras(Habitacion $habitacion, Carbon $fecha = null): bool
    {
        $fecha = $fecha ?? now();

        $reglas = ReglaNegocio::obtenerReglasAplicables(
            $habitacion,
            ReglaNegocio::TIPO_ALQUILER_HORAS,
            $fecha
        );

        return $reglas->count() > 0;
    }

    /**
     * Obtener rango de horas disponibles para alquiler
     */
    public function obtenerRangoHorasDisponibles(Habitacion $habitacion, Carbon $fecha = null): array
    {
        $fecha = $fecha ?? now();

        $reglas = ReglaNegocio::obtenerReglasAplicables(
            $habitacion,
            ReglaNegocio::TIPO_ALQUILER_HORAS,
            $fecha
        );

        if ($reglas->isEmpty()) {
            return ['min' => 0, 'max' => 0];
        }

        $horasMinimas = $reglas->min('horas_minimas') ?? 1;
        $horasMaximas = $reglas->max('horas_maximas') ?? 24;

        return [
            'min' => $horasMinimas,
            'max' => $horasMaximas
        ];
    }

    /**
     * Simular costos para diferentes rangos de horas
     */
    public function simularCostosAlquiler(
        Habitacion $habitacion,
        array $rangosHoras = [2, 4, 6, 8, 12, 24],
        Carbon $fecha = null
    ): array {
        $fecha = $fecha ?? now();
        $simulaciones = [];

        foreach ($rangosHoras as $horas) {
            $calculo = $this->calcularPrecioAlquilerHoras($habitacion, $horas, $fecha);

            if ($calculo['precio_final'] > 0) {
                $simulaciones[] = [
                    'horas' => $horas,
                    'precio_total' => $calculo['precio_final'],
                    'precio_por_hora' => round($calculo['precio_final'] / $horas, 2),
                    'reglas_aplicadas' => count($calculo['reglas_aplicadas']),
                    'tiene_descuentos' => $calculo['descuentos'] > 0,
                    'tiene_recargos' => $calculo['recargos'] > 0
                ];
            }
        }

        return $simulaciones;
    }

    /**
     * Validar si se puede aplicar alquiler por horas en un rango de tiempo
     */
    public function validarDisponibilidadAlquilerHoras(
        Habitacion $habitacion,
        Carbon $fechaInicio,
        int $horas
    ): array {
        $fechaFin = $fechaInicio->copy()->addHours($horas);

        $validacion = [
            'es_valido' => true,
            'errores' => [],
            'advertencias' => [],
            'reglas_aplicables' => []
        ];

        // Verificar si hay reglas aplicables
        $reglas = $this->obtenerReglasAplicablesParaHabitacion($habitacion, $fechaInicio);
        $reglasAlquiler = $reglas->where('tipo', ReglaNegocio::TIPO_ALQUILER_HORAS);

        if ($reglasAlquiler->isEmpty()) {
            $validacion['es_valido'] = false;
            $validacion['errores'][] = 'No hay reglas de alquiler por horas disponibles para esta habitación';
            return $validacion;
        }

        // Verificar rangos de horas
        foreach ($reglasAlquiler as $regla) {
            if ($regla->horas_minimas && $horas < $regla->horas_minimas) {
                $validacion['errores'][] = "La regla '{$regla->nombre}' requiere mínimo {$regla->horas_minimas} horas";
            }

            if ($regla->horas_maximas && $horas > $regla->horas_maximas) {
                $validacion['errores'][] = "La regla '{$regla->nombre}' permite máximo {$regla->horas_maximas} horas";
            }
        }

        // Verificar restricciones temporales
        $horaInicio = $fechaInicio->format('H:i:s');
        $horaFin = $fechaFin->format('H:i:s');

        foreach ($reglasAlquiler as $regla) {
            if ($regla->hora_inicio && $horaInicio < $regla->hora_inicio->format('H:i:s')) {
                $validacion['advertencias'][] = "La regla '{$regla->nombre}' inicia a las {$regla->hora_inicio->format('H:i')}";
            }

            if ($regla->hora_fin && $horaFin > $regla->hora_fin->format('H:i:s')) {
                $validacion['advertencias'][] = "La regla '{$regla->nombre}' termina a las {$regla->hora_fin->format('H:i')}";
            }
        }

        if (!empty($validacion['errores'])) {
            $validacion['es_valido'] = false;
        }

        $validacion['reglas_aplicables'] = $reglasAlquiler->toArray();

        return $validacion;
    }

    /**
     * Calcular descuento basado en regla
     */
    private function calcularDescuento(float $montoBase, ReglaNegocio $regla): float
    {
        if ($regla->penalizacion_tipo === ReglaNegocio::PENALIZACION_PORCENTAJE) {
            return $montoBase * ($regla->penalizacion_monto / 100);
        }

        return $regla->penalizacion_monto ?? 0;
    }

    /**
     * Calcular recargo basado en regla
     */
    private function calcularRecargo(float $montoBase, ReglaNegocio $regla): float
    {
        if ($regla->penalizacion_tipo === ReglaNegocio::PENALIZACION_PORCENTAJE) {
            return $montoBase * ($regla->penalizacion_monto / 100);
        }

        return $regla->penalizacion_monto ?? 0;
    }

    /**
     * Obtener resumen de reglas activas por tipo
     */
    public function obtenerResumenReglas(): array
    {
        $reglas = ReglaNegocio::activas()->get();

        return [
            'total_activas' => $reglas->count(),
            'por_tipo' => [
                'alquiler_horas' => $reglas->where('tipo', ReglaNegocio::TIPO_ALQUILER_HORAS)->count(),
                'penalizacion_checkout' => $reglas->where('tipo', ReglaNegocio::TIPO_PENALIZACION_CHECKOUT)->count(),
                'descuentos' => $reglas->where('tipo', ReglaNegocio::TIPO_DESCUENTO)->count(),
                'recargos' => $reglas->where('tipo', ReglaNegocio::TIPO_RECARGO)->count(),
            ],
            'por_aplicabilidad' => [
                'todas' => $reglas->where('aplicabilidad_habitaciones', ReglaNegocio::APLICABILIDAD_TODAS)->count(),
                'tipos_especificos' => $reglas->where('aplicabilidad_habitaciones', ReglaNegocio::APLICABILIDAD_TIPOS_ESPECIFICOS)->count(),
                'habitaciones_especificas' => $reglas->where('aplicabilidad_habitaciones', ReglaNegocio::APLICABILIDAD_HABITACIONES_ESPECIFICAS)->count(),
            ]
        ];
    }
}
