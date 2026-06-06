<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramacionViaje extends Model
{
    protected $table = 'programacion_viajes';

    protected $fillable = [
        'ruta_id',
        'conductor_id',
        'vehiculo_id',
        'fecha_salida',
        'fecha_llegada_estimada',
        'estado',
    ];
}