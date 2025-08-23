<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HabitacionTipo extends Model
{
    protected $fillable = ['name', 'precio_base', 'precio_caracteristicas', 'precio_final', 'activa', 'capacidad'];

    protected $casts = [
        'precio_base' => 'decimal:2', // Garantiza dos decimales
        'precio_caracteristicas' => 'decimal:2',
        'precio_final' => 'decimal:2',
        'activa' => 'boolean',
    ];

    public function habitaciones()
    {
        return $this->hasMany(Habitacion::class, 'habitacion_tipo_id');
    }

    public function caracteristicas(): BelongsToMany
    {
        return $this->belongsToMany(Caracteristica::class, 'habitacion_tipo_caracteristica');
    }

    /**
     * Calcula automáticamente el precio final basado en precio base + características
     */
    public function calcularPrecioFinal(): void
    {
        // Asegurar que las características estén cargadas
        if (!$this->relationLoaded('caracteristicas')) {
            $this->load('caracteristicas');
        }

        // Calcular el precio total de las características
        $precioCaracteristicas = $this->caracteristicas->sum('precio');

        // Actualizar el campo precio_caracteristicas
        $this->precio_caracteristicas = round($precioCaracteristicas, 2);

        // Calcular y asignar el precio final
        $this->precio_final = round((float) $this->precio_base + $precioCaracteristicas, 2);
    }

    /**
     * Recalcula el precio final y lo guarda en la base de datos
     */
    public function recalcularYGuardarPrecio(): bool
    {
        $this->calcularPrecioFinal();
        return $this->save();
    }

    /**
     * Accessor para obtener el costo total (alias del precio final)
     */
    public function getCostoTotalAttribute(): float
    {
        return (float) $this->precio_final;
    }

    /**
     * Accessor para obtener solo el costo de las características
     */
    public function getCostoTotalCaracteristicasAttribute(): float
    {
        return (float) $this->precio_caracteristicas;
    }

    /**
     * Scope para obtener tipos de habitación activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Método para sincronizar características y recalcular precio
     */
    public function sincronizarCaracteristicas(array $caracteristicasIds): void
    {
        $this->caracteristicas()->sync($caracteristicasIds);
        $this->refresh(); // Recargar el modelo con las nuevas relaciones
        $this->recalcularYGuardarPrecio();
    }

    /**
     * Reglas de validación para el modelo.
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'activa' => ['boolean'],
            'capacidad' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public static function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'precio_base.required' => 'El precio base es obligatorio.',
            'precio_base.numeric' => 'El precio base debe ser un número válido.',
            'precio_base.min' => 'El precio base no puede ser negativo.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.integer' => 'La capacidad debe ser un número entero.',
            'capacidad.min' => 'La capacidad mínima debe ser al menos 1.',
        ];
    }
}
