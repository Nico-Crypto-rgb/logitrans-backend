<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table    = 'usuarios';
    
    protected $fillable = [
        'nombre',
        'email', 
        'password',
        'rol',
        'token',
        'activo'
    ];

    // Nunca devolver el password en las respuestas JSON
    protected $hidden = ['password'];

    const ROLES = ['admin', 'logistica', 'operador'];
}