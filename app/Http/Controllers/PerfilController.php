<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('perfil.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('perfil.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'apellidos' => [
                'nullable',
                'string',
                'max:100',
                'min:3',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('/[<>\"\'&;]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ],
            'edad' => 'nullable|integer|min:1|max:120',
            'ine' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('/[<>\"\'&;]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ],
            'permiso' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('/[<>\"\'&;]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ],
            'telefono' => [
                'nullable',
                'string',
                'size:10',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('/[<>\"\'&;]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('/[<>]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos como < o >.');
                    }
                }
            ],
            'estado' => 'in:activo,inactivo',
            'turno' => 'nullable|in:mañana,tarde,noche',
            'licencia' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
            'foto_conductor' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_taxi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('/[<>]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos como < o >.');
                    }
                }
            ],
        ], [
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
            'apellidos.min' => 'Los apellidos deben tener al menos 3 caracteres.',
            'telefono.size' => 'El teléfono debe tener exactamente 10 números.',
            'telefono.regex' => 'El teléfono solo puede contener números.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.'
        ]);

        // Sanitizar y actualizar campos generales
        $user->apellidos = $request->apellidos ? trim(strip_tags($request->apellidos)) : $user->apellidos;
        $user->edad = $request->edad ?? $user->edad;
        $user->ine = $request->ine ? trim(strip_tags($request->ine)) : $user->ine;
        $user->permiso = $request->permiso ? trim(strip_tags($request->permiso)) : $user->permiso;
        $user->telefono = $request->telefono ? trim(strip_tags($request->telefono)) : $user->telefono;
        $user->direccion = $request->direccion ? trim(strip_tags($request->direccion)) : $user->direccion;
        $user->estado = $request->estado ?? $user->estado;
        $user->turno = $request->turno;

        // Guardar archivos
        if ($request->hasFile('licencia')) {
            $user->licencia = $request->file('licencia')->store('licencias', 'public');
        }
        if ($request->hasFile('foto_conductor')) {
            $user->foto_conductor = $request->file('foto_conductor')->store('fotos', 'public');
        }
        if ($request->hasFile('foto_taxi')) {
            $user->foto_taxi = $request->file('foto_taxi')->store('fotos', 'public');
        }

        // Contraseña
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('perfil.show')->with('success', 'Perfil actualizado correctamente');
    }
}
