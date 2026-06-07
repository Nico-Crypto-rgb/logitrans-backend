<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    protected $table    = 'viajes';
    protected $fillable = [
        'programacion_id',
        'estado',
        'ubicacion_actual',
        'observaciones',
        'fecha_inicio',
        'fecha_fin',
    ];

    public function novedades()
    {
        return $this->hasMany(Novedad::class, 'viaje_id');
    }
}