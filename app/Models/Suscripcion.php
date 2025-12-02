<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Suscripcion extends Model
{
    use HasFactory;

    protected $table = 'suscripciones';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'fecha_inicio',
        'fecha_fin',
        'id_taxista',
        'pagado',
        'precio',
        'activo',
        'metodo_pago',
        'referencia_pago',
        'fecha_pago',
        'estado_pago',
        'id_transaccion',
        'monto'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_pago' => 'datetime',
        'precio' => 'decimal:2',
        'monto' => 'decimal:2'
    ];

    // Relaciones
    public function taxista()
    {
        return $this->belongsTo(Taxista::class, 'id_taxista');
    }

    // Métodos de utilidad
    public function estaActiva(): bool
    {
        return $this->activo == 1 
            && $this->pagado == 1 
            && $this->fecha_fin > now()
            && $this->estado_pago === 'completado';
    }

    public function estaVencida(): bool
    {
        return $this->fecha_fin < now();
    }

    public function diasRestantes(): int
    {
        if ($this->estaVencida()) {
            return 0;
        }
        return max(0, now()->diffInDays($this->fecha_fin, false));
    }

    public function necesitaPago(): bool
    {
        return $this->pagado == 0 
            || $this->estado_pago === 'pendiente' 
            || $this->estado_pago === 'fallido'
            || $this->estaVencida();
    }

    // Scope para obtener suscripciones activas
    public function scopeActivas($query)
    {
        return $query->where('activo', 1)
            ->where('pagado', 1)
            ->where('estado_pago', 'completado')
            ->where('fecha_fin', '>', now());
    }

    // Scope para obtener suscripciones vencidas
    public function scopeVencidas($query)
    {
        return $query->where('fecha_fin', '<', now())
            ->orWhere(function($q) {
                $q->where('pagado', 0)
                  ->orWhere('estado_pago', '!=', 'completado');
            });
    }
}
