<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alquiler extends Model
{
    use HasFactory;

    protected $table = 'alquileres';

    protected $fillable = [
        'habitacion_id',
        'tipo_alquiler',
        'fecha_inicio',
        'fecha_fin',
        'horas',
        'monto_total',
        'checkin_at',
        'checkout_at',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'checkin_at' => 'datetime',
        'checkout_at' => 'datetime',
    ];

    /**
     * Relación con la habitación.
     */
    public function habitacion(): BelongsTo
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id');
    }

    public function caracteristicas()
    {
        return $this->belongsToMany(Caracteristica::class, 'alquiler_caracteristica', 'alquiler_id', 'caracteristica_id');
    }

    public function getCaracteristicaHabitcion()
    {
        return $this->habitacion ? $this->habitacion->caracteristicas : collect();
    }

}
