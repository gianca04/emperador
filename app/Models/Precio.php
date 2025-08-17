<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    use HasFactory;

    protected $table = 'precios'; // Nombre de la tabla

    protected $fillable = [
        'precio_por_hora',
        'precio_por_mora',
        'precio_hora_adicional',
    ];

    /**
     * Definir el casting de los atributos a tipos de datos correctos.
     */
    protected $casts = [
        'precio_por_hora' => 'decimal:2',
        'precio_por_mora' => 'decimal:2',
        'precio_hora_adicional' => 'decimal:2',
    ];
}
