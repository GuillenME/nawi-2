@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">💳 Mi Suscripción</h3>
                    <a href="{{ route('taxista.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>

                <div class="card-body">
                    @if($suscripcion)
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Estado de Suscripción</h5>

                                @if($suscripcion->estaActiva())
                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-check-circle"></i> Suscripción Activa</h6>
                                        <p class="mb-0">
                                            <strong>Fecha de inicio:</strong> {{ $suscripcion->fecha_inicio->format('d/m/Y') }}<br>
                                            <strong>Fecha de vencimiento:</strong> {{ $suscripcion->fecha_fin->format('d/m/Y') }}<br>
                                            <strong>Días restantes:</strong> {{ $suscripcion->diasRestantes() }} días<br>
                                            <strong>Monto pagado:</strong> ${{ number_format($suscripcion->monto ?? $suscripcion->precio, 2) }} MXN<br>
                                            <strong>Método de pago:</strong> {{ ucfirst($suscripcion->metodo_pago ?? 'N/A') }}<br>
                                            @if($suscripcion->fecha_pago)
                                                <strong>Fecha de pago:</strong> {{ $suscripcion->fecha_pago->format('d/m/Y H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                @else
                                    <div class="alert alert-{{ $suscripcion->estaVencida() ? 'danger' : 'warning' }}">
                                        <h6>
                                            <i class="fas fa-{{ $suscripcion->estaVencida() ? 'times-circle' : 'exclamation-triangle' }}"></i>
                                            Suscripción {{ $suscripcion->estaVencida() ? 'Vencida' : 'Inactiva' }}
                                        </h6>
                                        <p class="mb-0">
                                            <strong>Estado:</strong> {{ ucfirst($suscripcion->estado_pago) }}<br>
                                            <strong>Fecha de inicio:</strong> {{ $suscripcion->fecha_inicio->format('d/m/Y') }}<br>
                                            <strong>Fecha de vencimiento:</strong> {{ $suscripcion->fecha_fin->format('d/m/Y') }}<br>
                                            @if(!$suscripcion->estaVencida())
                                                <strong>Días restantes:</strong> {{ $suscripcion->diasRestantes() }} días<br>
                                            @endif
                                            <strong>Monto:</strong> ${{ number_format($suscripcion->precio, 2) }} MXN/mes
                                        </p>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    @if($suscripcion->necesitaPago() || $suscripcion->estaVencida())
                                        <a href="{{ route('taxista.suscripcion.create') }}" class="btn btn-success btn-lg">
                                            <i class="fas fa-credit-card"></i> Renovar Suscripción
                                        </a>
                                    @else
                                        <a href="{{ route('taxista.suscripcion.create') }}" class="btn btn-primary">
                                            <i class="fas fa-sync"></i> Renovar Anticipadamente
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Información Importante</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-info-circle text-info"></i> Precio: $120.00 MXN/mes</li>
                                            <li><i class="fas fa-calendar text-info"></i> Renovación mensual</li>
                                            <li><i class="fas fa-shield-alt text-info"></i> Sin suscripción activa no podrás aceptar viajes</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> No tienes suscripción</h5>
                            <p>Para poder aceptar viajes, necesitas activar tu suscripción mensual.</p>
                            <a href="{{ route('taxista.suscripcion.create') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-plus"></i> Activar Suscripción
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

