<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramacionViaje extends Model
{
    // Nombre de la tabla correcto (plural)
    protected $table = 'programaciones_viajes';

    // Lista de campos permitidos para asignación masiva
    protected $fillable = [
        'ruta_id',
        'conductor_id',
        'vehiculo_id',
        'fecha_salida',
        'hora_salida',
        'fecha_estimada_llegada',
        'observaciones',
        'estado',
    ];
}