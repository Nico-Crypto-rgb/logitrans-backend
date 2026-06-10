<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'conductores';

    // Campos que permiten asignación masiva
    protected $fillable = [
        'nombres',
        'apellidos',
        'documento',
        'telefono',
        'correo',
        'numero_licencia',
        'categoria_licencia',
        'fecha_vencimiento_licencia',
        'estado',
    ];
}