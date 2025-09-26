<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class MatriculaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $matriculas = Matricula::with('estatus')->get();
        return response()->json([
            'success' => true,
            'data' => $matriculas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|string',
            'id_estatus' => 'required|string|exists:estatus_documentos,id'
        ]);

        $matricula = Matricula::create([
            'id' => Str::uuid(),
            'url' => $request->url,
            'fecha_subida' => now(),
            'id_estatus' => $request->id_estatus
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Matrícula creada exitosamente',
            'data' => $matricula->load('estatus')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $matricula = Matricula::with('estatus')->find($id);

        if (!$matricula) {
            return response()->json([
                'success' => false,
                'message' => 'Matrícula no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $matricula
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $matricula = Matricula::find($id);

        if (!$matricula) {
            return response()->json([
                'success' => false,
                'message' => 'Matrícula no encontrada'
            ], 404);
        }

        $request->validate([
            'url' => 'sometimes|string',
            'id_estatus' => 'sometimes|string|exists:estatus_documentos,id'
        ]);

        $matricula->update($request->only(['url', 'id_estatus']));

        return response()->json([
            'success' => true,
            'message' => 'Matrícula actualizada exitosamente',
            'data' => $matricula->load('estatus')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $matricula = Matricula::find($id);

        if (!$matricula) {
            return response()->json([
                'success' => false,
                'message' => 'Matrícula no encontrada'
            ], 404);
        }

        $matricula->delete();

        return response()->json([
            'success' => true,
            'message' => 'Matrícula eliminada exitosamente'
        ]);
    }
}
