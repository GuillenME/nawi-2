<?php

namespace App\Http\Controllers;

use App\Models\Licencia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class LicenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $licencias = Licencia::with('estatus')->get();
        return response()->json([
            'success' => true,
            'data' => $licencias
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

        $licencia = Licencia::create([
            'id' => Str::uuid(),
            'url' => $request->url,
            'fecha_subida' => now(),
            'id_estatus' => $request->id_estatus
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Licencia creada exitosamente',
            'data' => $licencia->load('estatus')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $licencia = Licencia::with('estatus')->find($id);

        if (!$licencia) {
            return response()->json([
                'success' => false,
                'message' => 'Licencia no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $licencia
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $licencia = Licencia::find($id);

        if (!$licencia) {
            return response()->json([
                'success' => false,
                'message' => 'Licencia no encontrada'
            ], 404);
        }

        $request->validate([
            'url' => 'sometimes|string',
            'id_estatus' => 'sometimes|string|exists:estatus_documentos,id'
        ]);

        $licencia->update($request->only(['url', 'id_estatus']));

        return response()->json([
            'success' => true,
            'message' => 'Licencia actualizada exitosamente',
            'data' => $licencia->load('estatus')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $licencia = Licencia::find($id);

        if (!$licencia) {
            return response()->json([
                'success' => false,
                'message' => 'Licencia no encontrada'
            ], 404);
        }

        $licencia->delete();

        return response()->json([
            'success' => true,
            'message' => 'Licencia eliminada exitosamente'
        ]);
    }
}
