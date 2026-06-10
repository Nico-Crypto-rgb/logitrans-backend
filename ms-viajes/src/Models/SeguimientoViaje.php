<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoViaje extends Model
{
    // Define explícitamente la tabla en la base de datos
    protected $table = 'seguimientos_viajes';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'programacion_viaje_id',
        'fecha',
        'hora',
        'estado',
        'novedad',
    ];

    // Eloquent detecta automáticamente 'created_at' y 'updated_at'
}