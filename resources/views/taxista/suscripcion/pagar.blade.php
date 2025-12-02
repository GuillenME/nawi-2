@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">💳 Pagar con Tarjeta</h3>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Monto a pagar: ${{ number_format($suscripcion->precio, 2) }} MXN</h5>
                        <p class="mb-0">Completa el pago para activar tu suscripción.</p>
                    </div>

                    <form id="payment-form">
                        <div id="card-element" class="mb-3">
                            <!-- Stripe Elements creará los elementos aquí -->
                        </div>

                        <div id="card-errors" role="alert" class="text-danger mb-3"></div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="submit-button" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card"></i> Pagar ${{ number_format($suscripcion->precio, 2) }} MXN
                            </button>
                            <a href="{{ route('taxista.suscripcion.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ $stripe_key }}');
const elements = stripe.elements();
const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#424770',
            '::placeholder': {
                color: '#aab7c4',
            },
        },
        invalid: {
            color: '#9e2146',
        },
    },
});

cardElement.mount('#card-element');

const cardErrors = document.getElementById('card-errors');
cardElement.on('change', ({error}) => {
    if (error) {
        cardErrors.textContent = error.message;
    } else {
        cardErrors.textContent = '';
    }
});

const form = document.getElementById('payment-form');
const submitButton = document.getElementById('submit-button');

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    
    submitButton.disabled = true;
    submitButton.textContent = 'Procesando...';

    const {error, paymentIntent} = await stripe.confirmCardPayment('{{ $client_secret }}', {
        payment_method: {
            card: cardElement,
        }
    });

    if (error) {
        cardErrors.textContent = error.message;
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-credit-card"></i> Pagar ${{ number_format($suscripcion->precio, 2) }} MXN';
    } else if (paymentIntent.status === 'succeeded') {
        // Redirigir a confirmación
        window.location.href = '{{ route("taxista.suscripcion.index") }}?payment=success';
    }
});
</script>
@endsection


