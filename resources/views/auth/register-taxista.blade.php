@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<style>
    body {
        font-family: 'Montserrat', sans-serif;
    }

    .container {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .card {
        background: rgba(255, 255, 255, 0.05) !important;
        backdrop-filter: blur(12px);
        border: 2px solid rgba(255, 193, 7, 0.3);
        box-shadow: 0 4px 20px rgba(255, 193, 7, 0.3);
        border-radius: 15px;
        color: #fff;
        overflow: hidden;
        width: 100%;
        max-width: 700px;
    }

    .card-header {
        background: rgba(255, 193, 7, 0.1);
        border-bottom: 1px solid rgba(255, 193, 7, 0.3);
        padding: 25px;
    }

    .card-header h3 {
        color: #ffc107;
        text-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
        margin: 0;
        font-size: 1.8rem;
    }

    .card-body {
        padding: 35px;
    }

    .form-label {
        color: #fff;
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        border-radius: 8px;
        padding: 14px 18px;
        font-size: 1rem;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        color: #fff;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .btn-warning {
        background: linear-gradient(45deg, #ffc107, #ff9800);
        border: none;
        color: #000;
        font-weight: bold;
        border-radius: 8px;
        padding: 14px 20px;
        font-size: 1.1rem;
        transition: all 0.3s;
    }

    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 193, 7, 0.4);
        background: linear-gradient(45deg, #ff9800, #ffc107);
    }

    .alert {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: #fff;
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.2);
        border-color: rgba(220, 53, 69, 0.5);
    }

    .alert-info {
        background: rgba(23, 162, 184, 0.2);
        border-color: rgba(23, 162, 184, 0.5);
        font-size: 1rem;
        padding: 15px;
    }

    .alert-success {
        background: rgba(40, 167, 69, 0.2);
        border-color: rgba(40, 167, 69, 0.5);
    }

    a {
        color: #ffc107;
        text-decoration: none;
        transition: all 0.3s;
        font-weight: 600;
    }

    a:hover {
        color: #ff9800;
        text-shadow: 0 0 8px rgba(255, 193, 7, 0.8);
    }

    .text-muted {
        color: rgba(255, 255, 255, 0.9) !important;
        font-size: 1rem !important;
    }

    .text-center p {
        font-size: 1.1rem;
        color: #fff;
        margin-bottom: 12px;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .text-center p small {
        font-size: 1rem !important;
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .text-center a {
        font-size: 1.1rem;
        font-weight: 600;
        color: #ffc107 !important;
        text-shadow: 0 0 8px rgba(255, 193, 7, 0.8);
    }
</style>
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">🚕 Registro de Taxista</h3>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.taxista') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                           id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="apellido" class="form-label">Apellido *</label>
                                    <input type="text" class="form-control @error('apellido') is-invalid @enderror"
                                           id="apellido" name="apellido" value="{{ old('apellido') }}" required>
                                    @error('apellido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="telefono" class="form-label">Teléfono *</label>
                                    <input type="tel" class="form-control @error('telefono') is-invalid @enderror"
                                           id="telefono" name="telefono" value="{{ old('telefono') }}" required>
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Contraseña *</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                           name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Nota:</strong> Después del registro podrás subir tus documentos (matrícula y licencia) desde tu panel de taxista.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-taxi"></i> Registrarse como Taxista
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Iniciar Sesión</a></p>
                            <p class="text-muted">
                                <small>¿Eres pasajero? Descarga nuestra app móvil para registrarte.</small>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="{{ route('register.taxista') }}"]');
    const nombreInput = document.getElementById('nombre');
    const apellidoInput = document.getElementById('apellido');
    const telefonoInput = document.getElementById('telefono');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');

    // Función para validar y bloquear símbolos peligrosos
    function bloquearSimbolosPeligrosos(input, permitirNumeros = false, permitirEmail = false) {
        input.addEventListener('input', function(e) {
            let valor = e.target.value;
            // Lista de símbolos peligrosos
            const simbolosPeligrosos = /[<>"'&;{}\[\]()|\\`~!@#$%^*+=?:,\/]/g;

            if (permitirEmail) {
                // Para email, permitir @, ., _, - pero bloquear otros peligrosos
                valor = valor.replace(/[<>"'&;{}\[\]()|\\`~!#$%^*+=?:,\/]/g, '');
            } else if (permitirNumeros) {
                // Para teléfono, solo números
                valor = valor.replace(/[^0-9]/g, '');
            } else {
                // Para nombre/apellido, solo letras y espacios
                valor = valor.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');
            }

            if (e.target.value !== valor) {
                e.target.value = valor;
            }
        });
    }

    // Validación de nombre (solo letras, mínimo 3 caracteres)
    bloquearSimbolosPeligrosos(nombreInput);
    nombreInput.addEventListener('blur', function() {
        const valor = this.value.trim();
        if (valor.length > 0 && valor.length < 3) {
            this.setCustomValidity('El nombre debe tener al menos 3 caracteres.');
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(valor) && valor.length > 0) {
            this.setCustomValidity('El nombre solo puede contener letras y espacios.');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validación de apellido (solo letras, mínimo 3 caracteres)
    bloquearSimbolosPeligrosos(apellidoInput);
    apellidoInput.addEventListener('blur', function() {
        const valor = this.value.trim();
        if (valor.length > 0 && valor.length < 3) {
            this.setCustomValidity('El apellido debe tener al menos 3 caracteres.');
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(valor) && valor.length > 0) {
            this.setCustomValidity('El apellido solo puede contener letras y espacios.');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validación de teléfono (solo números, exactamente 10)
    bloquearSimbolosPeligrosos(telefonoInput, true);
    telefonoInput.addEventListener('input', function() {
        // Limitar a 10 dígitos
        if (this.value.length > 10) {
            this.value = this.value.slice(0, 10);
        }
    });
    telefonoInput.addEventListener('blur', function() {
        if (this.value.length !== 10) {
            this.setCustomValidity('El teléfono debe tener exactamente 10 números.');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validación de email (bloquear símbolos peligrosos)
    bloquearSimbolosPeligrosos(emailInput, false, true);

    // Validación de contraseña (mayúscula, minúscula, número, carácter especial)
    passwordInput.addEventListener('input', function() {
        // Bloquear símbolos peligrosos específicos
        this.value = this.value.replace(/[<>"'&;{}\[\]()|\\`~]/g, '');
    });
    passwordInput.addEventListener('blur', function() {
        const valor = this.value;
        const tieneMinuscula = /[a-z]/.test(valor);
        const tieneMayuscula = /[A-Z]/.test(valor);
        const tieneNumero = /\d/.test(valor);
        const tieneCaracterEspecial = /[@$!%*?&#]/.test(valor);

        if (valor.length < 8) {
            this.setCustomValidity('La contraseña debe tener al menos 8 caracteres.');
        } else if (!tieneMinuscula) {
            this.setCustomValidity('La contraseña debe contener al menos una letra minúscula.');
        } else if (!tieneMayuscula) {
            this.setCustomValidity('La contraseña debe contener al menos una letra mayúscula.');
        } else if (!tieneNumero) {
            this.setCustomValidity('La contraseña debe contener al menos un número.');
        } else if (!tieneCaracterEspecial) {
            this.setCustomValidity('La contraseña debe contener al menos un carácter especial (@$!%*?&#).');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validación de confirmación de contraseña
    passwordConfirmationInput.addEventListener('blur', function() {
        if (this.value !== passwordInput.value) {
            this.setCustomValidity('Las contraseñas no coinciden.');
        } else {
            this.setCustomValidity('');
        }
    });
    passwordInput.addEventListener('input', function() {
        if (passwordConfirmationInput.value) {
            passwordConfirmationInput.dispatchEvent(new Event('blur'));
        }
    });

    // Validación antes de enviar el formulario
    form.addEventListener('submit', function(e) {
        let esValido = true;

        // Validar nombre
        if (nombreInput.value.trim().length < 3) {
            nombreInput.setCustomValidity('El nombre debe tener al menos 3 caracteres.');
            esValido = false;
        }

        // Validar apellido
        if (apellidoInput.value.trim().length < 3) {
            apellidoInput.setCustomValidity('El apellido debe tener al menos 3 caracteres.');
            esValido = false;
        }

        // Validar teléfono
        if (telefonoInput.value.length !== 10) {
            telefonoInput.setCustomValidity('El teléfono debe tener exactamente 10 números.');
            esValido = false;
        }

        // Validar contraseña
        const password = passwordInput.value;
        if (password.length < 8 ||
            !/[a-z]/.test(password) ||
            !/[A-Z]/.test(password) ||
            !/\d/.test(password) ||
            !/[@$!%*?&#]/.test(password)) {
            passwordInput.setCustomValidity('La contraseña debe cumplir todos los requisitos.');
            esValido = false;
        }

        // Validar confirmación
        if (passwordConfirmationInput.value !== passwordInput.value) {
            passwordConfirmationInput.setCustomValidity('Las contraseñas no coinciden.');
            esValido = false;
        }

        if (!esValido) {
            e.preventDefault();
            // Forzar la visualización de mensajes de validación
            form.reportValidity();
        }
    });
});
</script>

@endsection
