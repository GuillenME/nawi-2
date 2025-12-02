@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">💳 Detalles de Suscripción</h3>
                </div>

                <div class="card-body">
                    <div class="alert alert-{{ $suscripcion->estaActiva() ? 'success' : 'warning' }}">
                        <h5>
                            <i class="fas fa-{{ $suscripcion->estaActiva() ? 'check-circle' : 'exclamation-triangle' }}"></i>
                            Estado: {{ ucfirst($suscripcion->estado_pago) }}
                        </h5>
                    </div>

                    <div class="mb-3">
                        <strong>Fecha de inicio:</strong> {{ $suscripcion->fecha_inicio->format('d/m/Y') }}<br>
                        <strong>Fecha de vencimiento:</strong> {{ $suscripcion->fecha_fin->format('d/m/Y') }}<br>
                        <strong>Días restantes:</strong> {{ $suscripcion->diasRestantes() }} días<br>
                        <strong>Precio:</strong> ${{ number_format($suscripcion->precio, 2) }} MXN<br>
                        <strong>Método de pago:</strong> {{ ucfirst($suscripcion->metodo_pago ?? 'N/A') }}<br>
                        @if($suscripcion->referencia_pago)
                            <strong>Referencia:</strong> {{ $suscripcion->referencia_pago }}<br>
                        @endif
                        @if($suscripcion->fecha_pago)
                            <strong>Fecha de pago:</strong> {{ $suscripcion->fecha_pago->format('d/m/Y H:i') }}
                        @endif
                    </div>

                    @if($suscripcion->estado_pago === 'pendiente' && $suscripcion->metodo_pago !== 'tarjeta')
                        <div class="alert alert-info">
                            <h6>Confirmar Pago</h6>
                            <p>Si ya realizaste el pago, puedes confirmarlo aquí proporcionando tu referencia de pago.</p>
                            
                            <form method="POST" action="{{ route('taxista.suscripcion.confirmar-pago', $suscripcion->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="referencia_pago" class="form-label">Referencia de Pago</label>
                                    <input type="text" class="form-control" id="referencia_pago" name="referencia_pago" 
                                           value="{{ $suscripcion->referencia_pago }}" 
                                           placeholder="Número de referencia, comprobante, etc.">
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Confirmar Pago
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('taxista.suscripcion.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


