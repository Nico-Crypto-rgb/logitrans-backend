<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    protected $fillable = [
        'placa',
        'tipo',
        'capacidad_kg',
        'marca',
        'modelo',
        'anio',
        'estado',
    ];
}