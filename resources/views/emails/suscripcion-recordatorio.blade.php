<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Suscripción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #ffc107;
            color: #333;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>⏰ Recordatorio de Suscripción</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $taxista->usuario->nombre }} {{ $taxista->usuario->apellido }}</strong>,</p>
        
        <div class="highlight">
            <p><strong>Tu suscripción vence en {{ $diasRestantes }} {{ $diasRestantes == 1 ? 'día' : 'días' }}</strong></p>
            <p>Fecha de vencimiento: <strong>{{ $suscripcion->fecha_fin->format('d/m/Y') }}</strong></p>
        </div>
        
        <p>Para evitar la interrupción del servicio, te recomendamos renovar tu suscripción antes de que venza.</p>
        
        <p><strong>Precio de renovación:</strong> ${{ number_format($suscripcion->precio, 2) }} MXN (mensual)</p>
        
        <div style="text-align: center;">
            <a href="{{ url('/taxista/suscripcion') }}" class="button">Renovar Ahora</a>
        </div>
        
        <p style="margin-top: 30px;">Si ya renovaste tu suscripción, puedes ignorar este mensaje.</p>
        
        <p>Saludos,<br>El equipo de NAWI</p>
    </div>
    <div class="footer">
        <p>Este es un email automático, por favor no respondas a este mensaje.</p>
    </div>
</body>
</html>

