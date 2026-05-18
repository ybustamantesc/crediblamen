# Script para importar saldos iniciales
$url = "http://localhost/Conta/importar_saldos_iniciales.php"
$csvFile = "c:\xampp\htdocs\Conta\temp\saldos_iniciales_balance.csv"

# Verificar que el archivo existe
if (-not (Test-Path $csvFile)) {
    Write-Host "ERROR: No se encontró el archivo CSV en $csvFile" -ForegroundColor Red
    exit 1
}

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "IMPORTACIÓN DE SALDOS INICIALES" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Archivo CSV: $csvFile" -ForegroundColor Yellow
Write-Host "URL: $url" -ForegroundColor Yellow
Write-Host ""

# Leer primeras líneas del CSV para mostrar vista previa
Write-Host "Vista previa del archivo CSV:" -ForegroundColor Green
Get-Content $csvFile -First 5 | ForEach-Object { Write-Host $_ }
Write-Host ""

# Solicitar fecha de apertura
$fechaApertura = Read-Host "Ingrese la fecha de apertura (formato YYYY-MM-DD, presione Enter para usar 2025-01-01)"
if ([string]::IsNullOrWhiteSpace($fechaApertura)) {
    $fechaApertura = "2025-01-01"
}

Write-Host ""
Write-Host "Fecha de apertura: $fechaApertura" -ForegroundColor Yellow
Write-Host ""
Write-Host "Procesando importación..." -ForegroundColor Cyan

# Crear el multipart form data
$boundary = [System.Guid]::NewGuid().ToString()
$LF = "`r`n"

# Leer el contenido del archivo
$fileContent = [System.IO.File]::ReadAllBytes($csvFile)
$fileName = [System.IO.Path]::GetFileName($csvFile)

# Construir el body del POST
$bodyLines = @(
    "--$boundary",
    "Content-Disposition: form-data; name=`"saldosFile`"; filename=`"$fileName`"",
    "Content-Type: text/csv",
    "",
    [System.Text.Encoding]::UTF8.GetString($fileContent),
    "--$boundary",
    "Content-Disposition: form-data; name=`"fechaApertura`"",
    "",
    $fechaApertura,
    "--$boundary",
    "Content-Disposition: form-data; name=`"descripcion`"",
    "",
    "Asiento de Apertura - Saldos Iniciales",
    "--$boundary--"
)

$body = $bodyLines -join $LF

try {
    # Hacer la petición POST
    $response = Invoke-WebRequest -Uri $url `
        -Method Post `
        -ContentType "multipart/form-data; boundary=$boundary" `
        -Body ([System.Text.Encoding]::UTF8.GetBytes($body)) `
        -UseBasicParsing `
        -TimeoutSec 300

    Write-Host ""
    Write-Host "================================================" -ForegroundColor Green
    Write-Host "RESPUESTA DEL SERVIDOR" -ForegroundColor Green
    Write-Host "================================================" -ForegroundColor Green
    Write-Host ""
    
    # Parsear y mostrar el JSON de respuesta
    $result = $response.Content | ConvertFrom-Json
    
    if ($result.status -eq "success") {
        Write-Host "✓ IMPORTACIÓN EXITOSA" -ForegroundColor Green
        Write-Host ""
        Write-Host "Detalles:" -ForegroundColor Cyan
        Write-Host "  - ID del asiento: $($result.data.journal_id)" -ForegroundColor White
        Write-Host "  - Fecha de apertura: $($result.data.fecha_apertura)" -ForegroundColor White
        Write-Host "  - Cuentas procesadas: $($result.data.total_cuentas_procesadas)" -ForegroundColor White
        Write-Host "  - Cuentas creadas: $($result.data.cuentas_creadas)" -ForegroundColor White
        Write-Host "  - Cuentas existentes: $($result.data.cuentas_existentes)" -ForegroundColor White
        Write-Host "  - Entradas creadas: $($result.data.entries_creadas)" -ForegroundColor White
        Write-Host "  - Total debe: $($result.data.total_debe)" -ForegroundColor White
        Write-Host "  - Total haber: $($result.data.total_haber)" -ForegroundColor White
        Write-Host "  - ¿Cuadra?: $(if($result.data.cuadra){'SÍ'}else{'NO'})" -ForegroundColor $(if($result.data.cuadra){'Green'}else{'Yellow'})
        
        if ($result.data.cuenta_ajuste_creada) {
            Write-Host "  - Se creó cuenta de ajuste" -ForegroundColor Yellow
        }
        
        if ($result.data.errores -and $result.data.errores.Count -gt 0) {
            Write-Host ""
            Write-Host "Errores encontrados:" -ForegroundColor Yellow
            $result.data.errores | ForEach-Object { Write-Host "  - $_" -ForegroundColor Yellow }
        }
    } else {
        Write-Host "✗ ERROR EN LA IMPORTACIÓN" -ForegroundColor Red
        Write-Host ""
        Write-Host "Mensaje: $($result.message)" -ForegroundColor Red
    }
    
    Write-Host ""
    Write-Host "Respuesta completa (JSON):" -ForegroundColor Cyan
    Write-Host $response.Content -ForegroundColor White
    
} catch {
    Write-Host ""
    Write-Host "✗ ERROR AL EJECUTAR LA PETICIÓN" -ForegroundColor Red
    Write-Host ""
    Write-Host "Mensaje de error: $($_.Exception.Message)" -ForegroundColor Red
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host ""
        Write-Host "Respuesta del servidor:" -ForegroundColor Yellow
        Write-Host $responseBody -ForegroundColor White
    }
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Proceso finalizado" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
