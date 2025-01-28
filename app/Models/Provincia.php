<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
	protected $table = 'provincias';
	public $timestamps = false;

	protected $casts = [
		'departamento_id' => 'int'
	];

	protected $fillable = [
		'name',
		'departamento_id'
	];

	public function departamento()
	{
		return $this->belongsTo(Departamento::class, 'departamento_id');
	}

	public function distritos()
	{
		return $this->hasMany(Distrito::class, 'provincia_id');
	}

	public function users()
	{
		return $this->hasMany(User::class, 'provincia_id');
	}
}
