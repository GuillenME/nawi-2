<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Pasajero;
use App\Models\Taxista;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Registro de pasajero (rol = 2)
     */
    public function registerPasajero(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:45',
            'apellido' => 'required|string|max:45',
            'telefono' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|string|min:6'
        ]);

        // Crear usuario con rol pasajero (id = 2)
        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_rol' => '2' // Rol pasajero
        ]);

        // Crear registro en tabla pasajeros
        Pasajero::create([
            'id' => Str::uuid(),
            'id_usuario' => $usuario->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pasajero registrado exitosamente',
            'data' => [
                'usuario' => $usuario->load('rol'),
                'tipo' => 'pasajero'
            ]
        ], 201);
    }

    /**
     * Registro de taxista (rol = 3) - Solo datos básicos
     */
    public function registerTaxista(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:45',
            'apellido' => 'required|string|max:45',
            'telefono' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|string|min:6'
        ]);

        // Crear usuario con rol taxista (id = 3)
        $usuario = Usuario::create([
            'id' => Str::uuid(),
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_rol' => '3' // Rol taxista
        ]);

        // Crear registro en tabla taxistas (sin documentos por ahora)
        Taxista::create([
            'id' => Str::uuid(),
            'id_usuario' => $usuario->id,
            'id_matricula' => null, // Se agregará después
            'id_licencia' => null   // Se agregará después
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Taxista registrado exitosamente. Puedes subir tus documentos después del login.',
            'data' => [
                'usuario' => $usuario->load('rol'),
                'tipo' => 'taxista'
            ]
        ], 201);
    }

    /**
     * Login de usuario con Passport
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        // Crear token de acceso
        $token = $usuario->createToken('API Token')->accessToken;

        // Determinar el tipo de usuario
        $tipo = '';
        if ($usuario->pasajero) {
            $tipo = 'pasajero';
        } elseif ($usuario->taxista) {
            $tipo = 'taxista';
        } elseif ($usuario->admin) {
            $tipo = 'admin';
        }

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'usuario' => $usuario->load('rol'),
                'tipo' => $tipo,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Logout de usuario (revocar token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }

    /**
     * Logout de todos los dispositivos (revocar todos los tokens)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesiones cerradas en todos los dispositivos'
        ]);
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function me(Request $request): JsonResponse
    {
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        // Determinar el tipo de usuario
        $tipo = '';
        if ($usuario->pasajero) {
            $tipo = 'pasajero';
        } elseif ($usuario->taxista) {
            $tipo = 'taxista';
        } elseif ($usuario->admin) {
            $tipo = 'admin';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'usuario' => $usuario->load('rol'),
                'tipo' => $tipo
            ]
        ]);
    }
}
