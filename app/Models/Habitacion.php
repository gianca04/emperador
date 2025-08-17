<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'habitaciones';

    protected $fillable = [
        'numero',
        'estado',
        'descripcion',
        'habitacion_tipo_id',
        'notas',
        'ubicacion',
        'precio_base',
        'precio_caracteristicas',
        'precio_final',
        'ultima_limpieza',
    ];

    protected $casts = [
        'ultima_limpieza' => 'datetime',
        'precio_base' => 'decimal:2',
        'precio_final' => 'decimal:2',
    ];

    protected function numero(): Attribute
    {
        return Attribute::make(
            get: fn($value) => str_pad($value, 3, '0', STR_PAD_LEFT),
            set: fn($value) => ltrim($value, '0') // Almacena sin ceros a la izquierda
        );
    }

    /**
     * Método para marcar la habitación como ocupada.
     */
    public function ocupar(): void
    {
        if ($this->estado !== 'Ocupada') {
            $this->update(['estado' => 'Ocupada']);
        }
    }

    public function liberar(): void
    {
        if ($this->estado !== 'Disponible') {
            $this->update(['estado' => 'Disponible']);
        }
    }

    public function tipo()
    {
        return $this->belongsTo(HabitacionTipo::class, 'habitacion_tipo_id');
    }

    public function getTipoNombre(): string
    {
        return $this->tipo?->name ?? 'No definido';
    }
    

    /**
     * Método para actualizar la fecha de última limpieza.
     */
    public function registrarLimpieza(): void
    {
        if (!$this->ultima_limpieza || $this->ultima_limpieza->format('Y-m-d H:i:s') !== now()->format('Y-m-d H:i:s')) {
            $this->update(['ultima_limpieza' => now()]);
        }
    }

    public function caracteristicas()
    {
        return $this->belongsToMany(Caracteristica::class, 'habitacion_caracteristica');
    }

    public function getCaracteristicasPorDefectoAttribute()
    {
        return $this->tipo ? $this->tipo->caracteristicas : collect();
    }

}
