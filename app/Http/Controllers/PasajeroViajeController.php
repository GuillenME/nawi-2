<?php

namespace App\Http\Controllers;

use App\Models\Viaje;
use App\Models\Pasajero;
use App\Models\CalificacionViaje;
use App\Models\Taxista;
use App\Models\Taxi;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PasajeroViajeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * POST /pasajero/crear-viaje
     * Crear un nuevo viaje (con o sin taxista específico)
     */
    public function crearViaje(Request $request): JsonResponse
    {
        // ✅ Obtener el usuario autenticado del token
        $usuarioAutenticado = $request->user();

        if (!$usuarioAutenticado) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        // ✅ Obtener el pasajero del usuario autenticado
        $pasajero = $usuarioAutenticado->pasajero;

        if (!$pasajero) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario autenticado no es un pasajero'
            ], 403);
        }

        // ✅ Validar solo los datos de ubicación
        $validator = Validator::make($request->all(), [
            'salida' => 'required|array',
            'salida.lat' => 'required|numeric|between:-90,90',
            'salida.lon' => 'required|numeric|between:-180,180',
            'destino' => 'required|array',
            'destino.lat' => 'required|numeric|between:-90,90',
            'destino.lon' => 'required|numeric|between:-180,180',
            'id_taxista' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Validar que el taxista existe (si se envía)
        $taxistaId = null;
        $taxiId = null; // ✅ Agregar esta variable
        if ($request->has('id_taxista') && $request->id_taxista) {
            // El id_taxista que viene de Flutter es el ID del usuario, no el ID de la tabla taxistas
            $usuarioTaxista = Usuario::find($request->id_taxista);

            if (!$usuarioTaxista) {
                return response()->json([
                    'success' => false,
                    'message' => 'El taxista seleccionado no existe en la base de datos'
                ], 422);
            }

            // Verificar que el usuario tenga el rol de taxista
            if ($usuarioTaxista->id_rol != '00000000-0000-0000-0000-000000000003') {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario seleccionado no es un taxista'
                ], 422);
            }

            // Buscar el registro de taxista asociado al usuario
            $taxista = Taxista::where('id_usuario', $usuarioTaxista->id)->first();

            if (!$taxista) {
                return response()->json([
                    'success' => false,
                    'message' => 'El taxista no tiene un registro en la tabla taxistas'
                ], 422);
            }

            $taxistaId = $taxista->id; // ID de la tabla taxistas

            // ✅ OBTENER EL TAXI DEL TAXISTA (obligatorio)
            $taxi = Taxi::where('id_taxista', $taxista->id)->first();

            if (!$taxi) {
                return response()->json([
                    'success' => false,
                    'message' => 'El taxista seleccionado no tiene un taxi registrado'
                ], 422);
            }

            $taxiId = $taxi->id;
        }

        try {
    DB::beginTransaction();

    $direccionOrigen = 'Origen';
    $direccionDestino = 'Destino';

    // ✅ Preparar datos del viaje
    // Establecer tiempo límite de aceptación (5 minutos por defecto, o el especificado)
    $minutosLimite = $request->input('tiempo_limite_minutos', 5);
    $tiempoLimite = now()->addMinutes($minutosLimite);

    $viajeData = [
        'id' => Str::uuid(),
        'id_pasajero' => $pasajero->id,
        'latitud_origen' => $request->salida['lat'],
        'longitud_origen' => $request->salida['lon'],
        'direccion_origen' => $direccionOrigen,
        'latitud_destino' => $request->destino['lat'],
        'longitud_destino' => $request->destino['lon'],
        'direccion_destino' => $direccionDestino,
        'estado' => Viaje::ESTADO_SOLICITADO,
        'tiempo_limite_aceptacion' => $tiempoLimite,
    ];

    // ✅ Solo incluir id_taxista y id_taxi si existe el taxista
    if ($taxistaId) {
        $viajeData['id_taxista'] = $taxistaId;
        $viajeData['id_taxi'] = $taxiId; // ✅ Asignar el taxi del taxista (siempre existe si hay taxista)
    }

    $viaje = Viaje::create($viajeData);

    DB::commit();

    // Cargar las relaciones necesarias
    $viaje->load(['taxista.usuario', 'taxi', 'pasajero.usuario']);

    // Obtener todos los datos del viaje incluyendo el ID
    $viajeData = $viaje->toArray();

    // Formatear las fechas si existen
    if ($viaje->fecha_aceptacion) {
        $viajeData['fecha_aceptacion'] = $viaje->fecha_aceptacion->toIso8601String();
    } else {
        $viajeData['fecha_aceptacion'] = null;
    }

    if ($viaje->fecha_completado) {
        $viajeData['fecha_completado'] = $viaje->fecha_completado->toIso8601String();
    } else {
        $viajeData['fecha_completado'] = null;
    }

    if ($viaje->created_at) {
        $viajeData['created_at'] = $viaje->created_at->toIso8601String();
    }

    if ($viaje->updated_at) {
        $viajeData['updated_at'] = $viaje->updated_at->toIso8601String();
    }

    if ($viaje->tiempo_limite_aceptacion) {
        $viajeData['tiempo_limite_aceptacion'] = $viaje->tiempo_limite_aceptacion->toIso8601String();
    } else {
        $viajeData['tiempo_limite_aceptacion'] = null;
    }

    $viajeData['tarifa'] = $viaje->tarifa ? (float)$viaje->tarifa : null;

    // Agregar información del taxista si existe
    $taxista = $viaje->taxista;
    $taxi = $viaje->taxi;

    if ($taxista && $taxista->usuario) {
        $viajeData['taxista'] = [
            'id' => $taxista->id,
            'nombre' => $taxista->usuario->nombre,
            'apellido' => $taxista->usuario->apellido,
            'email' => $taxista->usuario->email,
            'numero_taxi' => $taxi ? $taxi->numero_taxi : null,
            'taxi' => $taxi ? [
                'id' => $taxi->id,
                'marca' => $taxi->marca,
                'modelo' => $taxi->modelo,
                'numero_taxi' => $taxi->numero_taxi
            ] : null
        ];
    } else {
        $viajeData['taxista'] = null;
    }

    // Agregar información del pasajero
    if ($viaje->pasajero && $viaje->pasajero->usuario) {
        $viajeData['pasajero'] = [
            'id' => $viaje->pasajero->id,
            'nombre' => $viaje->pasajero->usuario->nombre,
            'apellido' => $viaje->pasajero->usuario->apellido,
            'email' => $viaje->pasajero->usuario->email
        ];
    } else {
        $viajeData['pasajero'] = null;
    }

    return response()->json([
        'success' => true,
        'message' => 'Viaje creado exitosamente',
        'data' => $viajeData
    ], 201);
} catch (\Exception $e) {
    DB::rollBack();

    Log::error('Error al crear viaje: ' . $e->getMessage(), [
        'usuario_id' => $usuarioAutenticado->id,
        'request' => $request->all()
    ]);

    return response()->json([
        'success' => false,
        'message' => 'Error al crear el viaje',
        'error' => $e->getMessage()
    ], 500);
}
    }

    /**
     * GET /pasajero/mis-viajes
     * Obtener todos los viajes del pasajero autenticado
     */
    public function misViajes(Request $request): JsonResponse
    {
        $pasajero = $request->user()->pasajero;

        if (!$pasajero) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un pasajero'
            ], 403);
        }

        $viajes = Viaje::where('id_pasajero', $pasajero->id)
            ->with(['taxi.taxista.usuario.fotos', 'taxista.usuario.fotos', 'pasajero.usuario.fotos', 'calificacion'])
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
     * POST /pasajero/cancelar-viaje/{viajeId}
     * Cancelar un viaje solicitado
     */
    public function cancelarViaje(Request $request, string $viajeId): JsonResponse
    {
        $pasajero = $request->user()->pasajero;

        if (!$pasajero) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un pasajero'
            ], 403);
        }

        $viaje = Viaje::where('id', $viajeId)
            ->where('id_pasajero', $pasajero->id)
            ->first();

        if (!$viaje) {
            return response()->json([
                'success' => false,
                'message' => 'Viaje no encontrado'
            ], 404);
        }

        // Solo puede cancelar si el estado es solicitado o aceptado
        if (!in_array($viaje->estado, [Viaje::ESTADO_SOLICITADO, Viaje::ESTADO_ACEPTADO])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cancelar un viaje en estado: ' . $viaje->estado
            ], 422);
        }

        try {
            $viaje->update(['estado' => Viaje::ESTADO_CANCELADO]);

            return response()->json([
                'success' => true,
                'message' => 'Viaje cancelado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar el viaje',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /pasajero/calificar-viaje/{viajeId}
     * Calificar un viaje completado
     */
    public function calificarViaje(Request $request, string $viajeId): JsonResponse
    {
        // Validar que el ID del viaje no esté vacío
        if (empty($viajeId)) {
            return response()->json([
                'success' => false,
                'message' => 'ID del viaje no válido'
            ], 400);
        }

        // Validar datos de entrada
        $validator = Validator::make($request->all(), [
            'calificacion' => 'required|integer|between:1,5',
            'comentario' => [
                'nullable',
                'string',
                'max:500',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('/[<>\"\'&;]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ]
        ], [
            'comentario.max' => 'El comentario no puede exceder 500 caracteres.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que el usuario autenticado sea un pasajero
        $pasajero = $request->user()->pasajero;

        if (!$pasajero) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un pasajero'
            ], 403);
        }

        // Buscar el viaje y verificar que pertenezca al pasajero
        $viaje = Viaje::where('id', $viajeId)
            ->where('id_pasajero', $pasajero->id)
            ->first();

        if (!$viaje) {
            return response()->json([
                'success' => false,
                'message' => 'Viaje no encontrado o no pertenece al pasajero autenticado'
            ], 404);
        }

        // Verificar que el viaje esté en estado "completado"
        if ($viaje->estado !== Viaje::ESTADO_COMPLETADO) {
            return response()->json([
                'success' => false,
                'message' => 'El viaje debe estar completado para poder calificarlo. Estado actual: ' . $viaje->estado
            ], 422);
        }

        // Verificar si ya fue calificado
        if ($viaje->calificacion) {
            return response()->json([
                'success' => false,
                'message' => 'Este viaje ya fue calificado'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $calificacion = CalificacionViaje::create([
                'id' => Str::uuid(),
                'calificacion' => $request->calificacion,
                'id_pasajero' => $pasajero->id,
                'id_viaje' => $viaje->id,
                'comentario' => $request->comentario
            ]);

            DB::commit();

            // Retornar respuesta con los datos requeridos
            return response()->json([
                'success' => true,
                'message' => 'Viaje calificado exitosamente',
                'data' => [
                    'id' => $viaje->id,
                    'calificacion' => (int)$calificacion->calificacion,
                    'comentario' => $calificacion->comentario
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al calificar el viaje',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

