<?php

namespace App\Jobs;

use App\Models\Suscripcion;
use App\Mail\SuscripcionVencidaMail;
use App\Mail\SuscripcionRecordatorioMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class VerificarSuscripcionesVencidas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Buscar suscripciones que están vencidas o no pagadas
        $suscripcionesVencidas = Suscripcion::where(function($query) {
            $query->where('fecha_fin', '<', now())
                  ->orWhere(function($q) {
                      $q->where('pagado', 0)
                        ->orWhere('estado_pago', '!=', 'completado');
                  });
        })
        ->where('activo', 1)
        ->with('taxista.usuario')
        ->get();

        $desactivadas = 0;

        foreach ($suscripcionesVencidas as $suscripcion) {
            // Desactivar suscripción
            $suscripcion->update([
                'activo' => 0
            ]);

            // Enviar email de suscripción vencida
            if ($suscripcion->taxista && $suscripcion->taxista->usuario) {
                try {
                    Mail::to($suscripcion->taxista->usuario->email)
                        ->send(new SuscripcionVencidaMail($suscripcion));
                } catch (\Exception $e) {
                    Log::error("Error al enviar email de suscripción vencida", [
                        'suscripcion_id' => $suscripcion->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $desactivadas++;
            Log::info("Suscripción desactivada: {$suscripcion->id} para taxista {$suscripcion->id_taxista}");
        }

        // Buscar suscripciones que vencen en 3 días y enviar recordatorio
        $suscripcionesPorVencer = Suscripcion::where('activo', 1)
            ->where('pagado', 1)
            ->where('estado_pago', 'completado')
            ->whereBetween('fecha_fin', [
                now(),
                now()->addDays(3)
            ])
            ->with('taxista.usuario')
            ->get();

        $recordatoriosEnviados = 0;

        foreach ($suscripcionesPorVencer as $suscripcion) {
            $diasRestantes = $suscripcion->diasRestantes();
            
            // Solo enviar recordatorio si vence en 3 días o menos
            if ($diasRestantes <= 3 && $suscripcion->taxista && $suscripcion->taxista->usuario) {
                try {
                    Mail::to($suscripcion->taxista->usuario->email)
                        ->send(new SuscripcionRecordatorioMail($suscripcion, $diasRestantes));
                    
                    $recordatoriosEnviados++;
                } catch (\Exception $e) {
                    Log::error("Error al enviar recordatorio de suscripción", [
                        'suscripcion_id' => $suscripcion->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        Log::info("Verificación de suscripciones completada. Desactivadas: {$desactivadas}, Recordatorios enviados: {$recordatoriosEnviados}");
    }
}
