<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Suscripcion;

class VerificarSuscripcionActiva
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();
        
        if (!$usuario || !$usuario->taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un taxista'
            ], 403);
        }

        $taxista = $usuario->taxista;
        
        // Buscar suscripción activa
        $suscripcionActiva = Suscripcion::where('id_taxista', $taxista->id)
            ->activas()
            ->latest('fecha_fin')
            ->first();

        if (!$suscripcionActiva) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes una suscripción activa. Por favor, renueva tu suscripción para continuar usando el servicio.',
                'error_code' => 'SUSCRIPCION_INACTIVA'
            ], 403);
        }

        return $next($request);
    }
}
