<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    use HasFactory;

    protected $table = 'organizaciones';

    protected $fillable = [
        'name',
        'ruc',
        'tipo_ruc',
        'telefono',
        'email',
        'direccion',
        'nombre_contacto',
        'telefono_contacto',
        'telefono_secundario',
        'email_contacto',
        'tipo_organizacion',
        'fecha_registro',
        'notas',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];
}
