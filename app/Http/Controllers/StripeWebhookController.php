<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use App\Mail\SuscripcionActivaMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    /**
     * Manejar webhooks de Stripe
     * 
     * IMPORTANTE: La cuenta que recibe el dinero está definida por
     * las credenciales STRIPE_SECRET en el archivo .env
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        if (!$webhookSecret) {
            Log::error('STRIPE_WEBHOOK_SECRET no está configurado');
            return response()->json(['error' => 'Webhook secret no configurado'], 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::error('Error al verificar firma de webhook de Stripe', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Firma inválida'], 400);
        }

        // Manejar diferentes tipos de eventos
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;

            default:
                Log::info('Evento de Stripe no manejado', [
                    'type' => $event->type
                ]);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Manejar pago exitoso
     */
    protected function handlePaymentSucceeded($paymentIntent)
    {
        try {
            $metadata = $paymentIntent->metadata ?? [];
            $suscripcionId = $metadata['suscripcion_id'] ?? null;

            if (!$suscripcionId) {
                Log::warning('PaymentIntent sin suscripcion_id en metadata', [
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return;
            }

            $suscripcion = Suscripcion::find($suscripcionId);

            if (!$suscripcion) {
                Log::error('Suscripción no encontrada para PaymentIntent', [
                    'suscripcion_id' => $suscripcionId,
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return;
            }

            // Actualizar suscripción como pagada
            $suscripcion->update([
                'pagado' => 1,
                'activo' => 1,
                'estado_pago' => 'completado',
                'fecha_pago' => now(),
                'id_transaccion' => $paymentIntent->id,
                'monto' => $paymentIntent->amount / 100, // Convertir de centavos a pesos
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

            Log::info('Pago confirmado automáticamente vía webhook', [
                'suscripcion_id' => $suscripcion->id,
                'payment_intent_id' => $paymentIntent->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error al procesar payment_intent.succeeded', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntent->id ?? null
            ]);
        }
    }

    /**
     * Manejar pago fallido
     */
    protected function handlePaymentFailed($paymentIntent)
    {
        try {
            $metadata = $paymentIntent->metadata ?? [];
            $suscripcionId = $metadata['suscripcion_id'] ?? null;

            if ($suscripcionId) {
                $suscripcion = Suscripcion::find($suscripcionId);
                
                if ($suscripcion) {
                    $suscripcion->update([
                        'estado_pago' => 'fallido',
                        'id_transaccion' => $paymentIntent->id
                    ]);

                    Log::info('Pago fallido registrado', [
                        'suscripcion_id' => $suscripcion->id,
                        'payment_intent_id' => $paymentIntent->id
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al procesar payment_intent.payment_failed', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
