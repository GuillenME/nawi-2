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

    <!-- Estilos -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

<style>
    nav {
        background: rgba(255, 255, 255, 0.05); /* Transparente estilo glass */
        backdrop-filter: blur(12px); /* efecto blur */
        border-bottom: 2px solid #ffc10753; /* borde amarillo */
        box-shadow: 0 4px 20px rgba(255, 193, 7, 0.3); /* glow amarillo */
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 1000;
        border-radius: 0 0 15px 15px; /* esquinas redondeadas abajo */
    }

    nav .nav-logo img {
        height: 55px;
        transition: transform 0.3s;
    }

    nav .nav-logo img:hover {
        transform: scale(1.1);
    }

    nav .nav-links a {
        color: #fff; /* letras blancas */
        text-decoration: none;
        font-weight: bold;
        margin: 0 15px;
        transition: color 0.3s, transform 0.2s, text-shadow 0.3s;
    }

    nav .nav-links a:hover {
        color: #ffc107; /* hover amarillo */
        transform: translateY(-2px);
        text-shadow: 0 0 8px rgba(255, 193, 7, 0.8); /* glow en hover */
    }
</style>


    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav>
            <div class="nav-logo">
                <a href="/"><img src="{{ asset('images/logo1.png') }}" alt="Logo Nawi"></a>
            </div>
            <div class="nav-links">
                <a href="/">Inicio</a>
                <a href="/taxistas">Taxistas</a>
                <a href="/sobre-nosotros">Sobre Nosotros</a>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({ once: true });
</script>

</body>
</html>
