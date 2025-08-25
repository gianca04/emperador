<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ReglaNegocio extends Model
{
    protected $table = 'regla_negocios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'activa',
        'horas_minimas',
        'horas_maximas',
        'precio_por_hora',
        'precio_fijo',
        'hora_checkout_limite',
        'penalizacion_monto',
        'penalizacion_tipo',
        'aplicabilidad_habitaciones',
        'fecha_inicio',
        'fecha_fin',
        'dias_semana',
        'hora_inicio',
        'hora_fin',
        'es_temporada_alta',
        'es_fin_semana',
        'es_feriado',
        'prioridad',
        'configuracion_adicional',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'precio_por_hora' => 'decimal:2',
        'precio_fijo' => 'decimal:2',
        'penalizacion_monto' => 'decimal:2',
        'hora_checkout_limite' => 'datetime:H:i',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'dias_semana' => 'array',
        'configuracion_adicional' => 'array',
        'es_temporada_alta' => 'boolean',
        'es_fin_semana' => 'boolean',
        'es_feriado' => 'boolean',
    ];

    // Constantes para tipos de reglas
    const TIPO_ALQUILER_HORAS = 'alquiler_horas';
    const TIPO_PENALIZACION_CHECKOUT = 'penalizacion_checkout';
    const TIPO_DESCUENTO = 'descuento';
    const TIPO_RECARGO = 'recargo';

    // Constantes para aplicabilidad
    const APLICABILIDAD_TODAS = 'todas';
    const APLICABILIDAD_TIPOS_ESPECIFICOS = 'tipos_especificos';
    const APLICABILIDAD_HABITACIONES_ESPECIFICAS = 'habitaciones_especificas';

    // Constantes para tipos de penalización
    const PENALIZACION_FIJO = 'fijo';
    const PENALIZACION_PORCENTAJE = 'porcentaje';
    const PENALIZACION_POR_HORA = 'por_hora';

    /**
     * Relación con tipos de habitación
     */
    public function tiposHabitacion(): BelongsToMany
    {
        return $this->belongsToMany(
            HabitacionTipo::class,
            'regla_negocio_habitacion_tipo',
            'regla_negocio_id',
            'habitacion_tipo_id'
        )->withPivot(['precio_override', 'activa'])
         ->withTimestamps();
    }

    /**
     * Relación con habitaciones específicas
     */
    public function habitaciones(): BelongsToMany
    {
        return $this->belongsToMany(
            Habitacion::class,
            'regla_negocio_habitacion',
            'regla_negocio_id',
            'habitacion_id'
        )->withPivot(['precio_override', 'activa'])
         ->withTimestamps();
    }

    /**
     * Usuario que creó la regla
     */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Usuario que actualizó la regla
     */
    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    /**
     * Scope para reglas activas
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para filtrar por tipo de regla
     */
    public function scopeDelTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para reglas aplicables en una fecha específica
     */
    public function scopeAplicablesEnFecha(Builder $query, Carbon $fecha): Builder
    {
        return $query->where(function ($q) use ($fecha) {
            $q->whereNull('fecha_inicio')
              ->orWhere('fecha_inicio', '<=', $fecha->format('Y-m-d'));
        })->where(function ($q) use ($fecha) {
            $q->whereNull('fecha_fin')
              ->orWhere('fecha_fin', '>=', $fecha->format('Y-m-d'));
        });
    }

    /**
     * Scope para reglas aplicables en un día de la semana
     */
    public function scopeAplicablesEnDiaSemana(Builder $query, int $diaSemana): Builder
    {
        return $query->where(function ($q) use ($diaSemana) {
            $q->whereNull('dias_semana')
              ->orWhereJsonContains('dias_semana', $diaSemana);
        });
    }

    /**
     * Scope para reglas aplicables en una hora específica
     */
    public function scopeAplicablesEnHora(Builder $query, string $hora): Builder
    {
        return $query->where(function ($q) use ($hora) {
            $q->whereNull('hora_inicio')
              ->orWhere('hora_inicio', '<=', $hora);
        })->where(function ($q) use ($hora) {
            $q->whereNull('hora_fin')
              ->orWhere('hora_fin', '>=', $hora);
        });
    }

    /**
     * Verificar si la regla aplica para una habitación específica
     */
    public function aplicaParaHabitacion(Habitacion $habitacion): bool
    {
        switch ($this->aplicabilidad_habitaciones) {
            case self::APLICABILIDAD_TODAS:
                return true;

            case self::APLICABILIDAD_TIPOS_ESPECIFICOS:
                return $this->tiposHabitacion()->where('habitacion_tipo_id', $habitacion->habitacion_tipo_id)->exists();

            case self::APLICABILIDAD_HABITACIONES_ESPECIFICAS:
                return $this->habitaciones()->where('habitacion_id', $habitacion->id)->exists();

            default:
                return false;
        }
    }

    /**
     * Calcular precio por horas para una habitación
     */
    public function calcularPrecioPorHoras(int $horas, Habitacion $habitacion = null): float
    {
        if ($this->tipo !== self::TIPO_ALQUILER_HORAS) {
            return 0;
        }

        // Verificar si las horas están en el rango permitido
        if ($this->horas_minimas && $horas < $this->horas_minimas) {
            return 0;
        }

        if ($this->horas_maximas && $horas > $this->horas_maximas) {
            return 0;
        }

        // Obtener precio específico si existe
        $precio = $this->obtenerPrecioEspecifico($habitacion);

        // Calcular según tipo de precio
        if ($this->precio_fijo) {
            return (float) $this->precio_fijo;
        }

        if ($this->precio_por_hora) {
            return (float) ($this->precio_por_hora * $horas);
        }

        return 0;
    }

    /**
     * Calcular penalización por checkout tardío
     */
    public function calcularPenalizacionCheckout(Carbon $horaCheckout, float $montoBase = 0): float
    {
        if ($this->tipo !== self::TIPO_PENALIZACION_CHECKOUT) {
            return 0;
        }

        $horaLimite = Carbon::createFromFormat('H:i:s', $this->hora_checkout_limite->format('H:i:s'));

        if ($horaCheckout->format('H:i:s') <= $horaLimite->format('H:i:s')) {
            return 0; // No hay penalización
        }

        switch ($this->penalizacion_tipo) {
            case self::PENALIZACION_FIJO:
                return (float) $this->penalizacion_monto;

            case self::PENALIZACION_PORCENTAJE:
                return $montoBase * ((float) $this->penalizacion_monto / 100);

            case self::PENALIZACION_POR_HORA:
                $horasExceso = $horaCheckout->diffInHours($horaLimite);
                return (float) ($this->penalizacion_monto * $horasExceso);

            default:
                return 0;
        }
    }

    /**
     * Obtener precio específico para una habitación (con overrides)
     */
    private function obtenerPrecioEspecifico(Habitacion $habitacion = null): float
    {
        if (!$habitacion) {
            return (float) ($this->precio_por_hora ?? $this->precio_fijo ?? 0);
        }

        // Primero verificar override específico de habitación
        $habitacionPivot = $this->habitaciones()->where('habitacion_id', $habitacion->id)->first();
        if ($habitacionPivot && $habitacionPivot->pivot->precio_override) {
            return (float) $habitacionPivot->pivot->precio_override;
        }

        // Luego verificar override de tipo de habitación
        $tipoPivot = $this->tiposHabitacion()->where('habitacion_tipo_id', $habitacion->habitacion_tipo_id)->first();
        if ($tipoPivot && $tipoPivot->pivot->precio_override) {
            return (float) $tipoPivot->pivot->precio_override;
        }

        // Finalmente usar precio general
        return (float) ($this->precio_por_hora ?? $this->precio_fijo ?? 0);
    }

    /**
     * Obtener reglas aplicables para una habitación en un momento específico
     */
    public static function obtenerReglasAplicables(
        Habitacion $habitacion,
        string $tipo,
        Carbon $fechaHora = null
    ): \Illuminate\Database\Eloquent\Collection {
        $fechaHora = $fechaHora ?? now();

        return self::activas()
            ->delTipo($tipo)
            ->aplicablesEnFecha($fechaHora)
            ->aplicablesEnDiaSemana($fechaHora->dayOfWeek)
            ->aplicablesEnHora($fechaHora->format('H:i:s'))
            ->orderBy('prioridad', 'desc')
            ->get()
            ->filter(fn($regla) => $regla->aplicaParaHabitacion($habitacion));
    }

    /**
     * Validaciones personalizadas
     */
    public static function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:regla_negocios'],
            'tipo' => ['required', 'in:' . implode(',', [
                self::TIPO_ALQUILER_HORAS,
                self::TIPO_PENALIZACION_CHECKOUT,
                self::TIPO_DESCUENTO,
                self::TIPO_RECARGO
            ])],
            'aplicabilidad_habitaciones' => ['required', 'in:' . implode(',', [
                self::APLICABILIDAD_TODAS,
                self::APLICABILIDAD_TIPOS_ESPECIFICOS,
                self::APLICABILIDAD_HABITACIONES_ESPECIFICAS
            ])],
            'horas_minimas' => ['nullable', 'integer', 'min:1'],
            'horas_maximas' => ['nullable', 'integer', 'min:1', 'gte:horas_minimas'],
            'precio_por_hora' => ['nullable', 'numeric', 'min:0'],
            'precio_fijo' => ['nullable', 'numeric', 'min:0'],
            'penalizacion_monto' => ['nullable', 'numeric', 'min:0'],
            'penalizacion_tipo' => ['nullable', 'in:' . implode(',', [
                self::PENALIZACION_FIJO,
                self::PENALIZACION_PORCENTAJE,
                self::PENALIZACION_POR_HORA
            ])],
            'prioridad' => ['integer', 'min:1'],
        ];
    }
}
