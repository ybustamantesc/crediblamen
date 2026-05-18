# Script simplificado para importar saldos iniciales
$url = "http://localhost/Conta/importar_saldos_iniciales.php"
$csvFile = "c:\xampp\htdocs\Conta\temp\saldos_iniciales_balance.csv"
$fechaApertura = "2025-01-01"

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "IMPORTACIÓN DE SALDOS INICIALES" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Archivo: $csvFile" -ForegroundColor Yellow
Write-Host "Fecha de apertura: $fechaApertura" -ForegroundColor Yellow
Write-Host ""

# Usar curl para hacer la petición
$curlCmd = "curl -X POST -F `"saldosFile=@$csvFile`" -F `"fechaApertura=$fechaApertura`" -F `"descripcion=Asiento de Apertura - Saldos Iniciales`" $url"

Write-Host "Ejecutando importación..." -ForegroundColor Cyan
Write-Host ""

try {
    $result = Invoke-Expression $curlCmd
    
    Write-Host "================================================" -ForegroundColor Green
    Write-Host "RESPUESTA DEL SERVIDOR" -ForegroundColor Green
    Write-Host "================================================" -ForegroundColor Green
    Write-Host $result
    Write-Host ""
    
    # Parsear JSON
    $jsonResult = $result | ConvertFrom-Json
    
    if ($jsonResult.status -eq "success") {
        Write-Host ""
        Write-Host "✓ IMPORTACIÓN EXITOSA" -ForegroundColor Green
        Write-Host "  - Asiento ID: $($jsonResult.data.journal_id)" -ForegroundColor White
        Write-Host "  - Cuentas procesadas: $($jsonResult.data.total_cuentas_procesadas)" -ForegroundColor White
        Write-Host "  - Cuentas creadas: $($jsonResult.data.cuentas_creadas)" -ForegroundColor White
        Write-Host "  - Total debe: $($jsonResult.data.total_debe)" -ForegroundColor White
        Write-Host "  - Total haber: $($jsonResult.data.total_haber)" -ForegroundColor White
    }
    
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
