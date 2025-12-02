@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">💳 Activar/Renovar Suscripción</h3>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Precio: $120.00 MXN por mes</h5>
                        <p class="mb-0">Selecciona tu método de pago preferido para activar tu suscripción.</p>
                    </div>

                    <form method="POST" action="{{ route('taxista.suscripcion.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label"><strong>Método de Pago *</strong></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card payment-method-card" onclick="selectPaymentMethod('tarjeta')" style="cursor: pointer; border: 2px solid #ddd;">
                                        <div class="card-body text-center">
                                            <i class="fas fa-credit-card fa-3x mb-2"></i>
                                            <h6>Tarjeta de Crédito/Débito</h6>
                                            <small class="text-muted">Pago seguro con Stripe</small>
                                        </div>
                                    </div>
                                    <input type="radio" name="metodo_pago" value="tarjeta" id="metodo_tarjeta" class="d-none" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card payment-method-card" onclick="selectPaymentMethod('transferencia')" style="cursor: pointer; border: 2px solid #ddd;">
                                        <div class="card-body text-center">
                                            <i class="fas fa-university fa-3x mb-2"></i>
                                            <h6>Transferencia Bancaria</h6>
                                            <small class="text-muted">Pago manual</small>
                                        </div>
                                    </div>
                                    <input type="radio" name="metodo_pago" value="transferencia" id="metodo_transferencia" class="d-none" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card payment-method-card" onclick="selectPaymentMethod('efectivo')" style="cursor: pointer; border: 2px solid #ddd;">
                                        <div class="card-body text-center">
                                            <i class="fas fa-money-bill fa-3x mb-2"></i>
                                            <h6>Efectivo</h6>
                                            <small class="text-muted">Pago en persona</small>
                                        </div>
                                    </div>
                                    <input type="radio" name="metodo_pago" value="efectivo" id="metodo_efectivo" class="d-none" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card payment-method-card" onclick="selectPaymentMethod('paypal')" style="cursor: pointer; border: 2px solid #ddd;">
                                        <div class="card-body text-center">
                                            <i class="fab fa-paypal fa-3x mb-2"></i>
                                            <h6>PayPal</h6>
                                            <small class="text-muted">Pago con PayPal</small>
                                        </div>
                                    </div>
                                    <input type="radio" name="metodo_pago" value="paypal" id="metodo_paypal" class="d-none" required>
                                </div>
                            </div>
                            @error('metodo_pago')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="referencia-field" style="display: none;">
                            <label for="referencia_pago" class="form-label">Referencia de Pago (Opcional)</label>
                            <input type="text" class="form-control" id="referencia_pago" name="referencia_pago" 
                                   placeholder="Número de referencia, comprobante, etc.">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check"></i> Continuar con el Pago
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

<script>
function selectPaymentMethod(method) {
    // Desmarcar todos los radio buttons
    document.querySelectorAll('input[name="metodo_pago"]').forEach(radio => {
        radio.checked = false;
    });
    
    // Desmarcar visualmente todas las tarjetas
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.style.border = '2px solid #ddd';
        card.style.backgroundColor = '';
    });
    
    // Marcar el seleccionado
    const radio = document.getElementById('metodo_' + method);
    const card = event.currentTarget;
    radio.checked = true;
    card.style.border = '2px solid #28a745';
    card.style.backgroundColor = '#f0f8f0';
    
    // Mostrar campo de referencia si no es tarjeta
    const referenciaField = document.getElementById('referencia-field');
    if (method !== 'tarjeta') {
        referenciaField.style.display = 'block';
    } else {
        referenciaField.style.display = 'none';
    }
}
</script>

<style>
.payment-method-card {
    transition: all 0.3s;
}
.payment-method-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endsection


