<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    protected $table = 'viajes';

    // Estos campos deben existir porque guardas los IDs en la base de datos
    protected $fillable = [
        'programacion_id',
        'estado',
        'ubicacion_actual',
        'observaciones',
        'fecha_inicio',
        'fecha_fin',
        'conductor_id',
        'vehiculo_id',
        'ruta_id',
    ];

    // Mantenemos solo la relación que sí existe en este servicio
    public function novedades()
    {
        return $this->hasMany(Novedad::class, 'viaje_id');
    }
    
    // NOTA: Hemos eliminado conductor(), vehiculo() y ruta()
    // porque estos modelos NO existen en ms-viajes.
}