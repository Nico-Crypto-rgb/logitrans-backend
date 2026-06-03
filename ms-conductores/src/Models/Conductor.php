<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    protected $table = 'conductores';

    protected $fillable = [
        'nombre',
        'cedula',
        'licencia',
        'telefono',
        'estado',
        'usuario_id',
    ];
}