<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
	protected $table = 'distritos';
	public $timestamps = false;

	protected $casts = [
		'provincia_id' => 'int',
		'departamento_id' => 'int'
	];

	protected $fillable = [
		'name',
		'provincia_id',
		'departamento_id'
	];

	public function provincia()
	{
		return $this->belongsTo(Provincia::class, 'provincia_id', 'id');
	}

	public function departamento()
	{
		return $this->belongsTo(Departamento::class, 'departamento_id', 'id');
	}

	public function users()
	{
		return $this->hasMany(User::class, 'distrito_id');
	}
}
