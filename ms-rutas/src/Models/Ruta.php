<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    // Aseguramos que apunte a la tabla correcta
    protected $table = 'rutas';

    // Definimos solo las columnas existentes en el SQL de creación
    protected $fillable = [
        'ciudad_origen',
        'ciudad_destino',
        'distancia',
        'tiempo_estimado',
        'observaciones',
    ];
}