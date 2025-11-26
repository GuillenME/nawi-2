<?php

namespace App\Http\Controllers;

use App\Models\Viaje;
use App\Models\Taxista;
use App\Models\Taxi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TaxistaViajeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * GET /taxista/viajes-disponibles
     * Obtener viajes disponibles para el taxista autenticado
     */
    public function viajesDisponibles(Request $request): JsonResponse
    {
        $taxista = $request->user()->taxista;

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un taxista'
            ], 403);
        }

        // Obtener viajes disponibles:
        // 1. Viajes con estado "solicitado"
        // 2. Que tengan id_taxista igual al taxista autenticado (solicitudes dirigidas a él)
        // 3. O viajes sin id_taxista asignado (solicitudes generales)
        $viajes = Viaje::where('estado', Viaje::ESTADO_SOLICITADO)
            ->where(function($query) use ($taxista) {
                $query->where('id_taxista', $taxista->id)
                      ->orWhereNull('id_taxista');
            })
            ->with(['pasajero.usuario', 'taxista.usuario', 'taxi.taxista.usuario', 'taxi'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $viajes->map(function ($viaje) {
                $taxista = $viaje->taxista ?: ($viaje->taxi ? $viaje->taxi->taxista : null);
                $taxistaUsuario = $taxista && $taxista->usuario ? $taxista->usuario : null;
                $taxi = $viaje->taxi;

                return [
                    'id' => $viaje->id,
                    'pasajero_id' => $viaje->id_pasajero,
                    'pasajero' => $viaje->pasajero && $viaje->pasajero->usuario ? [
                        'nombre' => $viaje->pasajero->usuario->nombre,
                        'apellido' => $viaje->pasajero->usuario->apellido,
                        'email' => $viaje->pasajero->usuario->email
                    ] : null,
                    'taxista' => $taxista && $taxistaUsuario ? [
                        'id' => $taxista->id,
                        'nombre' => $taxistaUsuario->nombre,
                        'apellido' => $taxistaUsuario->apellido,
                        'numero_taxi' => $taxi ? $taxi->numero_taxi : null,
                        'taxi' => $taxi ? [
                            'id' => $taxi->id,
                            'marca' => $taxi->marca,
                            'modelo' => $taxi->modelo,
                            'numero_taxi' => $taxi->numero_taxi
                        ] : null
                    ] : null,
                    'latitud_origen' => $viaje->latitud_origen,
                    'longitud_origen' => $viaje->longitud_origen,
                    'direccion_origen' => $viaje->direccion_origen,
                    'latitud_destino' => $viaje->latitud_destino,
                    'longitud_destino' => $viaje->longitud_destino,
                    'direccion_destino' => $viaje->direccion_destino,
                    'estado' => $viaje->estado,
                    'fecha_creacion' => $viaje->created_at->toIso8601String()
                ];
            })
        ]);
    }

    /**
     * GET /taxista/mis-viajes
     * Obtener todos los viajes del taxista autenticado
     */
    public function misViajes(Request $request): JsonResponse
    {
        $taxista = $request->user()->taxista;

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un taxista'
            ], 403);
        }

        // Obtener todos los viajes donde el taxista está asignado (a través de taxi o id_taxista)
        $viajes = Viaje::where(function($query) use ($taxista) {
                $query->where('id_taxista', $taxista->id)
                      ->orWhereHas('taxi', function($q) use ($taxista) {
                          $q->where('id_taxista', $taxista->id);
                      });
            })
            ->with(['pasajero.usuario.fotos', 'taxi.taxista.usuario.fotos', 'taxista.usuario.fotos', 'taxi', 'calificacion'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $viajes->map(function ($viaje) {
                // Obtener taxista (puede venir de taxi o directamente)
                $taxista = $viaje->taxista ?: ($viaje->taxi ? $viaje->taxi->taxista : null);
                $taxistaUsuario = $taxista && $taxista->usuario ? $taxista->usuario : null;

                // Formatear datos del pasajero
                $pasajeroData = null;
                if ($viaje->pasajero && $viaje->pasajero->usuario) {
                    $usuarioPasajero = $viaje->pasajero->usuario;
                    $fotoPasajero = $usuarioPasajero->fotos && $usuarioPasajero->fotos->count() > 0
                        ? $usuarioPasajero->fotos->first()->url
                        : null;

                    $pasajeroData = [
                        'id' => $usuarioPasajero->id,
                        'nombre' => $usuarioPasajero->nombre,
                        'apellido' => $usuarioPasajero->apellido,
                        'email' => $usuarioPasajero->email,
                        'id_rol' => $usuarioPasajero->id_rol,
                        'telefono' => $usuarioPasajero->telefono ?? null,
                        'foto' => $fotoPasajero,
                        'tipo' => 'pasajero'
                    ];
                }

                // Formatear datos del taxista
                $taxistaData = null;
                if ($taxista && $taxistaUsuario) {
                    $fotoTaxista = $taxistaUsuario->fotos && $taxistaUsuario->fotos->count() > 0
                        ? $taxistaUsuario->fotos->first()->url
                        : null;

                    $taxistaData = [
                        'id' => $taxistaUsuario->id,
                        'nombre' => $taxistaUsuario->nombre,
                        'apellido' => $taxistaUsuario->apellido,
                        'email' => $taxistaUsuario->email,
                        'id_rol' => $taxistaUsuario->id_rol,
                        'telefono' => $taxistaUsuario->telefono ?? null,
                        'foto' => $fotoTaxista,
                        'tipo' => 'taxista'
                    ];
                }

                return [
                    'id' => $viaje->id,
                    'pasajero_id' => $viaje->id_pasajero,
                    'taxista_id' => $viaje->id_taxista,
                    'latitud_origen' => $viaje->latitud_origen,
                    'longitud_origen' => $viaje->longitud_origen,
                    'direccion_origen' => $viaje->direccion_origen,
                    'latitud_destino' => $viaje->latitud_destino,
                    'longitud_destino' => $viaje->longitud_destino,
                    'direccion_destino' => $viaje->direccion_destino,
                    'estado' => $viaje->estado,
                    'fecha_creacion' => $viaje->created_at->toIso8601String(),
                    'fecha_aceptacion' => $viaje->fecha_aceptacion ? $viaje->fecha_aceptacion->toIso8601String() : null,
                    'fecha_completado' => $viaje->fecha_completado ? $viaje->fecha_completado->toIso8601String() : null,
                    'calificacion' => $viaje->calificacion ? (float)$viaje->calificacion->calificacion : null,
                    'comentario' => $viaje->calificacion ? $viaje->calificacion->comentario : null,
                    'pasajero' => $pasajeroData,
                    'taxista' => $taxistaData
                ];
            })
        ]);
    }

    /**
     * POST /taxista/aceptar-viaje/{viajeId}
     * Aceptar un viaje solicitado
     */
    public function aceptarViaje(Request $request, string $viajeId): JsonResponse
    {
        $taxista = $request->user()->taxista;

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un taxista'
            ], 403);
        }

        $viaje = Viaje::where('id', $viajeId)
            ->where('estado', Viaje::ESTADO_SOLICITADO)
            ->where(function($query) use ($taxista) {
                $query->where('id_taxista', $taxista->id)
                      ->orWhereNull('id_taxista');
            })
            ->first();

        if (!$viaje) {
            return response()->json([
                'success' => false,
                'message' => 'Viaje no disponible'
            ], 404);
        }

        // Obtener el taxi del taxista
        $taxi = Taxi::where('id_taxista', $taxista->id)->first();

        if (!$taxi) {
            return response()->json([
                'success' => false,
                'message' => 'Taxista no tiene taxi registrado'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $viaje->update([
                'id_taxista' => $taxista->id,
                'id_taxi' => $taxi->id,
                'estado' => Viaje::ESTADO_ACEPTADO,
                'fecha_aceptacion' => now()
            ]);

            DB::commit();

            // Recargar el viaje con todas las relaciones
            $viaje->refresh();
            $viaje->load(['pasajero.usuario.fotos', 'taxi.taxista.usuario.fotos', 'taxista.usuario.fotos', 'taxi', 'calificacion']);

            // Obtener taxista (puede venir de taxi o directamente)
            $taxistaRelacion = $viaje->taxista ?: ($viaje->taxi ? $viaje->taxi->taxista : null);
            $taxistaUsuario = $taxistaRelacion && $taxistaRelacion->usuario ? $taxistaRelacion->usuario : null;

            // Formatear datos del pasajero
            $pasajeroData = null;
            if ($viaje->pasajero && $viaje->pasajero->usuario) {
                $usuarioPasajero = $viaje->pasajero->usuario;
                $fotoPasajero = $usuarioPasajero->fotos && $usuarioPasajero->fotos->count() > 0
                    ? $usuarioPasajero->fotos->first()->url
                    : null;

                $pasajeroData = [
                    'id' => $usuarioPasajero->id,
                    'nombre' => $usuarioPasajero->nombre,
                    'apellido' => $usuarioPasajero->apellido,
                    'email' => $usuarioPasajero->email,
                    'id_rol' => $usuarioPasajero->id_rol,
                    'telefono' => $usuarioPasajero->telefono ?? null,
                    'foto' => $fotoPasajero,
                    'tipo' => 'pasajero'
                ];
            }

            // Formatear datos del taxista
            $taxistaData = null;
            if ($taxistaRelacion && $taxistaUsuario) {
                $fotoTaxista = $taxistaUsuario->fotos && $taxistaUsuario->fotos->count() > 0
                    ? $taxistaUsuario->fotos->first()->url
                    : null;

                $taxistaData = [
                    'id' => $taxistaUsuario->id,
                    'nombre' => $taxistaUsuario->nombre,
                    'apellido' => $taxistaUsuario->apellido,
                    'email' => $taxistaUsuario->email,
                    'id_rol' => $taxistaUsuario->id_rol,
                    'telefono' => $taxistaUsuario->telefono ?? null,
                    'foto' => $fotoTaxista,
                    'tipo' => 'taxista'
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Viaje aceptado exitosamente',
                'data' => [
                    'id' => $viaje->id,
                    'pasajero_id' => $viaje->id_pasajero,
                    'taxista_id' => $viaje->id_taxista,
                    'latitud_origen' => $viaje->latitud_origen,
                    'longitud_origen' => $viaje->longitud_origen,
                    'direccion_origen' => $viaje->direccion_origen,
                    'latitud_destino' => $viaje->latitud_destino,
                    'longitud_destino' => $viaje->longitud_destino,
                    'direccion_destino' => $viaje->direccion_destino,
                    'estado' => $viaje->estado,
                    'fecha_creacion' => $viaje->created_at->toIso8601String(),
                    'fecha_aceptacion' => $viaje->fecha_aceptacion ? $viaje->fecha_aceptacion->toIso8601String() : null,
                    'fecha_completado' => $viaje->fecha_completado ? $viaje->fecha_completado->toIso8601String() : null,
                    'calificacion' => $viaje->calificacion ? (float)$viaje->calificacion->calificacion : null,
                    'comentario' => $viaje->calificacion ? $viaje->calificacion->comentario : null,
                    'pasajero' => $pasajeroData,
                    'taxista' => $taxistaData
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al aceptar el viaje',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /taxista/rechazar-viaje/{viajeId}
     * Rechazar un viaje solicitado
     */
    public function rechazarViaje(Request $request, string $viajeId): JsonResponse
    {
        $taxista = $request->user()->taxista;

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un taxista'
            ], 403);
        }

        $viaje = Viaje::where('id', $viajeId)
            ->where('estado', Viaje::ESTADO_SOLICITADO)
            ->where('id_taxista', $taxista->id) // Solo puede rechazar viajes dirigidos a él
            ->first();

        if (!$viaje) {
            return response()->json([
                'success' => false,
                'message' => 'Viaje no disponible o no dirigido a este taxista'
            ], 404);
        }

        try {
            $viaje->update([
                'estado' => Viaje::ESTADO_RECHAZADO,
                'id_taxista' => null // Liberar el viaje para que otros taxistas puedan aceptarlo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Viaje rechazado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar el viaje',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /taxista/completar-viaje/{viajeId}
     * Marcar un viaje como completado
     */
    public function completarViaje(Request $request, string $viajeId): JsonResponse
    {
        $taxista = $request->user()->taxista;

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un taxista'
            ], 403);
        }

        $viaje = Viaje::where('id', $viajeId)
            ->where(function($query) use ($taxista) {
                $query->where('id_taxista', $taxista->id)
                      ->orWhereHas('taxi', function($q) use ($taxista) {
                          $q->where('id_taxista', $taxista->id);
                      });
            })
            ->whereIn('estado', [Viaje::ESTADO_ACEPTADO, Viaje::ESTADO_EN_PROGRESO])
            ->first();

        if (!$viaje) {
            return response()->json([
                'success' => false,
                'message' => 'Viaje no encontrado o no autorizado'
            ], 404);
        }

        try {
            $viaje->update([
                'estado' => Viaje::ESTADO_COMPLETADO,
                'fecha_completado' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Viaje completado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al completar el viaje',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

