<?php

namespace App\Http\Controllers;

use App\Models\Taxista;
use App\Models\Matricula;
use App\Models\Licencia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TaxistaController extends Controller
{
    public function __construct()
    {
        // Solo aplicar autenticación a métodos específicos de API
        $this->middleware('auth:api')->only([
            'me', 'uploadMatricula', 'uploadLicencia', 'getDocuments'
        ]);

        // Aplicar autenticación web a métodos que requieren login
        $this->middleware('auth')->only([
            'dashboard', 'documents', 'uploadMatriculaWeb', 'uploadLicenciaWeb'
        ]);
    }

    /**
     * Obtener información del taxista autenticado
     */
    public function me(): JsonResponse
    {
        $user = auth()->user();
        $taxista = Taxista::with(['usuario.rol', 'matricula.estatus', 'licencia.estatus'])
                          ->where('id_usuario', $user->id)
                          ->first();

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Taxista no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $taxista
        ]);
    }

    /**
     * Subir matrícula
     */
    public function uploadMatricula(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|string',
            'id_estatus' => 'sometimes|string|exists:estatus_documentos,id'
        ]);

        $user = auth()->user();
        $taxista = Taxista::where('id_usuario', $user->id)->first();

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Taxista no encontrado'
            ], 404);
        }

        // Crear matrícula
        $matricula = Matricula::create([
            'id' => Str::uuid(),
            'url' => $request->url,
            'fecha_subida' => now(),
            'id_estatus' => $request->id_estatus ?? '1' // pendiente por defecto
        ]);

        // Actualizar taxista con la matrícula
        $taxista->update(['id_matricula' => $matricula->id]);

        return response()->json([
            'success' => true,
            'message' => 'Matrícula subida exitosamente',
            'data' => $matricula->load('estatus')
        ], 201);
    }

    /**
     * Subir licencia
     */
    public function uploadLicencia(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|string',
            'id_estatus' => 'sometimes|string|exists:estatus_documentos,id'
        ]);

        $user = auth()->user();
        $taxista = Taxista::where('id_usuario', $user->id)->first();

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Taxista no encontrado'
            ], 404);
        }

        // Crear licencia
        $licencia = Licencia::create([
            'id' => Str::uuid(),
            'url' => $request->url,
            'fecha_subida' => now(),
            'id_estatus' => $request->id_estatus ?? '1' // pendiente por defecto
        ]);

        // Actualizar taxista con la licencia
        $taxista->update(['id_licencia' => $licencia->id]);

        return response()->json([
            'success' => true,
            'message' => 'Licencia subida exitosamente',
            'data' => $licencia->load('estatus')
        ], 201);
    }

    /**
     * Obtener documentos del taxista
     */
    public function getDocuments(): JsonResponse
    {
        $user = auth()->user();
        $taxista = Taxista::with(['matricula.estatus', 'licencia.estatus'])
                          ->where('id_usuario', $user->id)
                          ->first();

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Taxista no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'matricula' => $taxista->matricula,
                'licencia' => $taxista->licencia
            ]
        ]);
    }

    /**
     * Dashboard del taxista
     */
    public function dashboard()
    {
        return view('taxista.dashboard');
    }

    /**
     * Vista de documentos del taxista
     */
    public function documents()
    {
        return view('taxista.documents');
    }

    /**
     * Subir matrícula (vista web)
     */
    public function uploadMatriculaWeb(Request $request)
    {
        $request->validate([
            'url' => 'required|string'
        ]);

        $user = auth()->user();
        $taxista = Taxista::where('id_usuario', $user->id)->first();

        if (!$taxista) {
            return back()->with('error', 'Taxista no encontrado');
        }

        // Crear matrícula
        $matricula = Matricula::create([
            'id' => Str::uuid(),
            'url' => $request->url,
            'fecha_subida' => now(),
            'id_estatus' => '1' // pendiente
        ]);

        // Actualizar taxista con la matrícula
        $taxista->update(['id_matricula' => $matricula->id]);

        return back()->with('success', 'Matrícula subida exitosamente');
    }

    /**
     * Subir licencia (vista web)
     */
    public function uploadLicenciaWeb(Request $request)
    {
        $request->validate([
            'url' => 'required|string'
        ]);

        $user = auth()->user();
        $taxista = Taxista::where('id_usuario', $user->id)->first();

        if (!$taxista) {
            return back()->with('error', 'Taxista no encontrado');
        }

        // Crear licencia
        $licencia = Licencia::create([
            'id' => Str::uuid(),
            'url' => $request->url,
            'fecha_subida' => now(),
            'id_estatus' => '1' // pendiente
        ]);

        // Actualizar taxista con la licencia
        $taxista->update(['id_licencia' => $licencia->id]);

        return back()->with('success', 'Licencia subida exitosamente');
    }

    /**
     * Vista para taxistas (mantener compatibilidad)
     */
    public function index()
    {
        $taxistas = Taxista::with(['usuario', 'matricula.estatus', 'licencia.estatus'])->get();
        return view('taxistas.index', compact('taxistas'));
    }
}
