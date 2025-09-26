@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://static.wixstatic.com/media/952b60_67f559efb50a4101804756294550c92a~mv2.jpg') no-repeat center center/cover;
            color: #fff;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            text-align: center;
        }

        h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 6px #000;
        }

        p.subtitle {
            font-size: 1.3rem;
            max-width: 650px;
            margin-bottom: 50px;
            text-shadow: 1px 1px 3px #000;
        }

        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 50px;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            width: 250px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        }

        .card i {
            font-size: 3rem;
            color: #ffc107;
            margin-bottom: 15px;
        }

        .card h3 {
            margin-bottom: 10px;
            font-size: 1.4rem;
            color: #fff

        }

        .card p {
            font-size: 1rem;
            line-height: 1.4;
            color: #fff
        }

        .cta {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .cta .d-flex {
            display: flex !important;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 20px;
        }

        .cta .btn {
            min-width: 200px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .cta-secondary {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
            flex-wrap: wrap; /* Para que en móvil se acomoden bien */
        }


        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: #000;
            font-weight: bold;
            border-radius: 12px;
            text-decoration: none;
            font-size: 1.1rem;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }
        .btn-secondary {
            display: inline-block;
            padding: 12px 24px;
            border: 2px solid #ffc107;
            color: #ffc107;
            font-weight: bold;
            border-radius: 12px;
            text-decoration: none;
            font-size: 1rem;
            background: transparent;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 193, 7, 0.1);
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0,0,0,0.4);
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }

        .footer {
            margin-top: auto;
            padding: 20px 0;
            width: 100%;
            text-align: center;
            font-size: 0.9rem;
            color: #eee;
            text-shadow: 1px 1px 2px #000;
        }

        @media (max-width: 768px) {
            h1 { font-size: 2.2rem; }
            p.subtitle { font-size: 1.1rem; }
            .cards { flex-direction: column; gap: 20px; }
        }
    </style>
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
