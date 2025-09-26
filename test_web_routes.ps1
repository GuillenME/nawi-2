# Script para probar las rutas web
$baseUrl = "http://localhost:8000"

Write-Host "PROBANDO RUTAS WEB DE NAWI" -ForegroundColor Green
Write-Host "============================" -ForegroundColor Green
Write-Host ""

# 1. Página principal
Write-Host "1. Probando página principal..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "$baseUrl/" -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Página principal funciona" -ForegroundColor Green
    } else {
        Write-Host "❌ Error en página principal: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error accediendo a página principal: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# 2. Registro de taxista
Write-Host "2. Probando registro de taxista..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "$baseUrl/register/taxista" -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Registro de taxista funciona" -ForegroundColor Green
    } else {
        Write-Host "❌ Error en registro de taxista: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error accediendo a registro de taxista: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# 3. Login
Write-Host "3. Probando login..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "$baseUrl/login" -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Login funciona" -ForegroundColor Green
    } else {
        Write-Host "❌ Error en login: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error accediendo a login: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "🎉 Pruebas completadas!" -ForegroundColor Green
Write-Host ""
Write-Host "URLs disponibles:" -ForegroundColor Cyan
Write-Host "  - Pagina principal: http://localhost:8000/" -ForegroundColor White
Write-Host "  - Registro taxista: http://localhost:8000/register/taxista" -ForegroundColor White
Write-Host "  - Login: http://localhost:8000/login" -ForegroundColor White
Write-Host ""
Write-Host "APIs para app movil:" -ForegroundColor Cyan
Write-Host "  - Registro pasajero: POST http://localhost:8000/api/register/pasajero" -ForegroundColor White
Write-Host "  - Login: POST http://localhost:8000/api/login" -ForegroundColor White
