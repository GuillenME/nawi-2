<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción Vencida</title>
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
            background-color: #dc3545;
            color: white;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>⚠️ Suscripción Vencida</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $taxista->usuario->nombre }} {{ $taxista->usuario->apellido }}</strong>,</p>
        
        <p>Te informamos que tu suscripción a NAWI ha vencido el <strong>{{ $suscripcion->fecha_fin->format('d/m/Y') }}</strong>.</p>
        
        <p>Para continuar usando nuestros servicios y aceptar viajes, necesitas renovar tu suscripción.</p>
        
        <p><strong>Precio de renovación:</strong> ${{ number_format($suscripcion->precio, 2) }} MXN (mensual)</p>
        
        <div style="text-align: center;">
            <a href="{{ url('/taxista/suscripcion') }}" class="button">Renovar Suscripción</a>
        </div>
        
        <p style="margin-top: 30px;">Si tienes alguna pregunta, no dudes en contactarnos.</p>
        
        <p>Saludos,<br>El equipo de NAWI</p>
    </div>
    <div class="footer">
        <p>Este es un email automático, por favor no respondas a este mensaje.</p>
    </div>
</body>
</html>

