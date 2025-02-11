<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Departament;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dni',
        'apellido',
        'nombre',
        'nacimiento',
        'telefono',
        'direccion',
        'distrito_id',
        'provincia_id', // Nuevo campo
        'departamento_id', // Nuevo campo
        'active',
        'photo', // Nuevo campO
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'nacimiento' => 'date',
        'active' => 'boolean',
    ];

    /**
     * Define la relación con el modelo Distrito.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    /**
     * Define la relación con el modelo Provincia.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    /**
     * Define la relación con el modelo Departamento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Scope para obtener usuarios activos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para obtener usuarios inactivos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    public function calendars(){
        return $this->belongsToMany(Calendar::class);
    }

    public function departments(){
        return $this->belongsToMany(Departament::class);
    }

    public function holidays(){
        return $this->hasMany(Holiday::class);
    }

    public function timesheets(){
        return $this->hasMany(Timesheet::class);
    }
}
