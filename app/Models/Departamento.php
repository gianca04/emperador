<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
	protected $table = 'departamentos';
	public $timestamps = false;

	protected $fillable = [
		'name'
	];

	public function distritos()
	{
		return $this->hasMany(Distrito::class, 'departamento_id');
	}

	public function provincias()
	{
		return $this->hasMany(Provincia::class, 'departamento_id');
	}

	public function users()
	{
		return $this->hasMany(User::class, 'departamento_id');
	}

}
