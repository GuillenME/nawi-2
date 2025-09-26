<div class="card-body">
                    <h2>{{ $taxista->nombre }} {{ $taxista->apellidos }}</h2>
                    <p>Edad: {{ $taxista->edad }}</p>
                    <p>Teléfono: {{ $taxista->num_telefono }}</p>
                    <p>Estado: <strong>{{ ucfirst($taxista->estado) }}</strong></p>
                    <p>Turno: {{ ucfirst($taxista->turno) }}</p>