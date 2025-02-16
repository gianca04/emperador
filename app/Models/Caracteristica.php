<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;

class Caracteristica extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'activa',
        'precio',
        'removible',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'removible' => 'boolean',
        'precio' => 'decimal:2',
    ];

    /**
     * Reglas de validación para Filament.
     */
    public static function rules($id = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('caracteristicas', 'name')->ignore($id),
            ],
            'precio' => ['required', 'numeric', 'min:0'],
            'icono' => ['nullable', 'image', 'max:1024'],
            'activa' => ['required', 'boolean'],
            'removible' => ['required', 'boolean'],
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public static function messages(): array
    {
        return [
            'name.required' => 'El nombre de la característica es obligatorio.',
            'name.unique' => 'Ya existe una característica con este nombre.',
            'precio.required' => 'Debe indicar un precio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
            'icono.image' => 'Debe subir un archivo de imagen válido.',
            'icono.max' => 'La imagen no debe superar 1MB.',
            'activa.required' => 'Debe indicar si está activa.',
            'removible.required' => 'Debe indicar si es removible.',
        ];
    }

    /**
     * Relación con HabitacionTipo.
     */
    public function habitacionTipos(): BelongsToMany
    {
        return $this->belongsToMany(HabitacionTipo::class, 'habitacion_tipo_caracteristica');
    }

    public function habitaciones(): BelongsToMany
    {
        return $this->belongsToMany(HabitacionTipo::class, 'habitacion_caracteristica');
    }

    public function alquileres(): BelongsToMany
    {
        return $this->belongsToMany(HabitacionTipo::class, 'alquiler_caracteristica');
    }


    /**
     * Formateo del nombre con el precio.
     */
    public function getFormattedNameAttribute(): string
    {
        return "{$this->name} - $ {$this->precio}";
    }
}
