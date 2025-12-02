<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use App\Models\Taxista;
use App\Services\PaymentService;
use App\Mail\SuscripcionActivaMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SuscripcionController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('auth:api');
        $this->paymentService = $paymentService;
    }

    /**
     * Obtener la suscripción actual del taxista
     */
    public function miSuscripcion(Request $request): JsonResponse
    {
        $taxista = $request->user()->taxista;

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un taxista'
            ], 403);
        }

        $suscripcion = Suscripcion::where('id_taxista', $taxista->id)
            ->latest('created_at')
            ->first();

        if (!$suscripcion) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró suscripción'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $suscripcion->id,
                'fecha_inicio' => $suscripcion->fecha_inicio->toIso8601String(),
                'fecha_fin' => $suscripcion->fecha_fin->toIso8601String(),
                'precio' => (float)$suscripcion->precio,
                'monto' => $suscripcion->monto ? (float)$suscripcion->monto : null,
                'pagado' => (bool)$suscripcion->pagado,
                'activo' => (bool)$suscripcion->activo,
                'estado_pago' => $suscripcion->estado_pago,
                'metodo_pago' => $suscripcion->metodo_pago,
                'fecha_pago' => $suscripcion->fecha_pago ? $suscripcion->fecha_pago->toIso8601String() : null,
                'dias_restantes' => $suscripcion->diasRestantes(),
                'esta_activa' => $suscripcion->estaActiva(),
                'necesita_pago' => $suscripcion->necesitaPago()
            ]
        ]);
    }

    /**
     * Crear o renovar suscripción
     */
    public function crearSuscripcion(Request $request): JsonResponse
    {
        $request->validate([
            'metodo_pago' => 'required|string|in:tarjeta,transferencia,efectivo,paypal',
            'referencia_pago' => 'nullable|string|max:255',
            'monto' => 'nullable|numeric|min:0|max:9999.99'
        ]);

        $taxista = $request->user()->taxista;

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un taxista'
            ], 403);
        }

        $precioMensual = 120.00;
        $monto = $request->monto ?? $precioMensual;
        $fechaInicio = now();
        $fechaFin = $fechaInicio->copy()->addMonth();

        // Desactivar suscripciones anteriores
        Suscripcion::where('id_taxista', $taxista->id)
            ->where('activo', 1)
            ->update(['activo' => 0]);

        // Si el método de pago es tarjeta, crear PaymentIntent de Stripe
        $paymentIntent = null;
        if ($request->metodo_pago === 'tarjeta') {
            $result = $this->paymentService->createPaymentIntent(
                $monto,
                'mxn',
                [
                    'suscripcion_id' => 'pending',
                    'taxista_id' => $taxista->id,
                ]
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar el pago: ' . $result['error']
                ], 500);
            }

            $paymentIntent = $result;
        }

        // Crear nueva suscripción
        $suscripcion = Suscripcion::create([
            'id' => Str::uuid(),
            'id_taxista' => $taxista->id,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'precio' => $precioMensual,
            'monto' => $monto,
            'pagado' => 0,
            'activo' => 0, // Se activará cuando se confirme el pago
            'metodo_pago' => $request->metodo_pago,
            'referencia_pago' => $request->referencia_pago,
            'estado_pago' => 'pendiente',
            'id_transaccion' => $paymentIntent ? $paymentIntent['payment_intent_id'] : null
        ]);

        // Actualizar metadata del PaymentIntent con el ID de suscripción
        if ($paymentIntent) {
            try {
                \Stripe\PaymentIntent::update($paymentIntent['payment_intent_id'], [
                    'metadata' => [
                        'suscripcion_id' => $suscripcion->id,
                        'taxista_id' => $taxista->id,
                    ]
                ]);
            } catch (\Exception $e) {
                // Log error pero no fallar
                \Log::error('Error al actualizar PaymentIntent', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Suscripción creada. Por favor, completa el pago para activarla.',
            'data' => [
                'id' => $suscripcion->id,
                'precio' => (float)$suscripcion->precio,
                'monto' => (float)$suscripcion->monto,
                'metodo_pago' => $suscripcion->metodo_pago,
                'estado_pago' => $suscripcion->estado_pago,
                'fecha_fin' => $suscripcion->fecha_fin->toIso8601String(),
                'client_secret' => $paymentIntent ? $paymentIntent['client_secret'] : null
            ]
        ], 201);
    }

    /**
     * Confirmar pago de suscripción
     */
    public function confirmarPago(Request $request, string $suscripcionId): JsonResponse
    {
        $request->validate([
            'id_transaccion' => 'nullable|string|max:255',
            'referencia_pago' => 'nullable|string|max:255'
        ]);

        $taxista = $request->user()->taxista;

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un taxista'
            ], 403);
        }

        $suscripcion = Suscripcion::where('id', $suscripcionId)
            ->where('id_taxista', $taxista->id)
            ->first();

        if (!$suscripcion) {
            return response()->json([
                'success' => false,
                'message' => 'Suscripción no encontrada'
            ], 404);
        }

        // Si hay id_transaccion, verificar el pago con Stripe
        if ($request->id_transaccion && $suscripcion->metodo_pago === 'tarjeta') {
            $result = $this->paymentService->confirmPayment($request->id_transaccion);
            
            if (!$result['success'] || $result['status'] !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'El pago no se pudo confirmar: ' . ($result['error'] ?? 'Estado inválido')
                ], 400);
            }
        }

        // Actualizar suscripción como pagada
        $suscripcion->update([
            'pagado' => 1,
            'activo' => 1,
            'estado_pago' => 'completado',
            'fecha_pago' => now(),
            'id_transaccion' => $request->id_transaccion ?? $suscripcion->id_transaccion,
            'referencia_pago' => $request->referencia_pago ?? $suscripcion->referencia_pago
        ]);

        // Enviar email de confirmación
        if ($suscripcion->taxista && $suscripcion->taxista->usuario) {
            try {
                Mail::to($suscripcion->taxista->usuario->email)
                    ->send(new SuscripcionActivaMail($suscripcion));
            } catch (\Exception $e) {
                \Log::error('Error al enviar email de suscripción activa', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago confirmado. Tu suscripción está activa.',
            'data' => [
                'id' => $suscripcion->id,
                'activo' => true,
                'pagado' => true,
                'estado_pago' => $suscripcion->estado_pago,
                'fecha_fin' => $suscripcion->fecha_fin->toIso8601String(),
                'dias_restantes' => $suscripcion->diasRestantes()
            ]
        ]);
    }

    /**
     * Historial de suscripciones
     */
    public function historial(Request $request): JsonResponse
    {
        $taxista = $request->user()->taxista;

        if (!$taxista) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no es un taxista'
            ], 403);
        }

        $suscripciones = Suscripcion::where('id_taxista', $taxista->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $suscripciones->map(function ($suscripcion) {
                return [
                    'id' => $suscripcion->id,
                    'fecha_inicio' => $suscripcion->fecha_inicio->toIso8601String(),
                    'fecha_fin' => $suscripcion->fecha_fin->toIso8601String(),
                    'precio' => (float)$suscripcion->precio,
                    'monto' => $suscripcion->monto ? (float)$suscripcion->monto : null,
                    'pagado' => (bool)$suscripcion->pagado,
                    'activo' => (bool)$suscripcion->activo,
                    'estado_pago' => $suscripcion->estado_pago,
                    'metodo_pago' => $suscripcion->metodo_pago,
                    'fecha_pago' => $suscripcion->fecha_pago ? $suscripcion->fecha_pago->toIso8601String() : null,
                    'dias_restantes' => $suscripcion->diasRestantes(),
                    'esta_activa' => $suscripcion->estaActiva()
                ];
            })
        ]);
    }
}
