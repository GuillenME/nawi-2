<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use App\Services\PaymentService;
use App\Mail\SuscripcionActivaMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WebSuscripcionController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('auth');
        $this->paymentService = $paymentService;
    }

    /**
     * Mostrar página de suscripción (solo para taxistas)
     */
    public function index()
    {
        $usuario = Auth::user();

        // Verificar que sea taxista
        if (!$usuario->taxista) {
            return redirect()->route('home')->with('error', 'Solo los taxistas pueden acceder a esta sección.');
        }

        $taxista = $usuario->taxista;

        // Obtener suscripción actual
        $suscripcion = Suscripcion::where('id_taxista', $taxista->id)
            ->latest('created_at')
            ->first();

        return view('taxista.suscripcion.index', [
            'suscripcion' => $suscripcion,
            'taxista' => $taxista
        ]);
    }

    /**
     * Mostrar formulario para crear/renovar suscripción
     */
    public function create()
    {
        $usuario = Auth::user();

        if (!$usuario->taxista) {
            return redirect()->route('home')->with('error', 'Solo los taxistas pueden acceder a esta sección.');
        }

        return view('taxista.suscripcion.create');
    }

    /**
     * Procesar creación de suscripción
     */
    public function store(Request $request)
    {
        $usuario = Auth::user();

        if (!$usuario->taxista) {
            return redirect()->route('home')->with('error', 'Solo los taxistas pueden acceder a esta sección.');
        }

        $request->validate([
            'metodo_pago' => 'required|string|in:tarjeta,transferencia,efectivo,paypal',
            'referencia_pago' => 'nullable|string|max:255',
        ]);

        $taxista = $usuario->taxista;
        $precioMensual = 120.00;
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
                $precioMensual,
                'mxn',
                [
                    'suscripcion_id' => 'pending',
                    'taxista_id' => $taxista->id,
                ]
            );

            if (!$result['success']) {
                return back()->with('error', 'Error al procesar el pago: ' . $result['error'])->withInput();
            }

            $paymentIntent = $result;
        }

        // Crear nueva suscripción
        $suscripcion = Suscripcion::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'id_taxista' => $taxista->id,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'precio' => $precioMensual,
            'monto' => $precioMensual,
            'pagado' => 0,
            'activo' => 0,
            'metodo_pago' => $request->metodo_pago,
            'referencia_pago' => $request->referencia_pago,
            'estado_pago' => 'pendiente',
            'id_transaccion' => $paymentIntent ? $paymentIntent['payment_intent_id'] : null
        ]);

        // Actualizar metadata del PaymentIntent
        if ($paymentIntent) {
            try {
                \Stripe\PaymentIntent::update($paymentIntent['payment_intent_id'], [
                    'metadata' => [
                        'suscripcion_id' => $suscripcion->id,
                        'taxista_id' => $taxista->id,
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('Error al actualizar PaymentIntent', ['error' => $e->getMessage()]);
            }
        }

        // Si es tarjeta, redirigir a página de pago con Stripe
        if ($request->metodo_pago === 'tarjeta') {
            return view('taxista.suscripcion.pagar', [
                'suscripcion' => $suscripcion,
                'client_secret' => $paymentIntent['client_secret'],
                'stripe_key' => config('services.stripe.key')
            ]);
        }

        // Para otros métodos, mostrar instrucciones
        return redirect()->route('taxista.suscripcion.show', $suscripcion->id)
            ->with('success', 'Suscripción creada. Completa el pago para activarla.');
    }

    /**
     * Mostrar detalles de suscripción
     */
    public function show($id)
    {
        $usuario = Auth::user();

        if (!$usuario->taxista) {
            return redirect()->route('home')->with('error', 'Solo los taxistas pueden acceder a esta sección.');
        }

        $suscripcion = Suscripcion::where('id', $id)
            ->where('id_taxista', $usuario->taxista->id)
            ->firstOrFail();

        return view('taxista.suscripcion.show', [
            'suscripcion' => $suscripcion
        ]);
    }

    /**
     * Confirmar pago manual (para transferencia, efectivo, etc.)
     */
    public function confirmarPago(Request $request, $id)
    {
        $usuario = Auth::user();

        if (!$usuario->taxista) {
            return redirect()->route('home')->with('error', 'Solo los taxistas pueden acceder a esta sección.');
        }

        $suscripcion = Suscripcion::where('id', $id)
            ->where('id_taxista', $usuario->taxista->id)
            ->firstOrFail();

        $request->validate([
            'referencia_pago' => 'nullable|string|max:255'
        ]);

        // Actualizar suscripción como pagada
        $suscripcion->update([
            'pagado' => 1,
            'activo' => 1,
            'estado_pago' => 'completado',
            'fecha_pago' => now(),
            'referencia_pago' => $request->referencia_pago ?? $suscripcion->referencia_pago
        ]);

        // Enviar email de confirmación
        if ($suscripcion->taxista && $suscripcion->taxista->usuario) {
            try {
                Mail::to($suscripcion->taxista->usuario->email)
                    ->send(new SuscripcionActivaMail($suscripcion));
            } catch (\Exception $e) {
                Log::error('Error al enviar email de suscripción activa', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return redirect()->route('taxista.suscripcion.index')
            ->with('success', '¡Pago confirmado! Tu suscripción está activa.');
    }
}
