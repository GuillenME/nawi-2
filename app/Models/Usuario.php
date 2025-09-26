<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nombre',
        'apellido',
        'telefono',
        'email',
        'password',
        'id_rol'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relaciones
    public function rol()
    {
        return $this->belongsTo(Role::class, 'id_rol');
    }

    public function pasajero()
    {
        return $this->hasOne(Pasajero::class, 'id_usuario');
    }

    public function taxista()
    {
        return $this->hasOne(Taxista::class, 'id_usuario');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id_usuario');
    }

    public function tokens()
    {
        return $this->hasMany(Token::class, 'id_usuario');
    }

    public function fotos()
    {
        return $this->hasMany(Foto::class, 'id_usuario');
    }
}
