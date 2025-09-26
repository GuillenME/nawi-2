@extends('layouts.app')

@section('content')
<style>
    html, body {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                    url('https://static.wixstatic.com/media/952b60_67f559efb50a4101804756294550c92a~mv2.jpg')
                    no-repeat center center/cover;
        color: #fff;
    }

    .container {
        flex: 1;
        width: 90%;
        max-width: 1200px;
        margin: 40px auto;
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 2.5rem;
        text-shadow: 2px 2px 6px #000;
    }

    /* Grid */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    /* Card Flip */
    .card {
        color: #2c2c2c;
        perspective: 1000px;
        cursor: pointer;
    }

    .card-inner {
        position: relative;
        width: 100%;
        height: 350px;
        transition: transform 0.8s;
        transform-style: preserve-3d;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.5);
    }

    .card.flipped .card-inner {
        transform: rotateY(180deg);
    }

    /* Caras */
    .card-front, .card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .card-front {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .card-front img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .card-front .card-body {
        padding: 15px;
        text-align: center;
    }

    .card-front .card-body h2 {
        font-size: 1.25rem;
        margin-bottom: 8px;
    }

    .card-back {
        transform: rotateY(180deg);
        padding: 20px;
        overflow-y: auto;
    }

    .card-back h2 {
        margin-bottom: 10px;
    }

    .card-back p {
        margin-bottom: 6px;
        font-size: 0.95rem;
        color: #333;
    }

    .card-back hr {
        margin: 10px 0;
        border: 0;
        border-top: 1px solid #eee;
    }

    .toggle-btn {
        display: inline-block;
        margin-top: 10px;
        background: #ffc107;
        color: #000;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .toggle-btn:hover {
        background: #ffb300;
    }
</style>

<div class="container">
    <h1>Nuestros Taxistas</h1>

    <div class="grid">
        @forelse($taxistas as $taxista)
            <div class="card">
                <div class="card-inner">
                    <!-- FRONT -->
                    <div class="card-front">
                        <img src="{{ asset('images/taxistas/default.jpg') }}"
                             alt="Foto de {{ $taxista->usuario->nombre }}">
                        <div class="card-body">
                            <h2>{{ $taxista->usuario->nombre }} {{ $taxista->usuario->apellido }}</h2>
                            <p>Taxista Verificado</p>
                            <span class="toggle-btn">Más información</span>
                        </div>
                    </div>

                    <!-- BACK -->
                    <div class="card-back">
                        <h2>{{ $taxista->usuario->nombre }} {{ $taxista->usuario->apellido }}</h2>
                        <p>Teléfono: {{ $taxista->usuario->telefono }}</p>
                        <p>Email: {{ $taxista->usuario->email }}</p>
                        <hr>
                        <h3>Documentos:</h3>
                        @if($taxista->matricula)
                            <p>Matrícula: <strong>{{ ucfirst($taxista->matricula->estatus->nombre) }}</strong></p>
                        @else
                            <p>Matrícula: <em>Pendiente</em></p>
                        @endif

                        @if($taxista->licencia)
                            <p>Licencia: <strong>{{ ucfirst($taxista->licencia->estatus->nombre) }}</strong></p>
                        @else
                            <p>Licencia: <em>Pendiente</em></p>
                        @endif
                        <span class="toggle-btn">Volver</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center">
                <h3>No hay taxistas registrados aún</h3>
                <p>Los taxistas aparecerán aquí una vez que se registren y suban sus documentos.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    // Script para el flip de tarjetas
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', (e) => {
            if (e.target.classList.contains('toggle-btn')) {
                card.classList.toggle('flipped');
            }
        });
    });
</script>
@endsection
