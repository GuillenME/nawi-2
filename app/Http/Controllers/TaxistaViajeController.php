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
        // 4. Que no hayan expirado (tiempo_limite_aceptacion es null o mayor a ahora)
        // 5. Que tengan un ID válido (no null)
        $viajes = Viaje::where('estado', Viaje::ESTADO_SOLICITADO)
            ->whereNotNull('id') // Asegurar que el ID no sea null
            ->where(function($query) use ($taxista) {
                $query->where('id_taxista', $taxista->id)
                      ->orWhereNull('id_taxista');
            })
            ->where(function($query) {
                $query->whereNull('tiempo_limite_aceptacion')
                      ->orWhere('tiempo_limite_aceptacion', '>', now());
            })
            ->with(['pasajero.usuario', 'taxista.usuario', 'taxi.taxista.usuario', 'taxi'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $viajes->filter(function ($viaje) {
                // Filtrar viajes que no tengan ID válido
                return !empty($viaje->id);
            })->map(function ($viaje) {
                $taxista = $viaje->taxista ?: ($viaje->taxi ? $viaje->taxi->taxista : null);
                $taxistaUsuario = $taxista && $taxista->usuario ? $taxista->usuario : null;
                $taxi = $viaje->taxi;

                // Asegurar que el ID siempre esté presente y no sea null
                $viajeId = $viaje->id;
                if (empty($viajeId)) {
                    // Si por alguna razón el ID está vacío, saltar este viaje
                    return null;
                }

                return [
                    'id' => $viajeId, // Campo 'id' exactamente como se requiere
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
                    'fecha_creacion' => $viaje->created_at->toIso8601String(),
                    'tiempo_limite_aceptacion' => $viaje->tiempo_limite_aceptacion ? $viaje->tiempo_limite_aceptacion->toIso8601String() : null,
                    'tarifa' => $viaje->tarifa ? (float)$viaje->tarifa : null
                ];
            })->filter(function ($viaje) {
                // Filtrar cualquier viaje que sea null (por ID inválido)
                return $viaje !== null;
            })->values() // Reindexar el array para que los índices sean secuenciales
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
                    'tiempo_limite_aceptacion' => $viaje->tiempo_limite_aceptacion ? $viaje->tiempo_limite_aceptacion->toIso8601String() : null,
                    'tarifa' => $viaje->tarifa ? (float)$viaje->tarifa : null,
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
        // Validar que el ID del viaje no esté vacío o sea null
        if (empty($viajeId)) {
            return response()->json([
                'success' => false,
                'message' => 'ID del viaje no válido. El viaje no tiene un ID válido.'
            ], 400);
        }

        $taxista = $request->user()->taxista;

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un taxista'
            ], 403);
        }

        // Primero buscar el viaje sin restricciones de estado para dar mejor feedback
        $viaje = Viaje::find($viajeId);
        
        // Validar que el viaje encontrado tenga un ID válido
        if ($viaje && empty($viaje->id)) {
            return response()->json([
                'success' => false,
                'message' => 'ID del viaje no válido. El viaje no tiene un ID válido.'
            ], 400);
        }

        if (!$viaje) {
            return response()->json([
                'success' => false,
                'message' => 'Viaje no encontrado'
            ], 404);
        }

        // Validar que el viaje esté en estado solicitado
        if ($viaje->estado !== Viaje::ESTADO_SOLICITADO) {
            $mensajeEstado = match($viaje->estado) {
                Viaje::ESTADO_ACEPTADO => 'Este viaje ya fue aceptado por otro taxista',
                Viaje::ESTADO_EN_PROGRESO => 'Este viaje ya está en progreso',
                Viaje::ESTADO_COMPLETADO => 'Este viaje ya fue completado',
                Viaje::ESTADO_CANCELADO => 'Este viaje fue cancelado',
                Viaje::ESTADO_RECHAZADO => 'Este viaje fue rechazado',
                default => 'El viaje no está disponible. Estado actual: ' . $viaje->estado
            };
            
            return response()->json([
                'success' => false,
                'message' => $mensajeEstado
            ], 422);
        }

        // Validar que el viaje no haya expirado
        if ($viaje->tiempo_limite_aceptacion && now()->greaterThan($viaje->tiempo_limite_aceptacion)) {
            // Marcar el viaje como cancelado si expiró
            $viaje->update(['estado' => Viaje::ESTADO_CANCELADO]);
            
            return response()->json([
                'success' => false,
                'message' => 'El tiempo límite para aceptar este viaje ha expirado'
            ], 422);
        }

        // Validar que el viaje esté disponible para este taxista
        // Puede aceptar si: id_taxista es null (disponible para todos) O id_taxista es igual al taxista autenticado
        if ($viaje->id_taxista !== null && $viaje->id_taxista !== $taxista->id) {
            return response()->json([
                'success' => false,
                'message' => 'Este viaje fue asignado a otro taxista'
            ], 422);
        }

        // Validar tarifa si se proporciona
        $validator = Validator::make($request->all(), [
            'tarifa' => 'nullable|numeric|min:0|max:9999.99'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
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

            // Re-verificar el estado del viaje dentro de la transacción con bloqueo
            // para evitar condiciones de carrera
            $viajeBloqueado = Viaje::lockForUpdate()->find($viajeId);
            
            if (!$viajeBloqueado || $viajeBloqueado->estado !== Viaje::ESTADO_SOLICITADO) {
                DB::rollBack();
                $mensajeEstado = $viajeBloqueado ? match($viajeBloqueado->estado) {
                    Viaje::ESTADO_ACEPTADO => 'Este viaje ya fue aceptado por otro taxista',
                    Viaje::ESTADO_EN_PROGRESO => 'Este viaje ya está en progreso',
                    Viaje::ESTADO_COMPLETADO => 'Este viaje ya fue completado',
                    Viaje::ESTADO_CANCELADO => 'Este viaje fue cancelado',
                    Viaje::ESTADO_RECHAZADO => 'Este viaje fue rechazado',
                    default => 'El viaje no está disponible. Estado actual: ' . $viajeBloqueado->estado
                } : 'Viaje no encontrado';
                
                return response()->json([
                    'success' => false,
                    'message' => $mensajeEstado
                ], 422);
            }

            // Verificar nuevamente que el viaje esté disponible para este taxista
            if ($viajeBloqueado->id_taxista !== null && $viajeBloqueado->id_taxista !== $taxista->id) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Este viaje fue asignado a otro taxista'
                ], 422);
            }

            $updateData = [
                'id_taxista' => $taxista->id,
                'id_taxi' => $taxi->id,
                'estado' => Viaje::ESTADO_ACEPTADO,
                'fecha_aceptacion' => now()
            ];

            // Agregar tarifa si se proporciona
            if ($request->has('tarifa') && $request->tarifa !== null) {
                $updateData['tarifa'] = $request->tarifa;
            }

            $viajeBloqueado->update($updateData);
            $viaje = $viajeBloqueado;

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
                    'tiempo_limite_aceptacion' => $viaje->tiempo_limite_aceptacion ? $viaje->tiempo_limite_aceptacion->toIso8601String() : null,
                    'tarifa' => $viaje->tarifa ? (float)$viaje->tarifa : null,
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

        // Primero buscar el viaje sin restricciones para dar mejor feedback
        $viaje = Viaje::find($viajeId);

        if (!$viaje) {
            return response()->json([
                'success' => false,
                'message' => 'Viaje no encontrado'
            ], 404);
        }

        // Validar que el viaje esté en estado solicitado
        if ($viaje->estado !== Viaje::ESTADO_SOLICITADO) {
            $mensajeEstado = match($viaje->estado) {
                Viaje::ESTADO_ACEPTADO => 'Este viaje ya fue aceptado',
                Viaje::ESTADO_EN_PROGRESO => 'Este viaje ya está en progreso',
                Viaje::ESTADO_COMPLETADO => 'Este viaje ya fue completado',
                Viaje::ESTADO_CANCELADO => 'Este viaje fue cancelado',
                Viaje::ESTADO_RECHAZADO => 'Este viaje ya fue rechazado',
                default => 'El viaje no está disponible. Estado actual: ' . $viaje->estado
            };
            
            return response()->json([
                'success' => false,
                'message' => $mensajeEstado
            ], 422);
        }

        // Solo puede rechazar viajes dirigidos específicamente a él
        if ($viaje->id_taxista !== $taxista->id) {
            return response()->json([
                'success' => false,
                'message' => 'Este viaje no está dirigido a ti'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Re-verificar el estado del viaje dentro de la transacción con bloqueo
            $viajeBloqueado = Viaje::lockForUpdate()->find($viajeId);
            
            if (!$viajeBloqueado || $viajeBloqueado->estado !== Viaje::ESTADO_SOLICITADO) {
                DB::rollBack();
                $mensajeEstado = $viajeBloqueado ? match($viajeBloqueado->estado) {
                    Viaje::ESTADO_ACEPTADO => 'Este viaje ya fue aceptado',
                    Viaje::ESTADO_EN_PROGRESO => 'Este viaje ya está en progreso',
                    Viaje::ESTADO_COMPLETADO => 'Este viaje ya fue completado',
                    Viaje::ESTADO_CANCELADO => 'Este viaje fue cancelado',
                    Viaje::ESTADO_RECHAZADO => 'Este viaje ya fue rechazado',
                    default => 'El viaje no está disponible. Estado actual: ' . $viajeBloqueado->estado
                } : 'Viaje no encontrado';
                
                return response()->json([
                    'success' => false,
                    'message' => $mensajeEstado
                ], 422);
            }

            // Verificar nuevamente que el viaje esté dirigido a este taxista
            if ($viajeBloqueado->id_taxista !== $taxista->id) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Este viaje no está dirigido a ti'
                ], 422);
            }

            $viajeBloqueado->update([
                'estado' => Viaje::ESTADO_RECHAZADO,
                'id_taxista' => null // Liberar el viaje para que otros taxistas puedan aceptarlo
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Viaje rechazado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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

