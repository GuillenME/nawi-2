<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Suscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

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

        $usuario = Usuario::with('rol')->where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas no coinciden con nuestros registros.'],
            ]);
        }

        // Autenticar al usuario
        Auth::login($usuario);

        // Redirigir según el tipo de usuario
        if ($usuario->rol->nombre === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($usuario->taxista) {
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
            'nombre' => [
                'required',
                'string',
                'max:45',
                'min:3',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',
                function ($attribute, $value, $fail) {
                    // Bloquear símbolos peligrosos que puedan causar XSS, SQL injection, etc.
                    if (preg_match('/[<>\"\'&;{}\[\]()|\\`~!@#$%^*+=?:,\/]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ],
            'apellido' => [
                'required',
                'string',
                'max:45',
                'min:3',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',
                function ($attribute, $value, $fail) {
                    // Bloquear símbolos peligrosos que puedan causar XSS, SQL injection, etc.
                    if (preg_match('/[<>\"\'&;{}\[\]()|\\`~!@#$%^*+=?:,\/]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ],
            'telefono' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/',
                function ($attribute, $value, $fail) {
                    // Bloquear símbolos peligrosos
                    if (preg_match('/[<>\"\'&;{}\[\]()|\\`~!@#$%^*+=?:,\/\s\w]/', $value)) {
                        $fail('El campo ' . $attribute . ' solo puede contener números.');
                    }
                }
            ],
            'email' => [
                'required',
                'email',
                'max:100',
                'unique:usuarios,email',
                function ($attribute, $value, $fail) {
                    // Bloquear símbolos peligrosos específicos que no son parte de un email válido
                    if (preg_match('/[<>\"\'&;{}\[\]()|\\`~!#$%^*+=?:,\/]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/',
                function ($attribute, $value, $fail) {
                    // Bloquear símbolos peligrosos específicos que pueden causar problemas
                    if (preg_match('/[<>\"\'&;{}\[\]()|\\`~]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos como < > " \' & ; { } [ ] ( ) | \\ ` ~.');
                    }
                }
            ]
        ], [
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'apellido.min' => 'El apellido debe tener al menos 3 caracteres.',
            'telefono.size' => 'El teléfono debe tener exactamente 10 números.',
            'telefono.regex' => 'El teléfono solo puede contener números (10 dígitos).',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&#).',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ]);

        // Sanitizar datos antes de guardar
        $nombre = trim(strip_tags($request->nombre));
        $apellido = trim(strip_tags($request->apellido));
        $telefono = trim(strip_tags($request->telefono));
        $email = trim(filter_var($request->email, FILTER_SANITIZE_EMAIL));

        // Crear usuario con rol taxista (id = 3)
        $usuario = Usuario::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'nombre' => $nombre,
            'apellido' => $apellido,
            'telefono' => $telefono,
            'email' => $email,
            'password' => Hash::make($request->password),
            'id_rol' => '00000000-0000-0000-0000-000000000003' // Rol taxista
        ]);

        // Crear registro en tabla taxistas (sin documentos por ahora)
        $taxista = $usuario->taxista()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'id_matricula' => null,
            'id_licencia' => null
        ]);

        // Crear suscripción inicial (sin pago, pendiente)
        $fechaInicio = now();
        $fechaFin = $fechaInicio->copy()->addMonth();

        Suscripcion::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'id_taxista' => $taxista->id,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'precio' => 120.00,
            'pagado' => 0,
            'activo' => 0,
            'estado_pago' => 'pendiente'
        ]);

        // Autenticar al usuario después del registro
        Auth::login($usuario);

        return redirect()->route('taxista.dashboard')
            ->with('success', '¡Registro exitoso! Debes activar tu suscripción para usar el servicio.');
    }
}
