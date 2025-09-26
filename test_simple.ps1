# Script simple para probar la API de NAWI
$baseUrl = "http://nawi-2.me/api"

Write-Host "PROBANDO API DE NAWI" -ForegroundColor Green
Write-Host "===================" -ForegroundColor Green

# 1. Registrar pasajero
Write-Host "1. Registrando pasajero..." -ForegroundColor Yellow
$registerData = @{
    nombre = "Test"
    apellido = "User"
    telefono = "1234567890"
    email = "test@nawi.com"
    password = "password123"
} | ConvertTo-Json

try {
    $registerResponse = Invoke-RestMethod -Uri "$baseUrl/register/pasajero" -Method POST -ContentType "application/json" -Body $registerData
    Write-Host "Registro exitoso:" -ForegroundColor Green
    $registerResponse | ConvertTo-Json -Depth 3
} catch {
    Write-Host "Error en registro:" -ForegroundColor Red
    Write-Host $_.Exception.Message
}
Write-Host ""

# 2. Login
Write-Host "2. Haciendo login..." -ForegroundColor Yellow
$loginData = @{
    email = "test@nawi.com"
    password = "password123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$baseUrl/login" -Method POST -ContentType "application/json" -Body $loginData
    Write-Host "Login exitoso:" -ForegroundColor Green
    $loginResponse | ConvertTo-Json -Depth 3

    if ($loginResponse.data.access_token) {
        $token = $loginResponse.data.access_token
        Write-Host "Token obtenido: $($token.Substring(0, 20))..." -ForegroundColor Cyan

        # 3. Obtener información del usuario
        Write-Host "3. Obteniendo información del usuario..." -ForegroundColor Yellow
        try {
            $userResponse = Invoke-RestMethod -Uri "$baseUrl/user" -Method GET -Headers @{"Authorization" = "Bearer $token"}
            Write-Host "Usuario obtenido:" -ForegroundColor Green
            $userResponse | ConvertTo-Json -Depth 3
        } catch {
            Write-Host "Error obteniendo usuario:" -ForegroundColor Red
            Write-Host $_.Exception.Message
        }

    } else {
        Write-Host "No se obtuvo token del login" -ForegroundColor Red
    }

} catch {
    Write-Host "Error en login:" -ForegroundColor Red
    Write-Host $_.Exception.Message
}

Write-Host "Pruebas completadas!" -ForegroundColor Green
