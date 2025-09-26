# Script para probar el registro de taxistas
$baseUrl = "http://nawi-2.me/api"

Write-Host "PROBANDO REGISTRO DE TAXISTAS" -ForegroundColor Green
Write-Host "=============================" -ForegroundColor Green
Write-Host ""

# 1. Crear matrícula
Write-Host "1. Creando matrícula..." -ForegroundColor Yellow
$matriculaData = @{
    url = "https://example.com/matricula.jpg"
    fecha_subida = (Get-Date).ToString("yyyy-MM-dd HH:mm:ss")
    id_estatus = "1"  # pendiente
} | ConvertTo-Json

try {
    $matriculaResponse = Invoke-RestMethod -Uri "$baseUrl/matriculas" -Method POST -ContentType "application/json" -Body $matriculaData
    Write-Host "Matrícula creada:" -ForegroundColor Green
    $matriculaResponse | ConvertTo-Json -Depth 3
    $matriculaId = $matriculaResponse.data.id
} catch {
    Write-Host "Error creando matrícula:" -ForegroundColor Red
    Write-Host $_.Exception.Message
    exit
}
Write-Host ""

# 2. Crear licencia
Write-Host "2. Creando licencia..." -ForegroundColor Yellow
$licenciaData = @{
    url = "https://example.com/licencia.jpg"
    fecha_subida = (Get-Date).ToString("yyyy-MM-dd HH:mm:ss")
    id_estatus = "1"  # pendiente
} | ConvertTo-Json

try {
    $licenciaResponse = Invoke-RestMethod -Uri "$baseUrl/licencias" -Method POST -ContentType "application/json" -Body $licenciaData
    Write-Host "Licencia creada:" -ForegroundColor Green
    $licenciaResponse | ConvertTo-Json -Depth 3
    $licenciaId = $licenciaResponse.data.id
} catch {
    Write-Host "Error creando licencia:" -ForegroundColor Red
    Write-Host $_.Exception.Message
    exit
}
Write-Host ""

# 3. Registrar taxista
Write-Host "3. Registrando taxista..." -ForegroundColor Yellow
$taxistaData = @{
    nombre = "Carlos"
    apellido = "Taxista"
    telefono = "9876543210"
    email = "carlos@taxista.com"
    password = "password123"
    id_matricula = $matriculaId
    id_licencia = $licenciaId
} | ConvertTo-Json

try {
    $taxistaResponse = Invoke-RestMethod -Uri "$baseUrl/register/taxista" -Method POST -ContentType "application/json" -Body $taxistaData
    Write-Host "Taxista registrado:" -ForegroundColor Green
    $taxistaResponse | ConvertTo-Json -Depth 3
} catch {
    Write-Host "Error registrando taxista:" -ForegroundColor Red
    Write-Host $_.Exception.Message
}
Write-Host ""

Write-Host "Pruebas completadas!" -ForegroundColor Green
