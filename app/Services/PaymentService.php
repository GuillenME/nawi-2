<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $stripeSecret;
    protected $stripeKey;

    public function __construct()
    {
        // La cuenta que recibe el dinero está definida por STRIPE_SECRET en .env
        // Esta clave está asociada a una cuenta específica de Stripe
        $this->stripeSecret = config('services.stripe.secret');
        $this->stripeKey = config('services.stripe.key');
        
        if (!$this->stripeSecret) {
            throw new \Exception('STRIPE_SECRET no está configurado en .env');
        }
        
        Stripe::setApiKey($this->stripeSecret);
    }

    /**
     * Crear un PaymentIntent para procesar el pago
     */
    public function createPaymentIntent(float $amount, string $currency = 'mxn', array $metadata = []): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($amount * 100), // Stripe usa centavos
                'currency' => strtolower($currency),
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Error al crear PaymentIntent', [
                'error' => $e->getMessage(),
                'amount' => $amount,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Confirmar un pago
     */
    public function confirmPayment(string $paymentIntentId): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'status' => 'succeeded',
                    'payment_intent' => $paymentIntent,
                ];
            }

            return [
                'success' => false,
                'status' => $paymentIntent->status,
                'error' => 'El pago no se completó',
            ];
        } catch (ApiErrorException $e) {
            Log::error('Error al confirmar pago', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Crear o recuperar un cliente de Stripe
     */
    public function getOrCreateCustomer(string $email, string $name = null): ?Customer
    {
        try {
            // Buscar cliente existente
            $customers = Customer::all([
                'email' => $email,
                'limit' => 1,
            ]);

            if (count($customers->data) > 0) {
                return $customers->data[0];
            }

            // Crear nuevo cliente
            return Customer::create([
                'email' => $email,
                'name' => $name,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Error al crear/obtener cliente', [
                'error' => $e->getMessage(),
                'email' => $email,
            ]);

            return null;
        }
    }

    /**
     * Guardar método de pago para renovación automática
     */
    public function savePaymentMethod(string $customerId, string $paymentMethodId): array
    {
        try {
            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $customerId]);

            // Establecer como método de pago por defecto
            Customer::update($customerId, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);

            return [
                'success' => true,
                'payment_method_id' => $paymentMethodId,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Error al guardar método de pago', [
                'error' => $e->getMessage(),
                'customer_id' => $customerId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
