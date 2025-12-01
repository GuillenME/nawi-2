<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GeneroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $generos = Genero::all();
        return response()->json([
            'success' => true,
            'data' => $generos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'tipo' => [
                'required',
                'string',
                'max:45',
                'min:3',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[<>\"\'&;]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ]
        ], [
            'tipo.regex' => 'El tipo solo puede contener letras y espacios.',
            'tipo.min' => 'El tipo debe tener al menos 3 caracteres.'
        ]);

        // Sanitizar datos antes de guardar
        $tipo = trim(strip_tags($request->tipo));
        $genero = Genero::create(['tipo' => $tipo]);

        return response()->json([
            'success' => true,
            'message' => 'Género creado exitosamente',
            'data' => $genero
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $genero = Genero::find($id);

        if (!$genero) {
            return response()->json([
                'success' => false,
                'message' => 'Género no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $genero
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'tipo' => [
                'required',
                'string',
                'max:45',
                'min:3',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[<>\"\'&;]/', $value)) {
                        $fail('El campo ' . $attribute . ' no puede contener símbolos peligrosos.');
                    }
                }
            ]
        ], [
            'tipo.regex' => 'El tipo solo puede contener letras y espacios.',
            'tipo.min' => 'El tipo debe tener al menos 3 caracteres.'
        ]);

        $genero = Genero::find($id);

        if (!$genero) {
            return response()->json([
                'success' => false,
                'message' => 'Género no encontrado'
            ], 404);
        }

        // Sanitizar datos antes de actualizar
        $tipo = trim(strip_tags($request->tipo));
        $genero->update(['tipo' => $tipo]);

        return response()->json([
            'success' => true,
            'message' => 'Género actualizado exitosamente',
            'data' => $genero
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $genero = Genero::find($id);

        if (!$genero) {
            return response()->json([
                'success' => false,
                'message' => 'Género no encontrado'
            ], 404);
        }

        $genero->delete();

        return response()->json([
            'success' => true,
            'message' => 'Género eliminado exitosamente'
        ]);
    }
}
