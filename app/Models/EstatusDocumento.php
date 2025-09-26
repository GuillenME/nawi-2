<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstatusDocumento extends Model
{
    use HasFactory;

    protected $table = 'estatus_documentos';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'nombre'
    ];

    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'id_estatus');
    }

    public function licencias()
    {
        return $this->hasMany(Licencia::class, 'id_estatus');
    }
}
