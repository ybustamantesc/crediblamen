$mysqlPath = "C:\xampp\mysql\bin\mysql.exe"
$database = "u987557742_testsystem"

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "BALANCE DE COMPROBACION - SALDOS INICIALES" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

$query = @"
SELECT 
    a.code as 'Codigo',
    a.name as 'Cuenta',
    a.type as 'Tipo',
    COALESCE(SUM(je.debit), 0) as 'Debe',
    COALESCE(SUM(je.credit), 0) as 'Haber',
    COALESCE(SUM(je.debit), 0) - COALESCE(SUM(je.credit), 0) as 'Saldo'
FROM tb_account a
LEFT JOIN tb_journal_entry je ON a.id = je.account_id AND je.journal_id = 20
WHERE a.code IN (
    SELECT DISTINCT a2.code 
    FROM tb_account a2
    INNER JOIN tb_journal_entry je2 ON a2.id = je2.account_id 
    WHERE je2.journal_id = 20
)
GROUP BY a.id, a.code, a.name, a.type
ORDER BY a.code;
"@

Write-Host "Detalle de cuentas con saldos iniciales:" -ForegroundColor Yellow
Write-Host ""

$result = & $mysqlPath -u root -h localhost $database -t -e $query
Write-Host $result

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "TOTALES DEL ASIENTO" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

$queryTotales = @"
SELECT 
    SUM(debit) as 'Total_Debe',
    SUM(credit) as 'Total_Haber',
    SUM(debit) - SUM(credit) as 'Diferencia'
FROM tb_journal_entry
WHERE journal_id = 20;
"@

$resultTotales = & $mysqlPath -u root -h localhost $database -t -e $queryTotales
Write-Host $resultTotales

Write-Host ""
