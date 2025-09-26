<?php

require_once 'vendor/autoload.php';

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

// Simular el entorno de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Probando login manual...\n";

try {
    // Buscar usuario
    $usuario = Usuario::where('email', 'test@example.com')->first();

    if ($usuario) {
        echo "✅ Usuario encontrado: " . $usuario->email . "\n";
        echo "Rol: " . $usuario->rol->nombre . "\n";

        // Verificar password
        $passwordCorrecto = Hash::check('password123', $usuario->password);
        echo "Password correcto: " . ($passwordCorrecto ? 'Sí' : 'No') . "\n";

        if ($passwordCorrecto) {
            // Intentar crear token
            try {
                $token = $usuario->createToken('Test Token')->accessToken;
                echo "✅ Token creado: " . substr($token, 0, 20) . "...\n";
            } catch (Exception $e) {
                echo "❌ Error creando token: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "❌ Usuario no encontrado\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
