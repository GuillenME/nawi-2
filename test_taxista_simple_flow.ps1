# Script para probar el nuevo flujo de taxistas (registro simple + subir documentos)
$baseUrl = "http://nawi-2.me/api"

Write-Host "PROBANDO NUEVO FLUJO DE TAXISTAS" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green
Write-Host ""

# 1. Registrar taxista (solo datos básicos)
Write-Host "1. Registrando taxista (datos básicos)..." -ForegroundColor Yellow
$taxistaData = @{
    nombre = "María"
    apellido = "Taxista"
    telefono = "5555555555"
    email = "maria@taxista.com"
    password = "password123"
} | ConvertTo-Json

try {
    $taxistaResponse = Invoke-RestMethod -Uri "$baseUrl/register/taxista" -Method POST -ContentType "application/json" -Body $taxistaData
    Write-Host "Taxista registrado:" -ForegroundColor Green
    $taxistaResponse | ConvertTo-Json -Depth 3
} catch {
    Write-Host "Error registrando taxista:" -ForegroundColor Red
    Write-Host $_.Exception.Message
    exit
}
Write-Host ""

# 2. Login del taxista
Write-Host "2. Haciendo login del taxista..." -ForegroundColor Yellow
$loginData = @{
    email = "maria@taxista.com"
    password = "password123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$baseUrl/login" -Method POST -ContentType "application/json" -Body $loginData
    Write-Host "Login exitoso:" -ForegroundColor Green
    $loginResponse | ConvertTo-Json -Depth 3

    if ($loginResponse.data.access_token) {
        $token = $loginResponse.data.access_token
        Write-Host "Token obtenido: $($token.Substring(0, 20))..." -ForegroundColor Cyan
        Write-Host ""

        # 3. Obtener información del taxista
        Write-Host "3. Obteniendo información del taxista..." -ForegroundColor Yellow
        try {
            $taxistaInfo = Invoke-RestMethod -Uri "$baseUrl/taxista/me" -Method GET -Headers @{"Authorization" = "Bearer $token"}
            Write-Host "Información del taxista:" -ForegroundColor Green
            $taxistaInfo | ConvertTo-Json -Depth 3
        } catch {
            Write-Host "Error obteniendo información del taxista:" -ForegroundColor Red
            Write-Host $_.Exception.Message
        }
        Write-Host ""

        # 4. Subir matrícula
        Write-Host "4. Subiendo matrícula..." -ForegroundColor Yellow
        $matriculaData = @{
            url = "https://example.com/matricula-maria.jpg"
        } | ConvertTo-Json

        try {
            $matriculaResponse = Invoke-RestMethod -Uri "$baseUrl/taxista/upload-matricula" -Method POST -ContentType "application/json" -Body $matriculaData -Headers @{"Authorization" = "Bearer $token"}
            Write-Host "Matrícula subida:" -ForegroundColor Green
            $matriculaResponse | ConvertTo-Json -Depth 3
        } catch {
            Write-Host "Error subiendo matrícula:" -ForegroundColor Red
            Write-Host $_.Exception.Message
        }
        Write-Host ""

        # 5. Subir licencia
        Write-Host "5. Subiendo licencia..." -ForegroundColor Yellow
        $licenciaData = @{
            url = "https://example.com/licencia-maria.jpg"
        } | ConvertTo-Json

        try {
            $licenciaResponse = Invoke-RestMethod -Uri "$baseUrl/taxista/upload-licencia" -Method POST -ContentType "application/json" -Body $licenciaData -Headers @{"Authorization" = "Bearer $token"}
            Write-Host "Licencia subida:" -ForegroundColor Green
            $licenciaResponse | ConvertTo-Json -Depth 3
        } catch {
            Write-Host "Error subiendo licencia:" -ForegroundColor Red
            Write-Host $_.Exception.Message
        }
        Write-Host ""

        # 6. Ver documentos subidos
        Write-Host "6. Verificando documentos subidos..." -ForegroundColor Yellow
        try {
            $documentsResponse = Invoke-RestMethod -Uri "$baseUrl/taxista/documents" -Method GET -Headers @{"Authorization" = "Bearer $token"}
            Write-Host "Documentos del taxista:" -ForegroundColor Green
            $documentsResponse | ConvertTo-Json -Depth 3
        } catch {
            Write-Host "Error obteniendo documentos:" -ForegroundColor Red
            Write-Host $_.Exception.Message
        }

    } else {
        Write-Host "No se obtuvo token del login" -ForegroundColor Red
    }

} catch {
    Write-Host "Error en login:" -ForegroundColor Red
    Write-Host $_.Exception.Message
}

Write-Host ""
Write-Host "Pruebas completadas!" -ForegroundColor Green
