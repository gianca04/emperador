<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';

    protected $fillable = ['estudiante_id', 'estado', 'fecha'];

    protected $casts = [
        'fecha' => 'datetime',
        'estado' => 'string',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }
}