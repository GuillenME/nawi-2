<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasajero extends Model
{
    use HasFactory;

    protected $table = 'pasajeros';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'id_usuario'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function viajes()
    {
        return $this->hasMany(Viaje::class, 'id_pasajero');
    }

    public function calificacionesViajes()
    {
        return $this->hasMany(CalificacionViaje::class, 'id_pasajero');
    }

    public function calificacionesTaxis()
    {
        return $this->hasMany(CalificacionTaxi::class, 'id_pasajero');
    }
}
