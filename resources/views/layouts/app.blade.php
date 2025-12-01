<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- PWA Configuration -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#ffc107">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="NAWI">
    <!-- Prevenir impresión automática -->
    <meta name="robots" content="noindex, nofollow">
    <!-- Favicon Configuration -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/nawi-icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/nawi-icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/nawi-icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/nawi-icon-512.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/nawi-icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/nawi-icon-192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/nawi-icon-192.png') }}">

    <!-- Estilos -->
    <!-- <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

    <script type="text/javascript" src="jquery-3.7.1.min.js"></script>

    <!-- Estilos del layout y navegación -->
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">

    <!-- Estilos adicionales de las vistas -->
    @stack('styles')

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
<script type="text/javascript">
    // Initialize the service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js', {
            scope: '.'
        }).then(function (registration) {
            // Registration was successful
            console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            console.log('Laravel PWA: ServiceWorker registration failed: ', err);
        });
    }
</script>
</head>
<body>
    <div id="app">
        <nav>
            <div class="nav-logo">
                <a href="/"><img src="{{ asset('images/logoab.png') }}" alt="Logo Nawi"></a>
            </div>
            <div class="nav-links">
                @auth
                    @if(auth()->user()->rol->nombre === 'admin')
                        <a href="/admin/dashboard">Admin</a>
                    @elseif(auth()->user()->rol->nombre === 'taxista')
                        <a href="{{ route('taxista.dashboard') }}">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #fff; font-weight: bold; cursor: pointer; margin: 0 15px; transition: color 0.3s, transform 0.2s, text-shadow 0.3s;" onmouseover="this.style.color='#ffc107'; this.style.transform='translateY(-2px)'; this.style.textShadow='0 0 8px rgba(255, 193, 7, 0.8)';" onmouseout="this.style.color='#fff'; this.style.transform=''; this.style.textShadow='';">
                            Cerrar Sesión
                        </button>
                    </form>
                @else
                    <a href="/">Inicio</a>
                    <a href="/taxistas">Taxistas</a>
                    <a href="/sobre-nosotros">Sobre Nosotros</a>
                    <a href="{{ route('download.apk') }}" class="download-apk-link" target="_blank" title="Descargar App Android">
                        <i class="fab fa-android"></i> Descargar App
                    </a>
                @endauth
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <!--<script src="{{ asset('install-pwa.js') }}"></script>-->

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js', { scope: '.' })
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }

        AOS.init({ once: true });

        // Prevenir impresión automática en páginas offline
        window.addEventListener('beforeprint', function(e) {
            e.preventDefault();
            return false;
        });

        // Deshabilitar atajo de teclado para imprimir (Ctrl+P / Cmd+P)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                alert('La impresión está deshabilitada en esta página.');
                return false;
            }
        });
    </script>

</body>
</html>
