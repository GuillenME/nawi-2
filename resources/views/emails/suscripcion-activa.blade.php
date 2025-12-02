<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción Activada</title>
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
            background-color: #28a745;
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
        .success-box {
            background-color: #d4edda;
            padding: 15px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Suscripción Activada</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $taxista->usuario->nombre }} {{ $taxista->usuario->apellido }}</strong>,</p>
        
        <div class="success-box">
            <p><strong>¡Tu suscripción ha sido activada exitosamente!</strong></p>
        </div>
        
        <p>Detalles de tu suscripción:</p>
        <ul>
            <li><strong>Fecha de inicio:</strong> {{ $suscripcion->fecha_inicio->format('d/m/Y') }}</li>
            <li><strong>Fecha de vencimiento:</strong> {{ $suscripcion->fecha_fin->format('d/m/Y') }}</li>
            <li><strong>Monto pagado:</strong> ${{ number_format($suscripcion->monto ?? $suscripcion->precio, 2) }} MXN</li>
            <li><strong>Método de pago:</strong> {{ ucfirst($suscripcion->metodo_pago ?? 'N/A') }}</li>
        </ul>
        
        <p>Ya puedes usar todos los servicios de NAWI, incluyendo:</p>
        <ul>
            <li>Ver viajes disponibles</li>
            <li>Aceptar y completar viajes</li>
            <li>Gestionar tu perfil</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="{{ url('/taxista/dashboard') }}" class="button">Ir al Dashboard</a>
        </div>
        
        <p style="margin-top: 30px;">Gracias por confiar en NAWI.</p>
        
        <p>Saludos,<br>El equipo de NAWI</p>
    </div>
    <div class="footer">
        <p>Este es un email automático, por favor no respondas a este mensaje.</p>
    </div>
</body>
</html>

