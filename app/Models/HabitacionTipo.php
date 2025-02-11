<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HabitacionTipo extends Model
{
    protected $fillable = ['name', 'precio_base', 'activa', 'capacidad'];

    protected $casts = [
        'precio_base' => 'decimal:2', // Garantiza dos decimales
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

    public function getCostoTotalAttribute()
    {
        if (!$this->relationLoaded('caracteristicas')) {
            $this->load('caracteristicas');
        }

        return round((float) $this->precio_base + $this->caracteristicas->sum('precio'), 2);
    }

    public function getCostoTotalCaracteristicasAttribute()
    {
        if (!$this->relationLoaded('caracteristicas')) {
            $this->load('caracteristicas');
        }

        return round($this->caracteristicas->sum('precio'), 2);
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
