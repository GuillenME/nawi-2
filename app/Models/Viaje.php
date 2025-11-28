<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Viaje extends Model
{
    use HasFactory;

    protected $table = 'viajes';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Constantes de estado
    const ESTADO_SOLICITADO = 'solicitado';
    const ESTADO_ACEPTADO = 'aceptado';
    const ESTADO_EN_PROGRESO = 'en_progreso';
    const ESTADO_COMPLETADO = 'completado';
    const ESTADO_CANCELADO = 'cancelado';
    const ESTADO_RECHAZADO = 'rechazado';

    protected $fillable = [
        'id',
        'id_pasajero',
        'id_taxista',
        'id_taxi',
        'latitud_origen',
        'longitud_origen',
        'direccion_origen',
        'latitud_destino',
        'longitud_destino',
        'direccion_destino',
        'estado',
        'fecha_aceptacion',
        'fecha_completado',
        'lat_actual',
        'lon_actual',
        'comentario',
        'tiempo_limite_aceptacion',
        'tarifa'
    ];

    protected $casts = [
        'latitud_origen' => 'float',
        'longitud_origen' => 'float',
        'latitud_destino' => 'float',
        'longitud_destino' => 'float',
        'lat_actual' => 'float',
        'lon_actual' => 'float',
        'fecha_aceptacion' => 'datetime',
        'fecha_completado' => 'datetime',
        'tiempo_limite_aceptacion' => 'datetime',
        'tarifa' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function pasajero(): BelongsTo
    {
        return $this->belongsTo(Pasajero::class, 'id_pasajero');
    }

    public function taxista(): BelongsTo
    {
        return $this->belongsTo(Taxista::class, 'id_taxista');
    }

    public function taxi(): BelongsTo
    {
        return $this->belongsTo(Taxi::class, 'id_taxi');
    }

    public function calificacion(): HasOne
    {
        return $this->hasOne(CalificacionViaje::class, 'id_viaje');
    }

    // Scopes
    public function scopeDelPasajero(Builder $query, string $pasajeroId): Builder
    {
        return $query->where('id_pasajero', $pasajeroId);
    }

    public function scopeDelTaxista(Builder $query, string $taxistaId): Builder
    {
        return $query->whereHas('taxi', function($q) use ($taxistaId) {
            $q->where('id_taxista', $taxistaId);
        })->orWhere('id_taxista', $taxistaId);
    }

    public function scopeDisponibles(Builder $query, ?string $taxistaId = null): Builder
    {
        $query = $query->where('estado', self::ESTADO_SOLICITADO);

        if ($taxistaId) {
            $query->where(function($q) use ($taxistaId) {
                $q->where('id_taxista', $taxistaId)
                  ->orWhereNull('id_taxista');
            });
        } else {
            $query->whereNull('id_taxista');
        }

        return $query;
    }

    public function scopeConEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado', $estado);
    }
}
