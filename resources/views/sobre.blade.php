@extends('layouts.app')

@section('content')
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
        font-family: 'Montserrat', sans-serif;
        margin: 0;
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
            url('https://static.wixstatic.com/media/952b60_67f559efb50a4101804756294550c92a~mv2.jpg') 
            no-repeat center center/cover;
        color: #fff;
    }

    .container {
        flex: 1;
        width: 90%;
        max-width: 1200px;
        margin: 50px auto;
        animation: fadeIn 1s ease-in-out;
    }

    h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 6px #000;
            text-align: center;
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
        /* grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); */
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
    }

    @media (max-width: 1024px) {
    .grid {
        grid-template-columns: repeat(2, 1fr); /* 2 por fila */
    }
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
        margin-top: 40px;
        border-radius: 8px;
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
@endsection
