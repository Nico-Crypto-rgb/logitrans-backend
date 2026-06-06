<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $table = 'rutas';

    protected $fillable = [
        'nombre',
        'origen',
        'destino',
        'distancia_km',
        'tiempo_estimado_horas',
        'activa',
    ];
}