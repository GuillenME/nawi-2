<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class WebAuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas no coinciden con nuestros registros.'],
            ]);
        }

        // Autenticar al usuario
        Auth::login($usuario);

        // Redirigir según el tipo de usuario
        if ($usuario->taxista) {
            return redirect()->route('taxista.dashboard');
        } else {
            return redirect()->route('home');
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Mostrar formulario de registro de taxista
     */
    public function showTaxistaRegisterForm()
    {
        return view('auth.register-taxista');
    }

    /**
     * Procesar registro de taxista
     */
    public function registerTaxista(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:45',
            'apellido' => 'required|string|max:45',
            'telefono' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        // Crear usuario con rol taxista (id = 3)
        $usuario = Usuario::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_rol' => '3' // Rol taxista
        ]);

        // Crear registro en tabla taxistas (sin documentos por ahora)
        $usuario->taxista()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'id_matricula' => null,
            'id_licencia' => null
        ]);

        // Autenticar al usuario después del registro
        Auth::login($usuario);

        return redirect()->route('taxista.dashboard')
            ->with('success', '¡Registro exitoso! Ahora puedes subir tus documentos.');
    }
}
