<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas'; 

    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_documento',
        'numero_documento',
        'email',
        'telefono',
        'telefono_secundario',
    ];

    protected $casts = [
        'tipo_documento'=> 'string',
    ];

    /**
     * Relación: Una persona puede ser cliente en muchos alquileres.
     */
    public function alquileresComoCliente()
    {
        //return $this->hasMany(Alquiler::class, 'cliente_id');
    }

    /**
     * Relación: Una persona puede ser cliente en muchas reservas.
     */
    public function reservasComoCliente()
    {
        //return $this->hasMany(Reserva::class, 'cliente_id');
    }

    /**
     * Relación: Una persona puede ser invitada en muchos alquileres (vía tabla pivote).
     */
    public function alquileresComoInvitado()
    {
        //return $this->belongsToMany(Alquiler::class, 'alquiler_persona', 'persona_id', 'alquiler_id')
        //            ->withTimestamps();
    }
}
