<?php

namespace App\Http\Controllers;

use App\Models\Taxista;
use App\Models\Matricula;
use App\Models\Licencia;
use App\Models\Suscripcion;
use App\Services\EstatusDocumentoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TaxistaController extends Controller
{
    protected $estatusService;

    public function __construct(EstatusDocumentoService $estatusService)
    {
        $this->estatusService = $estatusService;

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
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $user = auth()->user();
        $taxista = Taxista::where('id_usuario', $user->id)->first();

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Taxista no encontrado'
            ], 404);
        }

        // Subir archivo
        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . Str::random(10) . '.' . $archivo->getClientOriginalExtension();
        $archivo->move(public_path('uploads/matriculas'), $nombreArchivo);

        // Crear matrícula
        $matricula = Matricula::create([
            'id' => Str::uuid(),
            'url' => $nombreArchivo,
            'fecha_subida' => now(),
            'id_estatus' => $this->estatusService->getPendienteId()
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
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $user = auth()->user();
        $taxista = Taxista::where('id_usuario', $user->id)->first();

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Taxista no encontrado'
            ], 404);
        }

        // Subir archivo
        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . Str::random(10) . '.' . $archivo->getClientOriginalExtension();
        $archivo->move(public_path('uploads/licencias'), $nombreArchivo);

        // Crear licencia
        $licencia = Licencia::create([
            'id' => Str::uuid(),
            'url' => $nombreArchivo,
            'fecha_subida' => now(),
            'id_estatus' => $this->estatusService->getPendienteId()
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
        $user = auth()->user();
        $taxista = Taxista::with(['matricula.estatus', 'licencia.estatus', 'taxis'])
                          ->where('id_usuario', $user->id)
                          ->first();

        // Obtener suscripción actual
        $suscripcion = null;
        if ($taxista) {
            $suscripcion = \App\Models\Suscripcion::where('id_taxista', $taxista->id)
                ->latest('created_at')
                ->first();
        }

        return view('taxista.dashboard', compact('taxista', 'suscripcion'));
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
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $user = auth()->user();
        $taxista = Taxista::where('id_usuario', $user->id)->first();

        if (!$taxista) {
            return back()->with('error', 'Taxista no encontrado');
        }

        // Subir archivo
        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . Str::random(10) . '.' . $archivo->getClientOriginalExtension();
        $archivo->move(public_path('uploads/matriculas'), $nombreArchivo);

        // Crear matrícula
        $matricula = Matricula::create([
            'id' => Str::uuid(),
            'url' => $nombreArchivo,
            'fecha_subida' => now(),
            'id_estatus' => $this->estatusService->getPendienteId()
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
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $user = auth()->user();
        $taxista = Taxista::where('id_usuario', $user->id)->first();

        if (!$taxista) {
            return back()->with('error', 'Taxista no encontrado');
        }

        // Subir archivo
        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . Str::random(10) . '.' . $archivo->getClientOriginalExtension();
        $archivo->move(public_path('uploads/licencias'), $nombreArchivo);

        // Crear licencia
        $licencia = Licencia::create([
            'id' => Str::uuid(),
            'url' => $nombreArchivo,
            'fecha_subida' => now(),
            'id_estatus' => $this->estatusService->getPendienteId()
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
        try {
            $taxistas = Taxista::with([
                'usuario.fotos',
                'matricula.estatus',
                'licencia.estatus',
                'taxis'
            ])
            ->whereHas('matricula', function($query) {
                $query->where('id_estatus', $this->estatusService->getAprobadoId());
            })
            ->whereHas('licencia', function($query) {
                $query->where('id_estatus', $this->estatusService->getAprobadoId());
            })
            ->get();

            return view('taxistas.index', compact('taxistas'));
        } catch (\Exception $e) {
            Log::error('Error al cargar taxistas: ' . $e->getMessage());
            return view('taxistas.index', ['taxistas' => collect()]);
        }
    }
}
