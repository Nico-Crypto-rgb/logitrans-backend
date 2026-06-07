<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Novedad extends Model
{
    protected $table    = 'novedades';
    const UPDATED_AT    = null; // ← tabla sin updated_at

    protected $fillable = [
        'viaje_id',
        'tipo',
        'descripcion',
        'usuario_id',
    ];

    public function viaje()
    {
        return $this->belongsTo(Viaje::class, 'viaje_id');
    }
}