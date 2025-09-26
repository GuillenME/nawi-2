<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    protected $table = 'matriculas';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'url', 'fecha_subida', 'id_estatus'
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
    ];

    public function estatus()
    {
        return $this->belongsTo(EstatusDocumento::class, 'id_estatus');
    }

    public function taxistas()
    {
        return $this->hasMany(Taxista::class, 'id_matricula');
    }
}
