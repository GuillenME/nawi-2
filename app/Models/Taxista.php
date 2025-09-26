<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxista extends Model
{
    use HasFactory;

    protected $table = 'taxistas';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'id_matricula',
        'id_licencia',
        'id_usuario'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function matricula()
    {
        return $this->belongsTo(Matricula::class, 'id_matricula');
    }

    public function licencia()
    {
        return $this->belongsTo(Licencia::class, 'id_licencia');
    }

    public function taxis()
    {
        return $this->hasMany(Taxi::class, 'id_taxista');
    }

    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class, 'id_taxista');
    }

    public function calificaciones()
    {
        return $this->hasMany(CalificacionTaxi::class, 'id_taxista');
    }
}
