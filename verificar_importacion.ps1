$server = "localhost"
$database = "u987557742_testsystem"
$username = "root"
$password = ""

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "VERIFICACION DE IMPORTACION" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Crear conexión MySQL
$connectionString = "server=$server;database=$database;uid=$username;pwd=$password;charset=utf8mb4"

try {
    # Cargar el driver de MySQL
    [void][System.Reflection.Assembly]::LoadWithPartialName("MySql.Data")
    
    # Usar mysql.exe directamente
    $mysqlPath = "C:\xampp\mysql\bin\mysql.exe"
    
    Write-Host "Consultando el asiento creado..." -ForegroundColor Yellow
    Write-Host ""
    
    $query1 = "SELECT id, date, description, total_debit, total_credit FROM tb_journal WHERE id = 20;"
    $result1 = & $mysqlPath -u root -h localhost $database -e $query1
    Write-Host $result1
    
    Write-Host ""
    Write-Host "Primeras 10 líneas del asiento:" -ForegroundColor Yellow
    Write-Host ""
    
    $query2 = "SELECT je.id, a.code, a.name, je.debit, je.credit FROM tb_journal_entry je INNER JOIN tb_account a ON je.account_id = a.id WHERE je.journal_id = 20 LIMIT 10;"
    $result2 = & $mysqlPath -u root -h localhost $database -e $query2
    Write-Host $result2
    
    Write-Host ""
    Write-Host "Total de cuentas creadas:" -ForegroundColor Yellow
    Write-Host ""
    
    $query3 = "SELECT COUNT(*) as total FROM tb_account;"
    $result3 = & $mysqlPath -u root -h localhost $database -e $query3
    Write-Host $result3
    
    Write-Host ""
    Write-Host "Resumen por tipo de cuenta:" -ForegroundColor Yellow
    Write-Host ""
    
    $query4 = "SELECT type, COUNT(*) as cantidad FROM tb_account GROUP BY type;"
    $result4 = & $mysqlPath -u root -h localhost $database -e $query4
    Write-Host $result4
    
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Verificacion completada" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
