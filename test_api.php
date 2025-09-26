<?php

// Script para probar la API de NAWI
$baseUrl = 'http://localhost:8000/api';

echo "🧪 PROBANDO API DE NAWI\n";
echo "========================\n\n";

// Función para hacer requests
function makeRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// 1. Registrar pasajero
echo "1️⃣ Registrando pasajero...\n";
$registerData = [
    'nombre' => 'Test',
    'apellido' => 'User',
    'telefono' => '1234567890',
    'email' => 'test@example.com',
    'password' => 'password123'
];

$registerResponse = makeRequest($baseUrl . '/register/pasajero', 'POST', $registerData);
echo "Status: " . $registerResponse['code'] . "\n";
echo "Response: " . json_encode($registerResponse['data'], JSON_PRETTY_PRINT) . "\n\n";

// 2. Login
echo "2️⃣ Haciendo login...\n";
$loginData = [
    'email' => 'test@example.com',
    'password' => 'password123'
];

$loginResponse = makeRequest($baseUrl . '/login', 'POST', $loginData);
echo "Status: " . $loginResponse['code'] . "\n";
echo "Response: " . json_encode($loginResponse['data'], JSON_PRETTY_PRINT) . "\n\n";

if (isset($loginResponse['data']['data']['access_token'])) {
    $token = $loginResponse['data']['data']['access_token'];
    echo "✅ Token obtenido: " . substr($token, 0, 20) . "...\n\n";

    // 3. Obtener información del usuario
    echo "3️⃣ Obteniendo información del usuario...\n";
    $userResponse = makeRequest($baseUrl . '/user', 'GET', null, $token);
    echo "Status: " . $userResponse['code'] . "\n";
    echo "Response: " . json_encode($userResponse['data'], JSON_PRETTY_PRINT) . "\n\n";

    // 4. Listar pasajeros
    echo "4️⃣ Listando pasajeros...\n";
    $pasajerosResponse = makeRequest($baseUrl . '/pasajeros', 'GET', null, $token);
    echo "Status: " . $pasajerosResponse['code'] . "\n";
    echo "Response: " . json_encode($pasajerosResponse['data'], JSON_PRETTY_PRINT) . "\n\n";

    // 5. Probar sin token (debería fallar)
    echo "5️⃣ Probando sin token (debería fallar)...\n";
    $noTokenResponse = makeRequest($baseUrl . '/pasajeros', 'GET');
    echo "Status: " . $noTokenResponse['code'] . "\n";
    echo "Response: " . json_encode($noTokenResponse['data'], JSON_PRETTY_PRINT) . "\n\n";

    // 6. Logout
    echo "6️⃣ Haciendo logout...\n";
    $logoutResponse = makeRequest($baseUrl . '/logout', 'POST', null, $token);
    echo "Status: " . $logoutResponse['code'] . "\n";
    echo "Response: " . json_encode($logoutResponse['data'], JSON_PRETTY_PRINT) . "\n\n";

    // 7. Probar con token revocado (debería fallar)
    echo "7️⃣ Probando con token revocado (debería fallar)...\n";
    $revokedResponse = makeRequest($baseUrl . '/user', 'GET', null, $token);
    echo "Status: " . $revokedResponse['code'] . "\n";
    echo "Response: " . json_encode($revokedResponse['data'], JSON_PRETTY_PRINT) . "\n\n";

} else {
    echo "❌ No se pudo obtener el token\n";
}

echo "🎉 Pruebas completadas!\n";
