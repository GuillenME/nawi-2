<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sobre Nosotros</title>
<style>
    /* Reset básico */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    html, body {
        height: 100%;
        background: linear-gradient(135deg, #f4f7fb, #e2eafc);
    }

    body {
        display: flex;
        flex-direction: column;
    }

    /* --- NAVBAR --- */
    nav {
        background-color: #1a1a1a;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    nav .nav-logo img {
        height: 55px;
        transition: transform 0.3s;
    }

    nav .nav-logo img:hover {
        transform: scale(1.1);
    }

    nav .nav-links a {
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        margin: 0 15px;
        transition: color 0.3s, transform 0.2s;
    }

    nav .nav-links a:hover {
        color: #ffc107;
        transform: translateY(-2px);
    }

    /* --- CONTENIDO --- */
    .container {
        flex: 1;
        width: 90%;
        max-width: 1200px;
        margin: 50px auto;
        animation: fadeIn 1s ease-in-out;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        color: #1a1a1a;
        font-size: 2.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .intro-text {
        text-align: center;
        font-size: 1.3rem;
        font-weight: 500;
        background: linear-gradient(90deg, #41a001, #c7bc3b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 50px;
        line-height: 1.6;
        transition: transform 0.3s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .intro-text:hover {
        transform: scale(1.05);
    }

    /* --- GRID EQUIPO --- */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 25px;
    }

    /* --- TARJETAS FLIP --- */
    .card {
        perspective: 1000px;
    }

    .card-inner {
        position: relative;
        width: 100%;
        height: 320px;
        text-align: center;
        transition: transform 0.8s;
        transform-style: preserve-3d;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .card:hover .card-inner {
        transform: rotateY(180deg);
    }

    .card-front, .card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 15px;
        overflow: hidden;
    }

    .card-front img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 15px;
    }

    .card-back {
        background: linear-gradient(135deg, #1a1a1a, #333);
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 20px;
        transform: rotateY(180deg);
    }

    .card-back h2 {
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: #ffc107;
    }

    .card-back p {
        font-size: 0.95rem;
        line-height: 1.5;
    }

    /* --- FOOTER --- */
    footer {
        background-color: #1a1a1a;
        color: #fff;
        text-align: center;
        padding: 20px;
        font-size: 0.95rem;
        letter-spacing: 1px;
    }

    footer p {
        margin: 5px 0;
    }

    /* --- ANIMACIONES --- */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        h1 { font-size: 2.2rem; }
        .intro-text { font-size: 1.1rem; text-align: center; }
        .card-inner { height: 280px; }
    }
</style>
</head>
<body>

<nav>
    <div class="nav-logo">
        <img src="{{ asset('images/logo1.png') }}" alt="Logo Nawi">
    </div>
    <div class="nav-links">
        <a href="/taxistas">Taxistas</a>
        <a href="/sobre-nosotros">Sobre Nosotros</a>
    </div>
</nav>

<div class="container">
    <h1>Sobre Nosotros</h1>
    <p class="intro-text">💻 Somos un grupo de desarrollo apasionado por la innovación, comprometido con crear soluciones creativas y funcionales para cada desafío.</p>

    <div class="grid">
        @php
            $equipo = [
                ['nombre' => 'Mariana Eurice Guillen Navarro', 'foto' => asset('images/integrantes/mariana.jpg')],
                ['nombre' => 'Froilan Najera Morales', 'foto' => asset('images/integrantes/froilan.jpg')],
                ['nombre' => 'Hugo Ruben Díaz Cruz', 'foto' => asset('images/integrantes/hugo.jpg')],
                ['nombre' => 'José Alejandro Díaz Gómez', 'foto' => asset('images/integrantes/alex.jpg')],
            ];
        @endphp

        @foreach($equipo as $miembro)
            <div class="card">
                <div class="card-inner">
                    <div class="card-front">
                        <img src="{{ $miembro['foto'] ?? 'https://via.placeholder.com/400x300' }}" alt="{{ $miembro['nombre'] }}">
                    </div>
                    <div class="card-back">
                        <h2>{{ $miembro['nombre'] }}</h2>
                        <p>Rol en el equipo: Desarrollador</p>
                        <p>Apasionado por la innovación y la tecnología.</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<footer>
    <p>&copy; 2025 NAWI TECNOLOGÍAS S.A. DE C.V.</p>
    <p>Todos los derechos reservados.</p>
</footer>

</body>
</html>
