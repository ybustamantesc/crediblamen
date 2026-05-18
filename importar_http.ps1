Add-Type -AssemblyName System.Net.Http

$url = "http://localhost/Conta/importar_saldos_iniciales.php"
$csvFile = "C:\xampp\htdocs\Conta\temp\saldos_iniciales_balance.csv"
$fechaApertura = "2025-01-01"
$descripcion = "Asiento de Apertura - Saldos Iniciales"

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "IMPORTACION DE SALDOS INICIALES" -ForegroundColor Cyan  
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Archivo: $csvFile" -ForegroundColor Yellow
Write-Host "Fecha: $fechaApertura" -ForegroundColor Yellow
Write-Host ""

try {
    $httpClient = New-Object System.Net.Http.HttpClient
    $content = New-Object System.Net.Http.MultipartFormDataContent
    
    # Agregar el archivo CSV
    $fileStream = [System.IO.File]::OpenRead($csvFile)
    $fileContent = New-Object System.Net.Http.StreamContent($fileStream)
    $fileContent.Headers.ContentType = [System.Net.Http.Headers.MediaTypeHeaderValue]::Parse("text/csv")
    $content.Add($fileContent, "saldosFile", [System.IO.Path]::GetFileName($csvFile))
    
    # Agregar los campos del formulario
    $fechaContent = New-Object System.Net.Http.StringContent($fechaApertura)
    $content.Add($fechaContent, "fechaApertura")
    
    $descContent = New-Object System.Net.Http.StringContent($descripcion)
    $content.Add($descContent, "descripcion")
    
    Write-Host "Enviando peticion..." -ForegroundColor Cyan
    
    # Enviar la petición
    $response = $httpClient.PostAsync($url, $content).Result
    $responseBody = $response.Content.ReadAsStringAsync().Result
    
    $fileStream.Close()
    $httpClient.Dispose()
    
    Write-Host ""
    Write-Host "================================================" -ForegroundColor Green
    Write-Host "RESPUESTA DEL SERVIDOR" -ForegroundColor Green
    Write-Host "================================================" -ForegroundColor Green
    Write-Host $responseBody
    Write-Host ""
    
    # Parsear JSON
    $jsonResult = $responseBody | ConvertFrom-Json
    
    if ($jsonResult.status -eq "success") {
        Write-Host "IMPORTACION EXITOSA" -ForegroundColor Green
        Write-Host "  - Asiento ID: $($jsonResult.data.journal_id)" -ForegroundColor White
        Write-Host "  - Cuentas procesadas: $($jsonResult.data.total_cuentas_procesadas)" -ForegroundColor White
        Write-Host "  - Cuentas creadas: $($jsonResult.data.cuentas_creadas)" -ForegroundColor White
        Write-Host "  - Cuentas existentes: $($jsonResult.data.cuentas_existentes)" -ForegroundColor White
        Write-Host "  - Total debe: $($jsonResult.data.total_debe)" -ForegroundColor White
        Write-Host "  - Total haber: $($jsonResult.data.total_haber)" -ForegroundColor White
        Write-Host "  - Cuadra: $(if($jsonResult.data.cuadra){'SI'}else{'NO'})" -ForegroundColor $(if($jsonResult.data.cuadra){'Green'}else{'Yellow'})
    } else {
        Write-Host "ERROR: $($jsonResult.message)" -ForegroundColor Red
    }
    
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
