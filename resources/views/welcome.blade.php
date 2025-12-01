@extends('layouts.app')

@push('styles')
    <!-- Estilos de bienvenida -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
@endpush

@section('content')
    <div class="container">
        <h1 data-aos="fade-down" data-aos-duration="1000">Bienvenido a NAWI</h1>
        <p class="subtitle" data-aos="fade-up" data-aos-duration="1200">
            Conectamos a los habitantes de Ocosingo con un servicio de taxi seguro, accesible y eficiente.
            Lleva la tradición y cultura de nuestro pueblo donde vayas.
        </p>

        <div class="cards">
            <div class="card" data-aos="fade-right" data-aos-duration="1000">
                <i class="fas fa-taxi"></i>
                <h3>Taxi Seguro</h3>
                <p>Disfruta de viajes seguros con conductores confiables de la región.</p>
            </div>
            <div class="card" data-aos="fade-up" data-aos-duration="1200">
                <i class="fas fa-map-marked-alt"></i>
                <h3>Mapa Interactivo</h3>
                <p>Elige tu taxi en tiempo real y conoce la ubicación exacta de tu viaje.</p>
            </div>
            <div class="card" data-aos="fade-left" data-aos-duration="1000">
                <i class="fas fa-users"></i>
                <h3>Comunidad Local</h3>
                <p>Apoya a conductores locales y fomenta el desarrollo de Ocosingo.</p>
            </div>
        </div>

        <div class="cta" data-aos="zoom-in" data-aos-duration="1000">
            <p>¿Quieres ser parte de NAWI?</p>

            <!-- Botones principales -->
            <div class="d-flex gap-3 justify-content-center flex-wrap mb-3">
                <a href="{{ route('login') }}" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
                <a href="{{ route('register.taxista') }}" class="btn">
                    <i class="fas fa-taxi"></i> Registrarse como Taxista
                </a>
                <a href="{{ route('download.apk') }}" class="btn btn-success" target="_blank">
                    <i class="fab fa-android"></i> Descargar App
                </a>
            </div>

            <!-- Botones secundarios -->
            <div class="cta-secondary">
                <a href="/sobre-nosotros" class="btn-secondary">Sobre Nosotros</a>
                <a href="/taxistas" class="btn-secondary">Taxistas Verificados</a>
            </div>
        </div>

        <div class="footer">© {{ date('Y') }} NAWI | Raíces que se mueven contigo</div>
    </div>
@endsection
